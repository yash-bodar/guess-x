<?php

namespace App\Services;

use App\Exceptions\GameAlreadyFinishedException;
use App\Exceptions\InvalidGuessLengthException;
use App\Exceptions\InvalidWordException;
use App\Exceptions\NoWordsFoundException;
use App\Models\Game;
use App\Models\GameGuess;
use App\Models\Word;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WordGameService
{
    /**
     * Start a new word game session.
     *
     * // YB - 15-09-2026 Start a new game session with curated target word and X-factor clue
     *
     * @throws NoWordsFoundException
     */
    public function startNewGame(int $wordLength, ?\App\Models\User $user = null): Game
    {
        $wordLength = (int) $wordLength;

        // Prioritize curated targetable words for secret answer
        $randomWord = Word::where('length', $wordLength)
            ->where('is_targetable', true)
            ->where('is_valid', true)
            ->inRandomOrder()
            ->first();

        // Fallback to any valid word of that length
        if (! $randomWord) {
            $randomWord = Word::where('length', $wordLength)
                ->where('is_valid', true)
                ->inRandomOrder()
                ->first();
        }

        if (! $randomWord) {
            Log::error("No valid words found in database for length: {$wordLength}");
            throw new NoWordsFoundException($wordLength);
        }

        $secretWord = strtoupper(trim($randomWord->word));

        // Generate random 1-based X-Factor position (1 to wordLength)
        $xFactorPosition = random_int(1, $wordLength);
        $xFactorLetter = $secretWord[$xFactorPosition - 1];

        // Max guesses is strictly equal to word length (5 for 5-letter, 6 for 6, 7 for 7)
        $maxGuesses = $wordLength;

        return DB::transaction(function () use ($randomWord, $wordLength, $maxGuesses, $xFactorPosition, $xFactorLetter, $user) {
            $game = Game::create([
                'user_id' => $user?->id,
                'word_id' => $randomWord->id,
                'word_length' => $wordLength,
                'max_guesses' => $maxGuesses,
                'x_factor_position' => $xFactorPosition,
                'x_factor_letter' => $xFactorLetter,
                'status' => 'playing',
                'started_at' => now(),
            ]);

            Log::info("Word game started [Game ID: {$game->id}, User: " . ($user?->id ?? 'Guest') . ", Length: {$wordLength}, X-Factor: {$xFactorPosition}={$xFactorLetter}]");

            return $game;
        });
    }

    /**
     * Evaluate and process a user's guess for a game.
     *
     * // YB - 15-09-2026 Process guess submission with duplicate-aware tile evaluation and state management
     *
     * @throws GameAlreadyFinishedException
     * @throws InvalidGuessLengthException
     * @throws InvalidWordException
     */
    public function submitGuess(Game $game, string $guess): array
    {
        if ($game->isFinished()) {
            throw new GameAlreadyFinishedException($game->status);
        }

        $guess = strtoupper(trim($guess));
        $guessLength = mb_strlen($guess);

        if ($guessLength !== $game->word_length) {
            throw new InvalidGuessLengthException($game->word_length, $guessLength);
        }

        // Validate that the guess exists in the dictionary of valid words
        if (! $this->isValidWord($guess, $game->word_length)) {
            throw new InvalidWordException("Word '{$guess}' is not in the dictionary.");
        }

        $targetWord = strtoupper(trim($game->word->word));
        $resultTiles = $this->evaluateGuess($targetWord, $guess);

        $existingGuessesCount = $game->guesses()->count();
        $newGuessNumber = $existingGuessesCount + 1;

        $isWin = ! in_array('yellow', $resultTiles, true) && ! in_array('gray', $resultTiles, true);
        $isLoss = ! $isWin && ($newGuessNumber >= $game->max_guesses);

        $newStatus = $isWin ? 'won' : ($isLoss ? 'lost' : 'playing');

        return DB::transaction(function () use ($game, $guess, $resultTiles, $newGuessNumber, $newStatus, $targetWord, $isWin) {
            $gameGuess = GameGuess::create([
                'game_id' => $game->id,
                'guess' => $guess,
                'result' => $resultTiles,
                'guess_number' => $newGuessNumber,
            ]);

            if ($newStatus !== 'playing') {
                $game->update([
                    'status' => $newStatus,
                    'completed_at' => now(),
                ]);

                // Update registered user stats if game is linked to an account
                if ($game->user) {
                    $game->user->recordGameResult($isWin);
                }
            }

            Log::info("Guess processed [Game ID: {$game->id}, Guess: {$guess}, Result: " . implode(',', $resultTiles) . ", Status: {$newStatus}]");

            $guessesRemaining = max(0, $game->max_guesses - $newGuessNumber);

            return [
                'guess' => $guess,
                'guess_number' => $newGuessNumber,
                'result' => $resultTiles,
                'game_status' => $newStatus,
                'guesses_remaining' => $guessesRemaining,
                'secret_word' => ($newStatus === 'won' || $newStatus === 'lost') ? $targetWord : null,
            ];
        });
    }

    /**
     * Evaluate a guess against a target word using a two-pass duplicate-aware algorithm.
     *
     * // YB - 15-09-2026 Two-pass duplicate-aware Wordle tile evaluation (green, yellow, gray)
     *
     * @return array<int, string>
     */
    public function evaluateGuess(string $targetWord, string $guessWord): array
    {
        $targetWord = strtoupper(trim($targetWord));
        $guessWord = strtoupper(trim($guessWord));
        $length = mb_strlen($targetWord);

        $targetLetters = mb_str_split($targetWord);
        $guessLetters = mb_str_split($guessWord);

        $result = array_fill(0, $length, null);

        // Count frequencies of each letter in the target word
        $targetLetterCounts = [];
        foreach ($targetLetters as $char) {
            $targetLetterCounts[$char] = ($targetLetterCounts[$char] ?? 0) + 1;
        }

        // Pass 1: Mark exact matches (GREEN) and decrement available counts
        for ($i = 0; $i < $length; $i++) {
            if ($guessLetters[$i] === $targetLetters[$i]) {
                $result[$i] = 'green';
                $targetLetterCounts[$guessLetters[$i]]--;
            }
        }

        // Pass 2: Mark present in wrong position (YELLOW) or absent (GRAY)
        for ($i = 0; $i < $length; $i++) {
            if ($result[$i] !== null) {
                continue; // Already green
            }

            $char = $guessLetters[$i];
            if (isset($targetLetterCounts[$char]) && $targetLetterCounts[$char] > 0) {
                $result[$i] = 'yellow';
                $targetLetterCounts[$char]--;
            } else {
                $result[$i] = 'gray';
            }
        }

        return $result;
    }

    /**
     * Check if a word exists in the dictionary.
     *
     * // YB - 15-09-2026 Check word validity in dictionary
     */
    public function isValidWord(string $word, int $length): bool
    {
        return Word::where('length', $length)
            ->where('word', strtoupper(trim($word)))
            ->where('is_valid', true)
            ->exists();
    }
}

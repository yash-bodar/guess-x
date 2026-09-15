<?php

namespace Tests\Unit;

use App\Exceptions\GameAlreadyFinishedException;
use App\Exceptions\InvalidGuessLengthException;
use App\Exceptions\InvalidWordException;
use App\Models\Game;
use App\Models\Word;
use App\Services\WordGameService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WordGameServiceTest extends TestCase
{
    use RefreshDatabase;

    protected WordGameService $service;

    /**
     * Set up testing environment.
     *
     * // YB - 15-09-2026 Setup service instance and base test words
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new WordGameService();
    }

    /**
     * Test duplicate-aware tile evaluation for various edge cases.
     *
     * // YB - 15-09-2026 Test duplicate letter evaluation rules
     */
    public function test_duplicate_letter_evaluation_rules(): void
    {
        // Case 1: Target = SPEED, Guess = ERASE
        // E at index 0 is in SPEED (1 occurrence matched yellow), S at index 3 is yellow, E at index 4 is yellow
        $res1 = $this->service->evaluateGuess('SPEED', 'ERASE');
        $this->assertEquals(['yellow', 'gray', 'gray', 'yellow', 'yellow'], $res1);

        // Case 2: Target = APPLE, Guess = PUPPY
        // P at pos 2 matches P (green). P at pos 0 takes remaining P (yellow). Third P at pos 3 is gray!
        $res2 = $this->service->evaluateGuess('APPLE', 'PUPPY');
        $this->assertEquals(['yellow', 'gray', 'green', 'gray', 'gray'], $res2);

        // Case 3: Target = ROBOT, Guess = BOBBY
        // ROBOT has 1 B (at pos 2). Guess has exact match at pos 2 (green) and pos 1 (green). Remaining B's at pos 0 and pos 3 are gray.
        $res3 = $this->service->evaluateGuess('ROBOT', 'BOBBY');
        $this->assertEquals(['gray', 'green', 'green', 'gray', 'gray'], $res3);

        // Case 4: Target = BOMBS, Guess = BOBBY
        // B at pos 0 is green. O at pos 1 is green. B at pos 3 is green. Remaining B at pos 2 is gray because all B's in target are consumed by greens!
        $res4 = $this->service->evaluateGuess('BOMBS', 'BOBBY');
        $this->assertEquals(['green', 'green', 'gray', 'green', 'gray'], $res4);

        // Case 5: Target = CIVIL, Guess = LIMIT
        // I at index 1 is green. I at index 3 is green. L at index 0 is yellow. M at index 2 and T at index 4 are gray.
        $res5 = $this->service->evaluateGuess('CIVIL', 'LIMIT');
        $this->assertEquals(['yellow', 'green', 'gray', 'green', 'gray'], $res5);
    }

    /**
     * Test all green tiles on correct guess.
     *
     * // YB - 15-09-2026 Test exact match returns all green tiles
     */
    public function test_exact_match_returns_all_green(): void
    {
        $res = $this->service->evaluateGuess('PLANET', 'PLANET');
        $this->assertEquals(['green', 'green', 'green', 'green', 'green', 'green'], $res);
    }

    /**
     * Test completely incorrect guess returns all gray tiles.
     *
     * // YB - 15-09-2026 Test completely incorrect guess returns gray tiles
     */
    public function test_incorrect_guess_returns_all_gray(): void
    {
        $res = $this->service->evaluateGuess('BRICK', 'PLUMS');
        $this->assertEquals(['gray', 'gray', 'gray', 'gray', 'gray'], $res);
    }

    /**
     * Test starting a 5-letter game creates matching word length, max guesses, and valid X-Factor.
     *
     * // YB - 15-09-2026 Test 5-letter game initialization and X-Factor derivation
     */
    public function test_start_5_letter_game(): void
    {
        Word::create(['word' => 'PLANT', 'length' => 5, 'is_valid' => true]);

        $game = $this->service->startNewGame(5);

        $this->assertEquals(5, $game->word_length);
        $this->assertEquals(5, $game->max_guesses);
        $this->assertGreaterThanOrEqual(1, $game->x_factor_position);
        $this->assertLessThanOrEqual(5, $game->x_factor_position);
        $this->assertEquals('PLANT'[$game->x_factor_position - 1], $game->x_factor_letter);
        $this->assertEquals('playing', $game->status);
    }

    /**
     * Test starting a 6-letter game creates matching word length, max guesses, and valid X-Factor.
     *
     * // YB - 15-09-2026 Test 6-letter game initialization
     */
    public function test_start_6_letter_game(): void
    {
        Word::create(['word' => 'PLANET', 'length' => 6, 'is_valid' => true]);

        $game = $this->service->startNewGame(6);

        $this->assertEquals(6, $game->word_length);
        $this->assertEquals(6, $game->max_guesses);
        $this->assertGreaterThanOrEqual(1, $game->x_factor_position);
        $this->assertLessThanOrEqual(6, $game->x_factor_position);
        $this->assertEquals('PLANET'[$game->x_factor_position - 1], $game->x_factor_letter);
    }

    /**
     * Test starting a 7-letter game creates matching word length, max guesses, and valid X-Factor.
     *
     * // YB - 15-09-2026 Test 7-letter game initialization
     */
    public function test_start_7_letter_game(): void
    {
        Word::create(['word' => 'CABINET', 'length' => 7, 'is_valid' => true]);

        $game = $this->service->startNewGame(7);

        $this->assertEquals(7, $game->word_length);
        $this->assertEquals(7, $game->max_guesses);
        $this->assertGreaterThanOrEqual(1, $game->x_factor_position);
        $this->assertLessThanOrEqual(7, $game->x_factor_position);
        $this->assertEquals('CABINET'[$game->x_factor_position - 1], $game->x_factor_letter);
    }

    /**
     * Test guess validation: invalid length throws exception.
     *
     * // YB - 15-09-2026 Test guess length mismatch exception
     */
    public function test_too_short_or_too_long_guess_throws_exception(): void
    {
        $word = Word::create(['word' => 'CRANE', 'length' => 5, 'is_valid' => true]);
        $game = Game::create([
            'word_id' => $word->id,
            'word_length' => 5,
            'max_guesses' => 5,
            'x_factor_position' => 1,
            'x_factor_letter' => 'C',
            'status' => 'playing',
        ]);

        $this->expectException(InvalidGuessLengthException::class);
        $this->service->submitGuess($game, 'CAT');
    }

    /**
     * Test guess validation: invalid word not in dictionary throws exception.
     *
     * // YB - 15-09-2026 Test unknown word dictionary exception
     */
    public function test_invalid_word_throws_exception(): void
    {
        $word = Word::create(['word' => 'CRANE', 'length' => 5, 'is_valid' => true]);
        $game = Game::create([
            'word_id' => $word->id,
            'word_length' => 5,
            'max_guesses' => 5,
            'x_factor_position' => 1,
            'x_factor_letter' => 'C',
            'status' => 'playing',
        ]);

        $this->expectException(InvalidWordException::class);
        $this->service->submitGuess($game, 'ZZZZZ');
    }

    /**
     * Test winning state transition on correct guess.
     *
     * // YB - 15-09-2026 Test win condition transition
     */
    public function test_win_state_transition(): void
    {
        $word = Word::create(['word' => 'CRANE', 'length' => 5, 'is_valid' => true]);
        $game = Game::create([
            'word_id' => $word->id,
            'word_length' => 5,
            'max_guesses' => 5,
            'x_factor_position' => 1,
            'x_factor_letter' => 'C',
            'status' => 'playing',
        ]);

        $result = $this->service->submitGuess($game, 'CRANE');

        $this->assertEquals('won', $result['game_status']);
        $this->assertEquals('CRANE', $result['secret_word']);
        $this->assertEquals('won', $game->fresh()->status);
    }

    /**
     * Test losing state transition when max guesses are reached.
     *
     * // YB - 15-09-2026 Test loss condition after max guesses
     */
    public function test_loss_state_transition_after_max_guesses(): void
    {
        $target = Word::create(['word' => 'CRANE', 'length' => 5, 'is_valid' => true]);
        $guessWord = Word::create(['word' => 'AUDIO', 'length' => 5, 'is_valid' => true]);

        $game = Game::create([
            'word_id' => $target->id,
            'word_length' => 5,
            'max_guesses' => 5,
            'x_factor_position' => 1,
            'x_factor_letter' => 'C',
            'status' => 'playing',
        ]);

        // Submit 4 incorrect guesses
        for ($i = 1; $i <= 4; $i++) {
            $res = $this->service->submitGuess($game, 'AUDIO');
            $this->assertEquals('playing', $res['game_status']);
        }

        // 5th guess (maximum)
        $finalRes = $this->service->submitGuess($game, 'AUDIO');
        $this->assertEquals('lost', $finalRes['game_status']);
        $this->assertEquals('CRANE', $finalRes['secret_word']);
        $this->assertEquals(0, $finalRes['guesses_remaining']);
        $this->assertEquals('lost', $game->fresh()->status);

        // Attempting a 6th guess should throw GameAlreadyFinishedException
        $this->expectException(GameAlreadyFinishedException::class);
        $this->service->submitGuess($game->fresh(), 'AUDIO');
    }
}

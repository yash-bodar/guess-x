<?php

namespace App\Http\Controllers;

use App\Exceptions\GameAlreadyFinishedException;
use App\Exceptions\InvalidGuessLengthException;
use App\Exceptions\InvalidWordException;
use App\Exceptions\NoWordsFoundException;
use App\Http\Requests\StartGameRequest;
use App\Http\Requests\SubmitGuessRequest;
use App\Http\Resources\GameResource;
use App\Models\Game;
use App\Services\WordGameService;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class GameController extends Controller
{
    /**
     * Display the main game SPA view.
     *
     * // YB - 15-09-2026 Render main Vue 3 game interface via Inertia
     */
    public function index(): Response
    {
        return Inertia::render('Game/Index', [
            'initialMode' => 5,
            'supportedModes' => [5, 6, 7],
        ]);
    }

    /**
     * Start or resume today's Daily Challenge for an authenticated user.
     *
     * // YB - 17-09-2026 Initialize or resume synchronized daily challenge (authenticated users only)
     */
    public function dailyStart(\Illuminate\Http\Request $request, WordGameService $gameService): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Please sign in to play the Daily Challenge.',
            ], 401);
        }

        try {
            $game = $gameService->startDailyGame($user);
            $game->loadMissing(['guesses', 'word']);

            $secondsUntilNext = max(0, now()->endOfDay()->diffInSeconds(now()));

            return response()->json([
                'success' => true,
                'data' => new GameResource($game),
                'meta' => [
                    'is_daily' => true,
                    'daily_date' => $game->daily_date ? $game->daily_date->toDateString() : now()->toDateString(),
                    'daily_streak' => $user->daily_streak,
                    'daily_max_streak' => $user->daily_max_streak,
                    'seconds_until_next' => $secondsUntilNext,
                ],
            ], 200);
        } catch (NoWordsFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Retrieve status of today's Daily Challenge for current user/guest.
     *
     * // YB - 17-09-2026 Check daily challenge completion state and countdown to midnight UTC
     */
    public function dailyStatus(\Illuminate\Http\Request $request): JsonResponse
    {
        $user = $request->user();
        $today = now()->toDateString();
        $secondsUntilNext = max(0, now()->endOfDay()->diffInSeconds(now()));

        if (! $user) {
            return response()->json([
                'success' => true,
                'data' => [
                    'authenticated' => false,
                    'message' => 'Sign in to access Daily Challenge.',
                    'seconds_until_next' => $secondsUntilNext,
                ],
            ]);
        }

        $dailyGame = Game::where('user_id', $user->id)
            ->where('is_daily', true)
            ->whereDate('daily_date', $today)
            ->with(['guesses', 'word'])
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'authenticated' => true,
                'has_played_today' => (bool) $dailyGame,
                'is_completed' => $dailyGame ? $dailyGame->isFinished() : false,
                'game' => $dailyGame ? new GameResource($dailyGame) : null,
                'daily_streak' => $user->daily_streak,
                'daily_max_streak' => $user->daily_max_streak,
                'seconds_until_next' => $secondsUntilNext,
            ],
        ]);
    }

    /**
     * Start a new game session.
     *
     * // YB - 15-09-2026 Initialize new game session with requested word length
     */
    public function start(StartGameRequest $request, WordGameService $gameService): JsonResponse
    {
        try {
            $wordLength = (int) $request->validated('word_length');
            $game = $gameService->startNewGame($wordLength, $request->user());

            return response()->json([
                'success' => true,
                'data' => new GameResource($game),
            ], 201);
        } catch (NoWordsFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Submit a guess for an active game session.
     *
     * // YB - 15-09-2026 Process submitted guess and return evaluation tiles
     */
    public function guess(SubmitGuessRequest $request, Game $game, WordGameService $gameService): JsonResponse
    {
        // Enforce user ownership isolation (prevent cross-session play between guest and logged-in accounts)
        if ($game->user_id !== $request->user()?->id) {
            return response()->json([
                'success' => false,
                'message' => 'This game session does not belong to the current user.',
            ], 403);
        }

        try {
            $result = $gameService->submitGuess($game, $request->validated('guess'));

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (InvalidWordException|InvalidGuessLengthException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (GameAlreadyFinishedException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Fetch the current state of a game.
     *
     * // YB - 15-09-2026 Retrieve active or finished game state
     */
    public function show(\Illuminate\Http\Request $request, Game $game): JsonResponse
    {
        // Enforce user ownership isolation
        if ($game->user_id !== $request->user()?->id) {
            return response()->json([
                'success' => false,
                'message' => 'This game session does not belong to the current user.',
            ], 403);
        }

        $game->load('guesses');

        return response()->json([
            'success' => true,
            'data' => new GameResource($game),
        ]);
    }
}

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

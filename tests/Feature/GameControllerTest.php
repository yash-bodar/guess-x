<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Word;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Set up test word dictionary.
     *
     * // YB - 15-09-2026 Seed test dictionary words
     */
    protected function setUp(): void
    {
        parent::setUp();

        Word::create(['word' => 'APPLE', 'length' => 5, 'is_valid' => true]);
        Word::create(['word' => 'CRANE', 'length' => 5, 'is_valid' => true]);
        Word::create(['word' => 'PLANET', 'length' => 6, 'is_valid' => true]);
        Word::create(['word' => 'CABINET', 'length' => 7, 'is_valid' => true]);
    }

    /**
     * Test game page renders successfully.
     *
     * // YB - 15-09-2026 Test main game page response
     */
    public function test_game_index_page_renders(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /**
     * Test starting a game via API endpoint.
     *
     * // YB - 15-09-2026 Test POST /api/game/start endpoint
     */
    public function test_start_game_api(): void
    {
        $response = $this->postJson('/api/game/start', [
            'word_length' => 5,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'word_length',
                    'max_guesses',
                    'x_factor' => [
                        'position',
                        'letter',
                    ],
                    'status',
                    'guesses',
                    'guesses_remaining',
                    'secret_word',
                ],
            ]);

        // Secret word must be strictly hidden (null) during active gameplay
        $this->assertNull($response->json('data.secret_word'));
        $this->assertEquals(5, $response->json('data.word_length'));
        $this->assertEquals(5, $response->json('data.max_guesses'));
        $this->assertEquals(5, $response->json('data.guesses_remaining'));
    }

    /**
     * Test guess submission via API endpoint.
     *
     * // YB - 15-09-2026 Test POST /api/game/{game}/guess endpoint
     */
    public function test_submit_guess_api(): void
    {
        $target = Word::where('word', 'APPLE')->first();
        $game = Game::create([
            'word_id' => $target->id,
            'word_length' => 5,
            'max_guesses' => 5,
            'x_factor_position' => 1,
            'x_factor_letter' => 'A',
            'status' => 'playing',
        ]);

        $response = $this->postJson("/api/game/{$game->id}/guess", [
            'guess' => 'CRANE',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'guess' => 'CRANE',
                    'guess_number' => 1,
                    'game_status' => 'playing',
                    'guesses_remaining' => 4,
                    'secret_word' => null,
                ],
            ]);
    }

    /**
     * Test win via API reveals the secret word.
     *
     * // YB - 15-09-2026 Test winning guess reveals secret word
     */
    public function test_win_reveals_secret_word(): void
    {
        $target = Word::where('word', 'APPLE')->first();
        $game = Game::create([
            'word_id' => $target->id,
            'word_length' => 5,
            'max_guesses' => 5,
            'x_factor_position' => 1,
            'x_factor_letter' => 'A',
            'status' => 'playing',
        ]);

        $response = $this->postJson("/api/game/{$game->id}/guess", [
            'guess' => 'APPLE',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'guess' => 'APPLE',
                    'game_status' => 'won',
                    'secret_word' => 'APPLE',
                ],
            ]);
    }

    /**
     * Test invalid word length returns 422 error.
     *
     * // YB - 15-09-2026 Test invalid word length error response
     */
    public function test_invalid_guess_length_returns_422(): void
    {
        $target = Word::where('word', 'APPLE')->first();
        $game = Game::create([
            'word_id' => $target->id,
            'word_length' => 5,
            'max_guesses' => 5,
            'x_factor_position' => 1,
            'x_factor_letter' => 'A',
            'status' => 'playing',
        ]);

        $response = $this->postJson("/api/game/{$game->id}/guess", [
            'guess' => 'PLANET',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }
}

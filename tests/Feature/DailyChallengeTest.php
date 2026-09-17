<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Word;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DailyChallengeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Set up test environment and target words.
     *
     * // YB - 17-09-2026 Seed test dictionary words for daily challenge tests
     */
    protected function setUp(): void
    {
        parent::setUp();

        Word::create(['word' => 'APPLE', 'length' => 5, 'is_valid' => true, 'is_targetable' => true]);
        Word::create(['word' => 'CRANE', 'length' => 5, 'is_valid' => true, 'is_targetable' => true]);
        Word::create(['word' => 'GRAPE', 'length' => 5, 'is_valid' => true, 'is_targetable' => true]);
        Word::create(['word' => 'LIGHT', 'length' => 5, 'is_valid' => true, 'is_targetable' => true]);
        Word::create(['word' => 'WORLD', 'length' => 5, 'is_valid' => true, 'is_targetable' => true]);
    }

    /**
     * Test guest cannot start daily challenge (must be authenticated).
     *
     * // YB - 17-09-2026 Verify guest is rejected with HTTP 401 on daily start
     */
    public function test_guest_cannot_start_daily_challenge(): void
    {
        $response = $this->postJson('/api/game/daily/start');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Please sign in to play the Daily Challenge.',
            ]);
    }

    /**
     * Test authenticated user can start daily challenge.
     *
     * // YB - 17-09-2026 Verify signed-in user successfully initiates daily challenge
     */
    public function test_signed_in_user_can_start_daily_challenge(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/game/daily/start');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'meta' => [
                    'is_daily' => true,
                ],
            ])
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'word_length',
                    'max_guesses',
                    'is_daily',
                    'daily_date',
                    'x_factor',
                ],
            ]);
    }

    /**
     * Test different users receive the exact same daily word on the same date.
     *
     * // YB - 17-09-2026 Verify deterministic daily challenge synchronization across users
     */
    public function test_different_users_receive_same_daily_word(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $res1 = $this->actingAs($user1)->postJson('/api/game/daily/start');
        $res2 = $this->actingAs($user2)->postJson('/api/game/daily/start');

        $game1 = $user1->games()->where('is_daily', true)->first();
        $game2 = $user2->games()->where('is_daily', true)->first();

        $this->assertEquals($game1->word_id, $game2->word_id);
        $this->assertEquals($game1->x_factor_position, $game2->x_factor_position);
        $this->assertEquals($game1->x_factor_letter, $game2->x_factor_letter);
    }

    /**
     * Test user cannot create a second daily game on the same date (resumes existing).
     *
     * // YB - 17-09-2026 Verify idempotency of daily challenge start
     */
    public function test_user_resumes_same_daily_game_on_same_date(): void
    {
        $user = User::factory()->create();

        $res1 = $this->actingAs($user)->postJson('/api/game/daily/start');
        $gameId1 = $res1->json('data.id');

        $res2 = $this->actingAs($user)->postJson('/api/game/daily/start');
        $gameId2 = $res2->json('data.id');

        $this->assertEquals($gameId1, $gameId2);
        $this->assertEquals(1, $user->games()->where('is_daily', true)->count());
    }

    /**
     * Test completing daily challenge updates daily streak.
     *
     * // YB - 17-09-2026 Verify winning daily challenge increments user daily_streak
     */
    public function test_winning_daily_challenge_updates_daily_streak(): void
    {
        $user = User::factory()->create([
            'daily_streak' => 0,
            'daily_max_streak' => 0,
        ]);

        $res = $this->actingAs($user)->postJson('/api/game/daily/start');
        $gameId = $res->json('data.id');

        $game = $user->games()->where('id', $gameId)->first();
        $secretWord = $game->word->word;

        // Submit winning guess
        $guessRes = $this->actingAs($user)->postJson("/api/game/{$gameId}/guess", [
            'guess' => $secretWord,
        ]);

        $guessRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'game_status' => 'won',
                ],
            ]);

        $user->refresh();
        $this->assertEquals(1, $user->daily_streak);
        $this->assertEquals(1, $user->daily_max_streak);
        $this->assertEquals(now()->toDateString(), $user->last_daily_date->toDateString());
    }

    /**
     * Test daily status endpoint for guest and authenticated user.
     *
     * // YB - 17-09-2026 Verify daily status API endpoint
     */
    public function test_daily_status_endpoint(): void
    {
        // Guest check
        $guestRes = $this->getJson('/api/game/daily/status');
        $guestRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'authenticated' => false,
                ],
            ]);

        // Authenticated check
        $user = User::factory()->create();
        $authRes = $this->actingAs($user)->getJson('/api/game/daily/status');
        $authRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'authenticated' => true,
                    'has_played_today' => false,
                ],
            ]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\User;
use App\Models\Word;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Set up test data.
     *
     * // YB - 15-09-2026 Set up initial test words
     */
    protected function setUp(): void
    {
        parent::setUp();

        Word::create(['word' => 'APPLE', 'length' => 5, 'is_valid' => true]);
    }

    /**
     * Test user registration.
     *
     * // YB - 15-09-2026 Test POST /api/auth/register endpoint
     */
    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'user' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);
        $this->assertAuthenticated();
    }

    /**
     * Test user login with valid credentials.
     *
     * // YB - 15-09-2026 Test POST /api/auth/login endpoint
     */
    public function test_user_can_login(): void
    {
        $user = User::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'jane@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'user' => [
                    'email' => 'jane@example.com',
                ],
            ]);

        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test user login with invalid password returns 422.
     *
     * // YB - 15-09-2026 Test invalid login credentials
     */
    public function test_login_with_invalid_credentials_fails(): void
    {
        User::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'jane@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertGuest();
    }

    /**
     * Test user logout.
     *
     * // YB - 15-09-2026 Test POST /api/auth/logout endpoint
     */
    public function test_user_can_logout(): void
    {
        $user = User::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($user);

        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertGuest();
    }

    /**
     * Test requesting password reset code.
     *
     * // YB - 15-09-2026 Test POST /api/auth/forgot-password endpoint
     */
    public function test_forgot_password_generates_token(): void
    {
        User::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => 'jane@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'jane@example.com',
        ]);
    }

    /**
     * Test resetting password with valid code.
     *
     * // YB - 15-09-2026 Test POST /api/auth/reset-password endpoint
     */
    public function test_reset_password_with_valid_code(): void
    {
        $user = User::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => Hash::make('oldpassword'),
        ]);

        $pin = '654321';
        DB::table('password_reset_tokens')->insert([
            'email' => 'jane@example.com',
            'token' => Hash::make($pin),
            'created_at' => Carbon::now(),
        ]);

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => 'jane@example.com',
            'token' => $pin,
            'password' => 'brandnewpass123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $user->refresh();
        $this->assertTrue(Hash::check('brandnewpass123', $user->password));
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => 'jane@example.com',
        ]);
    }

    /**
     * Test changing password for authenticated user.
     *
     * // YB - 15-09-2026 Test POST /api/auth/change-password endpoint
     */
    public function test_authenticated_user_can_change_password(): void
    {
        $user = User::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => Hash::make('currentpass123'),
        ]);

        $this->actingAs($user);

        $response = $this->postJson('/api/auth/change-password', [
            'current_password' => 'currentpass123',
            'password' => 'updatedpass456',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $user->refresh();
        $this->assertTrue(Hash::check('updatedpass456', $user->password));
    }

    /**
     * Test retrieving active mid-game state for auto-resume.
     *
     * // YB - 15-09-2026 Test GET /api/game/{game} endpoint for mid-game recovery
     */
    public function test_mid_game_state_recovery(): void
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

        $response = $this->getJson("/api/game/{$game->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $game->id,
                    'word_length' => 5,
                    'max_guesses' => 5,
                    'status' => 'playing',
                    'secret_word' => null,
                ],
            ]);
    }
}

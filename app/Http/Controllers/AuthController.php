<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Authenticate user with email and password.
     *
     * // YB - 15-09-2026 Process email and password login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = (bool) $request->input('remember', true);

        if (! Auth::attempt($credentials, $remember)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password.',
            ], 422);
        }

        $request->session()->regenerate();
        $request->session()->put('auth_provider', 'email');
        $user = Auth::user();

        Log::info("User logged in [ID: {$user->id}, Email: {$user->email}]");

        return response()->json([
            'success' => true,
            'message' => 'Logged in successfully.',
            'user' => $user,
        ]);
    }

    /**
     * Register a new user account.
     *
     * // YB - 15-09-2026 Process new user registration
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
        ]);

        Auth::login($user, true);
        $request->session()->regenerate();
        $request->session()->put('auth_provider', 'email');

        Log::info("New user registered [ID: {$user->id}, Email: {$user->email}]");

        return response()->json([
            'success' => true,
            'message' => 'Account created successfully.',
            'user' => $user,
        ], 201);
    }

    /**
     * Log the authenticated user out.
     *
     * // YB - 15-09-2026 Log user out and invalidate session
     */
    public function logout(Request $request): JsonResponse
    {
        $userId = Auth::id();
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info("User logged out [ID: {$userId}]");

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Request a password reset code.
     *
     * // YB - 15-09-2026 Issue secure password reset code
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $email = $request->validated('email');
        $user = User::where('email', $email)->first();

        // Generate 6-digit numeric reset PIN
        $token = (string) random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]
        );

        Log::info("Password reset requested for [Email: {$email}, PIN: {$token}]");

        return response()->json([
            'success' => true,
            'message' => 'Password reset code generated. Use the 6-digit code to reset your password.',
            // Return reset code in development / demo mode for seamless instant UX
            'debug_token' => config('app.debug') ? $token : null,
        ]);
    }

    /**
     * Reset account password using the provided reset code.
     *
     * // YB - 15-09-2026 Verify reset code and update user password
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $email = $request->validated('email');
        $token = $request->validated('token');
        $newPassword = $request->validated('password');

        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (! $record || ! Hash::check($token, $record->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired password reset code.',
            ], 422);
        }

        // Check 60-minute expiry window
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            return response()->json([
                'success' => false,
                'message' => 'Password reset code has expired. Please request a new one.',
            ], 422);
        }

        $user = User::where('email', $email)->firstOrFail();
        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        // Invalidate used reset token
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // Authenticate user
        Auth::login($user, true);
        $request->session()->regenerate();
        $request->session()->put('auth_provider', 'email');

        Log::info("Password successfully reset for [User ID: {$user->id}, Email: {$user->email}]");

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully! You are now logged in.',
            'user' => $user,
        ]);
    }

    /**
     * Change or set password for the authenticated player.
     *
     * // YB - 15-09-2026 Update password for authenticated user (including Google-linked accounts)
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = $request->user();
        $newPassword = $request->validated('password');

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        Log::info("Password updated for authenticated user [User ID: {$user->id}, Email: {$user->email}]");

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.',
            'user' => $user,
        ]);
    }

    /**
     * Get current authenticated user profile.
     *
     * // YB - 15-09-2026 Retrieve currently authenticated user
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'user' => $request->user(),
        ]);
    }

    /**
     * Redirect the user to the Google authentication page.
     *
     * // YB - 15-09-2026 Redirect to Google OAuth provider
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google authentication.
     *
     * // YB - 15-09-2026 Process Google OAuth callback and authenticate user
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Find existing user by google_id or email
            $user = User::where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            if ($user) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar() ?? $user->avatar,
                ]);
            } else {
                $user = User::create([
                    'name' => $googleUser->getName() ?? 'Player',
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => null,
                ]);
            }

            Auth::login($user, true);
            request()->session()->regenerate();
            request()->session()->put('auth_provider', 'google');

            Log::info("User signed in via Google [ID: {$user->id}, Email: {$user->email}]");

            return redirect()->route('game.index')->with('success', 'Signed in with Google successfully!');
        } catch (\Throwable $e) {
            Log::error('Google authentication error: ' . $e->getMessage());
            return redirect()->route('game.index')->with('error', 'Google sign-in failed. Please try again.');
        }
    }
}

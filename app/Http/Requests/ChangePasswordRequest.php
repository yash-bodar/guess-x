<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * // YB - 15-09-2026 Authorize change password request for authenticated users
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * // YB - 15-09-2026 Validation rules for updating or setting account password
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();
        $isGoogleSession = session('auth_provider') === 'google';
        $hasExistingPassword = ! empty($user?->password);

        // If user is logged in via Email/Password and has an existing password, current password is required
        $requiresCurrentPassword = $hasExistingPassword && ! $isGoogleSession;

        return [
            'current_password' => $requiresCurrentPassword ? ['required', 'current_password'] : ['nullable'],
            'password' => ['required', 'string', 'min:6'],
        ];
    }

    /**
     * Custom validation messages.
     *
     * // YB - 15-09-2026 Custom error messages
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'current_password.current_password' => 'The provided current password does not match your existing password.',
        ];
    }
}

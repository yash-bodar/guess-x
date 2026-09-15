<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * // YB - 15-09-2026 Authorize forgot password request
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * // YB - 15-09-2026 Validation rules for password reset request
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255', 'exists:users,email'],
        ];
    }

    /**
     * Custom error messages.
     *
     * // YB - 15-09-2026 Custom validation messages
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.exists' => 'We could not find an account associated with this email address.',
        ];
    }
}

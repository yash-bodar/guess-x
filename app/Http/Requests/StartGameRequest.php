<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StartGameRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * // YB - 15-09-2026 Authorization check for starting a game
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * // YB - 15-09-2026 Validation rules for starting a new game session
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'word_length' => ['required', 'integer', 'in:5,6,7'],
        ];
    }

    /**
     * Custom error messages for validation.
     *
     * // YB - 15-09-2026 Custom validation error messages
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'word_length.required' => 'Word length is required.',
            'word_length.in' => 'Supported word lengths are 5, 6, or 7 letters.',
        ];
    }
}

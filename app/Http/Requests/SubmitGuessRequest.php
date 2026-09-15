<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitGuessRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * // YB - 15-09-2026 Authorization check for guess submission
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * // YB - 15-09-2026 Validation rules for submitted guess
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'guess' => ['required', 'string', 'regex:/^[a-zA-Z]+$/'],
        ];
    }

    /**
     * Custom messages for validation errors.
     *
     * // YB - 15-09-2026 Custom validation error messages for guess
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'guess.required' => 'A guess word is required.',
            'guess.regex' => 'The guess must only contain alphabetic letters (A-Z).',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * // YB - 15-09-2026 Sanitize input by trimming and converting to uppercase
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('guess') && is_string($this->input('guess'))) {
            $this->merge([
                'guess' => strtoupper(trim($this->input('guess'))),
            ]);
        }
    }
}

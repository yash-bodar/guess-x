<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * // YB - 15-09-2026 Format game session data, ensuring secret word is hidden until finished
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $guessesCount = $this->guesses ? $this->guesses->count() : 0;
        $guessesRemaining = max(0, $this->max_guesses - $guessesCount);

        return [
            'id' => $this->id,
            'word_length' => $this->word_length,
            'max_guesses' => $this->max_guesses,
            'x_factor' => [
                'position' => $this->x_factor_position,
                'letter' => $this->x_factor_letter,
            ],
            'status' => $this->status,
            'guesses' => GameGuessResource::collection($this->whenLoaded('guesses', $this->guesses, [])),
            'guesses_remaining' => $guessesRemaining,
            'secret_word' => $this->isFinished() ? $this->word?->word : null,
            'started_at' => $this->started_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
        ];
    }
}

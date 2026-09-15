<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameGuessResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * // YB - 15-09-2026 Format game guess data for JSON API response
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'guess' => $this->guess,
            'result' => $this->result,
            'guess_number' => $this->guess_number,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}

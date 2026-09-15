<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameGuess extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'game_id',
        'guess',
        'result',
        'guess_number',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'result' => 'array',
        'guess_number' => 'integer',
    ];

    /**
     * Get the game this guess belongs to.
     *
     * // YB - 15-09-2026 Define relationship to game
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}

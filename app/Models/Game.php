<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'user_id',
        'word_id',
        'word_length',
        'max_guesses',
        'x_factor_position',
        'x_factor_letter',
        'status',
        'started_at',
        'completed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'user_id' => 'integer',
        'word_length' => 'integer',
        'max_guesses' => 'integer',
        'x_factor_position' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user who played this game (if authenticated).
     *
     * // YB - 15-09-2026 Define relationship to user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the word instance.
     *
     * // YB - 15-09-2026 Define relationship to secret word
     */
    public function word(): BelongsTo
    {
        return $this->belongsTo(Word::class);
    }

    /**
     * Get all guesses for this game session.
     *
     * // YB - 15-09-2026 Define relationship to guesses
     */
    public function guesses(): HasMany
    {
        return $this->hasMany(GameGuess::class)->orderBy('guess_number', 'asc');
    }

    /**
     * Check if game is completed.
     *
     * // YB - 15-09-2026 Helper to check if game has ended
     */
    public function isFinished(): bool
    {
        return in_array($this->status, ['won', 'lost'], true);
    }
}

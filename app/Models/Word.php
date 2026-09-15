<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Word extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'word',
        'length',
        'is_valid',
        'is_targetable',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'length' => 'integer',
        'is_valid' => 'boolean',
        'is_targetable' => 'boolean',
    ];

    /**
     * Get the games associated with this secret word.
     *
     * // YB - 15-09-2026 Define relationship to games
     */
    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }
}

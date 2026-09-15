<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'games_played',
        'games_won',
        'current_streak',
        'max_streak',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        'has_password',
    ];

    /**
     * Determine if user has a password set.
     *
     * // YB - 15-09-2026 Check if account has password set or is OAuth-only
     */
    public function getHasPasswordAttribute(): bool
    {
        return ! empty($this->password);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * // YB - 15-09-2026 Casts for user attributes and gaming statistics
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'games_played' => 'integer',
            'games_won' => 'integer',
            'current_streak' => 'integer',
            'max_streak' => 'integer',
        ];
    }

    /**
     * Games played by this user.
     *
     * // YB - 15-09-2026 User games relationship
     */
    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }

    /**
     * Update user statistics upon game conclusion.
     *
     * // YB - 15-09-2026 Atomically record win/loss and streak stats for user
     */
    public function recordGameResult(bool $isWin): void
    {
        $this->games_played += 1;

        if ($isWin) {
            $this->games_won += 1;
            $this->current_streak += 1;
            if ($this->current_streak > $this->max_streak) {
                $this->max_streak = $this->current_streak;
            }
        } else {
            $this->current_streak = 0;
        }

        $this->save();
    }
}

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
        'daily_streak',
        'daily_max_streak',
        'last_daily_date',
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
            'daily_streak' => 'integer',
            'daily_max_streak' => 'integer',
            'last_daily_date' => 'date',
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

    /**
     * Update user daily challenge statistics upon daily game completion.
     *
     * // YB - 17-09-2026 Record daily challenge result, maintaining daily streaks across calendar days
     */
    public function recordDailyGameResult(bool $isWin, string $challengeDate): void
    {
        $challengeDateObj = \Carbon\Carbon::parse($challengeDate)->startOfDay();

        if ($isWin) {
            $yesterday = $challengeDateObj->copy()->subDay()->toDateString();
            if ($this->last_daily_date && \Carbon\Carbon::parse($this->last_daily_date)->toDateString() === $yesterday) {
                $this->daily_streak += 1;
            } else {
                $this->daily_streak = 1;
            }

            if ($this->daily_streak > $this->daily_max_streak) {
                $this->daily_max_streak = $this->daily_streak;
            }
        } else {
            $this->daily_streak = 0;
        }

        $this->last_daily_date = $challengeDateObj->toDateString();
        $this->save();
    }
}

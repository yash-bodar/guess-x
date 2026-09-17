<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * // YB - 17-09-2026 Add daily challenge tracking to games and daily streak tracking to users
     */
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->boolean('is_daily')->default(false)->after('status');
            $table->date('daily_date')->nullable()->after('is_daily')->index();
            $table->unique(['user_id', 'daily_date'], 'games_user_daily_unique');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('daily_streak')->default(0)->after('max_streak');
            $table->unsignedInteger('daily_max_streak')->default(0)->after('daily_streak');
            $table->date('last_daily_date')->nullable()->after('daily_max_streak');
        });
    }

    /**
     * Reverse the migrations.
     *
     * // YB - 17-09-2026 Revert daily challenge fields from games and users tables
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropUnique('games_user_daily_unique');
            $table->dropColumn(['is_daily', 'daily_date']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['daily_streak', 'daily_max_streak', 'last_daily_date']);
        });
    }
};

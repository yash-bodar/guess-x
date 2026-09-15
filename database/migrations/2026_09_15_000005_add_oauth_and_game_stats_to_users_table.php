<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * // YB - 15-09-2026 Add Google OAuth columns and player stats to users table
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
            $table->string('google_id')->nullable()->unique()->after('email');
            $table->string('avatar')->nullable()->after('google_id');
            $table->unsignedInteger('games_played')->default(0)->after('avatar');
            $table->unsignedInteger('games_won')->default(0)->after('games_played');
            $table->unsignedInteger('current_streak')->default(0)->after('games_won');
            $table->unsignedInteger('max_streak')->default(0)->after('current_streak');
        });
    }

    /**
     * Reverse the migrations.
     *
     * // YB - 15-09-2026 Revert OAuth and player stats columns
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'google_id',
                'avatar',
                'games_played',
                'games_won',
                'current_streak',
                'max_streak',
            ]);
        });
    }
};

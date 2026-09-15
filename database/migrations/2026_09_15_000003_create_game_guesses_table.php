<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * // YB - 15-09-2026 Create game_guesses table for tracking user guesses and evaluation results
     */
    public function up(): void
    {
        Schema::create('game_guesses', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('game_id')->constrained('games')->cascadeOnDelete();
            $table->string('guess', 10);
            $table->json('result');
            $table->unsignedTinyInteger('guess_number');
            $table->timestamps();

            $table->index(['game_id', 'guess_number']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * // YB - 15-09-2026 Reverse game_guesses table migration
     */
    public function down(): void
    {
        Schema::dropIfExists('game_guesses');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * // YB - 15-09-2026 Create games table for tracking game sessions and X-factor
     */
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('word_id')->constrained('words')->cascadeOnDelete();
            $table->unsignedTinyInteger('word_length');
            $table->unsignedTinyInteger('max_guesses');
            $table->unsignedTinyInteger('x_factor_position');
            $table->char('x_factor_letter', 1);
            $table->string('status', 20)->default('playing')->index();
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * // YB - 15-09-2026 Reverse games table migration
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};

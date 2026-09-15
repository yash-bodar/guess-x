<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * // YB - 15-09-2026 Create words table for word dictionary storage
     */
    public function up(): void
    {
        Schema::create('words', function (Blueprint $table) {
            $table->id();
            $table->string('word', 10)->unique();
            $table->unsignedTinyInteger('length')->index();
            $table->boolean('is_valid')->default(true);
            $table->timestamps();

            $table->index(['length', 'is_valid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * // YB - 15-09-2026 Reverse words table migration
     */
    public function down(): void
    {
        Schema::dropIfExists('words');
    }
};

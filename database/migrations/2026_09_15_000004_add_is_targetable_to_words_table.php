<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * // YB - 15-09-2026 Add is_targetable flag to words table to distinguish secret answers from guessable words
     */
    public function up(): void
    {
        Schema::table('words', function (Blueprint $table) {
            $table->boolean('is_targetable')->default(true)->after('is_valid');
            $table->index(['length', 'is_targetable']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * // YB - 15-09-2026 Drop is_targetable column
     */
    public function down(): void
    {
        Schema::table('words', function (Blueprint $table) {
            $table->dropIndex(['length', 'is_targetable']);
            $table->dropColumn('is_targetable');
        });
    }
};

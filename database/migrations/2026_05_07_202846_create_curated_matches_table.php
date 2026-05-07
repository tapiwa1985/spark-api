<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('curated_matches', function (Blueprint $table) {
            $table->id();
            $table->decimal('rank_score');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->foreignId('curated_matches_window_id')->references('id')->on('curated_matches_windows');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curated_matches');
    }
};

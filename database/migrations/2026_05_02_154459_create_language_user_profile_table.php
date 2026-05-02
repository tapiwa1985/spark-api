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
        Schema::create('language_user_profile', function (Blueprint $table) {
            $table->foreignId('user_profile_id')->references('id')->on('user_profiles');
            $table->foreignId('language_id')->references('id')->on('languages');
            $table->unique(['language_id', 'user_profile_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('language_user_profile');
    }
};

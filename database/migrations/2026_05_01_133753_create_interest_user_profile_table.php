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
        Schema::create('interest_user_profile', function (Blueprint $table) {
            $table->foreignId('user_profile_id')->references('id')->on('user_profiles')->onDelete('cascade');
            $table->foreignId('interest_id')->references('id')->on('interests')->onDelete('cascade');
            $table->unique(['user_profile_id', 'interest_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interest_user_profile');
    }
};

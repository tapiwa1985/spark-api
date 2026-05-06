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
        Schema::create('user_discovery_preferences', function (Blueprint $table) {
            $table->id();
            $table->integer('min_age');
            $table->integer('max_age');
            $table->boolean('verified_only')->default(false);
            $table->integer('max_distance_radius_km');
            $table->enum('gender', ['male', 'female', 'both']);
            $table->foreignId('user_id')->references('id')->on('users')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_discovery_preferences');
    }
};

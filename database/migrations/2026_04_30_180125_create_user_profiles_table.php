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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->unique();
            $table->string('bio')->nullable();
            $table->date('dob')->nullable();
            $table->enum('gender', ['male','female'])->nullable();
            $table->string('job_title')->nullable();;
            $table->foreignId('industry_id')->nullable()->references('id')->on('industries');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};

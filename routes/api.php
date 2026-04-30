<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\IndustryController;

Route::prefix('v1')->group(function() {
    Route::prefix('auth')->group(function() {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
    });

    Route::middleware(['auth'])->group(function() {
        Route::get('industries', [IndustryController::class, 'index']);
        Route::put('user-profiles/{userProfileId}', [UserProfileController::class, 'update']);
    });
});


<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\AuthController;

Route::prefix('v1')->group(function() {
    Route::post('register', [UserProfileController::class, 'store']);
    Route::post('login', [AuthController::class, 'login']);
});


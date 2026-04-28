<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserProfileController;

Route::prefix('v1')->group(function() {
    Route::post('register', [UserProfileController::class, 'store']);
});


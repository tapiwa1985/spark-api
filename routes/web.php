<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LinkedInController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('auth')->group(function() {
    Route::prefix('linkedin')->group(function() {
        Route::get('/', [LinkedInController::class, 'redirect']);
        Route::get('callback', [LinkedInController::class, 'callback']);
    });
});
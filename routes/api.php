<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\IndustryController;
use App\Http\Controllers\Api\LinkedInController;
use App\Http\Controllers\Api\InterestCategoryController;
use App\Http\Controllers\Api\LanguageController;
use App\Http\Controllers\Api\LikeController;

Route::prefix('v1')->group(function() {
    Route::prefix('auth')->group(function() {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::prefix('linkedin')->group(function() {
            Route::get('/', [LinkedInController::class, 'getRedirectUrl']);
            Route::match(['GET', 'POST'], 'callback', [LinkedInController::class, 'callback']);
        });
    });

    Route::get('industries', [IndustryController::class, 'index']);

    Route::middleware(['auth'])->group(function() {
        Route::get('languages', [LanguageController::class, 'index']);

        Route::prefix('likes')->group(function() {
            Route::resource('/', LikeController::class);
        });

        Route::prefix('user-profiles')->group(function() {
            Route::put('/', [UserProfileController::class, 'update']);
            Route::put('interests', [UserProfileController::class, 'addInterests']);
            Route::put('languages', [UserProfileController::class, 'addLanguages']);
            Route::patch('bio', [UserProfileController::class, 'updateBio']);
            Route::post('images', [UserProfileController::class, 'uploadProfilePicture']);
            Route::patch('location', [UserProfileController::class, 'updateLocation']);
            Route::put('{imageId}/images', [UserProfileController::class, 'setDisplayImage']);
            Route::delete('{imageId}/images', [UserProfileController::class, 'deleteImage']);
        });

        Route::get('interest-categories', [InterestCategoryController::class, 'index']);
    });
});


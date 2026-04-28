<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind the UserProfileRepositoryInterface to the UserProfileRepository implementation
        $this->app->bind(
            \App\Repositories\Contracts\UserProfileRepositoryInterface::class,
            \App\Repositories\UserProfileRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\UserRepositoryInterface::class,
            \App\Repositories\UserRepository::class
        );

        $this->app->bind(
            \App\Services\Contracts\UserProfileServiceInterface::class,
            \App\Services\UserProfileService::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

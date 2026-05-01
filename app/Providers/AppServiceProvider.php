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

        $this->app->bind(
            \App\Services\Contracts\UserServiceInterface::class,
            \App\Services\UserService::class,
        );

        $this->app->bind(
            \App\Repositories\Contracts\ProfileImageRepositoryInterface::class,
            \App\Repositories\ProfileImageRepository::class,
        );

        $this->app->bind(
            \App\Utils\Contracts\ImageUploaderInterface::class,
            \App\Utils\ImageUploader::class,
        );

        $this->app->bind(
            \App\Repositories\Contracts\IndustryRepositoryInterface::class,
            \App\Repositories\IndustryRepository::class,
        );

        $this->app->bind(
            \App\Services\Contracts\IndustryServiceInterface::class,
            \App\Services\IndustryService::class,
        );

        $this->app->bind(
            \App\Repositories\Contracts\InterestRepositoryInterface::class,
            \App\Repositories\InterestRepository::class,
        );

        $this->app->bind(
            \App\Repositories\Contracts\InterestCategoryRepositoryInterface::class,
            \App\Repositories\InterestCategoryRepository::class,
        );

        $this->app->bind(
            \App\Services\Contracts\InterestCategoryServiceInterface::class,
            \App\Services\InterestCategoryService::class,
        );

        $this->app->bind(
            \App\Services\Contracts\ProfileImageServiceInterface::class,
            \App\Services\ProfileImageService::class,
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

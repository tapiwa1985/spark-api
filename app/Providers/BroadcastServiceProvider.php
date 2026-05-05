<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

/**
 * Registers broadcasting authentication endpoints used by private/presence channels.
 */
class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap broadcast auth routes.
     */
    public function boot(): void
    {
        Broadcast::routes([
            'middleware' => ['auth:api'],
        ]);

        require base_path('routes/channels.php');
    }
}

<?php

namespace Modules\Therapies;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class TherapiesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerRoutes();
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
    }

    /**
     * Register the module routes.
     */
    protected function registerRoutes(): void
    {
        Route::middleware('web')
            ->group(__DIR__ . '/Routes/web.php');

        Route::prefix('api')
            ->middleware('api')
            ->group(__DIR__ . '/Routes/api.php');
    }
}
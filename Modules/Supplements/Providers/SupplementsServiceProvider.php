<?php

namespace Modules\Supplements\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class SupplementsServiceProvider extends ServiceProvider
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
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    /**
     * Registrar las rutas del módulo de suplementos.
     */
    protected function registerRoutes(): void
    {
        Route::middleware('web')
            ->group(function () {
                $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
            });

        Route::middleware('api')
            ->prefix('api')
            ->group(function () {
                $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
            });
    }
}
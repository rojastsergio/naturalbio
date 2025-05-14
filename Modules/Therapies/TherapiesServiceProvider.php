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
        // Desactivado para evitar conflictos con rutas en routes/web.php
        // Route::middleware('web')
        //    ->group(__DIR__ . '/Routes/web.php');

        // Las rutas API pueden ser cargadas normalmente
        Route::prefix('api')
            ->middleware('api')
            ->group(__DIR__ . '/Routes/api.php');
    }
}
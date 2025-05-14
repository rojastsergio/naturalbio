<?php

namespace Modules\Prescriptions\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class PrescriptionsServiceProvider extends ServiceProvider
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
        $this->loadViewsFrom(base_path('Modules/Prescriptions/Resources/views'), 'prescriptions');
    }

    /**
     * Register the module routes.
     */
    protected function registerRoutes(): void
    {
        // Desactivado para evitar conflictos con rutas en routes/web.php
        // Route::middleware('web')
        //    ->group(base_path('Modules/Prescriptions/Routes/web.php'));

        // Las rutas API serán importadas directamente por routes/api.php
        // Route::prefix('api')
        //    ->middleware('api')
        //    ->group(base_path('Modules/Prescriptions/Routes/api.php'));
    }
}
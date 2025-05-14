<?php

namespace Modules\Patients\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class PatientServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerRoutes();
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
    }

    /**
     * Register the routes for this module.
     */
    protected function registerRoutes(): void
    {
        // Web routes are loaded from the main routes/web.php file
        // to avoid conflicts with route names like patients.index
        // $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');

        // Las rutas de API de pacientes serán importadas directamente
        // en routes/api.php para evitar conflictos de nombres
        // $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
    }
}
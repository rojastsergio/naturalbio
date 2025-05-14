<?php

namespace Modules\Appointments\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The module namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    protected $moduleNamespace = 'Modules\Appointments\Controllers';

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        // Desactivado para evitar conflictos con rutas en routes/web.php
        // Route::middleware('web')
        //     ->namespace($this->moduleNamespace)
        //     ->group(__DIR__.'/../Routes/web.php');
    }

    /**
     * Define the "api" routes for the application.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        // Desactivado para evitar conflictos con rutas en routes/api.php
        // Route::prefix('api')
        //     ->middleware('api')
        //     ->namespace($this->moduleNamespace)
        //     ->group(__DIR__.'/../Routes/api.php');
    }
}
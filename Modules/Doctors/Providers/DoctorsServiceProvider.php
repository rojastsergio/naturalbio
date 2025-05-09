<?php

namespace Modules\Doctors\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class DoctorsServiceProvider extends ServiceProvider
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
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
        
        // Registrar vistas (opcional, si tienes vistas Blade)
        // $this->loadViewsFrom(__DIR__.'/../Resources/views', 'doctors');
    }
}
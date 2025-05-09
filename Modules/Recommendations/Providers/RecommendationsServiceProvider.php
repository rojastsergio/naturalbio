<?php

namespace Modules\Recommendations\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class RecommendationsServiceProvider extends ServiceProvider
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
     * Register the module routes.
     */
    protected function registerRoutes(): void
    {
        Route::middleware('web')
            ->group(function () {
                $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
            });

        Route::prefix('api')
            ->middleware('api')
            ->group(function () {
                $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
            });
    }
}
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
        Route::middleware('web')
            ->group(base_path('Modules/Prescriptions/Routes/web.php'));

        Route::prefix('api')
            ->middleware('api')
            ->group(base_path('Modules/Prescriptions/Routes/api.php'));
    }
}
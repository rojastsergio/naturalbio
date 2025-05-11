<?php

namespace Modules\Appointments\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Gate;
use Modules\Appointments\Models\AppointmentType;
use Modules\Appointments\Policies\AppointmentTypePolicy;

class AppointmentsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Registrar servicios adicionales si es necesario
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Cargar rutas
        $this->loadRoutes();

        // Cargar migraciones
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        // Registrar políticas
        $this->registerPolicies();

        // Asegurarse de que los componentes Vue se registren
        $this->publishAssets();
    }

    /**
     * Cargar las rutas del módulo.
     *
     * @return void
     */
    protected function loadRoutes()
    {
        Route::middleware('web')
            ->namespace('Modules\Appointments\Controllers')
            ->group(__DIR__.'/../Routes/web.php');

        Route::prefix('api')
            ->middleware('api')
            ->namespace('Modules\Appointments\Controllers')
            ->group(__DIR__.'/../Routes/api.php');
    }

    /**
     * Publicar los assets necesarios para el módulo.
     *
     * @return void
     */
    protected function publishAssets()
    {
        $this->publishes([
            __DIR__.'/../Resources/js' => resource_path('js/modules/appointments'),
        ], 'appointments-assets');
    }

    /**
     * Registrar las políticas del módulo.
     *
     * @return void
     */
    protected function registerPolicies()
    {
        Gate::policy(AppointmentType::class, AppointmentTypePolicy::class);
    }
}
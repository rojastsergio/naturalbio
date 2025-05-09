<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use App\Providers\ModulesServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar el proveedor de servicios de módulos
        $this->app->register(ModulesServiceProvider::class);
        
        // Establecer longitud predeterminada de cadenas en migraciones
        Schema::defaultStringLength(191);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Forzar HTTPS en producción
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
        
        // Compartir variables globales con todas las vistas de Inertia
        Inertia::share([
            'app' => [
                'name' => config('app.name'),
                'locale' => app()->getLocale(),
                // Agregar la URL base para que esté disponible en el frontend
                'url' => config('app.url'),
            ],
            'flash' => function () {
                return [
                    'success' => session('success'),
                    'error' => session('error'),
                    'warning' => session('warning'),
                    'info' => session('info'),
                ];
            },
            'auth' => function () {
                return [
                    'user' => Auth::user() ? [
                        'id' => Auth::user()->id,
                        'name' => Auth::user()->name,
                        'email' => Auth::user()->email,
                        // Obtener roles solo si el método existe
                        'roles' => method_exists(Auth::user(), 'roles') ? Auth::user()->roles->pluck('name') : [],
                    ] : null,
                ];
            },
        ]);
        
        // Si Ziggy está instalado, compartir sus rutas con Inertia
        if (class_exists('\\Tightenco\\Ziggy\\Ziggy')) {
            Inertia::share('ziggy', function () {
                return [
                    'url' => config('app.url'),
                    'port' => parse_url(config('app.url'), PHP_URL_PORT),
                    'routes' => app('ziggy')->toArray(),
                ];
            });
        }
        
        // Configurar zona horaria para Guatemala
        date_default_timezone_set('America/Guatemala');
    }
}
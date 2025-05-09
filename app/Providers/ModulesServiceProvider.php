<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ModulesServiceProvider extends ServiceProvider
{
    /**
     * Lista de módulos disponibles y sus proveedores correspondientes.
     * 
     * @var array
     */
    protected static $modules = [
        'Patients' => [
            \Modules\Patients\Providers\PatientServiceProvider::class,
        ],
        // Módulos futuros se agregarán aquí
        'Appointments' => [
            \Modules\Appointments\Providers\AppointmentsServiceProvider::class,
        ],
        'Doctors' => [
            \Modules\Doctors\Providers\DoctorsServiceProvider::class,
        ],
        'Therapies' => [
            \Modules\Therapies\TherapiesServiceProvider::class,
        ],
        'Prescriptions' => [
            \Modules\Prescriptions\Providers\PrescriptionsServiceProvider::class,
        ],
    'Supplements' => [
        \Modules\Supplements\Providers\SupplementsServiceProvider::class,
    ],
    'Recommendations' => [
        \Modules\Recommendations\Providers\RecommendationsServiceProvider::class,
    ],
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        // Configuración de base de datos para compatibilidad
        Schema::defaultStringLength(191);
        
        // Registrar módulos activos
        $activeModules = [];
        
        foreach (self::$modules as $moduleName => $moduleProviders) {
            if (self::moduleIsActive($moduleName)) {
                $activeModules[] = $moduleName;
                
                foreach ($moduleProviders as $provider) {
                    if (class_exists($provider)) {
                        $this->app->register($provider);
                        Log::debug("Módulo registrado: {$provider}");
                    } else {
                        Log::warning("El proveedor {$provider} no existe para el módulo {$moduleName}");
                    }
                }
            }
        }
        
        // Registrar módulos activos en config para acceder desde otras partes de la app
        $this->app['config']->set('modules.active', $activeModules);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Log::info('ModulesServiceProvider booted con módulos: ' . implode(', ', $this->getActiveModules()));
        
        // Cargar rutas de módulos directamente si sus ServiceProviders no lo hacen
        $this->loadModuleRoutes();
    }

    /**
     * Cargar rutas directamente desde los módulos como respaldo
     * si el ServiceProvider del módulo no lo está haciendo.
     */
    protected function loadModuleRoutes(): void
    {
        foreach (self::getActiveModules() as $moduleName) {
            $webRoutesPath = base_path("Modules/{$moduleName}/Routes/web.php");
            $apiRoutesPath = base_path("Modules/{$moduleName}/Routes/api.php");
            
            if (File::exists($webRoutesPath)) {
                $this->loadRoutesFrom($webRoutesPath);
                Log::debug("Rutas web cargadas para el módulo {$moduleName}");
            }
            
            if (File::exists($apiRoutesPath)) {
                $this->loadRoutesFrom($apiRoutesPath);
                Log::debug("Rutas API cargadas para el módulo {$moduleName}");
            }
        }
    }

    /**
     * Verifica si un módulo está activo.
     * 
     * @param string $moduleName
     * @return bool
     */
    public static function moduleIsActive(string $moduleName): bool
    {
        $moduleExists = array_key_exists($moduleName, self::$modules);
        $directoryExists = File::isDirectory(base_path("Modules/{$moduleName}"));
        
        if ($moduleExists && !$directoryExists) {
            Log::warning("El módulo {$moduleName} está definido pero su directorio no existe");
        }
        
        return $moduleExists && $directoryExists;
    }

    /**
     * Obtiene la lista de módulos activos.
     * 
     * @return array
     */
    public static function getActiveModules(): array
    {
        $activeModules = [];
        
        foreach (array_keys(self::$modules) as $moduleName) {
            if (self::moduleIsActive($moduleName)) {
                $activeModules[] = $moduleName;
            }
        }
        
        return $activeModules;
    }
    
    /**
     * Verifica si un controlador existe dentro de un módulo.
     * 
     * @param string $moduleName
     * @param string $controllerName
     * @return bool
     */
    public static function controllerExists(string $moduleName, string $controllerName): bool
    {
        $controllerPath = base_path("Modules/{$moduleName}/Controllers/{$controllerName}.php");
        return File::exists($controllerPath);
    }
}
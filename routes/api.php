<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Ruta API pública para buscar pacientes (sin middleware de autenticación)
Route::get('/patients/search', function (Request $request) {
    $search = $request->input('search', '');

    if (strlen($search) < 2) {
        return response()->json(['patients' => []]);
    }

    $patients = \Modules\Patients\Models\Patient::where('name', 'like', "%{$search}%")
        ->orWhere('last_name', 'like', "%{$search}%")
        ->orWhere('expedient_number', 'like', "%{$search}%")
        ->limit(10)
        ->get()
        ->map(function($patient) {
            return [
                'id' => $patient->id,
                'name' => $patient->name,
                'last_name' => $patient->last_name,
                'expedient_number' => $patient->expedient_number,
                'full_name' => "{$patient->name} {$patient->last_name}" . ($patient->expedient_number ? " (#{$patient->expedient_number})" : "")
            ];
        });

    return response()->json(['patients' => $patients]);
})->name('api.patients.search.global');

// Rutas para municipios
Route::prefix('municipios')->group(function () {
    // Buscar municipios
    Route::get('/search', function (Request $request) {
        $search = $request->input('q', '');

        if (strlen($search) < 2) {
            return response()->json(['municipios' => []]);
        }

        $municipios = \App\Models\Municipality::with('department')
            ->where('name', 'like', "%{$search}%")
            ->orderBy('name')
            ->limit(10)
            ->get()
            ->map(function($municipio) {
                return [
                    'id' => $municipio->id,
                    'nombre' => $municipio->name,
                    'code' => $municipio->code,
                    'departamento' => [
                        'id' => $municipio->department->id,
                        'nombre' => $municipio->department->name,
                        'code' => $municipio->department->code,
                    ]
                ];
            });

        return response()->json(['municipios' => $municipios]);
    })->name('api.municipios.search');

    // Obtener un municipio específico
    Route::get('/{id}', function ($id) {
        $municipio = \App\Models\Municipality::with('department')->find($id);

        if (!$municipio) {
            return response()->json(['error' => 'Municipio no encontrado'], 404);
        }

        return response()->json([
            'municipio' => [
                'id' => $municipio->id,
                'nombre' => $municipio->name,
                'code' => $municipio->code,
                'departamento' => [
                    'id' => $municipio->department->id,
                    'nombre' => $municipio->department->name,
                    'code' => $municipio->department->code,
                ]
            ]
        ]);
    })->name('api.municipios.show');
});

// Importamos manualmente los módulos con posibles conflictos de rutas
require_once base_path('Modules/Patients/Routes/api.php');
require_once base_path('Modules/Doctors/Routes/api.php');
require_once base_path('Modules/Prescriptions/Routes/api.php');
require_once base_path('Modules/Appointments/Routes/api.php');
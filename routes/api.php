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
});

require_once base_path('Modules/Doctors/Routes/api.php');
require_once base_path('Modules/Prescriptions/Routes/api.php');
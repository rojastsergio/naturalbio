<?php

use Illuminate\Support\Facades\Route;
use Modules\Patients\Controllers\PatientController;
use Modules\Patients\Models\Patient;
use Illuminate\Http\Request;

Route::middleware(['api'])->group(function () {
    // Ruta pública para buscar pacientes desde otros módulos
    Route::get('/patient-search', function (Request $request) {
        $search = $request->input('search', '');

        if (strlen($search) < 2) {
            return response()->json(['patients' => []]);
        }

        $patients = Patient::select('id', 'name', 'last_name', 'expedient_number')
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('expedient_number', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->limit(10)
            ->get()
            ->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'name' => $patient->name,
                    'last_name' => $patient->last_name,
                    'expedient_number' => $patient->expedient_number,
                    'full_name' => "{$patient->name} {$patient->last_name}" . ($patient->expedient_number ? " (#{$patient->expedient_number})" : "")
                ];
            });

        return response()->json(['patients' => $patients]);
    })->name('api.patient-search');
    
    // Rutas específicas a través del controlador
    Route::get('/municipalities/search', [PatientController::class, 'searchMunicipalities'])
        ->name('api.patients.municipalities.search');
    
    Route::get('/municipalities/{id}', [PatientController::class, 'getMunicipality'])
        ->name('api.patients.municipalities.show');
});
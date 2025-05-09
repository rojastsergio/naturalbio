<?php

use Illuminate\Support\Facades\Route;
use Modules\Patients\Controllers\PatientController;
use Modules\Patients\Controllers\VitalSignController;

Route::middleware(['auth', 'verified'])->group(function () {
    // Rutas de pacientes con middleware de permisos
    Route::get('/pacientes', [PatientController::class, 'index'])
        ->middleware('check.permission:view patients')
        ->name('patients.index');
        
    Route::get('/pacientes/crear', [PatientController::class, 'create'])
        ->middleware('check.permission:create patients')
        ->name('patients.create');
        
    Route::post('/pacientes', [PatientController::class, 'store'])
        ->middleware('check.permission:create patients')
        ->name('patients.store');
        
    Route::get('/pacientes/{patient}', [PatientController::class, 'show'])
        ->middleware('check.permission:view patients')
        ->name('patients.show');
        
    Route::get('/pacientes/{patient}/editar', [PatientController::class, 'edit'])
        ->middleware('check.permission:update patients')
        ->name('patients.edit');
        
    Route::put('/pacientes/{patient}', [PatientController::class, 'update'])
        ->middleware('check.permission:update patients')
        ->name('patients.update');
        
    Route::delete('/pacientes/{patient}', [PatientController::class, 'destroy'])
        ->middleware('check.permission:delete patients')
        ->name('patients.destroy');

    // Rutas de signos vitales con middleware de permisos
    Route::post('/pacientes/{patient}/signos-vitales', [VitalSignController::class, 'store'])
        ->middleware('check.permission:create vitals')
        ->name('vitals.store');
        
    Route::put('/signos-vitales/{vitalSign}', [VitalSignController::class, 'update'])
        ->middleware('check.permission:update vitals')
        ->name('vitals.update');
        
    Route::delete('/signos-vitales/{vitalSign}', [VitalSignController::class, 'destroy'])
        ->middleware('check.permission:delete vitals')
        ->name('vitals.destroy');
});
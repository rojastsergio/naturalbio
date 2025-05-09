<?php

use Illuminate\Support\Facades\Route;
use Modules\Patients\Controllers\PatientController;
use Modules\Patients\Controllers\VitalSignController;

Route::middleware(['auth:sanctum'])->prefix('api')->group(function () {
    // API routes for patients
    Route::get('/patients', [PatientController::class, 'index'])
        ->middleware('check.permission:view patients');
    
    Route::post('/patients', [PatientController::class, 'store'])
        ->middleware('check.permission:create patients');
    
    Route::get('/patients/{patient}', [PatientController::class, 'show'])
        ->middleware('check.permission:view patients');
    
    Route::put('/patients/{patient}', [PatientController::class, 'update'])
        ->middleware('check.permission:update patients');
    
    Route::delete('/patients/{patient}', [PatientController::class, 'destroy'])
        ->middleware('check.permission:delete patients');

    // API routes for vital signs
    Route::post('/patients/{patient}/vitals', [VitalSignController::class, 'store'])
        ->middleware('check.permission:create vitals');
    
    Route::put('/vitals/{vitalSign}', [VitalSignController::class, 'update'])
        ->middleware('check.permission:update vitals');
    
    Route::delete('/vitals/{vitalSign}', [VitalSignController::class, 'destroy'])
        ->middleware('check.permission:delete vitals');
});
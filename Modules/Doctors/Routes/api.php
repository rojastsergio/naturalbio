<?php

use Illuminate\Support\Facades\Route;
use Modules\Doctors\Controllers\DoctorAvailabilityController;
use Modules\Doctors\Controllers\TherapyInstructionController;

Route::middleware(['auth:sanctum'])->group(function () {
    // API para disponibilidades
    Route::post('/doctor-availabilities', [DoctorAvailabilityController::class, 'store']);
    Route::put('/doctor-availabilities/{availability}', [DoctorAvailabilityController::class, 'update']);
    Route::delete('/doctor-availabilities/{availability}', [DoctorAvailabilityController::class, 'destroy']);
    Route::get('/doctor-availabilities/dates', [DoctorAvailabilityController::class, 'getAvailabilityForDates']);

    // API para instrucciones terapéuticas
    Route::post('/therapy-instructions', [TherapyInstructionController::class, 'store']);
    Route::put('/therapy-instructions/{instruction}', [TherapyInstructionController::class, 'update']);
    Route::delete('/therapy-instructions/{instruction}', [TherapyInstructionController::class, 'destroy']);

    // API para emergencias
    Route::post('/appointments/emergency', [\Modules\Appointments\Controllers\AppointmentController::class, 'storeEmergency']);
});
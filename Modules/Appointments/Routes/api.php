<?php

use Illuminate\Support\Facades\Route;
use Modules\Appointments\Controllers\AppointmentController;
use Modules\Appointments\Controllers\AppointmentTypeController;
use Modules\Appointments\Controllers\DoctorAvailabilityController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->group(function () {
    // Appointments
    Route::get('/appointments/availability', [AppointmentController::class, 'availability'])
        ->name('api.appointments.availability');
    Route::post('/appointments/{appointment}/reminder', [AppointmentController::class, 'sendReminder'])
        ->name('api.appointments.reminder');
    
    // Appointment Types
    Route::apiResource('appointment-types', AppointmentTypeController::class, [
        'as' => 'api'
    ]);
    
    // Doctor Availabilities
    Route::post('/doctor-availabilities/bulk', [DoctorAvailabilityController::class, 'bulkCreate'])
        ->name('api.doctor-availabilities.bulk-create');
    Route::apiResource('doctor-availabilities', DoctorAvailabilityController::class, [
        'as' => 'api'
    ]);
});
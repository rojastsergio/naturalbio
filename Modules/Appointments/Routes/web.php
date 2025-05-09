<?php

use Illuminate\Support\Facades\Route;
use Modules\Appointments\Controllers\AppointmentController;
use Modules\Appointments\Controllers\AppointmentTypeController;
use Modules\Appointments\Controllers\DoctorAvailabilityController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    // Appointments
    Route::resource('appointments', AppointmentController::class);
    
    // Appointment Types
    Route::resource('appointment-types', AppointmentTypeController::class);
    
    // Doctor Availabilities
    Route::resource('doctor-availabilities', DoctorAvailabilityController::class);
});
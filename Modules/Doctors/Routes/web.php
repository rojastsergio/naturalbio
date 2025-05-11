<?php

use Illuminate\Support\Facades\Route;
use Modules\Doctors\Controllers\DoctorController;
use Modules\Doctors\Controllers\DoctorAvailabilityController;
use Modules\Doctors\Controllers\TherapyInstructionController;

Route::middleware(['auth', 'verified'])->group(function () {
    // Rutas para doctores
    Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');
    Route::get('/doctors/create', [DoctorController::class, 'create'])->name('doctors.create');
    Route::post('/doctors', [DoctorController::class, 'store'])->name('doctors.store');
    Route::get('/doctors/{doctor}', [DoctorController::class, 'show'])->name('doctors.show');
    Route::get('/doctors/{doctor}/edit', [DoctorController::class, 'edit'])->name('doctors.edit');
    Route::put('/doctors/{doctor}', [DoctorController::class, 'update'])->name('doctors.update');
    Route::delete('/doctors/{doctor}', [DoctorController::class, 'destroy'])->name('doctors.destroy');
    
    // Dashboard del doctor
    Route::get('/doctor/dashboard', [DoctorController::class, 'dashboard'])->name('doctor.dashboard');

    // Gestión de disponibilidad
    Route::get('/doctor/availability', [DoctorAvailabilityController::class, 'index'])->name('doctor.availability');
    Route::get('/doctors/{doctor}/availability', [DoctorAvailabilityController::class, 'index'])->name('doctors.availability');

    // Gestión de emergencias
    Route::get('/doctor/emergency', [DoctorController::class, 'emergency'])->name('doctor.emergency');

    // Instrucciones terapéuticas
    Route::get('/doctor/therapy-instructions', [TherapyInstructionController::class, 'index'])->name('doctor.therapy-instructions');
    Route::get('/doctors/{doctor}/therapy-instructions', [TherapyInstructionController::class, 'index'])->name('doctors.therapy-instructions');
    Route::get('/therapy-instructions/{instruction}', [TherapyInstructionController::class, 'show'])->name('therapy-instructions.show');
});
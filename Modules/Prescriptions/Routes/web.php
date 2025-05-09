<?php

use Illuminate\Support\Facades\Route;
use Modules\Prescriptions\Controllers\PrescriptionController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('prescripciones')->group(function () {
        Route::get('/', [PrescriptionController::class, 'index'])->name('prescriptions.index');
        Route::get('/crear', [PrescriptionController::class, 'create'])->name('prescriptions.create');
        Route::post('/', [PrescriptionController::class, 'store'])->name('prescriptions.store');
        Route::get('/{prescription}', [PrescriptionController::class, 'show'])->name('prescriptions.show');
        Route::get('/{prescription}/editar', [PrescriptionController::class, 'edit'])->name('prescriptions.edit');
        Route::put('/{prescription}', [PrescriptionController::class, 'update'])->name('prescriptions.update');
        Route::delete('/{prescription}', [PrescriptionController::class, 'destroy'])->name('prescriptions.destroy');
        Route::post('/{prescription}/pdf', [PrescriptionController::class, 'generatePdf'])->name('prescriptions.pdf');
        Route::post('/{prescription}/notificar', [PrescriptionController::class, 'sendNotification'])->name('prescriptions.notify');
    });
});
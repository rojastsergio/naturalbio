<?php

use Illuminate\Support\Facades\Route;
use Modules\Prescriptions\Controllers\PrescriptionController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('api')->group(function () {
        Route::prefix('prescriptions')->group(function () {
            Route::get('/', [PrescriptionController::class, 'index']);
            Route::post('/', [PrescriptionController::class, 'store']);
            Route::get('/{prescription}', [PrescriptionController::class, 'show']);
            Route::put('/{prescription}', [PrescriptionController::class, 'update']);
            Route::delete('/{prescription}', [PrescriptionController::class, 'destroy']);
            Route::post('/{prescription}/pdf', [PrescriptionController::class, 'generatePdf']);
            Route::post('/{prescription}/notify', [PrescriptionController::class, 'sendNotification']);
        });
    });
});
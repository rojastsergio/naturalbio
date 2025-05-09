<?php

use Illuminate\Support\Facades\Route;
use Modules\Supplements\Controllers\SupplementController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('api')->group(function () {
        Route::get('/supplements', [SupplementController::class, 'index']);
        Route::post('/supplements', [SupplementController::class, 'store']);
        Route::get('/supplements/{supplement}', [SupplementController::class, 'show']);
        Route::put('/supplements/{supplement}', [SupplementController::class, 'update']);
        Route::delete('/supplements/{supplement}', [SupplementController::class, 'destroy']);
        Route::post('/supplements/{supplement}/remove-media', [SupplementController::class, 'removeMedia']);
    });
});
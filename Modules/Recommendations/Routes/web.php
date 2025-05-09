<?php

use Illuminate\Support\Facades\Route;
use Modules\Recommendations\Controllers\RecommendationController;

Route::middleware(['auth'])->group(function () {
    // Rutas de recomendaciones
    Route::get('/recommendations', [RecommendationController::class, 'index'])->name('recommendations.index');
    Route::get('/recommendations/create', [RecommendationController::class, 'create'])->name('recommendations.create');
    Route::post('/recommendations', [RecommendationController::class, 'store'])->name('recommendations.store');
    Route::get('/recommendations/{recommendation}', [RecommendationController::class, 'show'])->name('recommendations.show');
    Route::get('/recommendations/{recommendation}/edit', [RecommendationController::class, 'edit'])->name('recommendations.edit');
    Route::put('/recommendations/{recommendation}', [RecommendationController::class, 'update'])->name('recommendations.update');
    Route::delete('/recommendations/{recommendation}', [RecommendationController::class, 'destroy'])->name('recommendations.destroy');
    
    // Rutas adicionales para acciones específicas
    Route::post('/recommendations/{recommendation}/accept', [RecommendationController::class, 'acceptOrReject'])->name('recommendations.accept');
    Route::post('/recommendations/{recommendation}/task', [RecommendationController::class, 'completeTask'])->name('recommendations.task');
    
    // Ruta para mostrar las recomendaciones de un paciente específico
    Route::get('/patients/{patient}/recommendations', [RecommendationController::class, 'forPatient'])->name('patients.recommendations');
});
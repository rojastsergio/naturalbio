<?php

use Illuminate\Support\Facades\Route;
use Modules\Recommendations\Controllers\RecommendationController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/recommendations', [RecommendationController::class, 'index']);
    Route::post('/recommendations', [RecommendationController::class, 'store']);
    Route::get('/recommendations/{recommendation}', [RecommendationController::class, 'show']);
    Route::put('/recommendations/{recommendation}', [RecommendationController::class, 'update']);
    Route::delete('/recommendations/{recommendation}', [RecommendationController::class, 'destroy']);
    
    Route::post('/recommendations/{recommendation}/accept', [RecommendationController::class, 'acceptOrReject']);
    Route::post('/recommendations/{recommendation}/task', [RecommendationController::class, 'completeTask']);
    
    Route::get('/patients/{patient}/recommendations', [RecommendationController::class, 'forPatient']);
});
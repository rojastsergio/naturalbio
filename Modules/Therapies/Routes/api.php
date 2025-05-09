<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Therapies\Controllers\TherapyController;
use Modules\Therapies\Controllers\TherapyAssignmentController;
use Modules\Therapies\Controllers\TherapyProgressController;
use Modules\Therapies\Controllers\TherapyInstructionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->group(function () {
    // Therapy assignments
    Route::get('therapy-assignments/{assignment}/progress', [TherapyProgressController::class, 'index']);
    Route::post('therapy-assignments/{assignment}/progress', [TherapyProgressController::class, 'store']);
    
    // Therapy progress
    Route::patch('therapy-progresses/{progress}', [TherapyProgressController::class, 'update']);
});
<?php

use Illuminate\Support\Facades\Route;
use Modules\Therapies\Controllers\TherapyController;
use Modules\Therapies\Controllers\TherapyAssignmentController;
use Modules\Therapies\Controllers\TherapyProgressController;
use Modules\Therapies\Controllers\TherapyInstructionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth', 'verified'])->group(function () {
    // Therapy catalog
    Route::resource('therapies', TherapyController::class);
    
    // Therapy assignments
    Route::get('therapy-assignments', [TherapyAssignmentController::class, 'index'])->name('therapy-assignments.index');
    Route::get('therapy-assignments/{assignment}', [TherapyAssignmentController::class, 'show'])->name('therapy-assignments.show');
    Route::patch('therapy-assignments/{assignment}', [TherapyAssignmentController::class, 'update'])->name('therapy-assignments.update');
    
    // Therapy instructions
    Route::resource('therapy-instructions', TherapyInstructionController::class)->names([
        'index' => 'therapies.therapy-instructions.index',
        'create' => 'therapies.therapy-instructions.create',
        'store' => 'therapies.therapy-instructions.store',
        'show' => 'therapies.therapy-instructions.show',
        'edit' => 'therapies.therapy-instructions.edit',
        'update' => 'therapies.therapy-instructions.update',
        'destroy' => 'therapies.therapy-instructions.destroy',
    ]);
});
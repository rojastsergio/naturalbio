<?php

use Illuminate\Support\Facades\Route;
use Modules\Supplements\Controllers\SupplementController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('supplements')->group(function () {
        Route::get('/', [SupplementController::class, 'index'])->name('supplements.index');
        Route::get('/create', [SupplementController::class, 'create'])->name('supplements.create');
        Route::post('/', [SupplementController::class, 'store'])->name('supplements.store');
        Route::get('/{supplement}', [SupplementController::class, 'show'])->name('supplements.show');
        Route::get('/{supplement}/edit', [SupplementController::class, 'edit'])->name('supplements.edit');
        Route::put('/{supplement}', [SupplementController::class, 'update'])->name('supplements.update');
        Route::delete('/{supplement}', [SupplementController::class, 'destroy'])->name('supplements.destroy');
        Route::post('/{supplement}/remove-media', [SupplementController::class, 'removeMedia'])->name('supplements.remove-media');
    });
});
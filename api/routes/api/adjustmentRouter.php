<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdjustmentController;

Route::prefix('adjustment')->group(function () {
    Route::get('/', [AdjustmentController::class, 'index'])->name('adjustment.index');
    Route::post('/create', [AdjustmentController::class, 'store'])->name('adjustment.store');
    Route::get('/{id}', [AdjustmentController::class, 'show'])->name('adjustment.show');
    Route::put('/{id}', [AdjustmentController::class, 'update'])->name('adjustment.update');
    Route::delete('/{id}', [AdjustmentController::class, 'destroy'])->name('adjustment.destroy');
});

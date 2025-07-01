<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartmentController;

Route::prefix('departments')->group(function () {
    Route::get('/all', [DepartmentController::class, 'index'])->name('departments.index');
    Route::post('/create', [DepartmentController::class, 'store'])->name('departments.store');
    Route::get('/{id}', [DepartmentController::class, 'show'])->name('departments.show');
    Route::patch('/{id}', [DepartmentController::class, 'update'])->name('departments.update');
    Route::delete('/{id}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
});

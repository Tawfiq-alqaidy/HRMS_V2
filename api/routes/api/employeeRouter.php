<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;

Route::prefix('employees')->group(function () {
    Route::get('/', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/{id}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::post('/create', [EmployeeController::class, 'store'])->name('employees.store');
    Route::patch('/{id}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::patch('/archiving/{id}', [EmployeeController::class, 'archiving'])->name('employees.archiving');
    Route::patch('/restoring/{id}', [EmployeeController::class, 'restore'])->name('employees.restore');
    Route::delete('/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
});

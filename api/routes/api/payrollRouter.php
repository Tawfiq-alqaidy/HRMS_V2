<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PayrollController;

Route::prefix('payroll')->group(function () {
    Route::get('/all', [PayrollController::class, 'index'])->name('payroll.index');
    Route::get('/{id}', [PayrollController::class, 'show'])->name('payroll.show');
    Route::post('/', [PayrollController::class, 'store'])->name('payroll.store');
    Route::put('/{id}', [PayrollController::class, 'update'])->name('payroll.update');
    Route::delete('/{id}', [PayrollController::class, 'destroy'])->name('payroll.destroy');
});

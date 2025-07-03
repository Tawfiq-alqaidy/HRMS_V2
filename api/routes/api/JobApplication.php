<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobApplicationController;

Route::prefix('jobApplication')->group(function () {
    Route::get('/all', [JobApplicationController::class, 'index'])->name('jobApplication.index');
    Route::get('/{id}', [JobApplicationController::class, 'show'])->name('jobApplication.show');
    Route::post('/create', [JobApplicationController::class, 'store'])->name('jobApplication.store');
    Route::patch('/{id}', [JobApplicationController::class, 'update'])->name('jobApplication.update');
    Route::delete('/{id}', [JobApplicationController::class, 'destroy'])->name('jobApplication.destroy');
});

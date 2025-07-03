<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobPostingController;

Route::prefix('jobposting')->group(function () {
    Route::get('/all', [JobPostingController::class, 'index'])->name('jobposting.index');
    Route::get('/{id}', [JobPostingController::class, 'show'])->name('jobposting.show');
    Route::post('/create', [JobPostingController::class, 'store'])->name('jobposting.store');
    Route::patch('/{id}', [JobPostingController::class, 'update'])->name('jobposting.update');
    Route::delete('/{id}', [JobPostingController::class, 'destroy'])->name('jobposting.destroy');
});

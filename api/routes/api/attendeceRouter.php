<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;

Route::prefix('attendance')->group(function () {
    Route::get('/all', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::post('/start-work-day', [AttendanceController::class, 'startWorkDay'])->name('attendance.startWorkDay');
    Route::get('/{id}', [AttendanceController::class, 'show'])->name('attendance.show');
    Route::patch('/{id}', [AttendanceController::class, 'update'])->name('attendance.update');
    Route::delete('/{id}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');
});

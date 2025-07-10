<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;

Route::prefix('attendance')->group(function () {
    Route::get('/all', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/start-work-day', [AttendanceController::class, 'startWorkDay'])->name('attendance.startWorkDay');
    Route::post('/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.checkIn');
    Route::post('/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.checkOut');
    Route::get('/is-work-day-started', [AttendanceController::class, 'isWorkDayStarted'])->name('attendance.isWorkDayStarted');
    Route::post('/', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::options('/{id}', function () {
        return response('', 200);
    });
    Route::get('/{id}', [AttendanceController::class, 'show'])->name('attendance.show');
    Route::get('/attendanceAndDeparture/{id}', [AttendanceController::class, 'attendanceAndDeparture'])->name('attendance.attendanceAndDeparture');
    Route::patch('/{id}', [AttendanceController::class, 'update'])->name('attendance.update');
    Route::delete('/{id}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationDemoController;

Route::prefix('demo/notifications')->middleware('auth:sanctum')->group(function () {
    Route::post('/job-apply', [NotificationDemoController::class, 'createJobApplyNotification']);
    Route::post('/payroll', [NotificationDemoController::class, 'createPayrollNotification']);
    Route::post('/adjustment', [NotificationDemoController::class, 'createAdjustmentNotification']);
    Route::post('/bulk', [NotificationDemoController::class, 'createBulkNotification']);
    Route::get('/stats', [NotificationDemoController::class, 'getStats']);
});

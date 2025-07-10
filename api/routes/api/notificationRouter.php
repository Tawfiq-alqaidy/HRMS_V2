<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;

Route::prefix('notifications')->middleware('auth:sanctum')->group(function () {
    // Get all notifications
    Route::get('/', [NotificationController::class, 'index']);

    // Get unread notifications
    Route::get('/unread', [NotificationController::class, 'unread']);

    // Get unread count
    Route::get('/unread-count', [NotificationController::class, 'getUnreadCount']);

    // Mark single notification as read
    Route::patch('/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);

    // Mark multiple notifications as read
    Route::patch('/mark-multiple-as-read', [NotificationController::class, 'markMultipleAsRead']);

    // Mark all notifications as read
    Route::patch('/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);

    // Delete notification
    Route::delete('/{id}', [NotificationController::class, 'destroy']);

    // Create notification (for testing/internal use)
    Route::post('/', [NotificationController::class, 'store']);
});

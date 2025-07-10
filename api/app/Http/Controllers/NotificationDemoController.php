<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationDemoController extends Controller
{
    /**
     * Demo: Create a job application notification
     */
    public function createJobApplyNotification(Request $request): JsonResponse
    {
        $user = Auth::user();

        $notification = NotificationService::createJobApplyNotification(
            $user->id,
            'Senior Developer',
            'John Doe'
        );

        return response()->json([
            'success' => true,
            'message' => 'Job application notification created',
            'notification' => $notification
        ]);
    }

    /**
     * Demo: Create a payroll notification
     */
    public function createPayrollNotification(Request $request): JsonResponse
    {
        $user = Auth::user();

        $notification = NotificationService::createPayrollNotification(
            $user->id,
            'January 2025',
            5000.00
        );

        return response()->json([
            'success' => true,
            'message' => 'Payroll notification created',
            'notification' => $notification
        ]);
    }

    /**
     * Demo: Create an adjustment notification
     */
    public function createAdjustmentNotification(Request $request): JsonResponse
    {
        $user = Auth::user();

        $notification = NotificationService::createAdjustmentNotification(
            $user->id,
            'Overtime',
            200.00
        );

        return response()->json([
            'success' => true,
            'message' => 'Adjustment notification created',
            'notification' => $notification
        ]);
    }

    /**
     * Demo: Create notifications for multiple users
     */
    public function createBulkNotification(Request $request): JsonResponse
    {
        // Get first 3 users for demo
        $users = User::take(3)->pluck('id')->toArray();

        $notifications = NotificationService::createForMultipleUsers(
            $users,
            'payroll',
            'Monthly payroll processing has been completed'
        );

        return response()->json([
            'success' => true,
            'message' => 'Bulk notifications created',
            'notifications_count' => count($notifications),
            'notifications' => $notifications
        ]);
    }

    /**
     * Get notification statistics for current user
     */
    public function getStats(Request $request): JsonResponse
    {
        $user = Auth::user();
        $stats = NotificationService::getStatsForUser($user->id);

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}

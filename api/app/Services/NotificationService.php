<?php

namespace App\Services;

use App\Models\Notification;
use App\Events\NotificationBroadcast;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Create a new notification and broadcast it
     */
    public static function create(int $userId, string $type, string $message): ?Notification
    {
        try {
            // Validate type
            if (!in_array($type, Notification::getAllowedTypes())) {
                Log::error("Invalid notification type: {$type}");
                return null;
            }

            // Create notification
            $notification = Notification::create([
                'user_id' => $userId,
                'type' => $type,
                'message' => $message,
                'is_read' => false
            ]);

            // Broadcast the notification
            broadcast(new NotificationBroadcast($notification));

            Log::info("Notification created and broadcast for user {$userId}", [
                'notification_id' => $notification->id,
                'type' => $type
            ]);

            return $notification;
        } catch (\Exception $e) {
            Log::error('Error creating notification: ' . $e->getMessage(), [
                'user_id' => $userId,
                'type' => $type,
                'message' => $message
            ]);
            return null;
        }
    }

    /**
     * Create a job application notification
     */
    public static function createJobApplyNotification(int $userId, string $jobTitle, string $applicantName): ?Notification
    {
        $message = "New job application received for {$jobTitle} from {$applicantName}";
        return self::create($userId, Notification::TYPE_JOB_APPLY, $message);
    }

    /**
     * Create a payroll notification
     */
    public static function createPayrollNotification(int $userId, string $period, float $amount): ?Notification
    {
        $message = "Your payroll for {$period} has been processed. Amount: $" . number_format($amount, 2);
        return self::create($userId, Notification::TYPE_PAYROLL, $message);
    }

    /**
     * Create an adjustment notification
     */
    public static function createAdjustmentNotification(int $userId, string $adjustmentType, float $amount): ?Notification
    {
        $message = "A {$adjustmentType} adjustment of $" . number_format($amount, 2) . " has been applied to your account";
        return self::create($userId, Notification::TYPE_ADJUSTMENTS, $message);
    }

    /**
     * Create multiple notifications for multiple users
     */
    public static function createForMultipleUsers(array $userIds, string $type, string $message): array
    {
        $notifications = [];

        foreach ($userIds as $userId) {
            $notification = self::create($userId, $type, $message);
            if ($notification) {
                $notifications[] = $notification;
            }
        }

        return $notifications;
    }

    /**
     * Get notification statistics for a user
     */
    public static function getStatsForUser(int $userId): array
    {
        return [
            'total' => Notification::forUser($userId)->count(),
            'unread' => Notification::forUser($userId)->unread()->count(),
            'by_type' => [
                'job_apply' => Notification::forUser($userId)->ofType(Notification::TYPE_JOB_APPLY)->count(),
                'payroll' => Notification::forUser($userId)->ofType(Notification::TYPE_PAYROLL)->count(),
                'adjustments' => Notification::forUser($userId)->ofType(Notification::TYPE_ADJUSTMENTS)->count(),
            ]
        ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Events\NotificationBroadcast;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $perPage = $request->get('per_page', 15);
            $type = $request->get('type');

            $query = Notification::forUser($user->id)
                ->orderBy('created_at', 'desc');

            if ($type) {
                $query->ofType($type);
            }

            $notifications = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $notifications,
                'unread_count' => Notification::getUnreadCountForUser($user->id)
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching notifications: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch notifications'
            ], 500);
        }
    }

    /**
     * Get only unread notifications for the authenticated user
     */
    public function unread(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $perPage = $request->get('per_page', 15);
            $type = $request->get('type');

            $query = Notification::forUser($user->id)
                ->unread()
                ->orderBy('created_at', 'desc');

            if ($type) {
                $query->ofType($type);
            }

            $notifications = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $notifications,
                'unread_count' => Notification::getUnreadCountForUser($user->id)
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching unread notifications: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch unread notifications'
            ], 500);
        }
    }

    /**
     * Mark a single notification as read
     */
    public function markAsRead(Request $request, int $id): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $notification = Notification::forUser($user->id)->find($id);

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
                'data' => $notification->fresh(),
                'unread_count' => Notification::getUnreadCountForUser($user->id)
            ]);
        } catch (\Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read'
            ], 500);
        }
    }

    /**
     * Mark multiple notifications as read
     */
    public function markMultipleAsRead(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'notification_ids' => 'required|array|min:1',
                'notification_ids.*' => 'required|integer|exists:notifications,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $notificationIds = $request->input('notification_ids');
            $updatedCount = Notification::markMultipleAsRead($notificationIds, $user->id);

            return response()->json([
                'success' => true,
                'message' => "{$updatedCount} notifications marked as read",
                'updated_count' => $updatedCount,
                'unread_count' => Notification::getUnreadCountForUser($user->id)
            ]);
        } catch (\Exception $e) {
            Log::error('Error marking multiple notifications as read: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notifications as read'
            ], 500);
        }
    }

    /**
     * Mark all notifications as read for the authenticated user
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $updatedCount = Notification::forUser($user->id)
                ->unread()
                ->update(['is_read' => true]);

            return response()->json([
                'success' => true,
                'message' => "{$updatedCount} notifications marked as read",
                'updated_count' => $updatedCount,
                'unread_count' => 0
            ]);
        } catch (\Exception $e) {
            Log::error('Error marking all notifications as read: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to mark all notifications as read'
            ], 500);
        }
    }

    /**
     * Delete a notification
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $notification = Notification::forUser($user->id)->find($id);

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully',
                'unread_count' => Notification::getUnreadCountForUser($user->id)
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting notification: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete notification'
            ], 500);
        }
    }

    /**
     * Get unread count for the authenticated user
     */
    public function getUnreadCount(): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $unreadCount = Notification::getUnreadCountForUser($user->id);

            return response()->json([
                'success' => true,
                'unread_count' => $unreadCount
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting unread count: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get unread count'
            ], 500);
        }
    }

    /**
     * Create a new notification (for testing purposes or internal use)
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer|exists:users,id',
                'type' => 'required|string|in:' . implode(',', Notification::getAllowedTypes()),
                'message' => 'required|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $notification = Notification::create([
                'user_id' => $request->input('user_id'),
                'type' => $request->input('type'),
                'message' => $request->input('message'),
                'is_read' => false
            ]);

            // Broadcast the notification
            broadcast(new NotificationBroadcast($notification));

            return response()->json([
                'success' => true,
                'message' => 'Notification created successfully',
                'data' => $notification->load('user')
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating notification: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create notification'
            ], 500);
        }
    }
}

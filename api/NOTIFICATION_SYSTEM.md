# Real-Time Notification System Documentation

## Overview

This is a custom real-time notification system built for Laravel 12 using your existing custom notifications table. The system provides REST API endpoints and real-time broadcasting capabilities without using Laravel's default notification system.

## Table Structure

Your custom notifications table structure:

-   `id` (primary key)
-   `user_id` (foreign key to users.id)
-   `type` (enum: 'JobApply', 'payroll', 'adjustments')
-   `message` (text)
-   `is_read` (tinyint, default: 0)
-   `created_at` (timestamp)

## Setup Instructions

### 1. Install Broadcasting Dependencies

```bash
composer require pusher/pusher-php-server
```

### 2. Configure Broadcasting

Add these environment variables to your `.env` file:

```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_pusher_app_id
PUSHER_APP_KEY=your_pusher_app_key
PUSHER_APP_SECRET=your_pusher_app_secret
PUSHER_APP_CLUSTER=mt1
```

### 3. Set up Pusher Account

1. Go to [Pusher.com](https://pusher.com)
2. Create a new app
3. Copy your app credentials to the `.env` file

## API Endpoints

### Authentication Required (Sanctum)

All notification endpoints require authentication via Laravel Sanctum.

### Available Endpoints

#### 1. Get All Notifications

```
GET /api/notifications
```

Query Parameters:

-   `per_page` (optional): Number of notifications per page (default: 15)
-   `type` (optional): Filter by notification type ('JobApply', 'payroll', 'adjustments')

Response:

```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "user_id": 1,
                "type": "payroll",
                "message": "Your payroll has been processed",
                "is_read": false,
                "created_at": "2025-01-10T10:00:00.000000Z"
            }
        ],
        "last_page": 1,
        "total": 1
    },
    "unread_count": 5
}
```

#### 2. Get Unread Notifications

```
GET /api/notifications/unread
```

Same parameters and response structure as above, but only returns unread notifications.

#### 3. Get Unread Count

```
GET /api/notifications/unread-count
```

Response:

```json
{
    "success": true,
    "unread_count": 5
}
```

#### 4. Mark Single Notification as Read

```
PATCH /api/notifications/{id}/mark-as-read
```

Response:

```json
{
    "success": true,
    "message": "Notification marked as read",
    "data": {
        "id": 1,
        "user_id": 1,
        "type": "payroll",
        "message": "Your payroll has been processed",
        "is_read": true,
        "created_at": "2025-01-10T10:00:00.000000Z"
    },
    "unread_count": 4
}
```

#### 5. Mark Multiple Notifications as Read

```
PATCH /api/notifications/mark-multiple-as-read
```

Request Body:

```json
{
    "notification_ids": [1, 2, 3]
}
```

Response:

```json
{
    "success": true,
    "message": "3 notifications marked as read",
    "updated_count": 3,
    "unread_count": 2
}
```

#### 6. Mark All Notifications as Read

```
PATCH /api/notifications/mark-all-as-read
```

Response:

```json
{
    "success": true,
    "message": "5 notifications marked as read",
    "updated_count": 5,
    "unread_count": 0
}
```

#### 7. Delete Notification

```
DELETE /api/notifications/{id}
```

Response:

```json
{
    "success": true,
    "message": "Notification deleted successfully",
    "unread_count": 4
}
```

#### 8. Create Notification (For Testing)

```
POST /api/notifications
```

Request Body:

```json
{
    "user_id": 1,
    "type": "payroll",
    "message": "Your payroll has been processed"
}
```

Response:

```json
{
    "success": true,
    "message": "Notification created successfully",
    "data": {
        "id": 1,
        "user_id": 1,
        "type": "payroll",
        "message": "Your payroll has been processed",
        "is_read": false,
        "created_at": "2025-01-10T10:00:00.000000Z"
    }
}
```

## Real-Time Broadcasting

### Channel Structure

Each user subscribes to their private channel:

```
private-user.{user_id}
```

### Event Name

```
notification.new
```

### Broadcast Data

```json
{
    "id": 1,
    "type": "payroll",
    "message": "Your payroll has been processed",
    "is_read": false,
    "created_at": "2025-01-10T10:00:00.000000Z",
    "user_id": 1,
    "unread_count": 6
}
```

## Using the NotificationService

### Basic Usage

```php
use App\Services\NotificationService;

// Create a basic notification
$notification = NotificationService::create(
    $userId,
    'payroll',
    'Your payroll has been processed'
);

// Create specific notification types
$notification = NotificationService::createJobApplyNotification(
    $userId,
    'Senior Developer',
    'John Doe'
);

$notification = NotificationService::createPayrollNotification(
    $userId,
    'January 2025',
    5000.00
);

$notification = NotificationService::createAdjustmentNotification(
    $userId,
    'Overtime',
    200.00
);

// Create notifications for multiple users
$userIds = [1, 2, 3, 4, 5];
$notifications = NotificationService::createForMultipleUsers(
    $userIds,
    'payroll',
    'Monthly payroll processing completed'
);
```

### In Your Business Logic

```php
// In your PayrollController
public function processPayroll(Request $request)
{
    // Process payroll logic...

    // Send notification to employee
    NotificationService::createPayrollNotification(
        $employee->user_id,
        'January 2025',
        $payrollAmount
    );

    // The notification will be automatically stored in database
    // and broadcast to the user's private channel
}

// In your JobApplicationController
public function store(Request $request)
{
    // Store job application...

    // Notify HR about new application
    NotificationService::createJobApplyNotification(
        $hrUserId,
        $jobPosting->title,
        $applicant->name
    );
}
```

## Frontend Integration (Next.js)

### Installation

```bash
npm install pusher-js
```

### Basic Setup

```javascript
// lib/pusher.js
import Pusher from "pusher-js";

const pusher = new Pusher(process.env.NEXT_PUBLIC_PUSHER_KEY, {
    cluster: process.env.NEXT_PUBLIC_PUSHER_CLUSTER,
    authEndpoint: `${process.env.NEXT_PUBLIC_API_URL}/broadcasting/auth`,
    auth: {
        headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
    },
});

export default pusher;
```

### React Hook for Notifications

```javascript
// hooks/useNotifications.js
import { useEffect, useState } from "react";
import pusher from "../lib/pusher";

export const useNotifications = (userId) => {
    const [notifications, setNotifications] = useState([]);
    const [unreadCount, setUnreadCount] = useState(0);

    useEffect(() => {
        if (!userId) return;

        const channel = pusher.subscribe(`private-user.${userId}`);

        channel.bind("notification.new", (data) => {
            setNotifications((prev) => [data, ...prev]);
            setUnreadCount(data.unread_count);

            // Show toast notification
            showToast(data.message);
        });

        return () => {
            pusher.unsubscribe(`private-user.${userId}`);
        };
    }, [userId]);

    return { notifications, unreadCount };
};
```

### React Component Example

```javascript
// components/NotificationCenter.js
import { useNotifications } from "../hooks/useNotifications";

const NotificationCenter = ({ userId }) => {
    const { notifications, unreadCount } = useNotifications(userId);

    const markAsRead = async (notificationId) => {
        try {
            await fetch(`/api/notifications/${notificationId}/mark-as-read`, {
                method: "PATCH",
                headers: {
                    Authorization: `Bearer ${localStorage.getItem("token")}`,
                },
            });
        } catch (error) {
            console.error("Error marking notification as read:", error);
        }
    };

    return (
        <div className="notification-center">
            <div className="notification-badge">
                {unreadCount > 0 && <span>{unreadCount}</span>}
            </div>

            <div className="notifications-list">
                {notifications.map((notification) => (
                    <div
                        key={notification.id}
                        className={`notification-item ${
                            notification.is_read ? "read" : "unread"
                        }`}
                        onClick={() => markAsRead(notification.id)}
                    >
                        <div className="notification-type">
                            {notification.type}
                        </div>
                        <div className="notification-message">
                            {notification.message}
                        </div>
                        <div className="notification-time">
                            {notification.created_at}
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
};
```

## Demo Endpoints

We've included demo endpoints to test the system:

```
POST /api/demo/notifications/job-apply
POST /api/demo/notifications/payroll
POST /api/demo/notifications/adjustment
POST /api/demo/notifications/bulk
GET /api/demo/notifications/stats
```

## Security Considerations

1. **Private Channels**: Each user can only access their own notifications through private channels
2. **Authentication**: All endpoints require Sanctum authentication
3. **Authorization**: Users can only access their own notifications
4. **Validation**: All inputs are validated before processing

## Performance Considerations

1. **Indexing**: Ensure your notifications table has proper indexes:

    ```sql
    INDEX idx_user_id (user_id)
    INDEX idx_user_read (user_id, is_read)
    INDEX idx_created_at (created_at)
    ```

2. **Pagination**: All list endpoints use pagination to handle large datasets
3. **Caching**: Consider implementing Redis caching for frequently accessed data

## Testing

Use the demo endpoints to test the system:

1. Create notifications using the demo endpoints
2. Check real-time updates in your frontend
3. Test marking notifications as read
4. Verify broadcasting is working correctly

## Troubleshooting

1. **Broadcasting not working**: Check your Pusher credentials and ensure the BroadcastServiceProvider is registered
2. **Authentication issues**: Verify your Sanctum setup and token handling
3. **Database issues**: Ensure your notifications table structure matches the expected schema
4. **CORS issues**: Configure CORS properly for your frontend domain

This system provides a complete, production-ready notification system without using Laravel's default notification features.

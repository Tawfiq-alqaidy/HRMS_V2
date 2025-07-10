<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'type', 'message', 'is_read'];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Define the allowed notification types
    const TYPE_JOB_APPLY = 'JobApply';
    const TYPE_PAYROLL = 'payroll';
    const TYPE_ADJUSTMENTS = 'adjustments';

    public static function getAllowedTypes(): array
    {
        return [
            self::TYPE_JOB_APPLY,
            self::TYPE_PAYROLL,
            self::TYPE_ADJUSTMENTS,
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope for unread notifications
    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    // Scope for specific user notifications
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    // Scope for specific type
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    // Mark notification as read
    public function markAsRead(): bool
    {
        return $this->update(['is_read' => true]);
    }

    // Check if notification is read
    public function isRead(): bool
    {
        return $this->is_read;
    }

    // Check if notification is unread
    public function isUnread(): bool
    {
        return !$this->is_read;
    }

    // Static method to mark multiple notifications as read
    public static function markMultipleAsRead(array $ids, int $userId): int
    {
        return static::whereIn('id', $ids)
            ->where('user_id', $userId)
            ->update(['is_read' => true]);
    }

    // Static method to get unread count for a user
    public static function getUnreadCountForUser(int $userId): int
    {
        return static::forUser($userId)->unread()->count();
    }
}

<?php

namespace App\Services;

use App\Models\PlatformNotification;
use App\Models\User;

class NotificationService
{
    public function send(
        User $user,
        string $type,
        string $title,
        string $titleAr,
        string $body,
        string $bodyAr,
        array $data = [],
        ?string $actionUrl = null
    ): PlatformNotification {
        return PlatformNotification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'title_ar' => $titleAr,
            'body' => $body,
            'body_ar' => $bodyAr,
            'data' => $data,
            'action_url' => $actionUrl,
        ]);
    }

    public function markAllRead(User $user): void
    {
        PlatformNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
    }

    public function unreadCount(User $user): int
    {
        return PlatformNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
    }
}

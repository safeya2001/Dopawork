<?php

namespace App\Http\Controllers;

use App\Models\PlatformNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /** Show single notification detail */
    public function show(PlatformNotification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }
        if (!$notification->is_read) {
            $notification->update(['is_read' => true, 'read_at' => now()]);
        }
        return view('notifications.show', compact('notification'));
    }

    /** Full notifications page */
    public function index(Request $request)
    {
        $query = PlatformNotification::where('user_id', Auth::id())->latest();

        if ($request->filter === 'unread') {
            $query->where('is_read', false);
        }

        $notifications = $query->paginate(20)->withQueryString();

        // Mark all as read when viewing the page (optional: remove if you want manual)
        // PlatformNotification::where('user_id', Auth::id())->where('is_read', false)->update(['is_read'=>true,'read_at'=>now()]);

        return view('notifications.index', compact('notifications'));
    }

    /** Return latest 10 notifications as JSON for the dropdown */
    public function fetch()
    {
        $notifications = PlatformNotification::where('user_id', Auth::id())
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn($n) => [
                'id'         => $n->id,
                'title'      => app()->getLocale() === 'ar' ? ($n->title_ar ?: $n->title) : ($n->title ?: $n->title_ar),
                'body'       => app()->getLocale() === 'ar' ? ($n->body_ar  ?: $n->body)  : ($n->body  ?: $n->body_ar),
                'is_read'    => (bool) $n->is_read,
                'action_url' => $n->action_url,
                'created_at' => $n->created_at?->diffForHumans(),
            ]);

        $unread = PlatformNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['notifications' => $notifications, 'unread' => $unread]);
    }

    /** Mark all as read */
    public function markAllRead()
    {
        PlatformNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['ok' => true]);
    }

    /** Mark single as read */
    public function markRead(PlatformNotification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }
        $notification->update(['is_read' => true, 'read_at' => now()]);
        return response()->json(['ok' => true]);
    }
}

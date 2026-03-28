<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformNotification;
use App\Models\User;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $recent = PlatformNotification::whereNull('user_id')
            ->orWhere('type', 'announcement')
            ->latest()
            ->limit(20)
            ->get();

        return view('admin.announcements.index', compact('recent'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:200',
            'title_ar' => 'required|string|max:200',
            'body'     => 'required|string|max:1000',
            'body_ar'  => 'required|string|max:1000',
            'audience' => 'required|in:all,freelancers,clients',
        ]);

        $query = User::where('status', 'active');

        if ($request->audience === 'freelancers') {
            $query->where('role', 'freelancer');
        } elseif ($request->audience === 'clients') {
            $query->where('role', 'client');
        }

        $users = $query->pluck('id');

        $notifications = $users->map(fn($userId) => [
            'user_id'    => $userId,
            'type'       => 'announcement',
            'title'      => $request->title,
            'title_ar'   => $request->title_ar,
            'body'       => $request->body,
            'body_ar'    => $request->body_ar,
            'is_read'    => false,
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();

        // Insert in chunks to avoid huge queries
        foreach (array_chunk($notifications, 500) as $chunk) {
            PlatformNotification::insert($chunk);
        }

        return back()->with('success', app()->getLocale() === 'ar'
            ? "تم إرسال الإشعار لـ {$users->count()} مستخدم ✓"
            : "Announcement sent to {$users->count()} users ✓");
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Models\EscrowTransaction;
use App\Models\IdentityVerification;
use App\Models\Order;
use App\Models\PlatformNotification;
use App\Models\Service;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // ── Core stats ──────────────────────────────────────────────────────
        $stats = [
            'total_users'           => User::count(),
            'total_freelancers'     => User::where('role', 'freelancer')->count(),
            'total_clients'         => User::where('role', 'client')->count(),
            'pending_verifications' => IdentityVerification::where('status', 'pending')->count(),
            'pending_services'      => Service::where('status', 'pending_review')->count(),
            'active_orders'         => Order::whereIn('status', ['pending', 'in_progress', 'delivered'])->count(),
            'total_orders'          => Order::count(),
            'completed_orders'      => Order::where('status', 'completed')->count(),
            'cancelled_orders'      => Order::where('status', 'cancelled')->count(),
            'escrow_held'           => EscrowTransaction::where('status', 'held')->sum('amount'),
            'total_revenue'         => Order::where('status', 'completed')->sum('platform_fee'),
            'pending_withdrawals'   => WalletTransaction::where('type', 'withdrawal')->where('status', 'pending')->count(),
            'open_disputes'         => Dispute::where('status', 'open')->count(),
            // Growth vs last month
            'revenue_this_month'    => Order::where('status', 'completed')
                                        ->whereMonth('created_at', now()->month)
                                        ->whereYear('created_at', now()->year)
                                        ->sum('platform_fee'),
            'revenue_last_month'    => Order::where('status', 'completed')
                                        ->whereMonth('created_at', now()->subMonth()->month)
                                        ->whereYear('created_at', now()->subMonth()->year)
                                        ->sum('platform_fee'),
            'new_users_this_week'   => User::where('created_at', '>=', now()->startOfWeek())->count(),
            'new_users_last_week'   => User::whereBetween('created_at', [
                                        now()->subWeek()->startOfWeek(),
                                        now()->subWeek()->endOfWeek(),
                                       ])->count(),
            'orders_this_month'     => Order::whereMonth('created_at', now()->month)
                                        ->whereYear('created_at', now()->year)
                                        ->count(),
            'orders_last_month'     => Order::whereMonth('created_at', now()->subMonth()->month)
                                        ->whereYear('created_at', now()->subMonth()->year)
                                        ->count(),
        ];

        // ── Chart: revenue + orders last 14 days ────────────────────────────
        $rawChart = Order::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->selectRaw('DATE(created_at) as date, SUM(platform_fee) as revenue, COUNT(*) as cnt')
            ->groupBy('date')
            ->orderBy('date')
            ->get()->keyBy('date');

        $chartLabels = $chartRevenue = $chartOrders = [];
        for ($i = 13; $i >= 0; $i--) {
            $date           = now()->subDays($i)->format('Y-m-d');
            $chartLabels[]  = now()->subDays($i)->format('M d');
            $chartRevenue[] = round((float)($rawChart[$date]->revenue ?? 0), 3);
            $chartOrders[]  = (int)($rawChart[$date]->cnt ?? 0);
        }

        // ── Lists ─────────────────────────────────────────────────────────
        $recentOrders = Order::with(['client', 'freelancer', 'service'])
            ->latest()->limit(8)->get();

        $pendingVerifications = IdentityVerification::with('user')
            ->where('status', 'pending')->latest()->limit(5)->get();

        return view('admin.dashboard', compact(
            'stats', 'recentOrders', 'pendingVerifications',
            'chartLabels', 'chartRevenue', 'chartOrders'
        ));
    }

    public function verifications(Request $request)
    {
        $verifications = IdentityVerification::with('user')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        return view('admin.verifications.index', compact('verifications'));
    }

    public function approveVerification(IdentityVerification $verification)
    {
        $verification->update([
            'status'      => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        $verification->user->update(['status' => 'active']);

        PlatformNotification::create([
            'user_id'  => $verification->user_id,
            'type'     => 'identity_approved',
            'title'    => 'تم قبول هويتك',
            'title_ar' => 'تم قبول هويتك',
            'body'     => 'Congratulations! Your identity has been verified and your account is now active.',
            'body_ar'  => 'مبروك! تم التحقق من هويتك وأصبح حسابك نشطاً. يمكنك الآن استخدام جميع ميزات المنصة.',
        ]);

        return back()->with('success', "تم قبول هوية {$verification->user->name} بنجاح.");
    }

    public function rejectVerification(Request $request, IdentityVerification $verification)
    {
        $request->validate([
            'rejection_reason'    => 'required|string|max:500',
            'rejection_reason_ar' => 'required|string|max:500',
        ]);

        $verification->update([
            'status'              => 'rejected',
            'rejection_reason'    => $request->rejection_reason,
            'rejection_reason_ar' => $request->rejection_reason_ar,
            'reviewed_by'         => auth()->id(),
            'reviewed_at'         => now(),
        ]);

        $verification->user->update(['status' => 'inactive']);

        PlatformNotification::create([
            'user_id'  => $verification->user_id,
            'type'     => 'identity_rejected',
            'title'    => 'Identity Verification Rejected',
            'title_ar' => 'تم رفض وثائق الهوية',
            'body'     => 'Unfortunately, your identity documents were rejected. Reason: ' . $request->rejection_reason . '. Please resubmit.',
            'body_ar'  => 'للأسف، تم رفض وثائق هويتك. السبب: ' . $request->rejection_reason_ar . '. يرجى إعادة رفع الوثائق.',
        ]);

        return back()->with('success', 'تم رفض الطلب وإشعار المستخدم.');
    }

    public function approveService(Service $service)
    {
        $service->update(['status' => 'active']);
        return back()->with('success', 'تمت الموافقة على الخدمة وهي الآن مرئية.');
    }

    public function rejectService(Request $request, Service $service)
    {
        $request->validate(['rejection_reason' => 'required|string|max:500']);
        $service->update(['status' => 'rejected', 'rejection_reason' => $request->rejection_reason]);
        return back()->with('success', 'تم رفض الخدمة.');
    }

    public function services(Request $request)
    {
        $services = Service::with(['user', 'category'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        return view('admin.services.index', compact('services'));
    }

    public function showVerification(IdentityVerification $verification)
    {
        $verification->load('user', 'reviewer');
        return view('admin.verifications.show', compact('verification'));
    }

    public function serveDocument(IdentityVerification $verification, string $field)
    {
        abort_unless(in_array($field, ['front_image', 'back_image', 'selfie_image']), 404);

        $path = $verification->$field;
        abort_if(!$path || !\Storage::disk('private')->exists($path), 404);

        return \Storage::disk('private')->response($path);
    }

    public function escrow(Request $request)
    {
        $escrows = EscrowTransaction::with(['order.client', 'order.freelancer'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        $summary = [
            'held'     => EscrowTransaction::where('status', 'held')->sum('amount'),
            'released' => EscrowTransaction::where('status', 'released')->sum('amount'),
            'refunded' => EscrowTransaction::where('status', 'refunded')->sum('amount'),
        ];

        return view('admin.escrow.index', compact('escrows', 'summary'));
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to   ?? now()->toDateString();

        $orders = Order::whereBetween('created_at', ["{$from} 00:00:00", "{$to} 23:59:59"]);

        $stats = [
            'total_orders'     => (clone $orders)->count(),
            'completed_orders' => (clone $orders)->where('status', 'completed')->count(),
            'total_revenue'    => (clone $orders)->where('status', 'completed')->sum('total_amount'),
            'commission'       => (clone $orders)->where('status', 'completed')->sum('platform_fee'),
            'new_users'        => User::whereBetween('created_at', ["{$from} 00:00:00", "{$to} 23:59:59"])->count(),
            'new_freelancers'  => User::where('role', 'freelancer')->whereBetween('created_at', ["{$from} 00:00:00", "{$to} 23:59:59"])->count(),
            'withdrawals'      => WalletTransaction::where('type', 'withdrawal')->where('status', 'completed')
                                    ->whereBetween('created_at', ["{$from} 00:00:00", "{$to} 23:59:59"])->sum('amount'),
        ];

        // Daily revenue chart data
        $dailyRevenue = Order::where('status', 'completed')
            ->whereBetween('created_at', ["{$from} 00:00:00", "{$to} 23:59:59"])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(platform_fee) as revenue'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top freelancers by earnings in period
        $topFreelancers = User::where('role', 'freelancer')
            ->withCount(['ordersAsFreelancer as completed_count' => fn($q) =>
                $q->where('status', 'completed')
                  ->whereBetween('created_at', ["{$from} 00:00:00", "{$to} 23:59:59"])
            ])
            ->withSum(['ordersAsFreelancer as total_earned' => fn($q) =>
                $q->where('status', 'completed')
                  ->whereBetween('created_at', ["{$from} 00:00:00", "{$to} 23:59:59"])
            ], 'freelancer_earnings')
            ->orderByDesc('total_earned')
            ->limit(10)
            ->get();

        return view('admin.reports.index', compact('stats', 'from', 'to', 'dailyRevenue', 'topFreelancers'));
    }

    public function exportCsv(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to   ?? now()->toDateString();

        $orders = Order::with(['client', 'freelancer', 'service'])
            ->where('status', 'completed')
            ->whereBetween('created_at', ["{$from} 00:00:00", "{$to} 23:59:59"])
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=report-{$from}-to-{$to}.csv",
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM for Excel

            fputcsv($file, ['#', 'Order #', 'Client', 'Freelancer', 'Service', 'Amount (JOD)', 'Commission (JOD)', 'Date']);

            foreach ($orders as $i => $order) {
                fputcsv($file, [
                    $i + 1,
                    $order->order_number,
                    $order->client->name ?? '-',
                    $order->freelancer->name ?? '-',
                    $order->service->title ?? '-',
                    number_format($order->total_amount, 3),
                    number_format($order->platform_fee, 3),
                    $order->created_at->format('Y-m-d'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

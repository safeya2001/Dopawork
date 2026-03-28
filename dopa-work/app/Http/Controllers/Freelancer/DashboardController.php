<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProjectProposal;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'active_services'   => Service::where('user_id', $user->id)->where('status', 'active')->count(),
            'total_orders'      => Order::where('freelancer_id', $user->id)->count(),
            'completed'         => Order::where('freelancer_id', $user->id)->where('status', 'completed')->count(),
            'active_orders'     => Order::where('freelancer_id', $user->id)->whereIn('status', ['pending', 'in_progress', 'delivered', 'revision'])->count(),
            'active_contracts'  => ProjectProposal::where('freelancer_id', $user->id)->where('status', 'accepted')->count(),
            'pending_proposals' => ProjectProposal::where('freelancer_id', $user->id)->where('status', 'pending')->count(),
        ];

        $earnings = Order::where('freelancer_id', $user->id)
            ->where('status', 'completed')
            ->sum('freelancer_earnings');

        $orders = Order::where('freelancer_id', $user->id)
            ->with(['client', 'service'])
            ->latest()
            ->limit(5)
            ->get();

        $services = Service::where('user_id', $user->id)
            ->where('status', 'active')
            ->latest()
            ->limit(3)
            ->get();

        $activeContracts = ProjectProposal::where('freelancer_id', $user->id)
            ->where('status', 'accepted')
            ->with(['project', 'milestones' => fn($q) => $q->whereIn('status', ['pending', 'in_progress', 'revision_requested'])->orderBy('sort_order')])
            ->latest()
            ->limit(3)
            ->get();

        return view('freelancer.dashboard', compact('orders', 'stats', 'earnings', 'services', 'activeContracts'));
    }
}

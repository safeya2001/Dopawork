<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Active orders count for the notification strip
        $activeOrdersCount = Order::where('client_id', $user->id)
            ->whereIn('status', ['pending', 'in_progress', 'delivered', 'revision'])
            ->count();

        $activeOrders = Order::where('client_id', $user->id)
            ->whereIn('status', ['pending', 'in_progress', 'delivered', 'revision'])
            ->with(['freelancer', 'service'])
            ->latest()
            ->limit(3)
            ->get();

        // Categories for the horizontal scroll
        $categories = Category::active()->parentOnly()->orderBy('sort_order')->get();

        // Featured services
        $featuredServices = Service::active()
            ->where('is_featured', true)
            ->with(['user.freelancerProfile', 'category', 'packages'])
            ->inRandomOrder()
            ->take(8)
            ->get();

        // Newest services if not enough featured
        $newServices = Service::active()
            ->with(['user.freelancerProfile', 'category', 'packages'])
            ->latest()
            ->take(8)
            ->get();

        return view('client.dashboard', compact(
            'user', 'activeOrdersCount', 'activeOrders',
            'categories', 'featuredServices', 'newServices'
        ));
    }
}

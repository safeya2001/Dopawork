<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Redirect admin/super_admin directly to the dashboard
        if (auth()->check() && auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $categories = Category::active()->parentOnly()->orderBy('sort_order')->get();
        $popularCategories = $categories->take(8);
        $featuredServices = Service::active()
            ->where('is_featured', true)
            ->with(['user.freelancerProfile', 'category', 'packages'])
            ->take(8)
            ->get();

        $stats = [
            'freelancers' => User::where('role', 'freelancer')->where('status', 'active')->count(),
            'services' => Service::active()->count(),
            'orders' => Order::where('status', 'completed')->count(),
        ];

        return view('home.index', compact('categories', 'popularCategories', 'featuredServices', 'stats'));
    }

    public function setLocale(string $locale)
    {
        if (!in_array($locale, ['ar', 'en'])) {
            $locale = 'ar';
        }

        session(['locale' => $locale]);

        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }

        return redirect()->back()->withHeaders(['Vary' => 'Accept-Language']);
    }
}

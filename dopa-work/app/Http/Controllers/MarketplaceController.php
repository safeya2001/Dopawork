<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::active()->with(['user.freelancerProfile', 'category', 'packages']);

        if ($request->q) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->q}%")
                  ->orWhere('title_ar', 'like', "%{$request->q}%")
                  ->orWhere('description', 'like', "%{$request->q}%");
            });
        }

        if ($request->category) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        if ($request->min_price) {
            $query->whereHas('packages', fn($q) => $q->where('price', '>=', $request->min_price));
        }

        if ($request->max_price) {
            $query->whereHas('packages', fn($q) => $q->where('price', '<=', $request->max_price));
        }

        $sortMap = [
            'newest' => ['created_at', 'desc'],
            'rating' => ['rating', 'desc'],
            'price_low' => ['id', 'asc'], // simplified
            'orders' => ['orders_count', 'desc'],
        ];
        [$sortCol, $sortDir] = $sortMap[$request->sort] ?? ['is_featured', 'desc'];
        $query->orderBy($sortCol, $sortDir);

        $services = $query->paginate(12)->withQueryString();
        $categories = Category::active()->parentOnly()->with('children')->get();

        return view('services.index', compact('services', 'categories'));
    }

    public function show(string $slug)
    {
        $service = Service::active()
            ->where('slug', $slug)
            ->with(['user.freelancerProfile', 'category', 'packages', 'reviews.reviewer'])
            ->firstOrFail();

        $service->increment('views');

        $relatedServices = Service::active()
            ->where('category_id', $service->category_id)
            ->where('id', '!=', $service->id)
            ->take(4)
            ->get();

        return view('services.show', compact('service', 'relatedServices'));
    }

    public function freelancers(Request $request)
    {
        $query = User::where('role', 'freelancer')
            ->where('status', 'active')
            ->with(['freelancerProfile', 'services' => fn($q) => $q->active()->take(3)]);

        if ($request->q) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->q}%")
                  ->orWhereHas('freelancerProfile', fn($p) => $p->where('professional_title', 'like', "%{$request->q}%"));
            });
        }

        $freelancers = $query->paginate(12)->withQueryString();

        return view('freelancers.index', compact('freelancers'));
    }

    public function freelancerProfile(User $user)
    {
        if (!$user->isFreelancer()) abort(404);

        $user->load(['freelancerProfile', 'services' => fn($q) => $q->active()->with(['packages', 'category']), 'reviews.reviewer']);

        return view('freelancers.show', compact('user'));
    }
}

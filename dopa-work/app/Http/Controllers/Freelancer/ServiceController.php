<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use App\Models\ServicePackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::where('user_id', Auth::id())
            ->with(['category', 'packages'])
            ->withTrashed()
            ->latest()
            ->paginate(10);

        return view('freelancer.services.index', compact('services'));
    }

    public function create()
    {
        $categories = Category::active()->parentOnly()->with('children')->get();
        return view('freelancer.services.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'title_ar'       => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:categories,id',
            'description'    => 'required|string|min:100',
            'description_ar' => 'required|string|min:100',
            'tags'           => 'nullable|array|max:5',
            'cover_image'    => 'required|file|mimes:jpg,jpeg,png,webp|max:5120|dimensions:min_width=400,min_height=300',
            'gallery.*'      => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
            'delivery_days'  => 'required|integer|min:1|max:90',
            'revisions'      => 'required|integer|min:0|max:10',
            // Packages
            'packages.*.type'         => 'required|in:basic,standard,premium',
            'packages.*.name'         => 'required|string|max:100',
            'packages.*.name_ar'      => 'required|string|max:100',
            'packages.*.price'        => 'required|numeric|min:1|max:99999',
            'packages.*.delivery_days'=> 'required|integer|min:1',
            'packages.*.description'  => 'required|string|max:500',
            'packages.*.description_ar' => 'required|string|max:500',
        ]);

        $user = Auth::user();

        if (!$user->isVerified()) {
            return back()->withErrors(['error' => __('You must verify your identity before creating services.')]);
        }

        $coverPath = $request->file('cover_image')->store("services/covers", 'public');
        $gallery = [];
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $img) {
                $gallery[] = $img->store("services/gallery", 'public');
            }
        }

        $service = Service::create([
            'user_id' => $user->id,
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'title' => $request->title,
            'title_ar' => $request->title_ar,
            'slug' => Str::slug($request->title) . '-' . Str::random(6),
            'description' => $request->description,
            'description_ar' => $request->description_ar,
            'tags' => $request->tags,
            'cover_image' => $coverPath,
            'gallery' => $gallery,
            'delivery_days' => $request->delivery_days,
            'revisions' => $request->revisions,
            'status' => 'pending_review',
        ]);

        foreach ($request->packages as $pkg) {
            $service->packages()->create([
                'type' => $pkg['type'],
                'name' => $pkg['name'],
                'name_ar' => $pkg['name_ar'],
                'description' => $pkg['description'],
                'description_ar' => $pkg['description_ar'],
                'price' => $pkg['price'],
                'delivery_days' => $pkg['delivery_days'],
                'revisions' => $pkg['revisions'] ?? 1,
                'features' => $pkg['features'] ?? [],
                'features_ar' => $pkg['features_ar'] ?? [],
            ]);
        }

        return redirect()->route('freelancer.services.index')
            ->with('success', __('Service submitted for review. You will be notified once approved.'));
    }

    public function edit(Service $service)
    {
        if ($service->user_id !== Auth::id()) abort(403);

        $categories = Category::active()->parentOnly()->with('children')->get();
        $service->load('packages');

        return view('freelancer.services.edit', compact('service', 'categories'));
    }

    public function update(Request $request, Service $service)
    {
        if ($service->user_id !== Auth::id()) abort(403);

        $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'description' => 'required|string|min:100',
            'description_ar' => 'required|string|min:100',
            'cover_image' => 'nullable|image|max:5120',
        ]);

        $updateData = $request->only(['title', 'title_ar', 'description', 'description_ar', 'tags', 'delivery_days', 'revisions']);
        $updateData['status'] = 'pending_review'; // Re-review on edit

        if ($request->hasFile('cover_image')) {
            $updateData['cover_image'] = $request->file('cover_image')->store("services/covers", 'public');
        }

        $service->update($updateData);

        return redirect()->route('freelancer.services.index')
            ->with('success', __('Service updated and resubmitted for review.'));
    }

    public function destroy(Service $service)
    {
        if ($service->user_id !== Auth::id()) abort(403);

        $service->delete();

        return back()->with('success', __('Service removed successfully.'));
    }

    public function toggleStatus(Service $service)
    {
        if ($service->user_id !== Auth::id()) abort(403);

        if (!in_array($service->status, ['active', 'paused'])) {
            return back()->withErrors(['error' => __('Only active or paused services can be toggled.')]);
        }

        $service->update(['status' => $service->status === 'active' ? 'paused' : 'active']);

        return back()->with('success', __('Service status updated.'));
    }
}

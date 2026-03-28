<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\FreelancerProfile;
use App\Models\PortfolioItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FreelancerProfileController extends Controller
{
    public function edit()
    {
        $user    = Auth::user();
        $profile = FreelancerProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['experience_level' => 'entry', 'is_available' => true]
        );

        $categories     = Category::where('is_active', true)->whereNull('parent_id')->with('children')->orderBy('sort_order')->get();
        $portfolioItems = $profile->portfolioItems;

        return view('freelancer.profile.edit', compact('profile', 'categories', 'portfolioItems'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'professional_title'    => 'nullable|string|max:150',
            'professional_title_ar' => 'nullable|string|max:150',
            'overview'              => 'nullable|string|max:3000',
            'overview_ar'           => 'nullable|string|max:3000',
            'skills'                => 'nullable|string',
            'languages'             => 'nullable|string',
            'education'             => 'nullable|string|max:1000',
            'portfolio_url'         => 'nullable|url|max:255',
            'hourly_rate'           => 'nullable|numeric|min:0|max:9999',
            'experience_level'      => 'required|in:entry,intermediate,expert',
            'is_available'          => 'nullable|boolean',
            'category_ids'          => 'nullable|array|max:3',
            'category_ids.*'        => 'integer|exists:categories,id',
            'cert_name.*'           => 'nullable|string|max:150',
            'cert_issuer.*'         => 'nullable|string|max:150',
            'cert_year.*'           => 'nullable|integer|min:1990|max:2030',
        ]);

        $user = Auth::user();

        $certifications = [];
        foreach ($request->input('cert_name', []) as $i => $name) {
            if (!empty($name)) {
                $certifications[] = [
                    'name'   => $name,
                    'issuer' => $request->input("cert_issuer.{$i}"),
                    'year'   => $request->input("cert_year.{$i}"),
                ];
            }
        }

        FreelancerProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'professional_title'    => $request->professional_title,
                'professional_title_ar' => $request->professional_title_ar,
                'overview'              => $request->overview,
                'overview_ar'           => $request->overview_ar,
                'skills'                => $this->parseTagList($request->input('skills')),
                'languages'             => $this->parseTagList($request->input('languages')),
                'education'             => $request->education,
                'portfolio_url'         => $request->portfolio_url,
                'hourly_rate'           => $request->hourly_rate,
                'experience_level'      => $request->experience_level ?? 'entry',
                'is_available'          => $request->boolean('is_available'),
                'category_ids'          => $request->input('category_ids', []),
                'certifications'        => $certifications,
            ]
        );

        return redirect()->route('freelancer.profile.edit')
            ->with('success', app()->getLocale() === 'ar'
                ? 'تم حفظ الملف المهني بنجاح ✓'
                : 'Professional profile saved successfully ✓');
    }

    public function addPortfolioItem(Request $request)
    {
        $request->validate([
            'portfolio_type'  => 'required|in:image,pdf,link',
            'portfolio_title' => 'nullable|string|max:100',
            'portfolio_file'  => 'required_if:portfolio_type,image,pdf|nullable|file|max:5120|mimes:jpg,jpeg,png,gif,webp,pdf',
            'portfolio_url_item' => 'required_if:portfolio_type,link|nullable|url|max:255',
        ]);

        $user    = Auth::user();
        $profile = FreelancerProfile::firstOrCreate(['user_id' => $user->id], ['experience_level' => 'entry']);

        if ($profile->portfolioItems()->count() >= 8) {
            return back()->withErrors(['portfolio' => app()->getLocale() === 'ar'
                ? 'الحد الأقصى 8 عناصر في المعرض'
                : 'Maximum 8 portfolio items allowed']);
        }

        $filePath = null;
        $url      = null;

        if ($request->hasFile('portfolio_file')) {
            $filePath = $request->file('portfolio_file')->store('portfolio', 'public');
        } else {
            $url = $request->portfolio_url_item;
        }

        PortfolioItem::create([
            'freelancer_profile_id' => $profile->id,
            'title'      => $request->portfolio_title,
            'type'       => $request->portfolio_type,
            'file_path'  => $filePath,
            'url'        => $url,
            'sort_order' => $profile->portfolioItems()->count(),
        ]);

        return back()->with('success', app()->getLocale() === 'ar'
            ? 'تمت الإضافة بنجاح'
            : 'Item added successfully');
    }

    public function deletePortfolioItem(PortfolioItem $portfolioItem)
    {
        if ($portfolioItem->profile->user_id !== Auth::id()) {
            abort(403);
        }

        if ($portfolioItem->file_path) {
            Storage::disk('public')->delete($portfolioItem->file_path);
        }

        $portfolioItem->delete();

        return back()->with('success', app()->getLocale() === 'ar'
            ? 'تم الحذف'
            : 'Deleted successfully');
    }

    private function parseTagList(?string $raw): array
    {
        if (empty($raw)) {
            return [];
        }
        return collect(explode(',', $raw))
            ->map(fn($t) => trim($t))
            ->filter()
            ->values()
            ->all();
    }
}

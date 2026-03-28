<?php

namespace App\Http\Controllers;

use App\Models\FreelancerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        $user            = Auth::user();
        $freelancerProfile = null;
        if ($user->isFreelancer()) {
            $freelancerProfile = FreelancerProfile::firstOrCreate(
                ['user_id' => $user->id],
                ['experience_level' => 'entry', 'is_available' => true]
            );
        }
        return view('profile.edit', compact('user', 'freelancerProfile'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name'             => 'required|string|max:100',
            'name_ar'          => 'nullable|string|max:100',
            'company_name'     => 'nullable|string|max:150',
            'phone'            => ['nullable', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($user->id)],
            'city'             => 'nullable|string|max:100',
            'bio'              => 'nullable|string|max:1000',
            'avatar'           => 'nullable|image|max:2048',
            'current_password' => 'nullable|string',
            'new_password'     => 'nullable|string|min:8|confirmed',
        ];

        if ($user->isFreelancer()) {
            $rules += [
                'professional_title'    => 'nullable|string|max:150',
                'professional_title_ar' => 'nullable|string|max:150',
                'overview'              => 'nullable|string|max:3000',
                'overview_ar'           => 'nullable|string|max:3000',
                'skills'                => 'nullable|string',
                'languages'             => 'nullable|string',
                'education'             => 'nullable|string|max:500',
                'portfolio_url'         => 'nullable|url|max:255',
                'hourly_rate'           => 'nullable|numeric|min:0|max:9999',
                'experience_level'      => 'required|in:entry,intermediate,expert',
                'is_available'          => 'nullable|boolean',
            ];
        }

        $request->validate($rules);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        // Handle password change
        if ($request->filled('new_password')) {
            if (!$request->filled('current_password') || !Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => __('كلمة المرور الحالية غير صحيحة')])->withInput();
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->fill([
            'name'         => $request->name,
            'name_ar'      => $request->name_ar,
            'company_name' => $request->company_name,
            'phone'        => $request->phone,
            'city'         => $request->city,
            'bio'          => $request->bio,
        ]);

        $user->save();

        // Save freelancer professional profile
        if ($user->isFreelancer()) {
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
                ]
            );
        }

        return back()->with('success', app()->getLocale() === 'ar'
            ? 'تم حفظ التعديلات بنجاح ✓'
            : 'Profile updated successfully ✓');
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

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\IdentityVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VerificationController extends Controller
{
    public function pending()
    {
        $user         = Auth::user();
        $verification = $user->identityVerification;

        // If already approved, redirect to dashboard
        if ($verification?->status === 'approved') {
            return redirect()->route('freelancer.dashboard');
        }

        // If not submitted, redirect to upload
        if (!$verification) {
            return redirect()->route('verification.upload');
        }

        return view('auth.verification_pending', compact('verification'));
    }

    public function showUploadForm()
    {
        $user = Auth::user();
        $existing = $user->identityVerification;

        return view('auth.verification', compact('user', 'existing'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'document_type' => 'required|in:national_id,freelancer_permit,passport,residency_permit',
            'document_number' => 'nullable|string|max:50',
            'front_image' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'back_image' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'selfie_image' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'document_expiry' => 'nullable|date|after:today',
        ]);

        $user = Auth::user();

        $frontPath = $request->file('front_image')->store('verifications/documents', 'private');
        $backPath = $request->file('back_image')?->store('verifications/documents', 'private');
        $selfiePath = $request->file('selfie_image')?->store('verifications/selfies', 'private');

        IdentityVerification::updateOrCreate(
            ['user_id' => $user->id],
            [
                'document_type' => $request->document_type,
                'document_number' => $request->document_number,
                'front_image' => $frontPath,
                'back_image' => $backPath,
                'selfie_image' => $selfiePath,
                'document_expiry' => $request->document_expiry,
                'status' => 'pending',
                'rejection_reason' => null,
                'rejection_reason_ar' => null,
            ]
        );

        $user->update(['status' => 'pending_verification']);

        return redirect()->route('verification.pending')
            ->with('success', app()->getLocale() === 'ar'
                ? 'تم رفع وثائقك بنجاح! سيتم مراجعتها خلال 24-48 ساعة.'
                : 'Your documents have been submitted successfully! They will be reviewed within 24-48 hours.');
    }
}

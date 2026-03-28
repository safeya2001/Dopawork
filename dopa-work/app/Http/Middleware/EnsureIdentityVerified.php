<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIdentityVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Admins & clients bypass verification check
        if ($user->isAdmin() || $user->isClient()) {
            return $next($request);
        }

        $verification = $user->identityVerification;

        // Not submitted yet → send to upload page
        if (!$verification) {
            return redirect()->route('verification.upload')
                ->with('warning', app()->getLocale() === 'ar'
                    ? 'يرجى رفع وثائق هويتك للمتابعة'
                    : 'Please upload your identity documents to continue');
        }

        // Submitted but still pending → show waiting page
        if ($verification->status === 'pending') {
            return redirect()->route('verification.pending');
        }

        // Rejected → send back to upload form to resubmit
        if ($verification->status === 'rejected') {
            return redirect()->route('verification.upload')
                ->with('error', app()->getLocale() === 'ar'
                    ? 'تم رفض وثائقك. يرجى إعادة الرفع'
                    : 'Your documents were rejected. Please resubmit');
        }

        return $next($request);
    }
}


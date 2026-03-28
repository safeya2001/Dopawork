<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        // Priority: URL param > session > user preference > default (ar)
        $locale = 'ar'; // Default for Jordan

        if ($request->has('lang') && in_array($request->lang, ['ar', 'en'])) {
            $locale = $request->lang;
            session(['locale' => $locale]);
        } elseif (session('locale') && in_array(session('locale'), ['ar', 'en'])) {
            $locale = session('locale');
        } elseif (auth()->check() && auth()->user()->locale) {
            $locale = auth()->user()->locale;
        }

        app()->setLocale($locale);

        return $next($request);
    }
}

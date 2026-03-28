<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $locale    = app()->getLocale();
        $isAr      = $locale === 'ar';
        $siteName  = $isAr ? config('platform.name_ar','دوبا وورك') : config('app.name','Dopa Work');
        $pageTitle = trim($__env->yieldContent('title')) ?: $siteName;
        $fullTitle = $pageTitle . ' | ' . $siteName;
        $metaDesc  = $__env->yieldContent('meta_description') ?: ($isAr
            ? 'منصة دوبا وورك للعمل الحر – تواصل مع أفضل المستقلين في الوطن العربي لتنفيذ مشاريعك بسرعة وأمان.'
            : 'Dopa Work – the leading Arabic freelance marketplace. Hire top freelancers or find your next job with secure escrow payments.');
        $ogImage   = $__env->yieldContent('og_image') ?: asset('og-image.png');
        $canonical = $__env->yieldContent('canonical') ?: url()->current();
        $altLocale = $isAr ? 'en' : 'ar';
        $altUrl    = url()->current(); // same URL, locale toggled via session
    @endphp

    <title>{{ $fullTitle }}</title>

    {{-- Favicon (inline data URI – no file dependency) --}}
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'><rect width='64' height='64' rx='16' fill='%23ea580c'/><text x='50%25' y='50%25' dominant-baseline='central' text-anchor='middle' font-family='Arial,sans-serif' font-weight='900' font-size='42' fill='white'>d</text></svg>">

    {{-- SEO --}}
    <meta name="description"        content="{{ $metaDesc }}">
    <meta name="robots"             content="@yield('robots', 'index, follow')">
    <link  rel="canonical"          href="{{ $canonical }}">
    <link  rel="alternate"          hreflang="{{ $locale }}"    href="{{ $canonical }}">
    <link  rel="alternate"          hreflang="{{ $altLocale }}" href="{{ $altUrl }}">
    <link  rel="alternate"          hreflang="x-default"        href="{{ $altUrl }}">

    {{-- Open Graph --}}
    <meta property="og:type"        content="website">
    <meta property="og:locale"      content="{{ $isAr ? 'ar_JO' : 'en_US' }}">
    <meta property="og:site_name"   content="{{ $siteName }}">
    <meta property="og:title"       content="{{ $fullTitle }}">
    <meta property="og:description" content="{{ $metaDesc }}">
    <meta property="og:url"         content="{{ $canonical }}">
    <meta property="og:image"       content="{{ $ogImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height"content="630">

    {{-- Twitter Card --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="{{ $fullTitle }}">
    <meta name="twitter:description" content="{{ $metaDesc }}">
    <meta name="twitter:image"       content="{{ $ogImage }}">

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">

    @if($isAr)
    <style>body { font-family: 'Cairo', sans-serif; } .rtl-flip { transform: scaleX(-1); }</style>
    @else
    <style>body { font-family: 'Inter', sans-serif; }</style>
    @endif

    <style>
        [dir="rtl"] .space-x-4 > :not([hidden]) ~ :not([hidden]) { --tw-space-x-reverse: 1; }
        [dir="rtl"] .ms-auto { margin-right: auto; margin-left: 0; }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-900 {{ $isAr ? 'text-right' : 'text-left' }}">

{{-- Navigation --}}
@include('layouts.nav')

{{-- Flash Messages --}}
@if(session('success'))
    <div class="max-w-7xl mx-auto px-4 mt-4">
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    </div>
@endif

@if(session('warning'))
    <div class="max-w-7xl mx-auto px-4 mt-4">
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg p-4">
            {{ session('warning') }}
        </div>
    </div>
@endif

@if($errors->any())
    <div class="max-w-7xl mx-auto px-4 mt-4">
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-4">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

{{-- Main Content --}}
<main class="min-h-screen pb-20 md:pb-0">
    @yield('content')
</main>

{{-- Footer (desktop only) --}}
<div class="hidden md:block">
    @include('layouts.footer')
</div>

{{-- ===== MOBILE BOTTOM NAV (Fiverr style) ===== --}}
@auth
<nav class="fixed bottom-0 left-0 right-0 z-50 md:hidden bg-white border-t border-gray-200 safe-area-pb">
    <div class="flex items-center justify-around h-16 px-2">

        {{-- Home --}}
        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : (auth()->user()->isFreelancer() ? route('freelancer.dashboard') : route('client.dashboard')) }}"
           class="bottom-nav-item {{ request()->routeIs('client.dashboard','freelancer.dashboard','admin.dashboard') ? 'active' : '' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>{{ app()->getLocale()==='ar' ? 'الرئيسية' : 'Home' }}</span>
        </a>

        {{-- Messages --}}
        <a href="{{ route('messages.index') }}"
           class="bottom-nav-item {{ request()->routeIs('messages.*') ? 'active' : '' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <span>{{ app()->getLocale()==='ar' ? 'رسائل' : 'Inbox' }}</span>
        </a>

        {{-- Search (center - prominent) --}}
        <a href="{{ route('services.index') }}"
           class="flex flex-col items-center -mt-5">
            <div class="w-14 h-14 bg-primary-600 rounded-full flex items-center justify-center shadow-lg shadow-primary-200">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <span class="text-xs text-primary-600 font-semibold mt-1">{{ app()->getLocale()==='ar' ? 'بحث' : 'Search' }}</span>
        </a>

        {{-- Orders --}}
        @if(auth()->user()->isFreelancer())
            <a href="{{ route('freelancer.orders.index') }}"
               class="bottom-nav-item {{ request()->routeIs('freelancer.orders.*') ? 'active' : '' }}">
        @elseif(auth()->user()->isAdmin())
            <a href="{{ route('admin.orders.index') }}"
               class="bottom-nav-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
        @else
            <a href="{{ route('client.orders.index') }}"
               class="bottom-nav-item {{ request()->routeIs('client.orders.*') ? 'active' : '' }}">
        @endif
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span>{{ app()->getLocale()==='ar' ? 'طلبات' : 'Orders' }}</span>
        </a>

        {{-- Profile --}}
        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : (auth()->user()->isFreelancer() ? route('freelancer.dashboard') : route('client.dashboard')) }}"
           class="bottom-nav-item">
            <img src="{{ auth()->user()->avatar ? Storage::url(auth()->user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&color=3b82f6&background=dbeafe&size=40' }}"
                 class="w-7 h-7 rounded-full border-2 {{ request()->routeIs('*.dashboard') ? 'border-primary-500' : 'border-gray-200' }}" alt="">
            <span class="{{ request()->routeIs('*.dashboard') ? 'text-primary-600' : 'text-gray-500' }}">
                {{ app()->getLocale()==='ar' ? 'حسابي' : 'Profile' }}
            </span>
        </a>

    </div>
</nav>
@else
{{-- Guest bottom nav --}}
<nav class="fixed bottom-0 left-0 right-0 z-50 md:hidden bg-white border-t border-gray-200">
    <div class="flex items-center justify-around h-16 px-2">
        <a href="{{ route('home') }}" class="bottom-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>{{ app()->getLocale()==='ar' ? 'الرئيسية' : 'Home' }}</span>
        </a>
        <a href="{{ route('services.index') }}" class="flex flex-col items-center -mt-5">
            <div class="w-14 h-14 bg-primary-600 rounded-full flex items-center justify-center shadow-lg shadow-primary-200">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <span class="text-xs text-primary-600 font-semibold mt-1">{{ app()->getLocale()==='ar' ? 'بحث' : 'Search' }}</span>
        </a>
        <a href="{{ route('login') }}" class="bottom-nav-item {{ request()->routeIs('login') ? 'active' : '' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span>{{ app()->getLocale()==='ar' ? 'دخول' : 'Sign In' }}</span>
        </a>
    </div>
</nav>
@endauth

<style>
.bottom-nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
    color: #6b7280;
    font-size: 0.65rem;
    font-weight: 500;
    padding: 4px 8px;
    border-radius: 8px;
    transition: color 0.15s;
    text-decoration: none;
    min-width: 48px;
}
.bottom-nav-item:hover { color: #2563eb; }
.bottom-nav-item.active { color: #2563eb; }
.bottom-nav-item svg { flex-shrink: 0; }
.safe-area-pb { padding-bottom: env(safe-area-inset-bottom, 0px); }
</style>

@stack('scripts')
</body>
</html>

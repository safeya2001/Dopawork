?@extends('layouts.app')
@section('title', app()->getLocale()==='ar' ? 'الرئيسية' : 'Home')

@section('content')
@php $isAr = app()->getLocale() === 'ar'; @endphp

{{-- ══════════════════════════════════════════
  HERO — Dark premium gradient
══════════════════════════════════════════ --}}
<section class="relative overflow-hidden" style="background:linear-gradient(135deg,#0c0a09 0%,#1c0f07 45%,#431407 80%,#7c2d12 100%);min-height:300px;">

    {{-- Animated glow blobs --}}
    <div class="absolute -top-24 {{ $isAr ? 'left-0' : 'right-0' }} w-[500px] h-[500px] rounded-full pointer-events-none animate-float-blob"
         style="background:radial-gradient(circle,rgba(249,115,22,0.16) 0%,transparent 65%);"></div>
    <div class="absolute -bottom-20 {{ $isAr ? 'right-0' : 'left-0' }} w-[350px] h-[350px] rounded-full pointer-events-none"
         style="background:radial-gradient(circle,rgba(234,88,12,0.12) 0%,transparent 70%);animation:floatBlob 10s ease-in-out infinite 2s;"></div>

    <div class="relative max-w-4xl mx-auto px-4 py-14 text-center">

        {{-- Greeting pill --}}
        <div class="inline-flex items-center gap-2 mb-6 px-4 py-1.5 rounded-full text-xs font-semibold text-orange-200 border animate-fade-in"
             style="background:rgba(249,115,22,0.12);border-color:rgba(249,115,22,0.28);">
            <span>👋</span>
            <span>{{ $isAr ? 'مرحباً، '.$user->name : 'Welcome back, '.$user->name }}</span>
            @if($activeOrdersCount > 0)
                <span class="w-1 h-1 rounded-full bg-orange-400/60"></span>
                <a href="{{ route('client.orders.index') }}" class="text-orange-300 hover:text-orange-200 transition-colors">
                    {{ $activeOrdersCount }} {{ $isAr ? 'طلبات نشطة' : 'active orders' }}
                </a>
            @endif
        </div>

        {{-- Headline --}}
        <h1 class="text-4xl md:text-5xl font-black text-white mb-3 leading-tight tracking-tight animate-fade-in-up">
            {{ $isAr ? 'ايش تبحث عنه' : 'Find the perfect' }}
            <span class="block" style="color:#f97316;">{{ $isAr ? 'اليوم؟' : 'service for you' }}</span>
        </h1>
        <p class="text-white/45 text-sm mb-8 max-w-sm mx-auto animate-fade-in-up animation-delay-100">
            {{ $isAr ? 'أكثر من 500 خدمة من أفضل المستقلين في الوطن العربي' : 'Browse 500+ services from top-rated freelancers' }}
        </p>

        {{-- Search bar --}}
        <form action="{{ route('services.index') }}" method="GET"
              class="max-w-2xl mx-auto animate-fade-in-up animation-delay-200">
            <div class="flex items-center bg-white rounded-2xl overflow-hidden shadow-2xl ring-1 ring-white/10">
                <svg class="w-5 h-5 text-gray-300 {{ $isAr ? 'mr-4 ml-2' : 'ml-4 mr-2' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="q"
                       placeholder="{{ $isAr ? 'ابحث: تصميم شعار، كتابة محتوى، برمجة...' : 'Search: logo design, content writing, coding...' }}"
                       class="flex-1 px-2 py-4 text-gray-800 text-sm bg-transparent outline-none {{ $isAr ? 'text-right' : 'text-left' }}"
                       style="box-shadow:none;">
                <button type="submit"
                        class="m-1.5 px-7 py-2.5 text-white text-sm font-bold rounded-xl shrink-0 transition-all hover:scale-105 active:scale-95"
                        style="background:linear-gradient(135deg,#f97316,#ea580c);box-shadow:0 4px 14px rgba(234,88,12,0.4);">
                    {{ $isAr ? 'ابحث' : 'Search' }}
                </button>
            </div>
        </form>

        {{-- Trust signals --}}
        <div class="flex items-center justify-center gap-6 mt-7 flex-wrap animate-fade-in animation-delay-300">
            @foreach(
                $isAr
                    ? ['✓ دفع آمن عبر الضمان','✓ جودة مضمونة','✓ +200 مستقل موثّق']
                    : ['✓ Secure Escrow','✓ Quality Guaranteed','✓ 200+ Verified Freelancers']
            as $t)
                <span class="text-white/35 text-xs font-medium tracking-wide">{{ $t }}</span>
            @endforeach
        </div>
    </div>
</section>

{{-- Active orders strip --}}
@if($activeOrders->isNotEmpty())
<div style="background:#fffbeb;border-bottom:1px solid #fde68a;">
    <div class="max-w-6xl mx-auto px-4 py-2.5">
        <div class="flex items-center gap-3 overflow-x-auto scrollbar-hide">
            <span class="text-xs font-bold text-amber-700 whitespace-nowrap shrink-0 flex items-center gap-1.5">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                </svg>
                {{ $isAr ? 'جارٍ التنفيذ:' : 'In Progress:' }}
            </span>
            @foreach($activeOrders as $order)
            <a href="{{ route('client.orders.show', $order) }}"
               class="shrink-0 flex items-center gap-2 bg-white border border-amber-200 rounded-xl px-3 py-1.5 text-xs hover:border-amber-400 hover:shadow-sm transition-all">
                <span class="w-1.5 h-1.5 rounded-full {{ $order->status === 'delivered' ? 'bg-green-400' : 'bg-orange-400' }}"></span>
                <span class="font-semibold text-gray-800 max-w-[112px] truncate">{{ $order->service?->title }}</span>
                <span class="text-gray-300">·</span>
                <span class="text-gray-500 truncate max-w-[80px]">{{ $order->freelancer?->name }}</span>
            </a>
            @endforeach
            @if($activeOrdersCount > 3)
            <a href="{{ route('client.orders.index') }}" class="shrink-0 text-xs font-bold text-orange-600 whitespace-nowrap hover:underline">
                +{{ $activeOrdersCount - 3 }} {{ $isAr ? 'أكثر' : 'more' }} →
            </a>
            @endif
        </div>
    </div>
</div>
@endif

{{-- ══════════════════════════════════════════
  MAIN CONTENT
══════════════════════════════════════════ --}}
<div class="max-w-6xl mx-auto px-4 py-10 space-y-14">

    {{-- CATEGORIES --}}
    @if($categories->isNotEmpty())
    <section class="animate-fade-in-up">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-xl font-black text-gray-900">{{ $isAr ? 'تصفح حسب الفئة' : 'Browse by Category' }}</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ $isAr ? 'اختر ما يناسبك' : 'Pick what you need' }}</p>
            </div>
            <a href="{{ route('services.index') }}"
               class="flex items-center gap-1 text-sm font-semibold text-orange-600 hover:text-orange-700 transition-colors">
                {{ $isAr ? 'عرض الكل' : 'See All' }}
                <svg class="w-4 h-4 {{ $isAr ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
        <div class="flex gap-3 overflow-x-auto pb-2 -mx-4 px-4 scrollbar-hide">
            @foreach($categories as $cat)
            <a href="{{ route('services.index', ['category' => $cat->slug]) }}"
               class="shrink-0 flex flex-col items-center gap-2.5 bg-white border-2 border-gray-100 rounded-2xl px-5 py-4 hover:border-orange-300 hover:bg-orange-50 hover:shadow-lg transition-all group text-center min-w-[90px]"
               style="transition:all 0.2s ease;">
                <span class="text-3xl group-hover:scale-110 transition-transform duration-200 leading-none">{{ $cat->icon ?? '💼' }}</span>
                <span class="text-xs font-semibold text-gray-600 group-hover:text-orange-700 leading-tight transition-colors">
                    {{ $isAr ? ($cat->name_ar ?? $cat->name) : $cat->name }}
                </span>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- FEATURED SERVICES --}}
    @if($featuredServices->isNotEmpty())
    <section class="animate-fade-in-up animation-delay-100">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-2xl flex items-center justify-center text-lg shadow-sm" style="background:linear-gradient(135deg,#fef9c3,#fef08a);">⭐</div>
                <div>
                    <h2 class="text-xl font-black text-gray-900">{{ $isAr ? 'خدمات مميزة' : 'Featured Services' }}</h2>
                    <p class="text-xs text-gray-400">{{ $isAr ? 'اختيار المنصة' : "Platform's top picks" }}</p>
                </div>
            </div>
            <a href="{{ route('services.index', ['sort' => 'rating']) }}"
               class="flex items-center gap-1 text-sm font-semibold text-orange-600 hover:text-orange-700 transition-colors">
                {{ $isAr ? 'عرض الكل' : 'See All' }}
                <svg class="w-4 h-4 {{ $isAr ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($featuredServices as $service)
                @include('components.service-card', ['service' => $service])
            @endforeach
        </div>
    </section>
    @endif

    {{-- NEW SERVICES --}}
    @if($newServices->isNotEmpty())
    <section class="animate-fade-in-up animation-delay-200">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-2xl flex items-center justify-center text-lg shadow-sm" style="background:linear-gradient(135deg,#dcfce7,#bbf7d0);">✨</div>
                <div>
                    <h2 class="text-xl font-black text-gray-900">{{ $isAr ? 'خدمات جديدة' : 'New Arrivals' }}</h2>
                    <p class="text-xs text-gray-400">{{ $isAr ? 'أحدث الخدمات المضافة' : 'Recently added' }}</p>
                </div>
            </div>
            <a href="{{ route('services.index', ['sort' => 'newest']) }}"
               class="flex items-center gap-1 text-sm font-semibold text-orange-600 hover:text-orange-700 transition-colors">
                {{ $isAr ? 'عرض الكل' : 'See All' }}
                <svg class="w-4 h-4 {{ $isAr ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($newServices as $service)
                @include('components.service-card', ['service' => $service])
            @endforeach
        </div>
    </section>
    @endif

    {{-- EMPTY STATE --}}
    @if($featuredServices->isEmpty() && $newServices->isEmpty())
    <div class="text-center py-24 animate-fade-in-up">
        <div class="w-24 h-24 mx-auto mb-6 rounded-3xl flex items-center justify-center text-5xl"
             style="background:linear-gradient(135deg,#fff7ed,#fed7aa);">🚀</div>
        <h3 class="text-2xl font-black text-gray-900 mb-2">
            {{ $isAr ? 'قريباً ستجد خدمات رائعة!' : 'Amazing services coming soon!' }}
        </h3>
        <p class="text-gray-400 text-sm mb-8 max-w-xs mx-auto leading-relaxed">
            {{ $isAr ? 'المنصة تنمو. ترقّب إضافة خدمات جديدة.' : 'The platform is growing. Stay tuned!' }}
        </p>
        <a href="{{ route('freelancers.index') }}"
           class="inline-flex items-center gap-2 text-white font-bold px-8 py-3.5 rounded-2xl text-sm transition-all hover:-translate-y-1"
           style="background:linear-gradient(135deg,#f97316,#ea580c);box-shadow:0 8px 24px rgba(234,88,12,0.35);">
            {{ $isAr ? 'تصفح المستقلين' : 'Browse Freelancers' }}
            <svg class="w-4 h-4 {{ $isAr ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
    @endif

    {{-- QUICK ACCESS --}}
    <section class="animate-fade-in-up animation-delay-300">
        <h2 class="text-xl font-black text-gray-900 mb-5">{{ $isAr ? 'اختصارات سريعة' : 'Quick Access' }}</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach([
                [
                    'route'    => 'client.orders.index',
                    'icon'     => '📋',
                    'bg'       => 'linear-gradient(135deg,#fff7ed,#ffedd5)',
                    'border'   => '#fed7aa',
                    'ar'       => 'طلباتي',
                    'en'       => 'My Orders',
                    'desc_ar'  => 'تابع طلباتك',
                    'desc_en'  => 'Track orders',
                ],
                [
                    'route'    => 'messages.index',
                    'icon'     => '💬',
                    'bg'       => 'linear-gradient(135deg,#f0fdf4,#dcfce7)',
                    'border'   => '#86efac',
                    'ar'       => 'رسائلي',
                    'en'       => 'Messages',
                    'desc_ar'  => 'تواصل مع المستقلين',
                    'desc_en'  => 'Chat freely',
                ],
                [
                    'route'    => 'services.index',
                    'icon'     => '🔍',
                    'bg'       => 'linear-gradient(135deg,#fefce8,#fef9c3)',
                    'border'   => '#fde047',
                    'ar'       => 'تصفح الخدمات',
                    'en'       => 'Browse',
                    'desc_ar'  => 'استكشف الخدمات',
                    'desc_en'  => 'Explore services',
                ],
                [
                    'route'    => 'freelancers.index',
                    'icon'     => '👥',
                    'bg'       => 'linear-gradient(135deg,#faf5ff,#ede9fe)',
                    'border'   => '#c4b5fd',
                    'ar'       => 'المستقلون',
                    'en'       => 'Freelancers',
                    'desc_ar'  => 'تعرف على المواهب',
                    'desc_en'  => 'Find talent',
                ],
            ] as $link)
            <a href="{{ route($link['route']) }}"
               class="flex items-center gap-3.5 border-2 rounded-2xl p-4 hover:shadow-lg transition-all group"
               style="background:{{ $link['bg'] }};border-color:{{ $link['border'] }};">
                <span class="w-11 h-11 rounded-xl flex items-center justify-center text-2xl bg-white shadow-sm shrink-0 group-hover:scale-110 transition-transform duration-200">
                    {{ $link['icon'] }}
                </span>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-gray-900 leading-tight">{{ $isAr ? $link['ar'] : $link['en'] }}</p>
                    <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $isAr ? $link['desc_ar'] : $link['desc_en'] }}</p>
                </div>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-gray-500 transition-colors shrink-0 {{ $isAr ? 'rotate-180' : '' }}"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            @endforeach
        </div>
    </section>

</div>

{{-- Bottom spacer --}}
<div class="h-10"></div>
@endsection

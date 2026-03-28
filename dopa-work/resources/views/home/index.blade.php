@extends('layouts.app')
@section('title', app()->getLocale() === 'ar' ? 'الرئيسية - منصة العمل الحر الأردنية' : 'Home - Jordan\'s Freelancing Platform')

@section('content')

{{-- Hero Section --}}
<section class="relative bg-gradient-to-br from-primary-700 via-primary-600 to-blue-500 text-white overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 {{ app()->getLocale() === 'ar' ? 'left-10' : 'right-10' }} w-72 h-72 bg-white rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 {{ app()->getLocale() === 'ar' ? 'right-0' : 'left-0' }} w-96 h-96 bg-blue-300 rounded-full blur-3xl"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 py-24 text-center">
        <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-1.5 rounded-full text-sm font-medium mb-6">
            🇯🇴 {{ app()->getLocale() === 'ar' ? 'منصة الأردن للعمل الحر' : "Jordan's #1 Freelancing Platform" }}
        </div>
        <h1 class="text-5xl md:text-6xl font-bold leading-tight mb-6 max-w-4xl mx-auto">
            @if(app()->getLocale() === 'ar')
                اعثر على أفضل<br><span class="text-yellow-300">المستقلين الأردنيين</span>
            @else
                Find the Best<br><span class="text-yellow-300">Jordanian Freelancers</span>
            @endif
        </h1>
        <p class="text-xl text-blue-100 mb-10 max-w-2xl mx-auto">
            {{ app()->getLocale() === 'ar'
                ? 'منصة آمنة ومضمونة تجمع أصحاب الأعمال بالمستقلين المحترفين في الأردن. دفع مؤمّن بالدينار الأردني.'
                : 'A secure platform connecting business owners with professional freelancers in Jordan. Payments in JOD, protected by escrow.' }}
        </p>

        {{-- Search Bar --}}
        <form action="{{ route('services.index') }}" method="GET" class="max-w-2xl mx-auto mb-8">
            <div class="flex gap-2 bg-white rounded-2xl p-2 shadow-2xl">
                <input type="text" name="q"
                    placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث عن خدمة مثل: تصميم موقع، كتابة محتوى...' : 'Search for a service like: web design, content writing...' }}"
                    class="flex-1 px-4 py-3 text-gray-800 bg-transparent outline-none text-sm {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-xl font-medium text-sm transition-colors whitespace-nowrap">
                    {{ app()->getLocale() === 'ar' ? '🔍 ابحث' : '🔍 Search' }}
                </button>
            </div>
        </form>

        {{-- Popular Searches --}}
        <div class="flex flex-wrap items-center justify-center gap-2">
            <span class="text-sm text-blue-200">{{ app()->getLocale() === 'ar' ? 'الأكثر طلباً:' : 'Popular:' }}</span>
            @foreach(($popularCategories ?? []) as $cat)
                <a href="{{ route('services.index', ['category' => $cat->slug]) }}"
                   class="bg-white/15 hover:bg-white/25 px-3 py-1 rounded-full text-sm transition-colors">
                    {{ $cat->display_name }}
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- Stats Bar --}}
<section class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 py-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <div>
                <p class="text-3xl font-bold text-primary-700">{{ number_format($stats['freelancers'] ?? 0) }}+</p>
                <p class="text-sm text-gray-500 mt-1">{{ app()->getLocale() === 'ar' ? 'مستقل محترف' : 'Professional Freelancers' }}</p>
            </div>
            <div>
                <p class="text-3xl font-bold text-primary-700">{{ number_format($stats['services'] ?? 0) }}+</p>
                <p class="text-sm text-gray-500 mt-1">{{ app()->getLocale() === 'ar' ? 'خدمة متاحة' : 'Available Services' }}</p>
            </div>
            <div>
                <p class="text-3xl font-bold text-primary-700">{{ number_format($stats['orders'] ?? 0) }}+</p>
                <p class="text-sm text-gray-500 mt-1">{{ app()->getLocale() === 'ar' ? 'طلب مكتمل' : 'Completed Orders' }}</p>
            </div>
            <div>
                <p class="text-3xl font-bold text-green-600">100%</p>
                <p class="text-sm text-gray-500 mt-1">{{ app()->getLocale() === 'ar' ? 'دفع مؤمّن بالضمان' : 'Escrow-Protected Payments' }}</p>
            </div>
        </div>
    </div>
</section>

{{-- Categories Section --}}
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold text-gray-900 mb-3">
                {{ app()->getLocale() === 'ar' ? 'تصفح حسب التخصص' : 'Browse by Category' }}
            </h2>
            <p class="text-gray-500">{{ app()->getLocale() === 'ar' ? 'اكتشف آلاف الخدمات من مستقلين أردنيين محترفين' : 'Discover thousands of services from professional Jordanian freelancers' }}</p>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($categories ?? [] as $category)
            <a href="{{ route('services.index', ['category' => $category->slug]) }}"
               class="group bg-white rounded-2xl p-5 text-center hover:shadow-md hover:border-primary-200 border border-gray-100 transition-all duration-200">
                <div class="text-4xl mb-3">{{ $category->icon ?? '💼' }}</div>
                <p class="text-sm font-semibold text-gray-800 group-hover:text-primary-600 transition-colors">
                    {{ $category->display_name }}
                </p>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- Featured Services --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">
                    {{ app()->getLocale() === 'ar' ? 'خدمات مميزة' : 'Featured Services' }}
                </h2>
                <p class="text-gray-500">{{ app()->getLocale() === 'ar' ? 'خدمات منتقاة بعناية لأعلى جودة' : 'Carefully selected services for the highest quality' }}</p>
            </div>
            <a href="{{ route('services.index') }}" class="text-primary-600 hover:text-primary-700 font-medium text-sm flex items-center gap-1">
                {{ app()->getLocale() === 'ar' ? 'عرض الكل ←' : 'View All →' }}
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredServices ?? [] as $service)
                @include('components.service-card', compact('service'))
            @endforeach
        </div>
    </div>
</section>

{{-- How It Works --}}
<section class="py-16 bg-primary-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-3">
                {{ app()->getLocale() === 'ar' ? 'كيف يعمل دوبا وورك؟' : 'How Does Dopa Work?' }}
            </h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach([
                ['icon' => '🔍', 'step' => '1', 'en' => 'Browse & Choose', 'ar' => 'تصفح واختر', 'en_desc' => 'Search for the service you need and choose a verified Jordanian freelancer.', 'ar_desc' => 'ابحث عن الخدمة التي تحتاجها واختر مستقلاً أردنياً موثوقاً.'],
                ['icon' => '🔒', 'step' => '2', 'en' => 'Secure Payment', 'ar' => 'ادفع بأمان', 'en_desc' => 'Pay in JOD — your money is held in escrow until you approve the work.', 'ar_desc' => 'ادفع بالدينار الأردني — أموالك محفوظة بنظام الضمان حتى موافقتك.'],
                ['icon' => '✅', 'step' => '3', 'en' => 'Review & Release', 'ar' => 'راجع وأفرج', 'en_desc' => 'Review the delivered work, approve it, and the payment is released to the freelancer.', 'ar_desc' => 'راجع العمل المسلّم، وافق عليه، وسيتم صرف المبلغ للمستقل.'],
            ] as $step)
            <div class="bg-white rounded-2xl p-8 text-center shadow-sm">
                <div class="text-5xl mb-4">{{ $step['icon'] }}</div>
                <div class="w-8 h-8 bg-primary-100 text-primary-700 rounded-full flex items-center justify-center font-bold text-sm mx-auto mb-3">
                    {{ $step['step'] }}
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">
                    {{ app()->getLocale() === 'ar' ? $step['ar'] : $step['en'] }}
                </h3>
                <p class="text-gray-500 text-sm leading-relaxed">
                    {{ app()->getLocale() === 'ar' ? $step['ar_desc'] : $step['en_desc'] }}
                </p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA Section --}}
<section class="py-20 bg-gradient-to-r from-primary-600 to-blue-700 text-white text-center">
    <div class="max-w-3xl mx-auto px-4">
        <h2 class="text-4xl font-bold mb-4">
            {{ app()->getLocale() === 'ar' ? 'هل أنت مستقل؟ ابدأ الكسب الآن' : 'Are You a Freelancer? Start Earning Today' }}
        </h2>
        <p class="text-blue-100 text-lg mb-8">
            {{ app()->getLocale() === 'ar'
                ? 'سجل كمستقل، أنشئ خدماتك، وابدأ في استقبال الطلبات من عملاء في جميع أنحاء الأردن.'
                : 'Register as a freelancer, create your services, and start receiving orders from clients across Jordan.' }}
        </p>
        <a href="{{ route('register') }}" class="inline-block bg-white text-primary-700 font-bold px-8 py-4 rounded-2xl hover:bg-gray-50 transition-colors text-lg">
            {{ app()->getLocale() === 'ar' ? 'سجل مجاناً كمستقل ←' : 'Register Free as a Freelancer →' }}
        </a>
    </div>
</section>

@endsection

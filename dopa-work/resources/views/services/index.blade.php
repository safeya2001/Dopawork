@extends('layouts.app')
@section('title', app()->getLocale() === 'ar' ? 'تصفح الخدمات' : 'Browse Services')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">

  {{-- Header + Search --}}
  <div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 mb-4">
      {{ app()->getLocale() === 'ar' ? 'تصفح الخدمات' : 'Browse Services' }}
    </h1>
    <form method="GET" action="{{ route('services.index') }}" class="flex gap-2">
      <input type="text" name="q" value="{{ request('q') }}"
        placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث عن خدمة...' : 'Search services...' }}"
        class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-100">
      <button type="submit" class="bg-primary-600 text-white px-5 py-2.5 rounded-xl text-sm font-medium hover:bg-primary-700 transition-colors">
        {{ app()->getLocale() === 'ar' ? 'بحث' : 'Search' }}
      </button>
      @if(request()->hasAny(['q','category','min_price','max_price','sort']))
        <a href="{{ route('services.index') }}" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50">
          {{ app()->getLocale() === 'ar' ? 'مسح' : 'Clear' }}
        </a>
      @endif
    </form>
  </div>

  <div class="flex gap-6">

    {{-- Sidebar Filters --}}
    <aside class="hidden lg:block w-56 flex-shrink-0">
      <form method="GET" action="{{ route('services.index') }}" id="filterForm">
        @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif

        {{-- Categories --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-4 mb-4">
          <h3 class="font-semibold text-gray-900 text-sm mb-3">{{ app()->getLocale() === 'ar' ? 'التصنيفات' : 'Categories' }}</h3>
          <ul class="space-y-1">
            <li>
              <a href="{{ route('services.index', request()->except('category')) }}"
                 class="block text-sm px-2 py-1 rounded-lg {{ !request('category') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                {{ app()->getLocale() === 'ar' ? 'الكل' : 'All' }}
              </a>
            </li>
            @foreach($categories as $cat)
              <li>
                <a href="{{ route('services.index', array_merge(request()->except('category'), ['category' => $cat->slug])) }}"
                   class="block text-sm px-2 py-1 rounded-lg {{ request('category') === $cat->slug ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                  {{ $cat->icon ?? '' }} {{ app()->getLocale() === 'ar' ? $cat->name_ar : $cat->name }}
                </a>
                @if($cat->children->count() && request('category') === $cat->slug)
                  <ul class="{{ app()->getLocale() === 'ar' ? 'mr-3' : 'ml-3' }} mt-1 space-y-1">
                    @foreach($cat->children as $child)
                      <li>
                        <a href="{{ route('services.index', array_merge(request()->except('category'), ['category' => $child->slug])) }}"
                           class="block text-xs px-2 py-1 rounded-lg {{ request('category') === $child->slug ? 'text-primary-700 font-medium' : 'text-gray-500 hover:bg-gray-50' }}">
                          › {{ app()->getLocale() === 'ar' ? $child->name_ar : $child->name }}
                        </a>
                      </li>
                    @endforeach
                  </ul>
                @endif
              </li>
            @endforeach
          </ul>
        </div>

        {{-- Price Range --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-4 mb-4">
          <h3 class="font-semibold text-gray-900 text-sm mb-3">{{ app()->getLocale() === 'ar' ? 'نطاق السعر (JOD)' : 'Price Range (JOD)' }}</h3>
          <div class="flex gap-2 items-center">
            <input type="number" name="min_price" value="{{ request('min_price') }}" min="0"
              placeholder="{{ app()->getLocale() === 'ar' ? 'من' : 'Min' }}"
              class="w-full px-2 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-primary-400">
            <span class="text-gray-400 text-xs">—</span>
            <input type="number" name="max_price" value="{{ request('max_price') }}" min="0"
              placeholder="{{ app()->getLocale() === 'ar' ? 'إلى' : 'Max' }}"
              class="w-full px-2 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-primary-400">
          </div>
          <button type="submit" class="w-full mt-3 bg-primary-600 text-white py-1.5 rounded-lg text-xs font-medium hover:bg-primary-700 transition-colors">
            {{ app()->getLocale() === 'ar' ? 'تطبيق' : 'Apply' }}
          </button>
        </div>

        {{-- Sort --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-4">
          <h3 class="font-semibold text-gray-900 text-sm mb-3">{{ app()->getLocale() === 'ar' ? 'الترتيب' : 'Sort By' }}</h3>
          @foreach([
            ['value'=>'newest','en'=>'Newest','ar'=>'الأحدث'],
            ['value'=>'rating','en'=>'Best Rated','ar'=>'الأعلى تقييماً'],
            ['value'=>'orders','en'=>'Most Orders','ar'=>'الأكثر طلباً'],
            ['value'=>'price_low','en'=>'Price: Low to High','ar'=>'السعر: من الأقل'],
          ] as $s)
            <label class="flex items-center gap-2 py-1 cursor-pointer">
              <input type="radio" name="sort" value="{{ $s['value'] }}"
                {{ request('sort', 'newest') === $s['value'] ? 'checked' : '' }}
                class="text-primary-600" onchange="document.getElementById('filterForm').submit()">
              <span class="text-sm text-gray-700">{{ app()->getLocale() === 'ar' ? $s['ar'] : $s['en'] }}</span>
            </label>
          @endforeach
        </div>
      </form>
    </aside>

    {{-- Services Grid --}}
    <div class="flex-1 min-w-0">

      {{-- Result count --}}
      <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-gray-500">
          {{ app()->getLocale() === 'ar'
            ? "عرض {$services->total()} خدمة"
            : "{$services->total()} services found" }}
        </p>
        {{-- Mobile sort --}}
        <select onchange="window.location.href=this.value" class="lg:hidden text-sm border border-gray-200 rounded-lg px-2 py-1.5">
          @foreach([['value'=>'newest','en'=>'Newest','ar'=>'الأحدث'],['value'=>'rating','en'=>'Top Rated','ar'=>'الأعلى'],['value'=>'orders','en'=>'Popular','ar'=>'الأشهر']] as $s)
            <option value="{{ route('services.index', array_merge(request()->all(), ['sort' => $s['value']])) }}" {{ request('sort','newest')===$s['value']?'selected':'' }}>
              {{ app()->getLocale()==='ar'?$s['ar']:$s['en'] }}
            </option>
          @endforeach
        </select>
      </div>

      @if($services->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
          <p class="text-5xl mb-4">🔍</p>
          <h3 class="text-lg font-semibold text-gray-700 mb-2">
            {{ app()->getLocale() === 'ar' ? 'لا توجد خدمات' : 'No services found' }}
          </h3>
          <p class="text-gray-400 text-sm">
            {{ app()->getLocale() === 'ar' ? 'جرب تغيير معايير البحث أو تصفح تصنيف مختلف.' : 'Try adjusting your filters or browse a different category.' }}
          </p>
          <a href="{{ route('services.index') }}" class="inline-block mt-4 text-primary-600 text-sm hover:underline">
            {{ app()->getLocale() === 'ar' ? 'عرض كل الخدمات' : 'View all services' }}
          </a>
        </div>
      @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
          @foreach($services as $service)
            @include('components.service-card', compact('service'))
          @endforeach
        </div>

        <div class="mt-8">
          {{ $services->links() }}
        </div>
      @endif
    </div>
  </div>
</div>
@endsection

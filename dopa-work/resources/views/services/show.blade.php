@extends('layouts.app')
@section('title', $service->display_title)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">

  {{-- Breadcrumb --}}
  <nav class="text-sm text-gray-400 mb-5 flex items-center gap-2">
    <a href="{{ route('home') }}" class="hover:text-primary-600">{{ app()->getLocale()==='ar'?'الرئيسية':'Home' }}</a>
    <span>/</span>
    <a href="{{ route('services.index') }}" class="hover:text-primary-600">{{ app()->getLocale()==='ar'?'الخدمات':'Services' }}</a>
    <span>/</span>
    <span class="text-gray-600 truncate max-w-xs">{{ $service->display_title }}</span>
  </nav>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- Main Content --}}
    <div class="lg:col-span-2">

      {{-- Title --}}
      <h1 class="text-2xl font-bold text-gray-900 mb-4">{{ $service->display_title }}</h1>

      {{-- Seller mini-info --}}
      <div class="flex items-center gap-3 mb-6">
        <img src="{{ $service->user->avatar ? Storage::url($service->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($service->user->name).'&color=3b82f6&background=dbeafe&size=40' }}"
             class="w-10 h-10 rounded-full" alt="">
        <div>
          <a href="{{ route('freelancers.show', $service->user) }}" class="font-semibold text-gray-900 hover:text-primary-600 text-sm">
            {{ $service->user->name }}
          </a>
          @if($service->user->freelancerProfile)
            <p class="text-xs text-gray-500">{{ $service->user->freelancerProfile->display_title }}</p>
          @endif
        </div>
        @if($service->reviews_count > 0)
          <div class="flex items-center gap-1 {{ app()->getLocale()==='ar'?'mr-auto':'ml-auto' }}">
            <span class="text-yellow-400">⭐</span>
            <span class="font-semibold text-sm">{{ number_format($service->rating,1) }}</span>
            <span class="text-gray-400 text-sm">({{ $service->reviews_count }})</span>
          </div>
        @endif
      </div>

      {{-- Gallery/Thumbnail --}}
      <div class="rounded-2xl overflow-hidden bg-gray-100 mb-6 h-72">
        @if($service->cover_image)
          <img src="{{ Storage::url($service->cover_image) }}" class="w-full h-full object-cover" alt="{{ $service->display_title }}">
        @else
          <div class="w-full h-full flex items-center justify-center text-7xl">
            {{ $service->category->icon ?? '💼' }}
          </div>
        @endif
      </div>

      {{-- Gallery thumbnails --}}
      @if($service->gallery && count($service->gallery))
        <div class="flex gap-2 mb-6 overflow-x-auto pb-1">
          @foreach($service->gallery as $img)
            <img src="{{ Storage::url($img) }}" class="w-20 h-20 rounded-xl object-cover flex-shrink-0 border-2 border-transparent hover:border-primary-400 cursor-pointer" alt="">
          @endforeach
        </div>
      @endif

      {{-- Description --}}
      <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6">
        <h2 class="font-semibold text-gray-900 mb-3">
          {{ app()->getLocale()==='ar'?'وصف الخدمة':'About This Service' }}
        </h2>
        <div class="text-gray-600 text-sm leading-relaxed whitespace-pre-line">
          {{ app()->getLocale()==='ar' && $service->description_ar ? $service->description_ar : $service->description }}
        </div>
      </div>

      {{-- Reviews --}}
      @if($service->reviews->count())
        <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6">
          <h2 class="font-semibold text-gray-900 mb-4">
            {{ app()->getLocale()==='ar'?'التقييمات':'Reviews' }}
            <span class="text-gray-400 font-normal text-sm">({{ $service->reviews_count }})</span>
          </h2>
          <div class="space-y-4">
            @foreach($service->reviews->take(5) as $review)
              <div class="border-b border-gray-50 pb-4 last:border-0 last:pb-0">
                <div class="flex items-center gap-2 mb-2">
                  <img src="https://ui-avatars.com/api/?name={{ urlencode($review->reviewer->name) }}&color=3b82f6&background=dbeafe&size=32"
                       class="w-8 h-8 rounded-full" alt="">
                  <div>
                    <p class="text-sm font-medium text-gray-900">{{ $review->reviewer->name }}</p>
                    <div class="flex">
                      @for($i=1;$i<=5;$i++)
                        <span class="{{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }} text-xs">★</span>
                      @endfor
                    </div>
                  </div>
                  <span class="{{ app()->getLocale()==='ar'?'mr-auto':'ml-auto' }} text-xs text-gray-400">{{ $review->created_at->format('Y/m/d') }}</span>
                </div>
                @if($review->comment)
                  <p class="text-sm text-gray-600">{{ app()->getLocale()==='ar' && $review->comment_ar ? $review->comment_ar : $review->comment }}</p>
                @endif
              </div>
            @endforeach
          </div>
        </div>
      @endif

      {{-- Related Services --}}
      @if($relatedServices->count())
        <div>
          <h2 class="font-semibold text-gray-900 mb-4">
            {{ app()->getLocale()==='ar'?'خدمات مشابهة':'Related Services' }}
          </h2>
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach($relatedServices as $related)
              @include('components.service-card', ['service' => $related])
            @endforeach
          </div>
        </div>
      @endif
    </div>

    {{-- Sticky Packages Sidebar --}}
    <div class="lg:col-span-1">
      <div class="sticky top-20">

        {{-- Package Tabs --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
          {{-- Tab headers --}}
          <div class="flex border-b border-gray-100" x-data="{ tab: '{{ $service->packages->first()?->type ?? 'basic' }}' }">
            @foreach($service->packages as $pkg)
              <button @click="tab = '{{ $pkg->type }}'"
                :class="tab === '{{ $pkg->type }}' ? 'bg-primary-50 text-primary-700 border-b-2 border-primary-600 font-semibold' : 'text-gray-500 hover:bg-gray-50'"
                class="flex-1 py-3 text-xs transition-all">
                {{ app()->getLocale()==='ar' ? $pkg->name_ar : $pkg->name }}
              </button>
            @endforeach
          </div>

          @foreach($service->packages as $pkg)
            <div x-show="tab === '{{ $pkg->type }}'" class="p-5">
              <div class="text-2xl font-bold text-primary-700 mb-1">
                {{ number_format($pkg->price, 3) }} <span class="text-sm font-medium text-gray-500">JOD</span>
              </div>
              <p class="text-sm text-gray-600 mb-4">
                {{ app()->getLocale()==='ar' && $pkg->description_ar ? $pkg->description_ar : $pkg->description }}
              </p>
              <ul class="space-y-2 mb-4">
                <li class="flex items-center gap-2 text-sm text-gray-700">
                  <span class="text-green-500">✓</span>
                  {{ $pkg->delivery_days }} {{ app()->getLocale()==='ar'?'يوم تسليم':'days delivery' }}
                </li>
                <li class="flex items-center gap-2 text-sm text-gray-700">
                  <span class="text-green-500">✓</span>
                  {{ $pkg->revisions }} {{ app()->getLocale()==='ar'?'تعديل':'revision(s)' }}
                </li>
                @foreach(($pkg->features ?? []) as $feat)
                  <li class="flex items-center gap-2 text-sm text-gray-700">
                    <span class="text-green-500">✓</span>
                    {{ app()->getLocale()==='ar' && isset(($pkg->features_ar ?? [])[$loop->index]) ? $pkg->features_ar[$loop->index] : $feat }}
                  </li>
                @endforeach
              </ul>
              @auth
                <a href="{{ route('client.checkout', ['service' => $service, 'package' => $pkg->id]) }}"
                   class="block w-full bg-primary-600 hover:bg-primary-700 text-white text-center font-semibold py-3 rounded-xl text-sm transition-colors">
                  {{ app()->getLocale()==='ar'?'اطلب الآن':'Order Now' }}
                </a>
              @else
                <a href="{{ route('login') }}"
                   class="block w-full bg-primary-600 hover:bg-primary-700 text-white text-center font-semibold py-3 rounded-xl text-sm transition-colors">
                  {{ app()->getLocale()==='ar'?'سجل دخول للطلب':'Sign In to Order' }}
                </a>
              @endauth
            </div>
          @endforeach

          @if($service->packages->isEmpty())
            <div class="p-5">
              <p class="text-sm text-gray-400 text-center">{{ app()->getLocale()==='ar'?'لا توجد باقات متاحة':'No packages available' }}</p>
            </div>
          @endif
        </div>

        {{-- Contact Seller --}}
        @auth
          @if(auth()->id() !== $service->user_id)
            <div class="mt-4">
              <form action="{{ route('messages.start', $service) }}" method="POST">
                @csrf
                <button type="submit" class="w-full border border-primary-600 text-primary-600 hover:bg-primary-50 font-medium py-2.5 rounded-xl text-sm transition-colors">
                  💬 {{ app()->getLocale()==='ar'?'تواصل مع البائع':'Contact Seller' }}
                </button>
              </form>
            </div>
          @endif
        @endauth

        {{-- Delivery & Info --}}
        <div class="mt-4 bg-gray-50 rounded-2xl p-4 text-sm text-gray-600 space-y-2">
          <div class="flex justify-between">
            <span>{{ app()->getLocale()==='ar'?'مدة التسليم':'Delivery' }}</span>
            <span class="font-medium">{{ $service->delivery_days }} {{ app()->getLocale()==='ar'?'أيام':'days' }}</span>
          </div>
          <div class="flex justify-between">
            <span>{{ app()->getLocale()==='ar'?'الطلبات':'Orders' }}</span>
            <span class="font-medium">{{ $service->orders_count }}</span>
          </div>
          <div class="flex justify-between">
            <span>{{ app()->getLocale()==='ar'?'المشاهدات':'Views' }}</span>
            <span class="font-medium">{{ $service->views }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script src="//unpkg.com/alpinejs" defer></script>
@endpush
@endsection

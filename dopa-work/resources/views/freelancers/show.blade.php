@extends('layouts.app')
@section('title', $user->name)

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- Profile Sidebar --}}
    <div class="lg:col-span-1">
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 text-center sticky top-20">
        <img src="{{ $user->avatar ? Storage::url($user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&color=3b82f6&background=dbeafe&size=120' }}"
             class="w-24 h-24 rounded-full mx-auto mb-3 border-4 border-primary-100" alt="">
        <h1 class="text-xl font-bold text-gray-900">{{ $user->name }}</h1>
        @if($user->freelancerProfile)
          <p class="text-gray-500 text-sm mt-1">{{ $user->freelancerProfile->display_title }}</p>
        @endif

        @if($user->freelancerProfile?->is_verified)
          <span class="inline-flex items-center gap-1 bg-green-50 text-green-700 text-xs font-medium px-3 py-1 rounded-full mt-2">
            ✓ {{ app()->getLocale()==='ar' ? 'موثق' : 'Verified' }}
          </span>
        @endif

        {{-- Stats --}}
        @if($user->freelancerProfile)
          <div class="grid grid-cols-3 gap-3 mt-5 border-t border-gray-50 pt-5">
            <div>
              <p class="text-lg font-bold text-gray-900">{{ $user->freelancerProfile->completed_orders }}</p>
              <p class="text-xs text-gray-400">{{ app()->getLocale()==='ar' ? 'مكتمل' : 'Completed' }}</p>
            </div>
            <div>
              <p class="text-lg font-bold text-gray-900">{{ number_format($user->freelancerProfile->rating, 1) }}</p>
              <p class="text-xs text-gray-400">{{ app()->getLocale()==='ar' ? 'التقييم' : 'Rating' }}</p>
            </div>
            <div>
              <p class="text-lg font-bold text-gray-900">{{ $user->freelancerProfile->member_since ?? date('Y') }}</p>
              <p class="text-xs text-gray-400">{{ app()->getLocale()==='ar' ? 'عضو منذ' : 'Member' }}</p>
            </div>
          </div>
        @endif

        {{-- Bio --}}
        @if($user->freelancerProfile?->overview)
          <p class="text-sm text-gray-600 mt-4 leading-relaxed text-start">
            {{ app()->getLocale()==='ar' && $user->freelancerProfile->overview_ar
               ? $user->freelancerProfile->overview_ar
               : $user->freelancerProfile->overview }}
          </p>
        @endif

        {{-- Skills --}}
        @if($user->freelancerProfile?->skills)
          <div class="flex flex-wrap gap-1.5 mt-4 justify-center">
            @foreach($user->freelancerProfile->skills as $skill)
              <span class="bg-blue-50 text-blue-700 text-xs px-2.5 py-1 rounded-full">{{ $skill }}</span>
            @endforeach
          </div>
        @endif

        {{-- Hourly Rate --}}
        @if($user->freelancerProfile?->hourly_rate)
          <div class="mt-4 pt-4 border-t border-gray-50 text-sm text-gray-600">
            {{ app()->getLocale()==='ar' ? 'السعر بالساعة' : 'Hourly Rate' }}:
            <span class="font-bold text-primary-700">{{ number_format($user->freelancerProfile->hourly_rate, 3) }} JOD</span>
          </div>
        @endif

        @auth
          @if(auth()->id() !== $user->id && $user->services->count())
            <form action="{{ route('messages.start', $user->services->first()) }}" method="POST" class="mt-5">
              @csrf
              <button type="submit" class="w-full border border-primary-600 text-primary-600 hover:bg-primary-50 font-medium py-2.5 rounded-xl text-sm transition-colors">
                💬 {{ app()->getLocale()==='ar' ? 'تواصل' : 'Contact' }}
              </button>
            </form>
          @endif
        @endauth
      </div>
    </div>

    {{-- Services --}}
    <div class="lg:col-span-2">
      <h2 class="text-xl font-bold text-gray-900 mb-5">
        {{ app()->getLocale()==='ar' ? 'خدماتي' : 'My Services' }}
        <span class="text-gray-400 font-normal text-base">({{ $user->services->count() }})</span>
      </h2>

      @if($user->services->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
          <p class="text-4xl mb-3">💼</p>
          <p class="text-gray-400 text-sm">{{ app()->getLocale()==='ar' ? 'لا توجد خدمات نشطة' : 'No active services yet' }}</p>
        </div>
      @else
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
          @foreach($user->services as $service)
            @include('components.service-card', compact('service'))
          @endforeach
        </div>
      @endif

      {{-- Reviews --}}
      @if($user->reviews->count())
        <div class="mt-8">
          <h2 class="text-xl font-bold text-gray-900 mb-5">
            {{ app()->getLocale()==='ar' ? 'التقييمات' : 'Reviews' }}
          </h2>
          <div class="space-y-4">
            @foreach($user->reviews->take(6) as $review)
              <div class="bg-white rounded-2xl border border-gray-100 p-4">
                <div class="flex items-center gap-3 mb-2">
                  <img src="https://ui-avatars.com/api/?name={{ urlencode($review->reviewer->name) }}&size=36&color=3b82f6&background=dbeafe"
                       class="w-9 h-9 rounded-full" alt="">
                  <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-900">{{ $review->reviewer->name }}</p>
                    <div class="flex">
                      @for($i=1;$i<=5;$i++)
                        <span class="{{ $i<=$review->rating?'text-yellow-400':'text-gray-200' }} text-xs">★</span>
                      @endfor
                    </div>
                  </div>
                  <span class="text-xs text-gray-400">{{ $review->created_at->format('Y/m/d') }}</span>
                </div>
                @if($review->comment)
                  <p class="text-sm text-gray-600">{{ app()->getLocale()==='ar' && $review->comment_ar ? $review->comment_ar : $review->comment }}</p>
                @endif
              </div>
            @endforeach
          </div>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection

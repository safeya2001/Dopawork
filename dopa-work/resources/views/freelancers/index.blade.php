@extends('layouts.app')
@section('title', app()->getLocale()==='ar' ? 'المستقلون' : 'Freelancers')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">

  <div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 mb-4">
      {{ app()->getLocale()==='ar' ? 'تصفح المستقلين' : 'Browse Freelancers' }}
    </h1>
    <form method="GET" action="{{ route('freelancers.index') }}" class="flex gap-2">
      <input type="text" name="q" value="{{ request('q') }}"
        placeholder="{{ app()->getLocale()==='ar' ? 'ابحث عن مستقل...' : 'Search freelancers...' }}"
        class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-100">
      <button type="submit" class="bg-primary-600 text-white px-5 py-2.5 rounded-xl text-sm font-medium hover:bg-primary-700 transition-colors">
        {{ app()->getLocale()==='ar' ? 'بحث' : 'Search' }}
      </button>
    </form>
  </div>

  @if($freelancers->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
      <p class="text-5xl mb-4">👤</p>
      <h3 class="text-lg font-semibold text-gray-700">{{ app()->getLocale()==='ar' ? 'لا يوجد مستقلون' : 'No freelancers found' }}</h3>
    </div>
  @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
      @foreach($freelancers as $freelancer)
        <a href="{{ route('freelancers.show', $freelancer) }}"
           class="bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-md transition-all group">
          <div class="text-center mb-4">
            <img src="{{ $freelancer->avatar ? Storage::url($freelancer->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($freelancer->name).'&color=3b82f6&background=dbeafe&size=80' }}"
                 class="w-16 h-16 rounded-full mx-auto mb-3 border-2 border-primary-100" alt="">
            <h3 class="font-semibold text-gray-900 group-hover:text-primary-600 transition-colors text-sm">
              {{ $freelancer->name }}
            </h3>
            @if($freelancer->freelancerProfile)
              <p class="text-xs text-gray-500 mt-0.5">{{ $freelancer->freelancerProfile->display_title }}</p>
              @if($freelancer->freelancerProfile->rating > 0)
                <div class="flex items-center justify-center gap-1 mt-1">
                  <span class="text-yellow-400 text-xs">⭐</span>
                  <span class="text-xs font-semibold text-gray-800">{{ number_format($freelancer->freelancerProfile->rating,1) }}</span>
                  <span class="text-xs text-gray-400">({{ $freelancer->freelancerProfile->total_reviews }})</span>
                </div>
              @endif
            @endif
          </div>

          {{-- Skills --}}
          @if($freelancer->freelancerProfile?->skills)
            <div class="flex flex-wrap gap-1 justify-center mb-3">
              @foreach(array_slice($freelancer->freelancerProfile->skills, 0, 4) as $skill)
                <span class="bg-blue-50 text-blue-700 text-xs px-2 py-0.5 rounded-full">{{ $skill }}</span>
              @endforeach
            </div>
          @endif

          <div class="flex justify-between text-xs text-gray-400 border-t border-gray-50 pt-3 mt-3">
            <span>{{ $freelancer->services->count() }} {{ app()->getLocale()==='ar' ? 'خدمة' : 'services' }}</span>
            @if($freelancer->freelancerProfile?->is_verified)
              <span class="text-green-600 font-medium">✓ {{ app()->getLocale()==='ar' ? 'موثق' : 'Verified' }}</span>
            @endif
          </div>
        </a>
      @endforeach
    </div>

    <div class="mt-8">{{ $freelancers->links() }}</div>
  @endif
</div>
@endsection

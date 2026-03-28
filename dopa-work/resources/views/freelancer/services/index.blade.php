@extends('layouts.app')
@section('title', 'خدماتي')
@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">🛠️ خدماتي</h1>
    <a href="{{ route('freelancer.services.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition-colors">
      + إضافة خدمة
    </a>
  </div>

  @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 mb-5 text-sm">✅ {{ session('success') }}</div>@endif

  @if($services->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
      <p class="text-5xl mb-4">🛠️</p>
      <p class="text-lg font-semibold text-gray-700 mb-2">لا توجد خدمات بعد</p>
      <p class="text-sm text-gray-400 mb-5">أضف خدمتك الأولى وابدأ في استقبال الطلبات</p>
      <a href="{{ route('freelancer.services.create') }}" class="bg-primary-600 text-white px-6 py-2.5 rounded-xl text-sm font-medium hover:bg-primary-700">إضافة خدمة</a>
    </div>
  @else
    <div class="space-y-4">
      @foreach($services as $service)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-5 flex-wrap">
          @if($service->cover_image)
            <img src="{{ Storage::url($service->cover_image) }}" class="w-20 h-16 rounded-xl object-cover shrink-0">
          @else
            <div class="w-20 h-16 rounded-xl bg-gray-100 flex items-center justify-center text-2xl shrink-0">🛠️</div>
          @endif
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap mb-1">
              <h3 class="font-semibold text-gray-900 text-sm">{{ $service->title_ar ?? $service->title }}</h3>
              @include('components.status-badge', ['status'=>$service->status])
            </div>
            <p class="text-xs text-gray-400 mb-2">{{ $service->category?->name_ar }}</p>
            <div class="flex gap-4 text-xs text-gray-500">
              <span>📦 {{ $service->packages->count() }} باقات</span>
              <span>🛒 {{ $service->orders_count ?? 0 }} طلب</span>
              <span>⭐ {{ $service->rating_avg ? number_format($service->rating_avg, 1) : '—' }}</span>
              @if($service->packages->isNotEmpty())
                <span class="text-primary-700 font-semibold">من {{ number_format($service->packages->min('price'), 3) }} JOD</span>
              @endif
            </div>
          </div>
          <div class="flex gap-2 shrink-0">
            @if($service->status === 'active')
              <a href="{{ route('services.show', $service->slug) }}" target="_blank"
                 class="border border-gray-200 text-gray-600 text-xs px-3 py-1.5 rounded-lg hover:bg-gray-50">👁️ عرض</a>
            @endif
            <a href="{{ route('freelancer.services.edit', $service) }}"
               class="bg-blue-100 text-blue-700 text-xs px-3 py-1.5 rounded-lg hover:bg-blue-200">تعديل</a>
            <form method="POST" action="{{ route('freelancer.services.toggle', $service) }}">
              @csrf
              <button class="{{ $service->status==='active'?'bg-yellow-100 text-yellow-700':'bg-green-100 text-green-700' }} text-xs px-3 py-1.5 rounded-lg">
                {{ $service->status==='active'?'إيقاف':'تفعيل' }}
              </button>
            </form>
          </div>
        </div>
      @endforeach
    </div>
    <div class="mt-4">{{ $services->links() }}</div>
  @endif
</div>
@endsection

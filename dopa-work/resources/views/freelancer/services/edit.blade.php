@extends('layouts.app')
@section('title', 'تعديل الخدمة')
@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
  <a href="{{ route('freelancer.services.index') }}" class="text-sm text-gray-400 hover:text-primary-600 mb-5 block">← خدماتي</a>
  <h1 class="text-2xl font-bold text-gray-900 mb-6">تعديل: {{ $service->title_ar }}</h1>

  @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5 text-sm text-red-700">
      <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif
  @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 mb-5 text-sm">✅ {{ session('success') }}</div>@endif

  <form method="POST" action="{{ route('freelancer.services.update', $service) }}" enctype="multipart/form-data" class="space-y-6">
    @csrf @method('PUT')

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
      <h2 class="font-semibold text-gray-900">📝 المعلومات الأساسية</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">عنوان الخدمة (عربي) *</label>
          <input name="title_ar" value="{{ old('title_ar', $service->title_ar) }}" required dir="rtl"
            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Service Title (English) *</label>
          <input name="title" value="{{ old('title', $service->title) }}" required
            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400">
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">وصف الخدمة (عربي) *</label>
        <textarea name="description_ar" rows="4" required minlength="100" dir="rtl"
          class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary-400 resize-none">{{ old('description_ar', $service->description_ar) }}</textarea>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Description (English) *</label>
        <textarea name="description" rows="4" required minlength="100"
          class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary-400 resize-none">{{ old('description', $service->description) }}</textarea>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">مدة التسليم (أيام)</label>
          <input name="delivery_days" type="number" min="1" max="90" value="{{ old('delivery_days', $service->delivery_days) }}"
            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">عدد التعديلات</label>
          <input name="revisions" type="number" min="0" max="10" value="{{ old('revisions', $service->revisions) }}"
            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400">
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">صورة الغلاف (اتركها فارغة للإبقاء على القديمة)</label>
        @if($service->cover_image)
          <img src="{{ Storage::url($service->cover_image) }}" class="w-32 h-24 object-cover rounded-xl mb-2">
        @endif
        <input type="file" name="cover_image" accept="image/*"
          class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none">
      </div>
    </div>

    {{-- Packages (edit price/days only) --}}
    @if($service->packages->isNotEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
      <h2 class="font-semibold text-gray-900 mb-4">📦 تعديل أسعار الباقات</h2>
      <div class="space-y-4">
        @foreach($service->packages as $pkg)
        <div class="border border-gray-100 rounded-xl p-4">
          <p class="font-semibold text-gray-700 mb-3 text-sm">{{ $pkg->name_ar }} ({{ $pkg->type }})</p>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="text-xs text-gray-500 mb-1 block">السعر (JOD)</label>
              <input name="package_prices[{{ $pkg->id }}]" type="number" step="0.001" min="1"
                value="{{ old("package_prices.{$pkg->id}", $pkg->price) }}"
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-primary-400">
            </div>
            <div>
              <label class="text-xs text-gray-500 mb-1 block">مدة التسليم (أيام)</label>
              <input name="package_days[{{ $pkg->id }}]" type="number" min="1"
                value="{{ old("package_days.{$pkg->id}", $pkg->delivery_days) }}"
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-primary-400">
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
    @endif

    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-sm text-yellow-800">
      ℹ️ سيعود الخدمة لحالة "قيد المراجعة" بعد الحفظ حتى تتم إعادة الموافقة عليها.
    </div>

    <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3.5 rounded-xl transition-colors text-sm">
      حفظ التعديلات
    </button>
  </form>
</div>
@endsection

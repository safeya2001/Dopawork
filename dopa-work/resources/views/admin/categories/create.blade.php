@extends('layouts.admin')
@section('title', 'إضافة فئة')
@section('content')
<div class="max-w-lg mx-auto px-4 py-8">
  <a href="{{ route('admin.categories.index') }}" class="text-sm text-gray-400 hover:text-primary-600 mb-5 block">← الفئات</a>
  <h1 class="text-2xl font-bold text-gray-900 mb-6">إضافة فئة جديدة</h1>

  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
    <form method="POST" action="{{ route('admin.categories.store') }}">
      @csrf
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">الاسم (English) *</label>
          <input name="name" value="{{ old('name') }}" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400 @error('name') border-red-400 @enderror">
          @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">الاسم بالعربية *</label>
          <input name="name_ar" value="{{ old('name_ar') }}" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400 @error('name_ar') border-red-400 @enderror" dir="rtl">
          @error('name_ar')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">الأيقونة (emoji)</label>
          <input name="icon" value="{{ old('icon') }}" placeholder="💼" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">الفئة الأم (اختياري)</label>
          <select name="parent_id" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-primary-400">
            <option value="">— فئة رئيسية —</option>
            @foreach($parents as $p)
              <option value="{{ $p->id }}" {{ old('parent_id')==$p->id?'selected':'' }}>{{ $p->name_ar }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">الترتيب</label>
          <input name="sort_order" type="number" value="{{ old('sort_order', 0) }}" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400">
        </div>
      </div>
      <button class="mt-6 w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 rounded-xl transition-colors text-sm">
        إضافة الفئة
      </button>
    </form>
  </div>
</div>
@endsection

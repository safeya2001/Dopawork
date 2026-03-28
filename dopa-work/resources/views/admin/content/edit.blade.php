@extends('layouts.admin')
@section('title', app()->getLocale()==='ar' ? 'تحرير المحتوى' : 'Edit Content')

@section('content')
@php
  $ar = app()->getLocale()==='ar';
  $labels = ['faq'=>['ar'=>'الأسئلة الشائعة','en'=>'FAQ'], 'terms'=>['ar'=>'الشروط والأحكام','en'=>'Terms & Conditions'], 'privacy'=>['ar'=>'سياسة الخصوصية','en'=>'Privacy Policy'], 'about'=>['ar'=>'من نحن','en'=>'About Us']];
  $label = $labels[$page] ?? ['ar'=>$page,'en'=>$page];
@endphp
<div class="max-w-4xl mx-auto px-4 py-8">
  <div class="flex items-center gap-3 mb-6">
    <a href="{{ route('admin.content.index') }}" class="text-sm text-gray-400 hover:text-gray-600">{{ $ar ? 'المحتوى' : 'Content' }}</a>
    <span class="text-gray-300">/</span>
    <h1 class="text-xl font-bold text-gray-900">{{ $ar ? $label['ar'] : $label['en'] }}</h1>
  </div>

  @if(session('success'))
    <div class="mb-5 rounded-xl bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm font-medium">{{ session('success') }}</div>
  @endif

  <form method="POST" action="{{ route('admin.content.update', $page) }}" class="space-y-5">
    @csrf @method('PUT')

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
      <label class="block font-semibold text-gray-800 text-sm">{{ $ar ? 'المحتوى (إنجليزي)' : 'Content (English)' }}</label>
      <textarea name="content_en" rows="16"
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 font-mono resize-y @error('content_en') border-red-400 @enderror"
                placeholder="Write content in English (HTML or plain text)...">{{ old('content_en', $content_en) }}</textarea>
      @error('content_en')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
      <label class="block font-semibold text-gray-800 text-sm">{{ $ar ? 'المحتوى (عربي)' : 'Content (Arabic)' }}</label>
      <textarea name="content_ar" rows="16" dir="rtl"
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 font-mono resize-y @error('content_ar') border-red-400 @enderror"
                placeholder="اكتب المحتوى بالعربية (HTML أو نص عادي)...">{{ old('content_ar', $content_ar) }}</textarea>
      @error('content_ar')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="flex justify-end gap-3">
      <a href="{{ route('admin.content.index') }}"
         class="px-5 py-2.5 text-sm rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">
        {{ $ar ? 'إلغاء' : 'Cancel' }}
      </a>
      <button type="submit"
              class="px-6 py-2.5 text-sm font-medium rounded-xl bg-primary-600 text-white hover:bg-primary-700 transition-colors">
        {{ $ar ? 'حفظ المحتوى' : 'Save Content' }}
      </button>
    </div>
  </form>
</div>
@endsection

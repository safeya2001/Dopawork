@extends('layouts.admin')
@section('title', app()->getLocale()==='ar' ? 'إدارة المحتوى' : 'Content Management')

@section('content')
@php $ar = app()->getLocale()==='ar'; @endphp
<div class="max-w-2xl mx-auto px-4 py-8">
  <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ $ar ? 'إدارة المحتوى' : 'Content Management' }}</h1>

  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm divide-y divide-gray-50">
    @foreach([
      ['key'=>'faq',     'ar'=>'الأسئلة الشائعة',      'en'=>'FAQ',              'icon'=>'❓'],
      ['key'=>'terms',   'ar'=>'الشروط والأحكام',       'en'=>'Terms & Conditions','icon'=>'📋'],
      ['key'=>'privacy', 'ar'=>'سياسة الخصوصية',        'en'=>'Privacy Policy',    'icon'=>'🔒'],
      ['key'=>'about',   'ar'=>'من نحن',                'en'=>'About Us',           'icon'=>'ℹ️'],
    ] as $page)
    <div class="flex items-center justify-between px-5 py-4">
      <div class="flex items-center gap-3">
        <span class="text-xl">{{ $page['icon'] }}</span>
        <p class="text-sm font-medium text-gray-900">{{ $ar ? $page['ar'] : $page['en'] }}</p>
      </div>
      <a href="{{ route('admin.content.edit', $page['key']) }}"
         class="text-xs font-medium text-primary-600 hover:underline border border-primary-200 px-3 py-1.5 rounded-lg hover:bg-primary-50 transition-colors">
        {{ $ar ? 'تحرير' : 'Edit' }}
      </a>
    </div>
    @endforeach
  </div>
</div>
@endsection

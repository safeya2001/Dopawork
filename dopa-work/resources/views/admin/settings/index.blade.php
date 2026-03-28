@extends('layouts.admin')
@section('title', app()->getLocale()==='ar' ? 'الإعدادات العامة' : 'Platform Settings')

@section('content')
@php $ar = app()->getLocale()==='ar'; @endphp
<div class="max-w-2xl mx-auto px-4 py-8">
  <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ $ar ? 'إعدادات المنصة' : 'Platform Settings' }}</h1>

  @if(session('success'))
    <div class="mb-5 rounded-xl bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm font-medium">{{ session('success') }}</div>
  @endif

  <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-5">
    @csrf @method('PUT')

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
      <h2 class="font-semibold text-gray-800 text-sm border-b pb-2">{{ $ar ? 'معلومات المنصة' : 'Platform Info' }}</h2>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs text-gray-500 mb-1">{{ $ar ? 'اسم المنصة (إنجليزي)' : 'Platform Name (English)' }}</label>
          <input type="text" name="platform_name" value="{{ $settings['platform_name']->value ?? 'Dopa Work' }}"
                 class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        <div>
          <label class="block text-xs text-gray-500 mb-1">{{ $ar ? 'اسم المنصة (عربي)' : 'Platform Name (Arabic)' }}</label>
          <input type="text" name="platform_name_ar" dir="rtl" value="{{ $settings['platform_name_ar']->value ?? 'دوبا وورك' }}"
                 class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        <div class="sm:col-span-2">
          <label class="block text-xs text-gray-500 mb-1">{{ $ar ? 'بريد الدعم الفني' : 'Support Email' }}</label>
          <input type="email" name="support_email" value="{{ $settings['support_email']->value ?? 'support@dopawork.jo' }}"
                 class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
      <h2 class="font-semibold text-gray-800 text-sm border-b pb-2">{{ $ar ? 'الإعدادات المالية' : 'Financial Settings' }}</h2>

      <div>
        <label class="block text-xs text-gray-500 mb-1">{{ $ar ? 'نسبة عمولة المنصة (%)' : 'Platform Commission (%)' }}</label>
        <div class="flex items-center gap-3">
          <div class="relative flex-1">
            <span class="absolute end-3 top-1/2 -translate-y-1/2 text-sm text-gray-400 font-medium">%</span>
            <input type="number" name="commission_percent" step="0.1" min="0" max="50"
                   value="{{ $settings['commission_percent']->value ?? '10' }}"
                   class="w-full rounded-xl border border-gray-200 px-3 py-2.5 pe-8 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
          </div>
          <p class="text-xs text-gray-400">{{ $ar ? 'الافتراضي: 10%' : 'Default: 10%' }}</p>
        </div>
        @error('commission_percent')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        <p class="text-xs text-gray-400 mt-1">{{ $ar ? 'يُخصم هذا من مكاسب المستقل في كل طلب مكتمل.' : 'This is deducted from freelancer earnings on each completed order.' }}</p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs text-gray-500 mb-1">{{ $ar ? 'الحد الأدنى للسحب (JOD)' : 'Min Withdrawal (JOD)' }}</label>
          <div class="relative">
            <span class="absolute end-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">JOD</span>
            <input type="number" name="min_withdrawal" step="0.001" min="0"
                   value="{{ $settings['min_withdrawal']->value ?? '5' }}"
                   class="w-full rounded-xl border border-gray-200 px-3 py-2.5 pe-12 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
          </div>
        </div>
        <div>
          <label class="block text-xs text-gray-500 mb-1">{{ $ar ? 'الحد الأقصى للسحب (JOD)' : 'Max Withdrawal (JOD)' }}</label>
          <div class="relative">
            <span class="absolute end-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">JOD</span>
            <input type="number" name="max_withdrawal" step="0.001" min="1"
                   value="{{ $settings['max_withdrawal']->value ?? '500' }}"
                   class="w-full rounded-xl border border-gray-200 px-3 py-2.5 pe-12 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
          </div>
        </div>
      </div>
    </div>

    <div class="flex justify-end">
      <button type="submit"
              class="px-6 py-2.5 text-sm font-medium rounded-xl bg-primary-600 text-white hover:bg-primary-700 transition-colors">
        {{ $ar ? 'حفظ الإعدادات' : 'Save Settings' }}
      </button>
    </div>
  </form>
</div>
@endsection

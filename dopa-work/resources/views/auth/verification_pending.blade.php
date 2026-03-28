@extends('layouts.app')
@section('title', app()->getLocale()==='ar' ? 'في انتظار التحقق' : 'Awaiting Verification')

@section('content')
<div class="min-h-[calc(100vh-64px)] flex items-center justify-center px-4 py-12 bg-gray-50">
  <div class="max-w-md w-full">

    {{-- Success Flash --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-2xl p-4 flex items-center gap-3 mb-4">
      <span class="text-2xl">✅</span>
      <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Card --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 text-center">

      {{-- Animated clock icon --}}
      <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-6">
        <span class="text-5xl">⏳</span>
      </div>

      <h1 class="text-2xl font-bold text-gray-900 mb-2">
        {{ app()->getLocale()==='ar' ? 'وثائقك قيد المراجعة' : 'Documents Under Review' }}
      </h1>
      <p class="text-gray-500 text-sm leading-relaxed mb-6">
        {{ app()->getLocale()==='ar'
          ? 'شكراً لك! تم استلام وثائقك بنجاح. سيقوم فريقنا بمراجعتها خلال 24-48 ساعة عمل وستصلك رسالة إشعار فور اتخاذ القرار.'
          : 'Thank you! Your documents have been received. Our team will review them within 24-48 business hours and you will be notified once a decision is made.' }}
      </p>

      {{-- Submitted info --}}
      <div class="bg-gray-50 rounded-2xl p-4 mb-6 text-start">
        <div class="flex items-center justify-between text-sm mb-3">
          <span class="text-gray-500">{{ app()->getLocale()==='ar' ? 'نوع الوثيقة' : 'Document Type' }}</span>
          <span class="font-semibold text-gray-800">
            @php
              $types = [
                'national_id'        => app()->getLocale()==='ar' ? 'بطاقة هوية وطنية' : 'National ID',
                'freelancer_permit'  => app()->getLocale()==='ar' ? 'رخصة عمل حر' : 'Freelancer Permit',
                'passport'           => app()->getLocale()==='ar' ? 'جواز سفر' : 'Passport',
                'residency_permit'   => app()->getLocale()==='ar' ? 'تصريح إقامة' : 'Residency Permit',
              ];
            @endphp
            {{ $types[$verification->document_type] ?? $verification->document_type }}
          </span>
        </div>
        <div class="flex items-center justify-between text-sm mb-3">
          <span class="text-gray-500">{{ app()->getLocale()==='ar' ? 'تاريخ الإرسال' : 'Submitted' }}</span>
          <span class="font-semibold text-gray-800">{{ $verification->created_at->format('d M Y, h:i A') }}</span>
        </div>
        <div class="flex items-center justify-between text-sm">
          <span class="text-gray-500">{{ app()->getLocale()==='ar' ? 'الحالة' : 'Status' }}</span>
          <span class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-700 text-xs font-semibold px-2.5 py-1 rounded-full">
            ⏳ {{ app()->getLocale()==='ar' ? 'قيد المراجعة' : 'Under Review' }}
          </span>
        </div>
      </div>

      {{-- What happens next --}}
      <div class="text-start mb-6">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">
          {{ app()->getLocale()==='ar' ? 'ماذا يحدث بعد ذلك؟' : 'What happens next?' }}
        </p>
        <div class="space-y-3">
          @foreach([
            ['icon'=>'🔍', 'ar'=>'يراجع فريقنا وثائقك', 'en'=>'Our team reviews your documents'],
            ['icon'=>'🔔', 'ar'=>'تصلك رسالة إشعار بالنتيجة', 'en'=>'You receive a notification with the result'],
            ['icon'=>'🚀', 'ar'=>'عند الموافقة تستطيع استخدام المنصة كاملاً', 'en'=>'Once approved, you have full platform access'],
          ] as $step)
            <div class="flex items-start gap-3">
              <span class="text-lg leading-tight">{{ $step['icon'] }}</span>
              <p class="text-sm text-gray-600">{{ app()->getLocale()==='ar' ? $step['ar'] : $step['en'] }}</p>
            </div>
          @endforeach
        </div>
      </div>

      {{-- Actions --}}
      <div class="flex flex-col gap-2">
        <a href="{{ route('verification.upload') }}"
           class="w-full py-2.5 text-sm font-medium border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition-colors">
          {{ app()->getLocale()==='ar' ? 'تعديل الوثائق المرفوعة' : 'Update Submitted Documents' }}
        </a>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit"
                  class="w-full py-2.5 text-sm text-red-500 hover:text-red-700 transition-colors">
            {{ app()->getLocale()==='ar' ? 'تسجيل الخروج' : 'Sign Out' }}
          </button>
        </form>
      </div>
    </div>

    {{-- Help text --}}
    <p class="text-center text-xs text-gray-400 mt-4">
      {{ app()->getLocale()==='ar' ? 'لديك استفسار؟ تواصل معنا عبر' : 'Have questions? Contact us at' }}
      <a href="mailto:support@dopawork.jo" class="text-primary-600 hover:underline">support@dopawork.jo</a>
    </p>

  </div>
</div>
@endsection

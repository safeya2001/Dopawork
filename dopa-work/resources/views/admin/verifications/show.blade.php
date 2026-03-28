@extends('layouts.admin')
@section('title', 'تفاصيل التحقق — ' . $verification->user->name)
@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">

  {{-- Back --}}
  <a href="{{ route('admin.verifications.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 mb-6">
    ← العودة لقائمة التحققات
  </a>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 mb-5 text-sm">✅ {{ session('success') }}</div>
  @endif

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left: Info --}}
    <div class="lg:col-span-1 space-y-4">

      {{-- User Card --}}
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <img src="https://ui-avatars.com/api/?name={{ urlencode($verification->user->name) }}&color=6366f1&background=e0e7ff&size=80"
             class="w-16 h-16 rounded-full mx-auto mb-3" alt="">
        <h2 class="text-center font-bold text-gray-900">{{ $verification->user->name }}</h2>
        <p class="text-center text-xs text-gray-500 mt-0.5">{{ $verification->user->email }}</p>
        <p class="text-center text-xs text-primary-600 font-medium mt-1">{{ $verification->user->role }}</p>

        <div class="mt-4 space-y-2 text-sm border-t pt-4">
          <div class="flex justify-between">
            <span class="text-gray-500">نوع الوثيقة</span>
            <span class="font-medium text-gray-800">{{ str_replace('_', ' ', ucfirst($verification->document_type)) }}</span>
          </div>
          @if($verification->document_number)
          <div class="flex justify-between">
            <span class="text-gray-500">رقم الوثيقة</span>
            <span class="font-medium text-gray-800">{{ $verification->document_number }}</span>
          </div>
          @endif
          @if($verification->document_expiry)
          <div class="flex justify-between">
            <span class="text-gray-500">تاريخ الانتهاء</span>
            <span class="font-medium text-gray-800">{{ $verification->document_expiry->format('Y-m-d') }}</span>
          </div>
          @endif
          <div class="flex justify-between">
            <span class="text-gray-500">تاريخ الطلب</span>
            <span class="font-medium text-gray-800">{{ $verification->created_at->format('Y/m/d H:i') }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-gray-500">الحالة</span>
            @php
              $sc = ['pending'=>['bg-yellow-100','text-yellow-700','معلق'],
                     'approved'=>['bg-green-100','text-green-700','موافق'],
                     'rejected'=>['bg-red-100','text-red-700','مرفوض']][$verification->status] ?? ['bg-gray-100','text-gray-700',$verification->status];
            @endphp
            <span class="text-xs px-2 py-1 rounded-full {{ $sc[0] }} {{ $sc[1] }} font-semibold">{{ $sc[2] }}</span>
          </div>
          @if($verification->reviewer)
          <div class="flex justify-between">
            <span class="text-gray-500">راجعه</span>
            <span class="font-medium text-gray-800">{{ $verification->reviewer->name }}</span>
          </div>
          @endif
        </div>

        @if($verification->status === 'rejected' && $verification->rejection_reason_ar)
        <div class="mt-4 bg-red-50 border border-red-100 rounded-xl p-3">
          <p class="text-xs font-semibold text-red-700 mb-1">سبب الرفض:</p>
          <p class="text-xs text-red-600">{{ $verification->rejection_reason_ar }}</p>
          <p class="text-xs text-red-400 mt-1">{{ $verification->rejection_reason }}</p>
        </div>
        @endif
      </div>

      {{-- Actions --}}
      @if($verification->status === 'pending')
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
        <h3 class="font-semibold text-gray-800 mb-1">الإجراءات</h3>
        <form method="POST" action="{{ route('admin.verifications.approve', $verification) }}">
          @csrf
          <button class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2.5 rounded-xl transition-colors">
            ✓ قبول الطلب
          </button>
        </form>

        <div x-data="{ open: false }">
          <button onclick="document.getElementById('reject-form').classList.toggle('hidden')"
            class="w-full bg-red-100 hover:bg-red-200 text-red-700 font-semibold py-2.5 rounded-xl transition-colors">
            ✕ رفض الطلب
          </button>
          <div id="reject-form" class="hidden mt-3 space-y-2">
            <form method="POST" action="{{ route('admin.verifications.reject', $verification) }}">
              @csrf
              <textarea name="rejection_reason" required rows="2"
                placeholder="Rejection reason (English)..."
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-red-400 resize-none"></textarea>
              <textarea name="rejection_reason_ar" required rows="2"
                placeholder="سبب الرفض بالعربية..."
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-red-400 resize-none mt-2"></textarea>
              <button class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-2.5 rounded-xl transition-colors mt-1">
                تأكيد الرفض
              </button>
            </form>
          </div>
        </div>
      </div>
      @endif
    </div>

    {{-- Right: Documents --}}
    <div class="lg:col-span-2 space-y-4">
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="font-semibold text-gray-900 mb-4">📎 الوثائق المرفوعة</h3>

        <div class="space-y-6">
          {{-- Front Image --}}
          @if($verification->front_image)
          <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">الوجه الأمامي</p>
            @if(str_ends_with(strtolower($verification->front_image), '.pdf'))
              <a href="{{ route('admin.verifications.document', [$verification, 'front_image']) }}" target="_blank"
                 class="flex items-center gap-3 bg-red-50 border border-red-200 rounded-xl p-4 hover:bg-red-100 transition-colors">
                <span class="text-3xl">📄</span>
                <div>
                  <p class="font-medium text-red-700">ملف PDF</p>
                  <p class="text-xs text-red-500">اضغط لفتح في تبويب جديد</p>
                </div>
              </a>
            @else
              <a href="{{ route('admin.verifications.document', [$verification, 'front_image']) }}" target="_blank">
                <img src="{{ route('admin.verifications.document', [$verification, 'front_image']) }}"
                     alt="الوجه الأمامي"
                     class="w-full max-h-72 object-contain rounded-xl border border-gray-200 bg-gray-50 hover:opacity-90 transition-opacity cursor-zoom-in">
              </a>
            @endif
          </div>
          @endif

          {{-- Back Image --}}
          @if($verification->back_image)
          <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">الوجه الخلفي</p>
            @if(str_ends_with(strtolower($verification->back_image), '.pdf'))
              <a href="{{ route('admin.verifications.document', [$verification, 'back_image']) }}" target="_blank"
                 class="flex items-center gap-3 bg-red-50 border border-red-200 rounded-xl p-4 hover:bg-red-100 transition-colors">
                <span class="text-3xl">📄</span>
                <div>
                  <p class="font-medium text-red-700">ملف PDF</p>
                  <p class="text-xs text-red-500">اضغط لفتح في تبويب جديد</p>
                </div>
              </a>
            @else
              <a href="{{ route('admin.verifications.document', [$verification, 'back_image']) }}" target="_blank">
                <img src="{{ route('admin.verifications.document', [$verification, 'back_image']) }}"
                     alt="الوجه الخلفي"
                     class="w-full max-h-72 object-contain rounded-xl border border-gray-200 bg-gray-50 hover:opacity-90 transition-opacity cursor-zoom-in">
              </a>
            @endif
          </div>
          @endif

          {{-- Selfie --}}
          @if($verification->selfie_image)
          <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">صورة السيلفي</p>
            <a href="{{ route('admin.verifications.document', [$verification, 'selfie_image']) }}" target="_blank">
              <img src="{{ route('admin.verifications.document', [$verification, 'selfie_image']) }}"
                   alt="سيلفي"
                   class="w-48 h-48 object-cover rounded-xl border border-gray-200 bg-gray-50 hover:opacity-90 transition-opacity cursor-zoom-in">
            </a>
          </div>
          @endif

          @if(!$verification->front_image && !$verification->back_image && !$verification->selfie_image)
            <div class="py-12 text-center text-gray-400">
              <span class="text-4xl block mb-2">📭</span>
              <p class="text-sm">لم يتم رفع أي وثائق</p>
            </div>
          @endif
        </div>
      </div>
    </div>

  </div>
</div>
@endsection

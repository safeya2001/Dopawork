@extends('layouts.app')
@section('title', app()->getLocale()==='ar' ? 'تأكيد الطلب' : 'Checkout')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">

  <div class="mb-6">
    <a href="{{ route('services.show', $service->slug) }}" class="text-sm text-gray-400 hover:text-primary-600 flex items-center gap-1">
      ← {{ app()->getLocale()==='ar'?'العودة للخدمة':'Back to Service' }}
    </a>
    <h1 class="text-2xl font-bold text-gray-900 mt-3">
      {{ app()->getLocale()==='ar'?'تأكيد الطلب':'Confirm Your Order' }}
    </h1>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Form --}}
    <div class="lg:col-span-2">
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form method="POST" action="{{ route('client.orders.place') }}">
          @csrf
          <input type="hidden" name="service_id" value="{{ $service->id }}">
          <input type="hidden" name="package_id" value="{{ $selectedPackage?->id }}">

          {{-- Package selector --}}
          @if($service->packages->count() > 1)
            <div class="mb-5">
              <label class="block text-sm font-semibold text-gray-700 mb-3">
                {{ app()->getLocale()==='ar'?'اختر الباقة':'Select Package' }}
              </label>
              <div class="grid grid-cols-3 gap-3">
                @foreach($service->packages as $pkg)
                  <label class="relative cursor-pointer">
                    <input type="radio" name="package_id" value="{{ $pkg->id }}"
                      {{ $selectedPackage?->id === $pkg->id ? 'checked' : '' }}
                      class="sr-only peer">
                    <div class="border-2 rounded-xl p-3 text-center peer-checked:border-primary-600 peer-checked:bg-primary-50 hover:border-gray-300 transition-all">
                      <p class="text-xs font-semibold text-gray-700">{{ app()->getLocale()==='ar' ? $pkg->name_ar : $pkg->name }}</p>
                      <p class="text-lg font-bold text-primary-700 mt-1">{{ number_format($pkg->price,3) }}</p>
                      <p class="text-xs text-gray-400">JOD</p>
                    </div>
                  </label>
                @endforeach
              </div>
            </div>
          @endif

          {{-- Requirements --}}
          <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              {{ app()->getLocale()==='ar'?'متطلباتك *':'Your Requirements *' }}
            </label>
            <p class="text-xs text-gray-400 mb-2">
              {{ app()->getLocale()==='ar'
                 ? 'اشرح بالتفصيل ما تحتاجه ليبدأ المستقل العمل على أفضل وجه.'
                 : 'Describe in detail what you need so the freelancer can start working properly.' }}
            </p>
            <textarea name="requirements" rows="6" required minlength="20" maxlength="5000"
              class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-100 resize-none @error('requirements') border-red-400 @enderror"
              placeholder="{{ app()->getLocale()==='ar'?'مثال: أريد تصميم شعار لمطعم أردني...' : 'Example: I need a logo for a Jordanian restaurant...' }}">{{ old('requirements') }}</textarea>
            @error('requirements')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
          </div>

          {{-- Wallet balance check --}}
          @if($selectedPackage)
            @php
              $total = round($selectedPackage->price * 1.15, 3);
              $balance = auth()->user()->wallet_balance;
              $canAfford = $balance >= $total;
            @endphp
            @if(!$canAfford)
              <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-4 text-sm text-red-700">
                ⚠️ {{ app()->getLocale()==='ar'
                  ? "رصيدك غير كافٍ. المطلوب: ".number_format($total,3)." JOD | رصيدك: ".number_format($balance,3)." JOD"
                  : "Insufficient balance. Required: ".number_format($total,3)." JOD | Balance: ".number_format($balance,3)." JOD" }}
                <a href="{{ route('wallet.deposit') }}" class="font-semibold underline">
                  {{ app()->getLocale()==='ar'?'اشحن الآن':'Top up now' }}
                </a>
              </div>
            @endif
          @endif

          <button type="submit" {{ isset($canAfford) && !$canAfford ? 'disabled' : '' }}
            class="w-full bg-primary-600 hover:bg-primary-700 disabled:bg-gray-300 text-white font-semibold py-3.5 rounded-xl transition-colors text-sm">
            🛒 {{ app()->getLocale()==='ar'?'تأكيد الطلب ودفع':'Confirm Order & Pay' }}
          </button>

          <p class="text-center text-xs text-gray-400 mt-3">
            🔒 {{ app()->getLocale()==='ar'?'مبلغك محمي بنظام الضمان الآمن':'Your payment is protected by our escrow system' }}
          </p>
        </form>
      </div>
    </div>

    {{-- Summary Sidebar --}}
    <div>
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sticky top-20">
        <h3 class="font-semibold text-gray-900 mb-4">{{ app()->getLocale()==='ar'?'ملخص الطلب':'Order Summary' }}</h3>

        <div class="flex items-center gap-3 mb-4 pb-4 border-b border-gray-50">
          <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center overflow-hidden flex-shrink-0">
            @if($service->cover_image)
              <img src="{{ Storage::url($service->cover_image) }}" class="w-full h-full object-cover">
            @else
              <span class="text-2xl">{{ $service->category->icon ?? '💼' }}</span>
            @endif
          </div>
          <div>
            <p class="text-sm font-semibold text-gray-900 leading-snug line-clamp-2">{{ $service->display_title }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $service->user->name }}</p>
          </div>
        </div>

        @if($selectedPackage)
          <div class="space-y-2 text-sm mb-4">
            <div class="flex justify-between text-gray-600">
              <span>{{ app()->getLocale()==='ar'?'الباقة':'Package' }}</span>
              <span class="font-medium">{{ app()->getLocale()==='ar'?$selectedPackage->name_ar:$selectedPackage->name }}</span>
            </div>
            <div class="flex justify-between text-gray-600">
              <span>{{ app()->getLocale()==='ar'?'التسليم':'Delivery' }}</span>
              <span class="font-medium">{{ $selectedPackage->delivery_days }} {{ app()->getLocale()==='ar'?'أيام':'days' }}</span>
            </div>
            <div class="flex justify-between text-gray-600">
              <span>{{ app()->getLocale()==='ar'?'سعر الخدمة':'Service Price' }}</span>
              <span class="font-medium">{{ number_format($selectedPackage->price,3) }} JOD</span>
            </div>
            <div class="flex justify-between text-gray-400 text-xs">
              <span>{{ app()->getLocale()==='ar'?'رسوم المنصة (15%)':'Platform Fee (15%)' }}</span>
              <span>{{ number_format($selectedPackage->price * 0.15, 3) }} JOD</span>
            </div>
            <div class="flex justify-between font-bold text-gray-900 pt-2 border-t border-gray-100 text-base">
              <span>{{ app()->getLocale()==='ar'?'الإجمالي':'Total' }}</span>
              <span class="text-primary-700">{{ number_format($selectedPackage->price * 1.15, 3) }} JOD</span>
            </div>
          </div>
        @endif

        <div class="flex items-center gap-2 text-xs text-gray-400 bg-gray-50 rounded-xl p-3">
          <span>🔒</span>
          <span>{{ app()->getLocale()==='ar'?'مبلغك يُحتجز في الضمان حتى قبول التسليم':'Funds held in escrow until you approve delivery' }}</span>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

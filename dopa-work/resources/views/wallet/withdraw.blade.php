@extends('layouts.app')
@section('title', app()->getLocale()==='ar' ? 'سحب رصيد' : 'Withdraw Funds')

@section('content')
<div class="max-w-lg mx-auto px-4 py-8">
  <div class="mb-6">
    <a href="{{ route('wallet.index') }}" class="text-sm text-gray-400 hover:text-primary-600 flex items-center gap-1">
      ← {{ app()->getLocale()==='ar'?'العودة للمحفظة':'Back to Wallet' }}
    </a>
    <h1 class="text-2xl font-bold text-gray-900 mt-3">{{ app()->getLocale()==='ar'?'سحب رصيد':'Withdraw Funds' }}</h1>
  </div>

  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

    <div class="p-4 bg-gray-50 rounded-xl mb-4 space-y-2">
      <div class="flex items-center justify-between">
        <p class="text-sm text-gray-600">{{ app()->getLocale()==='ar'?'إجمالي الرصيد':'Total Balance' }}</p>
        <p class="font-bold text-gray-900">{{ number_format($balance, 3) }} JOD</p>
      </div>
      @if($pendingWithdrawals > 0)
      <div class="flex items-center justify-between">
        <p class="text-sm text-orange-600">{{ app()->getLocale()==='ar'?'محجوز (طلبات سحب معلقة)':'Reserved (pending withdrawals)' }}</p>
        <p class="font-semibold text-orange-600">- {{ number_format($pendingWithdrawals, 3) }} JOD</p>
      </div>
      <div class="flex items-center justify-between border-t border-gray-200 pt-2">
        <p class="text-sm font-semibold text-gray-700">{{ app()->getLocale()==='ar'?'المتاح للسحب':'Available to Withdraw' }}</p>
        <p class="font-bold text-primary-700">{{ number_format($available, 3) }} JOD</p>
      </div>
      @endif
    <form method="POST" action="{{ route('wallet.withdraw.request') }}">
      @csrf

      <div class="mb-5">
        <label class="block text-sm font-semibold text-gray-700 mb-2">
          {{ app()->getLocale()==='ar'?'المبلغ (JOD)':'Amount (JOD)' }}
        </label>
        <div class="relative">
          <input type="number" name="amount" step="0.001" min="5" max="{{ $available }}" required
            value="{{ old('amount') }}"
            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-100 @error('amount') border-red-400 @enderror"
            placeholder="0.000">
          <span class="absolute end-4 top-1/2 -translate-y-1/2 text-sm text-gray-400 font-medium">JOD</span>
        </div>
        @error('amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="mb-5">
        <label class="block text-sm font-semibold text-gray-700 mb-3">
          {{ app()->getLocale()==='ar'?'طريقة السحب':'Withdrawal Method' }}
        </label>
        <div class="space-y-2">
          <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer hover:border-primary-300 transition-colors has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50">
            <input type="radio" name="method" value="cliq" {{ old('method','cliq')==='cliq'?'checked':'' }} class="accent-primary-600"
              onclick="document.getElementById('cliqFields').classList.remove('hidden'); document.getElementById('ibanFields').classList.add('hidden')">
            <span class="text-xl">🏦</span>
            <span class="text-sm font-medium text-gray-700">CliQ ({{ app()->getLocale()==='ar'?'الأردن':'Jordan' }})</span>
          </label>
          <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer hover:border-primary-300 transition-colors has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50">
            <input type="radio" name="method" value="bank" {{ old('method')==='bank'?'checked':'' }} class="accent-primary-600"
              onclick="document.getElementById('ibanFields').classList.remove('hidden'); document.getElementById('cliqFields').classList.add('hidden')">
            <span class="text-xl">🏛️</span>
            <span class="text-sm font-medium text-gray-700">{{ app()->getLocale()==='ar'?'تحويل بنكي (IBAN)':'Bank Transfer (IBAN)' }}</span>
          </label>
        </div>
      </div>

      <div id="cliqFields" class="mb-5">
        <label class="block text-sm font-semibold text-gray-700 mb-2">
          {{ app()->getLocale()==='ar'?'رقم CliQ أو اسم المستخدم':'CliQ Number or Alias' }}
        </label>
        <input type="text" name="cliq_alias" value="{{ old('cliq_alias') }}"
          class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary-400 @error('cliq_alias') border-red-400 @enderror"
          placeholder="{{ app()->getLocale()==='ar'?'مثال: 07xxxxxxxx أو @username':'e.g. 07xxxxxxxx or @username' }}">
        @error('cliq_alias')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      <div id="ibanFields" class="mb-5 hidden">
        <label class="block text-sm font-semibold text-gray-700 mb-2">
          {{ app()->getLocale()==='ar'?'رقم IBAN':'IBAN' }}
        </label>
        <input type="text" name="iban" value="{{ old('iban') }}"
          class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary-400 @error('iban') border-red-400 @enderror"
          placeholder="JO94CBJO0010000000000131000302">
        @error('iban')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="bg-yellow-50 border border-yellow-100 rounded-xl p-4 mb-5 text-xs text-yellow-700">
        ⏱️ {{ app()->getLocale()==='ar'
          ? 'تتم معالجة طلبات السحب خلال 1-3 أيام عمل بعد مراجعة الإدارة.'
          : 'Withdrawal requests are processed within 1-3 business days after admin review.' }}
      </div>

      <button type="submit"
        class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3.5 rounded-xl transition-colors text-sm">
        {{ app()->getLocale()==='ar'?'تقديم طلب السحب':'Submit Withdrawal Request' }}
      </button>
    </form>
  </div>
</div>
@endsection

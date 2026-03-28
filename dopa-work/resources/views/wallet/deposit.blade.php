@extends('layouts.app')
@section('title', app()->getLocale()==='ar' ? 'إيداع رصيد' : 'Deposit Funds')

@section('content')
<div class="max-w-lg mx-auto px-4 py-8">
  <div class="mb-6">
    <a href="{{ route('wallet.index') }}" class="text-sm text-gray-400 hover:text-primary-600 flex items-center gap-1">
      ← {{ app()->getLocale()==='ar'?'العودة للمحفظة':'Back to Wallet' }}
    </a>
    <h1 class="text-2xl font-bold text-gray-900 mt-3">{{ app()->getLocale()==='ar'?'إيداع رصيد':'Deposit Funds' }}</h1>
  </div>

  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
    <form method="POST" action="{{ route('wallet.deposit.process') }}" enctype="multipart/form-data">
      @csrf

      {{-- Amount --}}
      <div class="mb-5">
        <label class="block text-sm font-semibold text-gray-700 mb-2">
          {{ app()->getLocale()==='ar'?'المبلغ (JOD)':'Amount (JOD)' }}
        </label>
        <div class="relative">
          <input type="number" name="amount" id="amountInput" step="0.001" min="1" max="10000" required
            value="{{ old('amount') }}"
            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-100 @error('amount') border-red-400 @enderror"
            placeholder="0.000">
          <span class="absolute end-4 top-1/2 -translate-y-1/2 text-sm text-gray-400 font-medium">JOD</span>
        </div>
        @error('amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        <div class="flex gap-2 mt-2">
          @foreach([10, 25, 50, 100] as $preset)
            <button type="button" onclick="document.getElementById('amountInput').value='{{ $preset }}.000'; updateCliqAmount()"
              class="flex-1 py-1.5 border border-gray-200 rounded-lg text-xs text-gray-600 hover:border-primary-300 hover:text-primary-600 transition-colors">
              {{ $preset }} JOD
            </button>
          @endforeach
        </div>
      </div>

      {{-- Method --}}
      <div class="mb-5">
        <label class="block text-sm font-semibold text-gray-700 mb-3">
          {{ app()->getLocale()==='ar'?'طريقة الدفع':'Payment Method' }}
        </label>
        <div class="space-y-2">
          @foreach([
            ['value'=>'cliq',         'icon'=>'🏦', 'en'=>'CliQ (Jordan)',   'ar'=>'كليك (الأردن)'],
            ['value'=>'bank_transfer','icon'=>'🏛️', 'en'=>'Bank Transfer',   'ar'=>'تحويل بنكي'],
            ['value'=>'ewallet',      'icon'=>'📱', 'en'=>'E-Wallet',        'ar'=>'محفظة إلكترونية'],
          ] as $method)
            <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer hover:border-primary-300 transition-colors has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50">
              <input type="radio" name="method" value="{{ $method['value'] }}"
                {{ old('method','cliq') === $method['value'] ? 'checked' : '' }}
                onchange="onMethodChange(this.value)"
                class="accent-primary-600">
              <span class="text-xl">{{ $method['icon'] }}</span>
              <span class="text-sm font-medium text-gray-700">{{ app()->getLocale()==='ar' ? $method['ar'] : $method['en'] }}</span>
            </label>
          @endforeach
        </div>
        @error('method')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- CliQ Instructions Box --}}
      <div id="cliqBox" class="mb-5">
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4">
          <p class="text-sm font-semibold text-blue-900 mb-3">
            📱 {{ app()->getLocale()==='ar' ? 'خطوات الإيداع عبر كليك:' : 'CliQ Deposit Steps:' }}
          </p>
          <ol class="text-sm text-blue-800 space-y-2 list-decimal list-inside">
            <li>{{ app()->getLocale()==='ar' ? 'افتح تطبيق بنكك' : 'Open your bank app' }}</li>
            <li>
              {{ app()->getLocale()==='ar' ? 'حوّل المبلغ إلى الاسم المستعار:' : 'Transfer the amount to alias:' }}
              <span class="font-bold text-blue-900 bg-white px-2 py-0.5 rounded-lg border border-blue-200 mx-1">dopawork</span>
            </li>
            <li>
              {{ app()->getLocale()==='ar' ? 'المبلغ المطلوب تحويله:' : 'Amount to transfer:' }}
              <span id="cliqAmountDisplay" class="font-bold text-blue-900 bg-white px-2 py-0.5 rounded-lg border border-blue-200 mx-1">—</span>
              JOD
            </li>
            <li>{{ app()->getLocale()==='ar' ? 'خذ لقطة شاشة للإيصال' : 'Take a screenshot of the receipt' }}</li>
            <li>{{ app()->getLocale()==='ar' ? 'ارفع الإيصال أدناه واضغط إرسال' : 'Upload the receipt below and submit' }}</li>
          </ol>
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">
            📎 {{ app()->getLocale()==='ar' ? 'إيصال التحويل (صورة)' : 'Transfer Receipt (image)' }}
            <span class="text-red-500">*</span>
          </label>
          <input type="file" name="proof" id="proofInput" accept="image/*,.pdf"
            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-primary-400 @error('proof') border-red-400 @enderror">
          <p class="text-xs text-gray-400 mt-1">{{ app()->getLocale()==='ar' ? 'JPG، PNG، أو PDF — حد أقصى 5MB' : 'JPG, PNG or PDF — max 5MB' }}</p>
          @error('proof')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mt-3 bg-yellow-50 border border-yellow-200 rounded-xl p-3 text-xs text-yellow-800">
          ⏳ {{ app()->getLocale()==='ar'
            ? 'سيتم مراجعة طلبك من قبل الإدارة وإضافة الرصيد خلال ساعات عمل (الأحد–الخميس 9ص–5م).'
            : 'Your request will be reviewed by our team and funds credited within business hours (Sun–Thu 9am–5pm).' }}
        </div>
      </div>

      {{-- Manual methods note --}}
      <div id="manualBox" class="mb-5 hidden">
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 text-sm text-gray-600">
          {{ app()->getLocale()==='ar'
            ? 'تواصل مع الدعم على support@dopawork.jo لإتمام التحويل اليدوي.'
            : 'Contact support@dopawork.jo to complete a manual transfer.' }}
        </div>
      </div>

      <button type="submit"
        class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3.5 rounded-xl transition-colors text-sm">
        {{ app()->getLocale()==='ar'?'إرسال طلب الإيداع':'Submit Deposit Request' }}
      </button>
    </form>
  </div>
</div>

<script>
function onMethodChange(val) {
  document.getElementById('cliqBox').classList.toggle('hidden', val !== 'cliq');
  document.getElementById('manualBox').classList.toggle('hidden', val === 'cliq');
  document.getElementById('proofInput').required = (val === 'cliq');
  updateCliqAmount();
}
function updateCliqAmount() {
  const amt = parseFloat(document.getElementById('amountInput').value);
  const el = document.getElementById('cliqAmountDisplay');
  if (el) el.textContent = isNaN(amt) ? '—' : amt.toFixed(3);
}
document.getElementById('amountInput').addEventListener('input', updateCliqAmount);
onMethodChange('cliq');
updateCliqAmount();
</script>
@endsection
@extends('layouts.app')
@section('title', app()->getLocale()==='ar' ? 'محفظتي' : 'My Wallet')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
  <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ app()->getLocale()==='ar'?'محفظتي':'My Wallet' }}</h1>

  {{-- Balance Card --}}
  <div class="bg-gradient-to-r from-primary-600 to-primary-500 text-white rounded-2xl p-6 mb-6">
    <p class="text-sm opacity-80 mb-1">{{ app()->getLocale()==='ar'?'الرصيد المتاح':'Available Balance' }}</p>
    <p class="text-4xl font-bold">{{ number_format(auth()->user()->wallet_balance, 3) }} <span class="text-xl opacity-80">JOD</span></p>
    <div class="flex gap-3 mt-5">
      <a href="{{ route('wallet.deposit') }}"
         class="bg-white text-primary-700 px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-primary-50 transition-colors">
        + {{ app()->getLocale()==='ar'?'إيداع':'Deposit' }}
      </a>
      @if(!auth()->user()->isClient())
      <a href="{{ route('wallet.withdraw') }}"
         class="bg-white/20 hover:bg-white/30 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-colors">
        ↑ {{ app()->getLocale()==='ar'?'سحب':'Withdraw' }}
      </a>
      @endif
    </div>
  </div>

  {{-- Transaction History --}}
  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-100">
      <h3 class="font-semibold text-gray-900">{{ app()->getLocale()==='ar'?'سجل المعاملات':'Transaction History' }}</h3>
    </div>

    @if($transactions->isEmpty())
      <div class="p-12 text-center">
        <p class="text-4xl mb-3">💳</p>
        <p class="text-gray-400">{{ app()->getLocale()==='ar'?'لا توجد معاملات بعد':'No transactions yet' }}</p>
      </div>
    @else
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-gray-50 text-xs text-gray-500 uppercase">
              <th class="px-5 py-3 text-start">{{ app()->getLocale()==='ar'?'النوع':'Type' }}</th>
              <th class="px-5 py-3 text-start">{{ app()->getLocale()==='ar'?'الوصف':'Description' }}</th>
              <th class="px-5 py-3 text-start">{{ app()->getLocale()==='ar'?'المبلغ':'Amount' }}</th>
              <th class="px-5 py-3 text-start">{{ app()->getLocale()==='ar'?'التاريخ':'Date' }}</th>
              <th class="px-5 py-3 text-start">{{ app()->getLocale()==='ar'?'الحالة':'Status' }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            @foreach($transactions as $tx)
              <tr class="hover:bg-gray-50">
                <td class="px-5 py-3">
                  @php
                    $typeMap = [
                      'credit' => ['bg-green-100 text-green-800', 'إيداع', 'Credit'],
                      'debit'  => ['bg-red-100 text-red-800',   'خصم',  'Debit'],
                      'escrow_hold'    => ['bg-yellow-100 text-yellow-800', 'ضمان', 'Escrow'],
                      'escrow_release' => ['bg-blue-100 text-blue-800', 'إفراج', 'Released'],
                      'refund' => ['bg-purple-100 text-purple-800', 'استرداد', 'Refund'],
                    ];
                    $t = $typeMap[$tx->type] ?? ['bg-gray-100 text-gray-800', $tx->type, $tx->type];
                  @endphp
                  <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $t[0] }}">
                    {{ app()->getLocale()==='ar' ? $t[1] : $t[2] }}
                  </span>
                </td>
                <td class="px-5 py-3 text-gray-700 max-w-xs">
                  {{ app()->getLocale()==='ar' ? ($tx->description_ar ?? $tx->description) : $tx->description }}
                </td>
                <td class="px-5 py-3 font-semibold {{ in_array($tx->type, ['credit','escrow_release','refund']) ? 'text-green-700' : 'text-red-600' }}">
                  {{ in_array($tx->type, ['credit','escrow_release','refund']) ? '+' : '-' }}{{ number_format($tx->amount, 3) }} JOD
                </td>
                <td class="px-5 py-3 text-gray-400 text-xs">{{ $tx->created_at->format('Y/m/d H:i') }}</td>
                <td class="px-5 py-3">@include('components.status-badge', ['status' => $tx->status])</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="p-4">{{ $transactions->links() }}</div>
    @endif
  </div>
</div>
@endsection

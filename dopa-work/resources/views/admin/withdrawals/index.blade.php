@extends('layouts.admin')
@section('title', app()->getLocale()==='ar' ? 'طلبات السحب' : 'Withdrawal Requests')
@section('content')
@php $ar = app()->getLocale()==='ar'; @endphp
<div class="max-w-7xl mx-auto px-4 py-8" dir="{{ $ar ? 'rtl' : 'ltr' }}">

  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-bold text-gray-900">💸 {{ $ar ? 'طلبات السحب' : 'Withdrawal Requests' }}</h1>
      <p class="text-sm text-gray-500 mt-0.5">{{ $ar ? 'أكد التحويل عبر كليك ثم اضغط "تأكيد التحويل"' : 'Process the transfer via CliQ then click confirm' }}</p>
    </div>
    <span class="text-sm text-gray-500">{{ $withdrawals->total() }} {{ $ar ? 'طلب' : 'requests' }}</span>
  </div>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 mb-5 text-sm">✅ {{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 mb-5 text-sm">❌ {{ session('error') }}</div>
  @endif

  {{-- Summary Cards --}}
  @php
    $pendingCount    = \App\Models\WalletTransaction::where('type','withdrawal')->where('status','pending')->count();
    $completedCount  = \App\Models\WalletTransaction::where('type','withdrawal')->where('status','completed')->count();
    $totalPendingAmt = \App\Models\WalletTransaction::where('type','withdrawal')->where('status','pending')->sum('amount');
    $totalPaidAmt    = \App\Models\WalletTransaction::where('type','withdrawal')->where('status','completed')->sum('amount');
  @endphp
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-4 text-center">
      <p class="text-2xl font-bold text-yellow-800">{{ $pendingCount }}</p>
      <p class="text-sm text-yellow-600 mt-0.5">{{ $ar ? 'معلقة' : 'Pending' }}</p>
      <p class="text-xs text-yellow-500 mt-0.5">{{ number_format($totalPendingAmt, 3) }} JOD</p>
    </div>
    <div class="bg-green-50 border border-green-200 rounded-2xl p-4 text-center">
      <p class="text-2xl font-bold text-green-800">{{ $completedCount }}</p>
      <p class="text-sm text-green-600 mt-0.5">{{ $ar ? 'مكتملة' : 'Completed' }}</p>
      <p class="text-xs text-green-500 mt-0.5">{{ number_format($totalPaidAmt, 3) }} JOD</p>
    </div>
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 text-center col-span-2">
      <p class="text-sm font-semibold text-blue-800 mb-1">📋 {{ $ar ? 'تعليمات السحب' : 'Withdrawal Instructions' }}</p>
      @if($ar)
        <p class="text-xs text-blue-600">١. اقرأ تفاصيل الطلب وانسخ اسم كليك أو الآيبان</p>
        <p class="text-xs text-blue-600">٢. افتح تطبيق بنكك وحوّل المبلغ للمستقل</p>
        <p class="text-xs text-blue-600">٣. ارجع وأضف ملاحظة واضغط "تأكيد التحويل"</p>
      @else
        <p class="text-xs text-blue-600">1. Read the request details and copy the CliQ alias or IBAN</p>
        <p class="text-xs text-blue-600">2. Open your banking app and transfer the amount to the freelancer</p>
        <p class="text-xs text-blue-600">3. Come back, add a note, and click "Confirm Transfer"</p>
      @endif
    </div>
  </div>

  {{-- Filter Tabs --}}
  <div class="flex gap-2 mb-5 flex-wrap">
    @foreach(['all'=> ($ar?'الكل':'All'), 'pending'=>($ar?'⏳ معلقة':'⏳ Pending'), 'completed'=>($ar?'✅ مكتملة':'✅ Completed'), 'failed'=>($ar?'❌ مرفوضة':'❌ Rejected')] as $s => $label)
      <a href="{{ route('admin.withdrawals.index', ['status'=>$s]) }}"
         class="{{ ($status ?? 'all') === $s ? 'bg-orange-500 text-white' : 'bg-white text-gray-600 border border-gray-200' }} px-4 py-2 rounded-xl text-sm font-medium">
        {{ $label }}
      </a>
    @endforeach
  </div>

  {{-- Table --}}
  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    @if($withdrawals->isEmpty())
      <div class="p-16 text-center text-gray-400">
        <span class="text-5xl block mb-3">📭</span>
        <p class="text-sm">لا توجد طلبات سحب</p>
      </div>
    @else
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-gray-50 text-xs text-gray-500 border-b">
              <th class="px-5 py-3 text-start">{{ $ar ? 'المستقل' : 'Freelancer' }}</th>
              <th class="px-5 py-3 text-start">{{ $ar ? 'المبلغ' : 'Amount' }}</th>
              <th class="px-5 py-3 text-start">{{ $ar ? 'وجهة التحويل' : 'Transfer Destination' }}</th>
              <th class="px-5 py-3 text-start">{{ $ar ? 'التاريخ' : 'Date' }}</th>
              <th class="px-5 py-3 text-start">{{ $ar ? 'الحالة' : 'Status' }}</th>
              <th class="px-5 py-3 text-start w-56">{{ $ar ? 'إجراء' : 'Action' }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            @foreach($withdrawals as $w)
            @php
              $meta   = is_array($w->meta) ? $w->meta : (json_decode($w->meta, true) ?? []);
              $method = $meta['method'] ?? 'cliq';
              $dest   = $method === 'cliq' ? ($meta['cliq_alias'] ?? '—') : ($meta['iban'] ?? '—');
              $sc = ['pending'=>['bg-yellow-100','text-yellow-700','⏳ معلق'],'completed'=>['bg-green-100','text-green-700','✅ مكتمل'],'failed'=>['bg-red-100','text-red-700','❌ مرفوض']][$w->status] ?? ['bg-gray-100','text-gray-600',$w->status];
            @endphp
            <tr class="hover:bg-gray-50 align-top {{ $w->status==='pending' ? 'bg-yellow-50/30' : '' }}">
              {{-- Freelancer info --}}
              <td class="px-5 py-4">
                <p class="font-semibold text-gray-900">{{ $w->user->name }}</p>
                <p class="text-xs text-gray-400">{{ $w->user->email }}</p>
                <p class="text-xs text-blue-600 font-medium mt-0.5">
                  {{ $ar ? 'رصيده الحالي:' : 'Current balance:' }} {{ number_format($w->user->wallet_balance, 3) }} JOD
                </p>
              </td>

              {{-- Amount --}}
              <td class="px-5 py-4">
                <p class="text-xl font-bold text-gray-900">{{ number_format($w->amount, 3) }}</p>
                <p class="text-xs text-gray-400">JOD</p>
              </td>

              {{-- Destination (CliQ / IBAN) --}}
              <td class="px-5 py-4">
                @if($method === 'cliq')
                  <div class="inline-flex items-center gap-2 bg-orange-50 border border-orange-200 rounded-xl px-3 py-2">
                    <span class="text-orange-700 text-xs font-bold">📱 {{ $ar ? 'كليك' : 'CliQ' }}</span>
                    <span class="font-mono text-sm font-semibold text-orange-900" id="dest-{{$w->id}}">{{ $dest }}</span>
                    <button onclick="navigator.clipboard.writeText('{{ $dest }}'); this.textContent='✓'; setTimeout(()=>this.textContent='{{ $ar ? 'نسخ' : 'Copy' }}',1500)"
                            class="text-[10px] bg-orange-500 text-white px-2 py-0.5 rounded-lg hover:bg-orange-600">{{ $ar ? 'نسخ' : 'Copy' }}</button>
                  </div>
                @else
                  <div class="inline-flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2">
                    <span class="text-gray-600 text-xs font-bold">🏦 {{ $ar ? 'آيبان' : 'IBAN' }}</span>
                    <span class="font-mono text-xs text-gray-800" id="dest-{{$w->id}}">{{ $dest }}</span>
                    <button onclick="navigator.clipboard.writeText('{{ $dest }}'); this.textContent='✓'; setTimeout(()=>this.textContent='{{ $ar ? 'نسخ' : 'Copy' }}',1500)"
                            class="text-[10px] bg-gray-600 text-white px-2 py-0.5 rounded-lg hover:bg-gray-700">{{ $ar ? 'نسخ' : 'Copy' }}</button>
                  </div>
                @endif
              </td>

              {{-- Date --}}
              <td class="px-5 py-4 text-xs text-gray-400 whitespace-nowrap">
                {{ $w->created_at->format('Y/m/d') }}<br>{{ $w->created_at->format('H:i') }}
              </td>

              {{-- Status --}}
              <td class="px-5 py-4">
                <span class="text-xs px-2.5 py-1 rounded-full {{ $sc[0] }} {{ $sc[1] }} font-medium">{{ $sc[2] }}</span>
                @if($w->notes)
                  <p class="text-xs text-gray-400 mt-1">{{ $w->notes }}</p>
                @endif
              </td>

              {{-- Action --}}
              <td class="px-5 py-4">
                @if($w->status === 'pending')
                  <form method="POST" action="{{ route('admin.withdrawals.process', $w) }}" class="space-y-2">
                    @csrf
                    <input type="hidden" name="action" id="act-{{$w->id}}" value="completed">
                    <input type="text" name="notes" placeholder="رقم العملية / ملاحظة..."
                           class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:border-blue-400">
                    <div class="flex gap-1.5">
                      <button type="submit"
                              onclick="document.getElementById('act-{{$w->id}}').value='completed'; return confirm('تأكيد إتمام تحويل {{ number_format($w->amount,3) }} JOD إلى {{ $w->user->name }}؟')"
                              class="flex-1 bg-green-500 hover:bg-green-600 text-white text-xs font-medium py-1.5 rounded-lg transition-colors">
                        ✓ تأكيد التحويل
                      </button>
                      <button type="submit"
                              onclick="document.getElementById('act-{{$w->id}}').value='failed'; return confirm('رفض طلب السحب؟')"
                              class="flex-1 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-medium py-1.5 rounded-lg transition-colors">
                        ✕ رفض
                      </button>
                    </div>
                  </form>
                @else
                  <span class="text-xs text-gray-400">{{ $w->notes ?? '—' }}</span>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="p-4">{{ $withdrawals->links() }}</div>
    @endif
  </div>
</div>
@endsection
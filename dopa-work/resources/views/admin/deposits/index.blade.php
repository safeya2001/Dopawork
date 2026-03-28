@extends('layouts.admin')
@section('title', app()->getLocale()==='ar' ? 'طلبات الإيداع' : 'Deposit Requests')

@section('content')
@php $ar = app()->getLocale()==='ar'; @endphp
<div class="max-w-7xl mx-auto px-4 py-8">

  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">💳 {{ $ar ? 'طلبات الإيداع' : 'Deposit Requests' }}</h1>
    <span class="text-sm text-gray-500">{{ $deposits->total() }} {{ $ar ? 'طلب' : 'requests' }}</span>
  </div>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 mb-5 text-sm">✅ {{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 mb-5 text-sm">❌ {{ session('error') }}</div>
  @endif

  @php
    $pendingCount   = \App\Models\WalletTransaction::where('type','deposit')->where('status','pending')->count();
    $completedCount = \App\Models\WalletTransaction::where('type','deposit')->where('status','completed')->count();
    $pendingAmt     = \App\Models\WalletTransaction::where('type','deposit')->where('status','pending')->sum('amount');
  @endphp

  <div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-4 text-center">
      <p class="text-2xl font-bold text-yellow-800">{{ $pendingCount }}</p>
      <p class="text-sm text-yellow-600 mt-1">{{ $ar ? 'معلقة' : 'Pending' }}</p>
      <p class="text-xs text-yellow-500 mt-0.5">{{ number_format($pendingAmt, 3) }} JOD</p>
    </div>
    <div class="bg-green-50 border border-green-200 rounded-2xl p-4 text-center">
      <p class="text-2xl font-bold text-green-800">{{ $completedCount }}</p>
      <p class="text-sm text-green-600 mt-1">{{ $ar ? 'مقبولة' : 'Approved' }}</p>
    </div>
    <div class="bg-gray-50 border border-gray-200 rounded-2xl p-4 text-center">
      <p class="text-2xl font-bold text-gray-800">{{ $pendingCount + $completedCount }}</p>
      <p class="text-sm text-gray-600 mt-1">{{ $ar ? 'الإجمالي' : 'Total' }}</p>
    </div>
  </div>

  <div class="flex gap-2 mb-5">
    <a href="{{ route('admin.deposits.index') }}"
       class="{{ !request('status') || request('status')=='pending' ? 'bg-orange-500 text-white' : 'bg-white text-gray-600 border border-gray-200' }} px-4 py-2 rounded-xl text-sm font-medium">
      ⏳ {{ $ar ? 'معلقة' : 'Pending' }}
    </a>
    <a href="{{ route('admin.deposits.index', ['status'=> 'completed']) }}"
       class="{{ request('status')=='completed' ? 'bg-orange-500 text-white' : 'bg-white text-gray-600 border border-gray-200' }} px-4 py-2 rounded-xl text-sm font-medium">
      ✅ {{ $ar ? 'مقبولة' : 'Approved' }}
    </a>
    <a href="{{ route('admin.deposits.index', ['status'=> 'cancelled']) }}"
       class="{{ request('status')=='cancelled' ? 'bg-orange-500 text-white' : 'bg-white text-gray-600 border border-gray-200' }} px-4 py-2 rounded-xl text-sm font-medium">
      ❌ {{ $ar ? 'مرفوضة' : 'Rejected' }}
    </a>
  </div>

  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    @if($deposits->isEmpty())
      <div class="p-12 text-center text-gray-400">
        <span class="text-4xl block mb-2">📫</span>
        <p class="text-sm">لا توجد طلبات إيداع</p>
      </div>
    @else
      <table class="w-full text-sm">
        <thead>
          <tr class="bg-gray-50 text-xs text-gray-500 border-b">
            <th class="px-5 py-3 text-start">{{ $ar ? 'المستخدم' : 'User' }}</th>
            <th class="px-5 py-3 text-start">{{ $ar ? 'المبلغ' : 'Amount' }}</th>
            <th class="px-5 py-3 text-start">{{ $ar ? 'البيان' : 'Description' }}</th>
            <th class="px-5 py-3 text-start">{{ $ar ? 'الإيصال' : 'Receipt' }}</th>
            <th class="px-5 py-3 text-start">{{ $ar ? 'التاريخ' : 'Date' }}</th>
            <th class="px-5 py-3 text-start">{{ $ar ? 'الحالة' : 'Status' }}</th>
            <th class="px-5 py-3 text-start">{{ $ar ? 'إجراء' : 'Action' }}</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          @foreach($deposits as $tx)
          <tr class="hover:bg-gray-50 align-top {{ $tx->status==='pending' ? 'bg-yellow-50/30' : '' }}">
            <td class="px-5 py-4">
              <p class="font-semibold text-gray-900">{{ $tx->user->name }}</p>
              <p class="text-xs text-gray-400">{{ $tx->user->email }}</p>
            </td>
            <td class="px-5 py-4">
              <p class="text-lg font-bold text-orange-600">{{ number_format($tx->amount, 3) }}</p>
              <p class="text-xs text-gray-400">JOD</p>
            </td>
            <td class="px-5 py-4 text-xs text-gray-600 max-w-xs">
              {{ $tx->description_ar ?? $tx->description ?? '—' }}
            </td>
            <td class="px-5 py-4">
              @if($tx->proof_path)
                <a href="{{ Storage::url($tx->proof_path) }}" target="_blank"
                   class="text-orange-600 hover:underline text-xs">📎 عرض الإيصال</a>
              @else
                <span class="text-gray-400 text-xs">—</span>
              @endif
            </td>
            <td class="px-5 py-4 text-xs text-gray-400 whitespace-nowrap">
              {{ $tx->created_at->format('Y/m/d') }}<br>{{ $tx->created_at->format('H:i') }}
            </td>
            <td class="px-5 py-4">
              @if($tx->status==='pending')
                <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-700 font-medium">⏳ {{ $ar ? 'معلق' : 'Pending' }}</span>
              @elseif($tx->status==='completed')
                <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-medium">✅ {{ $ar ? 'مقبول' : 'Approved' }}</span>
              @else
                <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-700 font-medium">❌ {{ $ar ? 'مرفوض' : 'Rejected' }}</span>
              @endif
            </td>
            <td class="px-5 py-4">
              @if($tx->status==='pending')
                <div class="flex flex-col gap-1.5">
                  <form method="POST" action="{{ route('admin.deposits.approve', $tx) }}">
                    @csrf
                    <button class="w-full bg-green-500 hover:bg-green-600 text-white text-xs px-3 py-1.5 rounded-lg">✓ {{ $ar ? 'قبول' : 'Approve' }}</button>
                  </form>
                  <button onclick="document.getElementById('dep-{{$tx->id}}').classList.toggle('hidden')"
                    class="bg-red-100 hover:bg-red-200 text-red-700 text-xs px-3 py-1.5 rounded-lg">✕ {{ $ar ? 'رفض' : 'Reject' }}</button>
                  <div id="dep-{{$tx->id}}" class="hidden mt-1">
                    <form method="POST" action="{{ route('admin.deposits.reject', $tx) }}">
                      @csrf
                      <textarea name="admin_note" required rows="2" placeholder="{{ $ar ? 'سبب الرفض...' : 'Reason for rejection...' }}"
                        class="w-full border rounded-lg px-2 py-1.5 text-xs focus:outline-none resize-none mb-1"></textarea>
                      <button class="w-full bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1.5 rounded-lg">{{ $ar ? 'تأكيد الرفض' : 'Confirm Reject' }}</button>
                    </form>
                  </div>
                </div>
              @elseif($tx->admin_note)
                <p class="text-xs text-gray-400">{{ $tx->admin_note }}</p>
              @else
                <span class="text-xs text-gray-400">—</span>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      <div class="p-4">{{ $deposits->links() }}</div>
    @endif
  </div>
</div>
@endsection

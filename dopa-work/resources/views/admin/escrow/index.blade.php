@extends('layouts.admin')
@section('title', 'إدارة الضمان')
@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
  <h1 class="text-2xl font-bold text-gray-900 mb-6">🔒 إدارة الضمان (Escrow)</h1>

  <div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-5 text-center">
      <p class="text-xs text-yellow-600 mb-1">محجوز حالياً</p>
      <p class="text-2xl font-bold text-yellow-800">{{ number_format($summary['held'],3) }} JOD</p>
    </div>
    <div class="bg-green-50 border border-green-200 rounded-2xl p-5 text-center">
      <p class="text-xs text-green-600 mb-1">تم الإفراج</p>
      <p class="text-2xl font-bold text-green-800">{{ number_format($summary['released'],3) }} JOD</p>
    </div>
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 text-center">
      <p class="text-xs text-blue-600 mb-1">مسترد</p>
      <p class="text-2xl font-bold text-blue-800">{{ number_format($summary['refunded'],3) }} JOD</p>
    </div>
  </div>

  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    @if($escrows->isEmpty())
      <div class="p-12 text-center text-gray-400 text-sm">لا توجد معاملات ضمان</div>
    @else
      <table class="w-full text-sm">
        <thead><tr class="bg-gray-50 text-xs text-gray-500 uppercase border-b">
          <th class="px-5 py-3 text-start">الطلب</th>
          <th class="px-5 py-3 text-start">العميل</th>
          <th class="px-5 py-3 text-start">المستقل</th>
          <th class="px-5 py-3 text-start">المبلغ</th>
          <th class="px-5 py-3 text-start">رسوم المنصة</th>
          <th class="px-5 py-3 text-start">الحالة</th>
          <th class="px-5 py-3 text-start">الإفراج التلقائي</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-50">
          @foreach($escrows as $e)
          <tr class="hover:bg-gray-50">
            <td class="px-5 py-4">
              <a href="{{ route('admin.orders.show', $e->order) }}" class="text-primary-600 font-semibold text-xs hover:underline">
                {{ $e->order->order_number }}
              </a>
            </td>
            <td class="px-5 py-4 text-gray-700 text-xs">{{ $e->order->client?->name }}</td>
            <td class="px-5 py-4 text-gray-700 text-xs">{{ $e->order->freelancer?->name }}</td>
            <td class="px-5 py-4 font-bold text-gray-900">{{ number_format($e->amount,3) }} JOD</td>
            <td class="px-5 py-4 text-orange-600 text-xs">{{ number_format($e->platform_fee,3) }} JOD</td>
            <td class="px-5 py-4">@include('components.status-badge', ['status'=>$e->status])</td>
            <td class="px-5 py-4 text-gray-400 text-xs">{{ $e->auto_release_at?->format('Y/m/d') ?? '—' }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
      <div class="p-4">{{ $escrows->links() }}</div>
    @endif
  </div>
</div>
@endsection

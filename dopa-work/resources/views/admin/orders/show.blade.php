@extends('layouts.admin')
@section('title', $order->order_number)
@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
  <div class="flex items-center gap-3 mb-6">
    <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-400 hover:text-primary-600">← الطلبات</a>
    <span class="text-gray-300">/</span>
    <span class="text-gray-600 font-mono text-sm">{{ $order->order_number }}</span>
    @include('components.status-badge', ['status'=>$order->status])
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-5">
      {{-- Header --}}
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <h1 class="text-lg font-bold text-gray-900 mb-1">{{ $order->title ?? $order->service?->title }}</h1>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4 pt-4 border-t border-gray-50 text-sm">
          <div><p class="text-gray-400 text-xs">المبلغ الكلي</p><p class="font-bold text-primary-700">{{ number_format($order->total_amount,3) }} JOD</p></div>
          <div><p class="text-gray-400 text-xs">رسوم المنصة</p><p class="font-semibold text-orange-600">{{ number_format($order->platform_fee,3) }} JOD</p></div>
          <div><p class="text-gray-400 text-xs">للمستقل</p><p class="font-semibold text-green-700">{{ number_format($order->freelancer_earnings,3) }} JOD</p></div>
          <div><p class="text-gray-400 text-xs">الموعد</p><p class="font-semibold text-gray-800">{{ $order->deadline?->format('Y/m/d') ?? '—' }}</p></div>
        </div>
      </div>

      {{-- Requirements --}}
      @if($order->requirements)
      <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <h3 class="font-semibold text-gray-900 mb-2 text-sm">المتطلبات</h3>
        <p class="text-gray-600 text-sm whitespace-pre-line">{{ $order->requirements }}</p>
      </div>
      @endif

      {{-- Review --}}
      @if($order->review)
      <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-5">
        <h3 class="font-semibold text-yellow-900 mb-2 text-sm">⭐ التقييم</h3>
        <div class="flex items-center gap-1 mb-1">
          @for($i=1;$i<=5;$i++)<span class="{{ $i<=$order->review->rating?'text-yellow-400':'text-gray-300' }}">★</span>@endfor
          <span class="text-sm text-gray-500 ms-2">{{ $order->review->rating }}/5</span>
        </div>
        @if($order->review->comment)<p class="text-sm text-gray-700">{{ $order->review->comment }}</p>@endif
      </div>
      @endif
    </div>

    {{-- Sidebar --}}
    <div class="space-y-5">
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-sm space-y-3">
        <div><p class="text-gray-400 text-xs mb-1">العميل</p><p class="font-semibold">{{ $order->client?->name }}</p><p class="text-xs text-gray-400">{{ $order->client?->email }}</p></div>
        <div><p class="text-gray-400 text-xs mb-1">المستقل</p><p class="font-semibold">{{ $order->freelancer?->name }}</p><p class="text-xs text-gray-400">{{ $order->freelancer?->email }}</p></div>
      </div>

      @if($order->escrow)
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-sm">
        <h3 class="font-semibold mb-3 text-xs text-gray-500 uppercase">الضمان</h3>
        <div class="space-y-2">
          <div class="flex justify-between"><span class="text-gray-500">الحالة</span>@include('components.status-badge', ['status'=>$order->escrow->status])</div>
          <div class="flex justify-between"><span class="text-gray-500">المبلغ</span><span class="font-semibold">{{ number_format($order->escrow->amount,3) }} JOD</span></div>
        </div>
      </div>
      @endif

      @if($order->dispute)
      <div class="bg-red-50 border border-red-200 rounded-2xl p-5 text-sm">
        <h3 class="font-semibold text-red-900 mb-2">⚠️ نزاع</h3>
        <p class="text-red-700 text-xs">{{ $order->dispute->reason }}</p>
        <a href="{{ route('admin.disputes.show', $order->dispute) }}" class="text-red-600 text-xs underline mt-2 block">إدارة النزاع →</a>
      </div>
      @endif
    </div>
  </div>
</div>
@endsection

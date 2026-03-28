@extends('layouts.app')
@section('title', app()->getLocale()==='ar' ? 'الطلبات الواردة' : 'Incoming Orders')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
  <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ app()->getLocale()==='ar'?'الطلبات الواردة':'Incoming Orders' }}</h1>

  <div class="flex gap-2 mb-5 overflow-x-auto pb-1">
    @foreach([null=>(app()->getLocale()==='ar'?'الكل':'All'),'pending'=>(app()->getLocale()==='ar'?'معلق':'Pending'),'in_progress'=>(app()->getLocale()==='ar'?'جاري':'Active'),'delivered'=>(app()->getLocale()==='ar'?'مسلّم':'Delivered'),'completed'=>(app()->getLocale()==='ar'?'مكتمل':'Completed')] as $val => $label)
      <a href="{{ route('freelancer.orders.index', $val ? ['status'=>$val] : []) }}"
         class="flex-shrink-0 px-4 py-1.5 rounded-full text-sm font-medium border transition-colors
           {{ request('status','') === ($val ?? '') ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-gray-600 border-gray-200 hover:border-primary-300' }}">
        {{ $label }}
      </a>
    @endforeach
  </div>

  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    @if($orders->isEmpty())
      <div class="p-12 text-center">
        <p class="text-5xl mb-4">📭</p>
        <p class="text-gray-400">{{ app()->getLocale()==='ar'?'لا توجد طلبات':'No orders yet' }}</p>
      </div>
    @else
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead><tr class="bg-gray-50 text-xs text-gray-500 uppercase">
            <th class="px-5 py-3 text-start">{{ app()->getLocale()==='ar'?'الطلب':'Order' }}</th>
            <th class="px-5 py-3 text-start">{{ app()->getLocale()==='ar'?'العميل':'Client' }}</th>
            <th class="px-5 py-3 text-start">{{ app()->getLocale()==='ar'?'الحالة':'Status' }}</th>
            <th class="px-5 py-3 text-start">{{ app()->getLocale()==='ar'?'أرباحك':'Earnings' }}</th>
            <th class="px-5 py-3 text-start">{{ app()->getLocale()==='ar'?'الموعد':'Deadline' }}</th>
            <th class="px-5 py-3"></th>
          </tr></thead>
          <tbody class="divide-y divide-gray-50">
            @foreach($orders as $order)
              <tr class="hover:bg-gray-50">
                <td class="px-5 py-3">
                  <a href="{{ route('freelancer.orders.show', $order) }}" class="text-primary-600 hover:underline font-medium">{{ $order->order_number }}</a>
                  <p class="text-xs text-gray-400 truncate max-w-xs">{{ $order->service?->title }}</p>
                </td>
                <td class="px-5 py-3">
                  <div class="flex items-center gap-2">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($order->client?->name) }}&size=28&color=3b82f6&background=dbeafe" class="w-7 h-7 rounded-full">
                    <span class="text-gray-700">{{ $order->client?->name }}</span>
                  </div>
                </td>
                <td class="px-5 py-3">@include('components.status-badge', ['status' => $order->status])</td>
                <td class="px-5 py-3 font-semibold text-green-700">{{ number_format($order->freelancer_earnings,3) }} JOD</td>
                <td class="px-5 py-3 text-gray-400 text-xs">{{ $order->deadline?->format('Y/m/d') ?? '—' }}</td>
                <td class="px-5 py-3">
                  <a href="{{ route('freelancer.orders.show', $order) }}" class="text-xs text-primary-600 font-medium">{{ app()->getLocale()==='ar'?'عرض':'View' }} →</a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="p-4">{{ $orders->links() }}</div>
    @endif
  </div>
</div>
@endsection

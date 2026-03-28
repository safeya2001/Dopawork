@extends('layouts.app')
@section('title', app()->getLocale()==='ar' ? 'طلباتي' : 'My Orders')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">{{ app()->getLocale()==='ar' ? 'طلباتي' : 'My Orders' }}</h1>
    <a href="{{ route('services.index') }}" class="bg-primary-600 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-primary-700 transition-colors">
      + {{ app()->getLocale()==='ar' ? 'طلب جديد' : 'New Order' }}
    </a>
  </div>

  {{-- Status filter --}}
  <div class="flex gap-2 mb-5 overflow-x-auto pb-1">
    @foreach([null=>(app()->getLocale()==='ar'?'الكل':'All'),'pending'=>(app()->getLocale()==='ar'?'معلق':'Pending'),'in_progress'=>(app()->getLocale()==='ar'?'جاري':'Active'),'completed'=>(app()->getLocale()==='ar'?'مكتمل':'Completed'),'cancelled'=>(app()->getLocale()==='ar'?'ملغى':'Cancelled')] as $val => $label)
      <a href="{{ route('client.orders.index', $val ? ['status'=>$val] : []) }}"
         class="flex-shrink-0 px-4 py-1.5 rounded-full text-sm font-medium border transition-colors
           {{ request('status', '') === ($val ?? '') ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-gray-600 border-gray-200 hover:border-primary-300' }}">
        {{ $label }}
      </a>
    @endforeach
  </div>

  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    @if($orders->isEmpty())
      <div class="p-12 text-center">
        <p class="text-5xl mb-4">📋</p>
        <p class="text-gray-400">{{ app()->getLocale()==='ar' ? 'لا توجد طلبات' : 'No orders found' }}</p>
        <a href="{{ route('services.index') }}" class="mt-3 inline-block text-primary-600 text-sm hover:underline">
          {{ app()->getLocale()==='ar' ? 'تصفح الخدمات' : 'Browse Services' }}
        </a>
      </div>
    @else
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead><tr class="bg-gray-50 text-xs text-gray-500 uppercase">
            <th class="px-5 py-3 text-start">{{ app()->getLocale()==='ar'?'الطلب':'Order' }}</th>
            <th class="px-5 py-3 text-start">{{ app()->getLocale()==='ar'?'الخدمة':'Service' }}</th>
            <th class="px-5 py-3 text-start">{{ app()->getLocale()==='ar'?'المستقل':'Freelancer' }}</th>
            <th class="px-5 py-3 text-start">{{ app()->getLocale()==='ar'?'الحالة':'Status' }}</th>
            <th class="px-5 py-3 text-start">{{ app()->getLocale()==='ar'?'المبلغ':'Amount' }}</th>
            <th class="px-5 py-3 text-start">{{ app()->getLocale()==='ar'?'الموعد':'Deadline' }}</th>
            <th class="px-5 py-3"></th>
          </tr></thead>
          <tbody class="divide-y divide-gray-50">
            @foreach($orders as $order)
              <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-5 py-3">
                  <a href="{{ route('client.orders.show', $order) }}" class="text-primary-600 hover:underline font-medium">{{ $order->order_number }}</a>
                </td>
                <td class="px-5 py-3 text-gray-700 max-w-xs truncate">{{ $order->service?->title }}</td>
                <td class="px-5 py-3">
                  <div class="flex items-center gap-2">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($order->freelancer?->name) }}&size=28&color=3b82f6&background=dbeafe" class="w-7 h-7 rounded-full">
                    <span class="text-gray-700">{{ $order->freelancer?->name }}</span>
                  </div>
                </td>
                <td class="px-5 py-3">@include('components.status-badge', ['status' => $order->status])</td>
                <td class="px-5 py-3 font-semibold text-gray-900">{{ number_format($order->total_amount,3) }} JOD</td>
                <td class="px-5 py-3 text-gray-400 text-xs">{{ $order->deadline?->format('Y/m/d') ?? '—' }}</td>
                <td class="px-5 py-3">
                  <a href="{{ route('client.orders.show', $order) }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium">
                    {{ app()->getLocale()==='ar'?'عرض':'View' }} →
                  </a>
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

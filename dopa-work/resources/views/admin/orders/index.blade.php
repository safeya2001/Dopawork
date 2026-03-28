@extends('layouts.admin')
@section('title', 'إدارة الطلبات')
@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
  <h1 class="text-2xl font-bold text-gray-900 mb-6">📋 إدارة الطلبات</h1>

  <form method="GET" class="flex gap-3 mb-5 flex-wrap">
    <input name="search" value="{{ request('search') }}" placeholder="رقم الطلب..." class="border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-primary-400 w-48">
    <select name="status" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none">
      <option value="">كل الحالات</option>
      @foreach(['pending'=>'معلق','in_progress'=>'جاري','delivered'=>'مسلّم','revision'=>'تعديل','completed'=>'مكتمل','cancelled'=>'ملغي'] as $s=>$l)
        <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ $l }}</option>
      @endforeach
    </select>
    <button class="bg-primary-600 text-white px-4 py-2 rounded-xl text-sm">بحث</button>
    <a href="{{ route('admin.orders.index') }}" class="border border-gray-200 text-gray-600 px-4 py-2 rounded-xl text-sm">إعادة تعيين</a>
  </form>

  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    @if($orders->isEmpty())
      <div class="p-12 text-center text-gray-400 text-sm">لا توجد طلبات</div>
    @else
      <table class="w-full text-sm">
        <thead><tr class="bg-gray-50 text-xs text-gray-500 uppercase border-b">
          <th class="px-5 py-3 text-start">رقم الطلب</th>
          <th class="px-5 py-3 text-start">العميل</th>
          <th class="px-5 py-3 text-start">المستقل</th>
          <th class="px-5 py-3 text-start">الخدمة</th>
          <th class="px-5 py-3 text-start">المبلغ</th>
          <th class="px-5 py-3 text-start">الحالة</th>
          <th class="px-5 py-3 text-start">التاريخ</th>
          <th class="px-5 py-3 text-start"></th>
        </tr></thead>
        <tbody class="divide-y divide-gray-50">
          @foreach($orders as $order)
          <tr class="hover:bg-gray-50">
            <td class="px-5 py-4 font-mono text-xs text-primary-700 font-semibold">{{ $order->order_number }}</td>
            <td class="px-5 py-4 text-gray-700 text-xs">{{ $order->client?->name }}</td>
            <td class="px-5 py-4 text-gray-700 text-xs">{{ $order->freelancer?->name }}</td>
            <td class="px-5 py-4 text-gray-600 text-xs max-w-xs truncate">{{ $order->service?->title }}</td>
            <td class="px-5 py-4 font-semibold text-gray-900">{{ number_format($order->total_amount,3) }} JOD</td>
            <td class="px-5 py-4">@include('components.status-badge', ['status'=>$order->status])</td>
            <td class="px-5 py-4 text-gray-400 text-xs">{{ $order->created_at->format('Y/m/d') }}</td>
            <td class="px-5 py-4">
              <a href="{{ route('admin.orders.show', $order) }}" class="text-primary-600 text-xs hover:underline">تفاصيل →</a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      <div class="p-4">{{ $orders->links() }}</div>
    @endif
  </div>
</div>
@endsection

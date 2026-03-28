@extends('layouts.admin')
@section('title', 'النزاعات')
@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
  <h1 class="text-2xl font-bold text-gray-900 mb-6">⚠️ إدارة النزاعات</h1>

  <div class="flex gap-2 mb-5">
    @foreach(['open'=>'مفتوح','under_review'=>'قيد المراجعة','resolved'=>'محلول','closed'=>'مغلق'] as $s=>$l)
      <a href="{{ route('admin.disputes.index', ['status'=>$s]) }}"
         class="{{ request('status')===$s?'bg-primary-600 text-white':'bg-white text-gray-600 border border-gray-200' }} px-4 py-2 rounded-xl text-sm font-medium">
        {{ $l }}
      </a>
    @endforeach
    <a href="{{ route('admin.disputes.index') }}" class="{{ !request('status')?'bg-primary-600 text-white':'bg-white text-gray-600 border border-gray-200' }} px-4 py-2 rounded-xl text-sm font-medium">الكل</a>
  </div>

  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    @if($disputes->isEmpty())
      <div class="p-12 text-center text-gray-400 text-sm">لا توجد نزاعات</div>
    @else
      <table class="w-full text-sm">
        <thead><tr class="bg-gray-50 text-xs text-gray-500 uppercase border-b">
          <th class="px-5 py-3 text-start">الطلب</th>
          <th class="px-5 py-3 text-start">رافع النزاع</th>
          <th class="px-5 py-3 text-start">السبب</th>
          <th class="px-5 py-3 text-start">المبلغ</th>
          <th class="px-5 py-3 text-start">الحالة</th>
          <th class="px-5 py-3 text-start">التاريخ</th>
          <th class="px-5 py-3 text-start"></th>
        </tr></thead>
        <tbody class="divide-y divide-gray-50">
          @foreach($disputes as $d)
          <tr class="hover:bg-gray-50">
            <td class="px-5 py-4 font-mono text-xs text-primary-700 font-semibold">{{ $d->order?->order_number }}</td>
            <td class="px-5 py-4 text-gray-700 text-xs">{{ $d->raisedBy?->name }}</td>
            <td class="px-5 py-4 text-gray-600 text-xs max-w-xs truncate">{{ $d->reason }}</td>
            <td class="px-5 py-4 font-semibold">{{ number_format($d->order?->total_amount,3) }} JOD</td>
            <td class="px-5 py-4">@include('components.status-badge', ['status'=>$d->status])</td>
            <td class="px-5 py-4 text-gray-400 text-xs">{{ $d->created_at->format('Y/m/d') }}</td>
            <td class="px-5 py-4"><a href="{{ route('admin.disputes.show', $d) }}" class="text-primary-600 text-xs hover:underline">إدارة →</a></td>
          </tr>
          @endforeach
        </tbody>
      </table>
      <div class="p-4">{{ $disputes->links() }}</div>
    @endif
  </div>
</div>
@endsection

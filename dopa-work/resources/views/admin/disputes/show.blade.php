@extends('layouts.admin')
@section('title', 'تفاصيل النزاع')
@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
  <a href="{{ route('admin.disputes.index') }}" class="text-sm text-gray-400 hover:text-primary-600 mb-5 block">← النزاعات</a>

  @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 mb-5 text-sm">✅ {{ session('success') }}</div>@endif

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-5">
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-start justify-between mb-4">
          <div>
            <h2 class="font-bold text-gray-900">نزاع على الطلب: {{ $dispute->order?->order_number }}</h2>
            <p class="text-xs text-gray-400 mt-1">رُفع بواسطة: {{ $dispute->raisedBy?->name }} ({{ $dispute->raisedBy?->role }})</p>
          </div>
          @include('components.status-badge', ['status'=>$dispute->status])
        </div>
        <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-700">
          <p class="font-semibold text-gray-600 text-xs mb-2">سبب النزاع:</p>
          {{ $dispute->reason }}
        </div>
      </div>

      @if($dispute->status === 'open' || $dispute->status === 'under_review')
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <h3 class="font-semibold text-gray-900 mb-4">🔧 حل النزاع</h3>
        <form method="POST" action="{{ route('admin.disputes.resolve', $dispute) }}">
          @csrf
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">القرار</label>
            <select name="resolution" required class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-primary-400">
              <option value="">اختر القرار...</option>
              <option value="refund_client">استرداد المبلغ كاملاً للعميل</option>
              <option value="release_freelancer">إفراج المبلغ للمستقل</option>
              <option value="partial_split">تقسيم جزئي</option>
              <option value="no_action">إغلاق بدون إجراء</option>
            </select>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات القرار</label>
            <textarea name="resolution_notes" required rows="3"
              class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-primary-400 resize-none"
              placeholder="اشرح قرارك..."></textarea>
          </div>
          <button class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2.5 rounded-xl text-sm font-medium transition-colors">
            تأكيد القرار
          </button>
        </form>
      </div>
      @elseif($dispute->resolution_notes)
      <div class="bg-green-50 border border-green-200 rounded-2xl p-5">
        <h3 class="font-semibold text-green-900 mb-2">✅ تم الحل</h3>
        <p class="text-sm text-green-800">{{ $dispute->resolution_notes }}</p>
        <p class="text-xs text-green-600 mt-2">بواسطة: {{ $dispute->resolvedBy?->name }}</p>
      </div>
      @endif
    </div>

    <div class="space-y-5">
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-sm space-y-3">
        <h3 class="font-semibold text-gray-900 text-sm">تفاصيل الطلب</h3>
        <div class="flex justify-between"><span class="text-gray-400">العميل</span><span>{{ $dispute->order?->client?->name }}</span></div>
        <div class="flex justify-between"><span class="text-gray-400">المستقل</span><span>{{ $dispute->order?->freelancer?->name }}</span></div>
        <div class="flex justify-between"><span class="text-gray-400">المبلغ</span><span class="font-bold text-primary-700">{{ number_format($dispute->order?->total_amount,3) }} JOD</span></div>
        @if($dispute->order?->escrow)
          <div class="flex justify-between"><span class="text-gray-400">حالة الضمان</span>@include('components.status-badge', ['status'=>$dispute->order->escrow->status])</div>
        @endif
        <a href="{{ route('admin.orders.show', $dispute->order) }}" class="block text-center text-primary-600 text-xs hover:underline pt-2 border-t border-gray-50">
          عرض تفاصيل الطلب →
        </a>
      </div>
    </div>
  </div>
</div>
@endsection

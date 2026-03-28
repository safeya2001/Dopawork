@extends('layouts.app')
@section('title', $order->order_number)

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
  <nav class="text-sm text-gray-400 mb-5 flex items-center gap-2">
    <a href="{{ route('freelancer.dashboard') }}" class="hover:text-primary-600">{{ app()->getLocale()==='ar'?'لوحتي':'Dashboard' }}</a>
    <span>/</span>
    <a href="{{ route('freelancer.orders.index') }}" class="hover:text-primary-600">{{ app()->getLocale()==='ar'?'الطلبات':'Orders' }}</a>
    <span>/</span>
    <span class="text-gray-600">{{ $order->order_number }}</span>
  </nav>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-5">

      {{-- Header --}}
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-start justify-between flex-wrap gap-3">
          <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $order->order_number }}</h1>
            <p class="text-gray-500 text-sm mt-1">{{ $order->title }}</p>
          </div>
          @include('components.status-badge', ['status' => $order->status])
        </div>
        <div class="grid grid-cols-3 gap-4 mt-4 pt-4 border-t border-gray-50 text-sm">
          <div>
            <p class="text-gray-400 text-xs">{{ app()->getLocale()==='ar'?'أرباحك':'Your Earnings' }}</p>
            <p class="font-bold text-green-700">{{ number_format($order->freelancer_earnings,3) }} JOD</p>
          </div>
          <div>
            <p class="text-gray-400 text-xs">{{ app()->getLocale()==='ar'?'الموعد':'Deadline' }}</p>
            <p class="font-semibold text-gray-800">{{ $order->deadline?->format('Y/m/d') ?? '—' }}</p>
          </div>
          <div>
            <p class="text-gray-400 text-xs">{{ app()->getLocale()==='ar'?'التعديلات':'Revisions' }}</p>
            <p class="font-semibold text-gray-800">{{ $order->revisions_used }}/{{ $order->revisions_allowed }}</p>
          </div>
        </div>
      </div>

      {{-- Requirements --}}
      @if($order->requirements)
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
          <h3 class="font-semibold text-gray-900 mb-2">{{ app()->getLocale()==='ar'?'متطلبات العميل':'Client Requirements' }}</h3>
          <p class="text-gray-600 text-sm whitespace-pre-line">{{ $order->requirements }}</p>
        </div>
      @endif

      {{-- Action: Start Order --}}
      @if($order->status === 'pending')
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5">
          <p class="text-sm text-blue-700 mb-3">{{ app()->getLocale()==='ar'?'طلب جديد! ابدأ العمل لإعلام العميل.':'New order! Start working to notify the client.' }}</p>
          <form method="POST" action="{{ route('freelancer.orders.start', $order) }}">
            @csrf
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-medium transition-colors">
              ▶ {{ app()->getLocale()==='ar'?'بدء العمل':'Start Working' }}
            </button>
          </form>
        </div>
      @endif

      {{-- Action: Deliver --}}
      @if(in_array($order->status, ['in_progress','revision']))
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
          <h3 class="font-semibold text-gray-900 mb-3">📦 {{ app()->getLocale()==='ar'?'تسليم العمل':'Deliver Work' }}</h3>
          <form method="POST" action="{{ route('freelancer.orders.deliver', $order) }}" enctype="multipart/form-data">
            @csrf
            <textarea name="note" rows="4" required minlength="20" maxlength="3000"
              class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary-400 resize-none mb-3"
              placeholder="{{ app()->getLocale()==='ar'?'اشرح ما قمت بتسليمه...':'Describe what you are delivering...' }}"></textarea>
            <div class="mb-3">
              <label class="block text-xs text-gray-500 mb-1">{{ app()->getLocale()==='ar'?'الملفات (اختياري، حد أقصى 5)':'Attachments (optional, max 5)' }}</label>
              <input type="file" name="attachments[]" multiple accept="*/*"
                class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:bg-primary-50 file:text-primary-700 file:text-sm hover:file:bg-primary-100">
            </div>
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-xl text-sm font-medium transition-colors">
              📤 {{ app()->getLocale()==='ar'?'تسليم الطلب':'Submit Delivery' }}
            </button>
          </form>
        </div>
      @endif
    </div>

    {{-- Sidebar --}}
    <div class="space-y-5">
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <h3 class="font-semibold text-gray-900 mb-3 text-sm">{{ app()->getLocale()==='ar'?'العميل':'Client' }}</h3>
        <div class="flex items-center gap-3">
          <img src="https://ui-avatars.com/api/?name={{ urlencode($order->client?->name) }}&size=44&color=3b82f6&background=dbeafe" class="w-11 h-11 rounded-full">
          <div>
            <p class="font-semibold text-gray-900 text-sm">{{ $order->client?->name }}</p>
            <p class="text-xs text-gray-400">{{ $order->client?->email }}</p>
          </div>
        </div>
      </div>

      @if($order->conversation)
        <a href="{{ route('messages.show', $order->conversation) }}"
           class="block bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:border-primary-200 transition-colors">
          <p class="font-semibold text-gray-900 text-sm">💬 {{ app()->getLocale()==='ar'?'المحادثة':'Conversation' }}</p>
          <p class="text-xs text-gray-400 mt-1">{{ app()->getLocale()==='ar'?'فتح الدردشة':'Open chat' }} →</p>
        </a>
      @endif
    </div>
  </div>
</div>
@endsection

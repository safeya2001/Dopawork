@extends('layouts.app')
@section('title', app()->getLocale()==='ar' ? 'لوحة المستقل' : 'Freelancer Dashboard')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">

  <div class="flex items-center justify-between mb-8">
    <div>
      <h1 class="text-2xl font-bold text-gray-900">
        {{ app()->getLocale()==='ar' ? 'أهلاً، '.auth()->user()->name : 'Welcome, '.auth()->user()->name }}
      </h1>
      <p class="text-gray-500 text-sm mt-1">{{ now()->format('l, d F Y') }}</p>
    </div>
    <a href="{{ route('freelancer.services.create') }}"
       class="bg-primary-600 text-white px-5 py-2.5 rounded-xl text-sm font-medium hover:bg-primary-700 transition-colors">
      + {{ app()->getLocale()==='ar' ? 'إضافة خدمة' : 'Add Service' }}
    </a>
  </div>

  {{-- Stats --}}
  <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
    @foreach([
      ['key'=>'active_services',   'en'=>'Active Services',   'ar'=>'الخدمات النشطة',    'icon'=>'💼'],
      ['key'=>'active_orders',     'en'=>'Active Orders',     'ar'=>'الطلبات النشطة',     'icon'=>'⚡'],
      ['key'=>'completed',         'en'=>'Completed',         'ar'=>'مكتملة',              'icon'=>'✅'],
      ['key'=>'total_orders',      'en'=>'Total Orders',      'ar'=>'إجمالي الطلبات',      'icon'=>'📋'],
      ['key'=>'active_contracts',  'en'=>'Active Contracts',  'ar'=>'العقود النشطة',       'icon'=>'🤝'],
      ['key'=>'pending_proposals', 'en'=>'Proposals Sent',    'ar'=>'العروض المرسلة',      'icon'=>'📨'],
    ] as $s)
      <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
        <div class="text-3xl mb-2">{{ $s['icon'] }}</div>
        <p class="text-2xl font-bold text-gray-900">{{ $stats[$s['key']] }}</p>
        <p class="text-xs text-gray-500 mt-1">{{ app()->getLocale()==='ar' ? $s['ar'] : $s['en'] }}</p>
      </div>
    @endforeach
  </div>

  {{-- Earnings Banner --}}
  <div class="bg-gradient-to-r from-green-600 to-emerald-500 text-white rounded-2xl p-5 mb-8 flex items-center justify-between">
    <div>
      <p class="text-sm opacity-80">{{ app()->getLocale()==='ar' ? 'إجمالي الأرباح' : 'Total Earnings' }}</p>
      <p class="text-3xl font-bold mt-1">{{ number_format($earnings, 3) }} <span class="text-lg opacity-80">JOD</span></p>
    </div>
    <a href="{{ route('wallet.index') }}" class="bg-white/20 hover:bg-white/30 px-5 py-2.5 rounded-xl text-sm font-medium transition-colors">
      {{ app()->getLocale()==='ar' ? 'المحفظة' : 'My Wallet' }}
    </a>
  </div>

  {{-- Active Contracts Quick View --}}
  @if($activeContracts->isNotEmpty())
  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-6">
    <div class="flex items-center justify-between p-5 border-b border-gray-100">
      <h3 class="font-semibold text-gray-900">🤝 {{ app()->getLocale()==='ar' ? 'العقود النشطة' : 'Active Contracts' }}</h3>
      <a href="{{ route('freelancer.contracts.index') }}" class="text-primary-600 text-sm">
        {{ app()->getLocale()==='ar' ? 'عرض الكل' : 'View All' }} →
      </a>
    </div>
    <div class="divide-y divide-gray-50">
      @foreach($activeContracts as $contract)
        <div class="px-5 py-4">
          <div class="flex items-start justify-between gap-3">
            <div class="flex-1 min-w-0">
              <p class="font-semibold text-sm text-gray-900 line-clamp-1">{{ $contract->project?->title }}</p>
              <p class="text-xs text-gray-500 mt-0.5">{{ $contract->project?->client?->name }}</p>
            </div>
            <span class="text-xs font-medium text-green-600">{{ number_format($contract->budget, 3) }} JOD</span>
          </div>
          @if($contract->milestones->isNotEmpty())
            <div class="mt-2 space-y-1">
              @foreach($contract->milestones->take(2) as $ms)
                <div class="flex items-center gap-2 text-xs text-gray-600">
                  <span class="w-2 h-2 rounded-full {{ $ms->status === 'revision_requested' ? 'bg-orange-400' : 'bg-blue-400' }}"></span>
                  <span class="line-clamp-1 flex-1">{{ $ms->title }}</span>
                  <span class="{{ $ms->status === 'revision_requested' ? 'text-orange-600' : 'text-blue-600' }} font-medium shrink-0">
                    {{ $ms->status === 'revision_requested' ? (app()->getLocale()==='ar' ? 'مراجعة' : 'Revision') : (app()->getLocale()==='ar' ? 'جارٍ' : 'In Progress') }}
                  </span>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      @endforeach
    </div>
  </div>
  @endif

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Recent Orders --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm">
      <div class="flex items-center justify-between p-5 border-b border-gray-100">
        <h3 class="font-semibold text-gray-900">{{ app()->getLocale()==='ar'?'آخر الطلبات':'Recent Orders' }}</h3>
        <a href="{{ route('freelancer.orders.index') }}" class="text-primary-600 text-sm">
          {{ app()->getLocale()==='ar'?'عرض الكل':'View All' }} →
        </a>
      </div>
      @if($orders->isEmpty())
        <div class="p-10 text-center">
          <p class="text-4xl mb-3">📋</p>
          <p class="text-gray-400 text-sm">{{ app()->getLocale()==='ar'?'لا توجد طلبات بعد':'No orders yet' }}</p>
        </div>
      @else
        <div class="divide-y divide-gray-50">
          @foreach($orders as $order)
            <div class="flex items-center gap-3 px-5 py-4">
              <div class="flex-1 min-w-0">
                <a href="{{ route('freelancer.orders.show', $order) }}" class="text-sm font-semibold text-primary-600 hover:underline">{{ $order->order_number }}</a>
                <p class="text-xs text-gray-500 truncate">{{ $order->client?->name }} · {{ $order->service?->title }}</p>
              </div>
              <div class="text-right">
                <p class="text-sm font-bold text-gray-900">{{ number_format($order->freelancer_earnings,3) }} JOD</p>
                @include('components.status-badge', ['status' => $order->status])
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>

    {{-- Active Services --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
      <div class="flex items-center justify-between p-5 border-b border-gray-100">
        <h3 class="font-semibold text-gray-900 text-sm">{{ app()->getLocale()==='ar'?'خدماتي النشطة':'Active Services' }}</h3>
        <a href="{{ route('freelancer.services.index') }}" class="text-primary-600 text-xs">{{ app()->getLocale()==='ar'?'الكل':'All' }}</a>
      </div>
      @if($services->isEmpty())
        <div class="p-8 text-center">
          <p class="text-3xl mb-2">💼</p>
          <p class="text-xs text-gray-400 mb-3">{{ app()->getLocale()==='ar'?'لا توجد خدمات':'No services yet' }}</p>
          <a href="{{ route('freelancer.services.create') }}" class="text-primary-600 text-xs font-medium hover:underline">
            {{ app()->getLocale()==='ar'?'أضف خدمتك الأولى':'Create your first service' }}
          </a>
        </div>
      @else
        <div class="divide-y divide-gray-50">
          @foreach($services as $svc)
            <div class="px-5 py-3 flex items-center gap-2">
              <div class="flex-1 min-w-0">
                <a href="{{ route('freelancer.services.edit', $svc) }}" class="text-xs font-semibold text-gray-800 hover:text-primary-600 line-clamp-1">
                  {{ $svc->display_title }}
                </a>
                <p class="text-xs text-gray-400">{{ $svc->orders_count }} {{ app()->getLocale()==='ar'?'طلب':'orders' }}</p>
              </div>
              @include('components.status-badge', ['status' => $svc->status])
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>
</div>
@endsection

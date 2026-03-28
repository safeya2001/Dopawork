@extends('layouts.admin')
@section('title', app()->getLocale()==='ar' ? 'التقارير' : 'Reports')

@section('content')
@php $ar = app()->getLocale()==='ar'; @endphp
<div class="max-w-6xl mx-auto px-4 py-8">

  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">{{ $ar ? 'التقارير والإحصائيات' : 'Reports & Analytics' }}</h1>
  </div>

  {{-- Date Filter --}}
  <form method="GET" action="{{ route('admin.reports.index') }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
    <div class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="block text-xs text-gray-500 mb-1">{{ $ar ? 'من' : 'From' }}</label>
        <input type="date" name="from" value="{{ $from }}"
               class="rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
      </div>
      <div>
        <label class="block text-xs text-gray-500 mb-1">{{ $ar ? 'إلى' : 'To' }}</label>
        <input type="date" name="to" value="{{ $to }}"
               class="rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
      </div>
      <button type="submit" class="px-4 py-2 text-sm font-medium rounded-xl bg-primary-600 text-white hover:bg-primary-700 transition-colors">
        {{ $ar ? 'تصفية' : 'Filter' }}
      </button>
      <a href="{{ route('admin.reports.csv', ['from' => $from, 'to' => $to]) }}"
         class="px-4 py-2 text-sm font-medium rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">
        📊 {{ $ar ? 'تصدير CSV' : 'Export CSV' }}
      </a>
      <a href="{{ route('admin.payment-proof.bulk', ['from' => $from, 'to' => $to]) }}"
         class="px-4 py-2 text-sm font-medium rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">
        📄 {{ $ar ? 'تصدير PDF' : 'Export PDF' }}
      </a>
    </div>
  </form>

  {{-- Stats Cards --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @foreach([
      ['label_ar'=>'إجمالي الطلبات',    'label_en'=>'Total Orders',      'value'=>number_format($stats['total_orders']),                    'icon'=>'📦', 'color'=>'blue'],
      ['label_ar'=>'الطلبات المكتملة',  'label_en'=>'Completed Orders',  'value'=>number_format($stats['completed_orders']),                'icon'=>'✅', 'color'=>'green'],
      ['label_ar'=>'الإيرادات الكلية',  'label_en'=>'Total Revenue',     'value'=>number_format($stats['total_revenue'],3).' JOD',         'icon'=>'💰', 'color'=>'emerald'],
      ['label_ar'=>'عمولة المنصة',      'label_en'=>'Platform Commission','value'=>number_format($stats['commission'],3).' JOD',            'icon'=>'🏦', 'color'=>'purple'],
      ['label_ar'=>'مستخدمون جدد',      'label_en'=>'New Users',         'value'=>number_format($stats['new_users']),                       'icon'=>'👥', 'color'=>'indigo'],
      ['label_ar'=>'مستقلون جدد',       'label_en'=>'New Freelancers',   'value'=>number_format($stats['new_freelancers']),                 'icon'=>'💼', 'color'=>'yellow'],
      ['label_ar'=>'مسحوبات مكتملة',    'label_en'=>'Withdrawals Paid',  'value'=>number_format($stats['withdrawals'],3).' JOD',           'icon'=>'💸', 'color'=>'red'],
    ] as $card)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
      <div class="text-2xl mb-2">{{ $card['icon'] }}</div>
      <p class="text-xl font-bold text-gray-900">{{ $card['value'] }}</p>
      <p class="text-xs text-gray-500 mt-1">{{ $ar ? $card['label_ar'] : $card['label_en'] }}</p>
    </div>
    @endforeach
  </div>

  {{-- Top Freelancers --}}
  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
    <div class="p-4 border-b border-gray-100">
      <h2 class="font-semibold text-gray-900 text-sm">{{ $ar ? 'أعلى المستقلين أرباحاً' : 'Top Earning Freelancers' }}</h2>
    </div>
    <div class="divide-y divide-gray-50">
      @forelse($topFreelancers as $i => $f)
      <div class="flex items-center justify-between px-4 py-3">
        <div class="flex items-center gap-3">
          <span class="w-6 h-6 rounded-full bg-gray-100 text-xs font-bold text-gray-500 flex items-center justify-center">{{ $i+1 }}</span>
          <div>
            <p class="text-sm font-medium text-gray-900">{{ $f->name }}</p>
            <p class="text-xs text-gray-400">{{ $f->completed_count }} {{ $ar ? 'طلب مكتمل' : 'completed orders' }}</p>
          </div>
        </div>
        <span class="text-sm font-bold text-green-600">{{ number_format($f->total_earned ?? 0, 3) }} JOD</span>
      </div>
      @empty
      <p class="px-4 py-6 text-center text-sm text-gray-400">{{ $ar ? 'لا توجد بيانات للفترة المحددة' : 'No data for selected period' }}</p>
      @endforelse
    </div>
  </div>

</div>
@endsection

@extends('layouts.admin')
@section('title', 'لوحة التحليلات')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')
<div class="space-y-6">

    {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ app()->getLocale()==='ar' ? 'لوحة التحليلات' : 'Analytics Dashboard' }}
                </h1>
                <p class="text-gray-500 text-sm mt-0.5">{{ now()->translatedFormat('l، d F Y') }}</p>
            </div>
            <a href="{{ route('admin.reports.index') }}"
               class="hidden md:inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium px-4 py-2 rounded-xl transition-colors">
                📈 {{ app()->getLocale()==='ar' ? 'تقرير مفصّل' : 'Detailed Report' }}
            </a>
        </div>

        {{-- Alert banners --}}
        @if($stats['pending_verifications']>0 || $stats['open_disputes']>0 || $stats['pending_withdrawals']>0 || $stats['pending_services']>0)
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
            @if($stats['pending_verifications']>0)
            <a href="{{ route('admin.verifications.index', ['status'=>'pending']) }}"
               class="flex items-center gap-3 bg-yellow-50 border border-yellow-200 rounded-2xl p-3.5 hover:shadow-md transition-shadow group">
                <span class="text-2xl">🪪</span>
                <div class="flex-1">
                    <p class="text-xl font-bold text-yellow-800 leading-none">{{ $stats['pending_verifications'] }}</p>
                    <p class="text-xs text-yellow-700 mt-0.5">{{ app()->getLocale()==='ar' ? 'تحقق معلق' : 'Pending Verifications' }}</p>
                </div>
                <svg class="w-4 h-4 text-yellow-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            @endif
            @if($stats['open_disputes']>0)
            <a href="{{ route('admin.disputes.index') }}"
               class="flex items-center gap-3 bg-red-50 border border-red-200 rounded-2xl p-3.5 hover:shadow-md transition-shadow group">
                <span class="text-2xl">⚖️</span>
                <div class="flex-1">
                    <p class="text-xl font-bold text-red-800 leading-none">{{ $stats['open_disputes'] }}</p>
                    <p class="text-xs text-red-700 mt-0.5">{{ app()->getLocale()==='ar' ? 'نزاع مفتوح' : 'Open Disputes' }}</p>
                </div>
                <svg class="w-4 h-4 text-red-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            @endif
            @if($stats['pending_withdrawals']>0)
            <a href="{{ route('admin.withdrawals.index') }}"
               class="flex items-center gap-3 bg-purple-50 border border-purple-200 rounded-2xl p-3.5 hover:shadow-md transition-shadow group">
                <span class="text-2xl">💸</span>
                <div class="flex-1">
                    <p class="text-xl font-bold text-purple-800 leading-none">{{ $stats['pending_withdrawals'] }}</p>
                    <p class="text-xs text-purple-700 mt-0.5">{{ app()->getLocale()==='ar' ? 'سحب معلق' : 'Pending Withdrawals' }}</p>
                </div>
                <svg class="w-4 h-4 text-purple-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            @endif
            @if($stats['pending_services']>0)
            <a href="{{ route('admin.services.index') }}"
               class="flex items-center gap-3 bg-blue-50 border border-blue-200 rounded-2xl p-3.5 hover:shadow-md transition-shadow group">
                <span class="text-2xl">📋</span>
                <div class="flex-1">
                    <p class="text-xl font-bold text-blue-800 leading-none">{{ $stats['pending_services'] }}</p>
                    <p class="text-xs text-blue-700 mt-0.5">{{ app()->getLocale()==='ar' ? 'خدمة تنتظر الموافقة' : 'Services Pending Review' }}</p>
                </div>
                <svg class="w-4 h-4 text-blue-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            @endif
        </div>
        @endif

        {{-- KPI Cards --}}
        @php
        $revenueGrowth = $stats['revenue_last_month'] > 0
            ? round((($stats['revenue_this_month'] - $stats['revenue_last_month']) / $stats['revenue_last_month']) * 100, 1)
            : ($stats['revenue_this_month'] > 0 ? 100 : 0);
        $userGrowth = $stats['new_users_last_week'] > 0
            ? round((($stats['new_users_this_week'] - $stats['new_users_last_week']) / $stats['new_users_last_week']) * 100, 1)
            : ($stats['new_users_this_week'] > 0 ? 100 : 0);
        $orderGrowth = $stats['orders_last_month'] > 0
            ? round((($stats['orders_this_month'] - $stats['orders_last_month']) / $stats['orders_last_month']) * 100, 1)
            : ($stats['orders_this_month'] > 0 ? 100 : 0);
        $completionRate = $stats['total_orders'] > 0
            ? round(($stats['completed_orders'] / $stats['total_orders']) * 100, 1)
            : 0;
        @endphp

        <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
            {{-- Revenue this month --}}
            <a href="{{ route('admin.reports.index') }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 block hover:shadow-md hover:border-green-200 transition-all group">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center text-xl">💰</div>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $revenueGrowth >= 0 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                        {{ $revenueGrowth >= 0 ? '↑' : '↓' }} {{ abs($revenueGrowth) }}%
                    </span>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['revenue_this_month'], 3) }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ app()->getLocale()==='ar' ? 'إيرادات هذا الشهر (JOD)' : 'Revenue This Month (JOD)' }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ app()->getLocale()==='ar' ? 'الإجمالي:' : 'Total:' }} {{ number_format($stats['total_revenue'], 3) }} JOD</p>
            </a>

            {{-- Total Users --}}
            <a href="{{ route('admin.users.index') }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 block hover:shadow-md hover:border-blue-200 transition-all group">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center text-xl">👥</div>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $userGrowth >= 0 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                        {{ $userGrowth >= 0 ? '↑' : '↓' }} {{ abs($userGrowth) }}%
                    </span>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_users']) }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ app()->getLocale()==='ar' ? 'إجمالي المستخدمين' : 'Total Users' }}</p>
                <p class="text-xs text-gray-400 mt-1">+{{ $stats['new_users_this_week'] }} {{ app()->getLocale()==='ar' ? 'هذا الأسبوع' : 'this week' }}</p>
            </a>

            {{-- Orders this month --}}
            <a href="{{ route('admin.orders.index') }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 block hover:shadow-md hover:border-indigo-200 transition-all group">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center text-xl">📦</div>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $orderGrowth >= 0 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                        {{ $orderGrowth >= 0 ? '↑' : '↓' }} {{ abs($orderGrowth) }}%
                    </span>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['orders_this_month']) }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ app()->getLocale()==='ar' ? 'طلبات هذا الشهر' : 'Orders This Month' }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $stats['active_orders'] }} {{ app()->getLocale()==='ar' ? 'نشط الآن' : 'active now' }}</p>
            </a>

            {{-- Completion Rate --}}
            <a href="{{ route('admin.orders.index', ['status'=>'completed']) }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 block hover:shadow-md hover:border-emerald-200 transition-all group">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center text-xl">✅</div>
                    <span class="text-xs font-semibold px-2 py-0.5 bg-gray-50 text-gray-600 rounded-full">
                        {{ $stats['completed_orders'] }}/{{ $stats['total_orders'] }}
                    </span>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $completionRate }}%</p>
                <p class="text-xs text-gray-500 mt-1">{{ app()->getLocale()==='ar' ? 'معدل إتمام الطلبات' : 'Order Completion Rate' }}</p>
                <div class="mt-2 bg-gray-100 rounded-full h-1.5">
                    <div class="bg-emerald-500 h-1.5 rounded-full" style="width:{{ $completionRate }}%"></div>
                </div>
            </a>
        </div>

        {{-- Mini stats row --}}
        <div class="grid grid-cols-3 xl:grid-cols-6 gap-3">
            @foreach([
                ['val'=>$stats['total_freelancers'], 'icon'=>'💼', 'en'=>'Freelancers', 'ar'=>'مستقلون',     'bg'=>'bg-indigo-50',  'text'=>'text-indigo-700',  'route'=>'admin.freelancers.index',                           'params'=>[]],
                ['val'=>$stats['total_clients'],     'icon'=>'🛒', 'en'=>'Clients',     'ar'=>'عملاء',       'bg'=>'bg-purple-50',  'text'=>'text-purple-700',  'route'=>'admin.clients.index',                               'params'=>[]],
                ['val'=>number_format($stats['escrow_held'],3).' JOD', 'icon'=>'🔒', 'en'=>'In Escrow', 'ar'=>'في الضمان',    'bg'=>'bg-green-50',   'text'=>'text-green-700',   'route'=>'admin.escrow.index',                                'params'=>[]],
                ['val'=>$stats['active_orders'],     'icon'=>'⚡', 'en'=>'Active',      'ar'=>'نشط الآن',    'bg'=>'bg-yellow-50',  'text'=>'text-yellow-700',  'route'=>'admin.orders.index',                                'params'=>[]],
                ['val'=>$stats['cancelled_orders'],  'icon'=>'❌', 'en'=>'Cancelled',   'ar'=>'ملغي',        'bg'=>'bg-red-50',     'text'=>'text-red-700',     'route'=>'admin.orders.index',                                'params'=>['status'=>'cancelled']],
                ['val'=>$stats['pending_verifications'],'icon'=>'⏳','en'=>'Pending KYC','ar'=>'انتظار KYC', 'bg'=>'bg-orange-50',  'text'=>'text-orange-700',  'route'=>'admin.verifications.index',                         'params'=>['status'=>'pending']],
            ] as $mini)
            <a href="{{ route($mini['route'], $mini['params']) }}" class="bg-white rounded-xl border border-gray-100 p-3 text-center shadow-sm block hover:shadow-md hover:border-gray-200 transition-all">
                <span class="text-xl">{{ $mini['icon'] }}</span>
                <p class="text-sm font-bold text-gray-900 mt-1 truncate">{{ $mini['val'] }}</p>
                <p class="text-xs text-gray-500 truncate">{{ app()->getLocale()==='ar' ? $mini['ar'] : $mini['en'] }}</p>
            </a>
            @endforeach
        </div>

        {{-- Chart --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-semibold text-gray-900">
                        {{ app()->getLocale()==='ar' ? 'الإيرادات والطلبات — آخر 14 يوم' : 'Revenue & Orders — Last 14 Days' }}
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">{{ app()->getLocale()==='ar' ? 'الطلبات المكتملة فقط' : 'Completed orders only' }}</p>
                </div>
                <div class="flex items-center gap-4 text-xs text-gray-500">
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-primary-500 inline-block"></span> {{ app()->getLocale()==='ar' ? 'إيرادات JOD' : 'Revenue JOD' }}</span>
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-emerald-400 inline-block"></span> {{ app()->getLocale()==='ar' ? 'طلبات' : 'Orders' }}</span>
                </div>
            </div>
            <div class="relative h-56">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- Bottom grid: Recent Orders + Pending Verifications --}}
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

            {{-- Recent Orders --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">{{ app()->getLocale()==='ar' ? 'أحدث الطلبات' : 'Recent Orders' }}</h3>
                    <a href="{{ route('admin.orders.index') }}" class="text-primary-600 hover:text-primary-700 text-xs font-medium">
                        {{ app()->getLocale()==='ar' ? 'عرض الكل ←' : 'View All →' }}
                    </a>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($recentOrders as $order)
                    <a href="{{ route('admin.orders.show', $order) }}" class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 transition-colors">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">#{{ $order->order_number }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $order->client->name ?? '—' }} → {{ $order->freelancer->name ?? '—' }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-bold text-gray-900">{{ number_format($order->total_amount, 3) }}</p>
                            @php
                            $statusMap = [
                                'completed'  => ['bg-green-100','text-green-700','مكتمل'],
                                'in_progress'=> ['bg-blue-100','text-blue-700','جاري'],
                                'pending'    => ['bg-yellow-100','text-yellow-700','معلق'],
                                'delivered'  => ['bg-indigo-100','text-indigo-700','مُسلَّم'],
                                'cancelled'  => ['bg-red-100','text-red-700','ملغي'],
                            ];
                            $sc = $statusMap[$order->status] ?? ['bg-gray-100','text-gray-700', $order->status];
                            @endphp
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $sc[0] }} {{ $sc[1] }}">
                                {{ app()->getLocale()==='ar' ? $sc[2] : ucfirst(str_replace('_',' ',$order->status)) }}
                            </span>
                        </div>
                    </a>
                    @empty
                    <div class="px-5 py-10 text-center text-gray-400 text-sm">
                        {{ app()->getLocale()==='ar' ? 'لا توجد طلبات بعد' : 'No orders yet' }}
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Pending Verifications --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">{{ app()->getLocale()==='ar' ? 'طلبات التحقق المعلقة' : 'Pending Verifications' }}</h3>
                    <a href="{{ route('admin.verifications.index') }}" class="text-primary-600 hover:text-primary-700 text-xs font-medium">
                        {{ app()->getLocale()==='ar' ? 'عرض الكل ←' : 'View All →' }}
                    </a>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($pendingVerifications as $verification)
                    <div class="flex items-center gap-3 px-5 py-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($verification->user->name) }}&color=6366f1&background=e0e7ff&size=36"
                             class="w-9 h-9 rounded-full shrink-0" alt="">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900">{{ $verification->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ str_replace('_',' ', ucfirst($verification->document_type)) }}</p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <form method="POST" action="{{ route('admin.verifications.approve', $verification) }}">
                                @csrf
                                <button type="submit"
                                    class="bg-green-100 hover:bg-green-200 text-green-700 text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors">
                                    ✓ {{ app()->getLocale()==='ar' ? 'قبول' : 'Approve' }}
                                </button>
                            </form>
                            <a href="{{ route('admin.verifications.index') }}"
                               class="bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">
                                {{ app()->getLocale()==='ar' ? 'مراجعة' : 'Review' }}
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="px-5 py-10 text-center text-gray-400 text-sm">
                        ✅ {{ app()->getLocale()==='ar' ? 'لا توجد طلبات معلقة' : 'No pending verifications' }}
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>{{-- /main --}}
</div>

<script>
const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    data: {
        labels: {!! json_encode($chartLabels) !!},
        datasets: [
            {
                type: 'bar',
                label: '{{ app()->getLocale()==="ar" ? "إيرادات JOD" : "Revenue JOD" }}',
                data: {!! json_encode($chartRevenue) !!},
                backgroundColor: 'rgba(99,102,241,0.15)',
                borderColor: 'rgba(99,102,241,0.6)',
                borderWidth: 1.5,
                borderRadius: 6,
                yAxisID: 'yRevenue',
            },
            {
                type: 'line',
                label: '{{ app()->getLocale()==="ar" ? "طلبات" : "Orders" }}',
                data: {!! json_encode($chartOrders) !!},
                borderColor: '#34d399',
                backgroundColor: 'rgba(52,211,153,0.08)',
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: '#34d399',
                tension: 0.4,
                fill: true,
                yAxisID: 'yOrders',
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode:'index', intersect:false },
        plugins: { legend:{ display:false } },
        scales: {
            x: { grid:{ display:false }, ticks:{ font:{ size:11 } } },
            yRevenue: {
                position: '{{ app()->getLocale()==="ar" ? "right" : "left" }}',
                grid:{ color:'rgba(0,0,0,0.04)' },
                ticks:{ font:{ size:11 }, callback: v => v.toFixed(3) }
            },
            yOrders: {
                position: '{{ app()->getLocale()==="ar" ? "left" : "right" }}',
                grid:{ display:false },
                ticks:{ font:{ size:11 }, stepSize:1 }
            }
        }
    }
});
</script>
@endsection
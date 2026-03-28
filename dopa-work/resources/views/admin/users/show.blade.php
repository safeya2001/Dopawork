@extends('layouts.admin')
@section('title', $user->name)
@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
  <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-400 hover:text-primary-600 mb-5 block">← المستخدمون</a>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Profile Card --}}
    <div class="space-y-5">
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-center">
        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=80&color=3b82f6&background=dbeafe" class="w-20 h-20 rounded-full mx-auto mb-3">
        <p class="font-bold text-gray-900 text-lg">{{ $user->name }}</p>
        <p class="text-xs text-gray-400 mb-1">{{ $user->email }}</p>
        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">{{ $user->role }}</span>
        <div class="mt-4 pt-4 border-t border-gray-50">
          @include('components.status-badge', ['status'=>$user->status])
        </div>
        @if(!in_array($user->role, ['admin','super_admin']))
          <form method="POST" action="{{ route('admin.users.suspend', $user) }}" class="mt-3">
            @csrf
            <button class="{{ $user->status==='suspended'?'bg-green-500':'bg-red-500' }} hover:opacity-90 text-white text-sm px-4 py-2 rounded-xl w-full transition-colors">
              {{ $user->status==='suspended'?'تفعيل الحساب':'إيقاف الحساب' }}
            </button>
          </form>
        @endif
      </div>

      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-sm space-y-3">
        <div class="flex justify-between"><span class="text-gray-400">الرصيد</span><span class="font-bold text-primary-700">{{ number_format($user->wallet_balance,3) }} JOD</span></div>
        <div class="flex justify-between"><span class="text-gray-400">سجّل في</span><span>{{ $user->created_at->format('Y/m/d') }}</span></div>
        @if($user->isFreelancer() && $user->freelancerProfile)
          <div class="flex justify-between"><span class="text-gray-400">الطلبات المكتملة</span><span>{{ $user->freelancerProfile->completed_orders }}</span></div>
        @endif
        <div class="flex justify-between"><span class="text-gray-400">طلبات كعميل</span><span>{{ $orderStats['as_client'] }}</span></div>
        <div class="flex justify-between"><span class="text-gray-400">طلبات كمستقل</span><span>{{ $orderStats['as_freelancer'] }}</span></div>
      </div>
    </div>

    {{-- Main --}}
    <div class="lg:col-span-2 space-y-5">
      {{-- Recent Orders --}}
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <h3 class="font-semibold text-gray-900 mb-4">آخر الطلبات</h3>
        @if($recentOrders->isEmpty())
          <p class="text-gray-400 text-sm text-center py-4">لا توجد طلبات</p>
        @else
          <div class="space-y-3">
            @foreach($recentOrders as $order)
              <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                <div>
                  <a href="{{ route('admin.orders.show', $order) }}" class="text-sm font-semibold text-primary-600 hover:underline">{{ $order->order_number }}</a>
                  <p class="text-xs text-gray-400">{{ $order->service?->title }}</p>
                </div>
                <div class="text-end">
                  <p class="text-sm font-semibold">{{ number_format($order->total_amount,3) }} JOD</p>
                  @include('components.status-badge', ['status'=>$order->status])
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>

      {{-- Wallet Transactions --}}
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <h3 class="font-semibold text-gray-900 mb-4">آخر المعاملات المالية</h3>
        @if($user->walletTransactions->isEmpty())
          <p class="text-gray-400 text-sm text-center py-4">لا توجد معاملات</p>
        @else
          <div class="space-y-2">
            @foreach($user->walletTransactions as $tx)
              <div class="flex items-center justify-between text-sm py-1.5 border-b border-gray-50 last:border-0">
                <span class="text-gray-600 text-xs">{{ $tx->description_ar ?? $tx->description }}</span>
                <span class="{{ in_array($tx->type,['credit','deposit','escrow_release','refund'])?'text-green-700':'text-red-600' }} font-semibold text-xs">
                  {{ in_array($tx->type,['credit','deposit','escrow_release','refund'])?'+':'-' }}{{ number_format($tx->amount,3) }} JOD
                </span>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

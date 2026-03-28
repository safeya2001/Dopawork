@extends('layouts.admin')
@section('title', 'قائمة العملاء')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8" dir="rtl">

  {{-- Header --}}
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-bold text-gray-900">👤 العملاء</h1>
      <p class="text-sm text-gray-500 mt-0.5">إجمالي {{ $clients->total() }} عميل مسجل</p>
    </div>
    <a href="{{ route('admin.dashboard') }}"
       class="text-sm text-gray-500 hover:text-primary-600 flex items-center gap-1">
      ← لوحة التحكم
    </a>
  </div>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 mb-5 text-sm">✅ {{ session('success') }}</div>
  @endif

  {{-- Filters --}}
  <form method="GET" class="flex gap-3 mb-5 flex-wrap items-center">
    <input name="search" value="{{ request('search') }}"
           placeholder="ابحث باسم أو بريد..."
           class="border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-primary-400 w-60">
    <select name="status" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none">
      <option value="">كل الحالات</option>
      @foreach(['active'=>'نشط','suspended'=>'موقوف','inactive'=>'غير نشط','pending_verification'=>'معلق التحقق'] as $s=>$l)
        <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ $l }}</option>
      @endforeach
    </select>
    <button class="bg-primary-600 text-white px-4 py-2 rounded-xl text-sm">🔍 بحث</button>
    <a href="{{ route('admin.clients.index') }}" class="border border-gray-200 text-gray-600 px-4 py-2 rounded-xl text-sm">إعادة تعيين</a>
  </form>

  {{-- Summary Cards --}}
  @php
    $total     = \App\Models\User::where('role','client')->count();
    $active    = \App\Models\User::where('role','client')->where('status','active')->count();
    $suspended = \App\Models\User::where('role','client')->where('status','suspended')->count();
    $newThisMonth = \App\Models\User::where('role','client')
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count();
  @endphp
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 text-center">
      <p class="text-2xl font-bold text-blue-800">{{ $total }}</p>
      <p class="text-xs text-blue-600 mt-1">إجمالي العملاء</p>
    </div>
    <div class="bg-green-50 border border-green-200 rounded-2xl p-4 text-center">
      <p class="text-2xl font-bold text-green-800">{{ $active }}</p>
      <p class="text-xs text-green-600 mt-1">نشطون</p>
    </div>
    <div class="bg-red-50 border border-red-200 rounded-2xl p-4 text-center">
      <p class="text-2xl font-bold text-red-800">{{ $suspended }}</p>
      <p class="text-xs text-red-600 mt-1">موقوفون</p>
    </div>
    <div class="bg-purple-50 border border-purple-200 rounded-2xl p-4 text-center">
      <p class="text-2xl font-bold text-purple-800">{{ $newThisMonth }}</p>
      <p class="text-xs text-purple-600 mt-1">جدد هذا الشهر</p>
    </div>
  </div>

  {{-- Table --}}
  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    @if($clients->isEmpty())
      <div class="p-16 text-center text-gray-400">
        <span class="text-5xl block mb-3">👤</span>
        <p class="text-sm">لا يوجد عملاء مطابقون للفلتر الحالي</p>
      </div>
    @else
      <table class="w-full text-sm">
        <thead>
          <tr class="bg-gray-50 text-xs text-gray-500 border-b">
            <th class="px-5 py-3 text-start">العميل</th>
            <th class="px-5 py-3 text-start">الهاتف</th>
            <th class="px-5 py-3 text-start">الرصيد</th>
            <th class="px-5 py-3 text-start">الطلبات</th>
            <th class="px-5 py-3 text-start">الحالة</th>
            <th class="px-5 py-3 text-start">تاريخ التسجيل</th>
            <th class="px-5 py-3 text-start">إجراء</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          @foreach($clients as $client)
          <tr class="hover:bg-gray-50">
            <td class="px-5 py-4">
              <a href="{{ route('admin.users.show', $client) }}"
                 class="font-semibold text-gray-900 hover:text-primary-600">
                {{ $client->name }}
              </a>
              <p class="text-xs text-gray-400">{{ $client->email }}</p>
              @if($client->company_name)
                <p class="text-xs text-gray-500 mt-0.5">🏢 {{ $client->company_name }}</p>
              @endif
            </td>
            <td class="px-5 py-4 text-gray-600 text-xs">{{ $client->phone ?? '—' }}</td>
            <td class="px-5 py-4">
              <span class="font-semibold text-primary-700">{{ number_format($client->wallet_balance, 3) }}</span>
              <span class="text-xs text-gray-400"> JOD</span>
            </td>
            <td class="px-5 py-4">
              <span class="bg-blue-50 text-blue-700 text-xs px-2 py-1 rounded-full font-medium">
                {{ $client->orders_count ?? 0 }} طلب
              </span>
            </td>
            <td class="px-5 py-4">
              @php
                $sc = [
                  'active'               => ['bg-green-100','text-green-700','نشط'],
                  'suspended'            => ['bg-red-100','text-red-700','موقوف'],
                  'inactive'             => ['bg-gray-100','text-gray-600','غير نشط'],
                  'pending_verification' => ['bg-yellow-100','text-yellow-700','معلق'],
                ][$client->status] ?? ['bg-gray-100','text-gray-600',$client->status];
              @endphp
              <span class="text-xs px-2 py-1 rounded-full {{ $sc[0] }} {{ $sc[1] }} font-medium">{{ $sc[2] }}</span>
            </td>
            <td class="px-5 py-4 text-xs text-gray-400">
              {{ $client->created_at->format('Y/m/d') }}
            </td>
            <td class="px-5 py-4">
              <div class="flex gap-2">
                <a href="{{ route('admin.users.show', $client) }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs px-3 py-1.5 rounded-lg">
                  تفاصيل
                </a>
                @if(!in_array($client->role, ['admin','super_admin']))
                  <form method="POST" action="{{ route('admin.users.suspend', $client) }}">
                    @csrf
                    <button class="{{ $client->status==='suspended' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} text-xs px-3 py-1.5 rounded-lg">
                      {{ $client->status==='suspended' ? 'تفعيل' : 'إيقاف' }}
                    </button>
                  </form>
                @endif
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      <div class="p-4 border-t border-gray-50">{{ $clients->links() }}</div>
    @endif
  </div>
</div>
@endsection

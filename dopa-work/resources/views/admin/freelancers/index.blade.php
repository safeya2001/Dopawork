@extends('layouts.admin')
@section('title', 'قائمة المستقلين (فريلانسر)')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8" dir="rtl">

  {{-- Header --}}
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-bold text-gray-900">💼 المستقلون</h1>
      <p class="text-sm text-gray-500 mt-0.5">إجمالي {{ $freelancers->total() }} مستقل مسجل</p>
    </div>
    <a href="{{ route('admin.freelancers.create') }}"
       class="bg-primary-600 hover:bg-primary-700 text-white text-sm px-5 py-2.5 rounded-xl font-medium">
      + إضافة فريلانسر
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
    <a href="{{ route('admin.freelancers.index') }}" class="border border-gray-200 text-gray-600 px-4 py-2 rounded-xl text-sm">إعادة تعيين</a>
  </form>

  {{-- Summary Cards --}}
  @php
    $total     = \App\Models\User::where('role','freelancer')->count();
    $active    = \App\Models\User::where('role','freelancer')->where('status','active')->count();
    $suspended = \App\Models\User::where('role','freelancer')->where('status','suspended')->count();
    $newThisMonth = \App\Models\User::where('role','freelancer')
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count();
  @endphp
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-indigo-50 border border-indigo-200 rounded-2xl p-4 text-center">
      <p class="text-2xl font-bold text-indigo-800">{{ $total }}</p>
      <p class="text-xs text-indigo-600 mt-1">إجمالي المستقلين</p>
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
    @if($freelancers->isEmpty())
      <div class="p-16 text-center text-gray-400">
        <span class="text-5xl block mb-3">💼</span>
        <p class="text-sm">لا يوجد مستقلون مطابقون للفلتر الحالي</p>
        <a href="{{ route('admin.freelancers.create') }}"
           class="mt-4 inline-block bg-primary-600 text-white text-sm px-5 py-2 rounded-xl">
          + إضافة أول فريلانسر
        </a>
      </div>
    @else
      <table class="w-full text-sm">
        <thead>
          <tr class="bg-gray-50 text-xs text-gray-500 border-b">
            <th class="px-5 py-3 text-start">المستقل</th>
            <th class="px-5 py-3 text-start">الهاتف</th>
            <th class="px-5 py-3 text-start">الرصيد</th>
            <th class="px-5 py-3 text-start">الطلبات</th>
            <th class="px-5 py-3 text-start">التقييم</th>
            <th class="px-5 py-3 text-start">الحالة</th>
            <th class="px-5 py-3 text-start">تاريخ التسجيل</th>
            <th class="px-5 py-3 text-start">إجراء</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          @foreach($freelancers as $fl)
          <tr class="hover:bg-gray-50">
            <td class="px-5 py-4">
              <a href="{{ route('admin.users.show', $fl) }}"
                 class="font-semibold text-gray-900 hover:text-primary-600">
                {{ $fl->name }}
              </a>
              <p class="text-xs text-gray-400">{{ $fl->email }}</p>
              @if($fl->city)
                <p class="text-xs text-gray-500 mt-0.5">📍 {{ $fl->city }}</p>
              @endif
            </td>
            <td class="px-5 py-4 text-gray-600 text-xs">{{ $fl->phone ?? '—' }}</td>
            <td class="px-5 py-4">
              <span class="font-semibold text-primary-700">{{ number_format($fl->wallet_balance, 3) }}</span>
              <span class="text-xs text-gray-400"> JOD</span>
            </td>
            <td class="px-5 py-4">
              <span class="bg-indigo-50 text-indigo-700 text-xs px-2 py-1 rounded-full font-medium">
                {{ $fl->orders_count ?? 0 }} طلب
              </span>
            </td>
            <td class="px-5 py-4 text-xs text-gray-600">
              @if($fl->freelancerProfile && $fl->freelancerProfile->rating > 0)
                <span class="text-yellow-500">★</span> {{ number_format($fl->freelancerProfile->rating, 1) }}
              @else
                <span class="text-gray-400">—</span>
              @endif
            </td>
            <td class="px-5 py-4">
              @php
                $sc = [
                  'active'               => ['bg-green-100','text-green-700','نشط'],
                  'suspended'            => ['bg-red-100','text-red-700','موقوف'],
                  'inactive'             => ['bg-gray-100','text-gray-600','غير نشط'],
                  'pending_verification' => ['bg-yellow-100','text-yellow-700','معلق'],
                ][$fl->status] ?? ['bg-gray-100','text-gray-600',$fl->status];
              @endphp
              <span class="text-xs px-2 py-1 rounded-full {{ $sc[0] }} {{ $sc[1] }} font-medium">{{ $sc[2] }}</span>
            </td>
            <td class="px-5 py-4 text-xs text-gray-400">
              {{ $fl->created_at->format('Y/m/d') }}
            </td>
            <td class="px-5 py-4">
              <div class="flex gap-2">
                <a href="{{ route('admin.users.show', $fl) }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs px-3 py-1.5 rounded-lg">
                  تفاصيل
                </a>
                <form method="POST" action="{{ route('admin.users.suspend', $fl) }}">
                  @csrf
                  <button class="{{ $fl->status==='suspended' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} text-xs px-3 py-1.5 rounded-lg">
                    {{ $fl->status==='suspended' ? 'تفعيل' : 'إيقاف' }}
                  </button>
                </form>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      <div class="p-4 border-t border-gray-50">{{ $freelancers->links() }}</div>
    @endif
  </div>
</div>
@endsection

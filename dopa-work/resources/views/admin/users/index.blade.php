@extends('layouts.admin')
@section('title', 'إدارة المستخدمين')
@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
  <h1 class="text-2xl font-bold text-gray-900 mb-6">👥 إدارة المستخدمين</h1>

  <form method="GET" class="flex gap-3 mb-5 flex-wrap">
    <input name="search" value="{{ request('search') }}" placeholder="اسم أو بريد..." class="border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-primary-400 w-52">
    <select name="role" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none">
      <option value="">كل الأدوار</option>
      @foreach(['client'=>'عميل','freelancer'=>'مستقل','admin'=>'أدمن'] as $r=>$l)
        <option value="{{ $r }}" {{ request('role')===$r?'selected':'' }}>{{ $l }}</option>
      @endforeach
    </select>
    <select name="status" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none">
      <option value="">كل الحالات</option>
      @foreach(['active'=>'نشط','suspended'=>'موقوف','inactive'=>'غير نشط'] as $s=>$l)
        <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ $l }}</option>
      @endforeach
    </select>
    <button class="bg-primary-600 text-white px-4 py-2 rounded-xl text-sm">بحث</button>
    <a href="{{ route('admin.users.index') }}" class="border border-gray-200 text-gray-600 px-4 py-2 rounded-xl text-sm">إعادة تعيين</a>
  </form>

  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    @if($users->isEmpty())
      <div class="p-12 text-center text-gray-400 text-sm">لا توجد مستخدمين</div>
    @else
      <table class="w-full text-sm">
        <thead><tr class="bg-gray-50 text-xs text-gray-500 uppercase border-b">
          <th class="px-5 py-3 text-start">المستخدم</th>
          <th class="px-5 py-3 text-start">الدور</th>
          <th class="px-5 py-3 text-start">الرصيد</th>
          <th class="px-5 py-3 text-start">الحالة</th>
          <th class="px-5 py-3 text-start">التسجيل</th>
          <th class="px-5 py-3 text-start">إجراء</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-50">
          @foreach($users as $user)
          <tr class="hover:bg-gray-50">
            <td class="px-5 py-4">
              <a href="{{ route('admin.users.show', $user) }}" class="font-semibold text-gray-900 hover:text-primary-600">{{ $user->name }}</a>
              <p class="text-xs text-gray-400">{{ $user->email }}</p>
            </td>
            <td class="px-5 py-4">
              <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">{{ $user->role }}</span>
            </td>
            <td class="px-5 py-4 font-semibold text-primary-700">{{ number_format($user->wallet_balance,3) }} JOD</td>
            <td class="px-5 py-4">@include('components.status-badge', ['status'=>$user->status])</td>
            <td class="px-5 py-4 text-gray-400 text-xs">{{ $user->created_at->format('Y/m/d') }}</td>
            <td class="px-5 py-4">
              @if(!in_array($user->role, ['admin','super_admin']))
                <form method="POST" action="{{ route('admin.users.suspend', $user) }}">
                  @csrf
                  <button class="{{ $user->status==='suspended'?'bg-green-100 text-green-700':'bg-red-100 text-red-700' }} text-xs px-3 py-1.5 rounded-lg">
                    {{ $user->status==='suspended'?'تفعيل':'إيقاف' }}
                  </button>
                </form>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      <div class="p-4">{{ $users->links() }}</div>
    @endif
  </div>
</div>
@endsection

@extends('layouts.admin')
@section('title', 'إدارة الخدمات')
@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">

  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">🛠️ إدارة الخدمات</h1>
    <span class="text-sm text-gray-500">{{ $services->total() }} خدمة</span>
  </div>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 mb-5 text-sm">✅ {{ session('success') }}</div>
  @endif

  {{-- Status Filter --}}
  <div class="flex gap-2 mb-5 flex-wrap">
    <a href="{{ route('admin.services.index') }}"
       class="{{ !request('status') ? 'bg-gray-800 text-white' : 'bg-white text-gray-600 border border-gray-200' }} px-4 py-2 rounded-xl text-sm font-medium">
      الكل
    </a>
    @foreach(['pending_review'=>'⏳ تنتظر المراجعة','active'=>'✅ نشطة','rejected'=>'❌ مرفوضة','inactive'=>'⏸️ معطلة'] as $s => $label)
      <a href="{{ route('admin.services.index', ['status'=>$s]) }}"
         class="{{ request('status')===$s ? 'bg-primary-600 text-white' : 'bg-white text-gray-600 border border-gray-200' }} px-4 py-2 rounded-xl text-sm font-medium">
        {{ $label }}
      </a>
    @endforeach
  </div>

  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    @if($services->isEmpty())
      <div class="p-12 text-center text-gray-400">
        <span class="text-4xl block mb-2">📭</span>
        <p class="text-sm">لا توجد خدمات</p>
      </div>
    @else
      <table class="w-full text-sm">
        <thead>
          <tr class="bg-gray-50 text-xs text-gray-500 border-b">
            <th class="px-5 py-3 text-start">الخدمة</th>
            <th class="px-5 py-3 text-start">المستقل</th>
            <th class="px-5 py-3 text-start">التصنيف</th>
            <th class="px-5 py-3 text-start">السعر</th>
            <th class="px-5 py-3 text-start">الطلبات</th>
            <th class="px-5 py-3 text-start">الحالة</th>
            <th class="px-5 py-3 text-start">إجراء</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          @foreach($services as $service)
          <tr class="hover:bg-gray-50 align-top">
            <td class="px-5 py-4 max-w-xs">
              <p class="font-semibold text-gray-900 truncate">{{ $service->title }}</p>
              @if($service->title_ar)
                <p class="text-xs text-gray-400 truncate mt-0.5">{{ $service->title_ar }}</p>
              @endif
              <p class="text-xs text-gray-400 mt-1">{{ $service->created_at->format('Y/m/d') }}</p>
            </td>
            <td class="px-5 py-4">
              <p class="font-medium text-gray-800">{{ $service->user->name ?? '—' }}</p>
              <p class="text-xs text-gray-400">{{ $service->user->email ?? '' }}</p>
            </td>
            <td class="px-5 py-4 text-gray-600 text-xs">
              {{ $service->category->name ?? '—' }}
            </td>
            <td class="px-5 py-4">
              @if($service->packages && count($service->packages ?? []) > 0)
                <p class="font-semibold text-primary-700">{{ number_format(collect($service->packages)->min('price'), 3) }} JOD</p>
                <p class="text-xs text-gray-400">من</p>
              @else
                <span class="text-gray-400 text-xs">—</span>
              @endif
            </td>
            <td class="px-5 py-4 text-center">
              <span class="font-bold text-gray-800">{{ $service->orders_count ?? 0 }}</span>
            </td>
            <td class="px-5 py-4">
              @php
                $sc = [
                  'active'         => ['bg-green-100','text-green-700','نشطة'],
                  'pending_review' => ['bg-yellow-100','text-yellow-700','مراجعة'],
                  'rejected'       => ['bg-red-100','text-red-700','مرفوضة'],
                  'inactive'       => ['bg-gray-100','text-gray-600','معطلة'],
                ][$service->status] ?? ['bg-gray-100','text-gray-600',$service->status];
              @endphp
              <span class="text-xs px-2 py-1 rounded-full {{ $sc[0] }} {{ $sc[1] }} font-medium">{{ $sc[2] }}</span>
            </td>
            <td class="px-5 py-4">
              <div class="flex flex-col gap-1.5">
                @if($service->status === 'pending_review')
                  <form method="POST" action="{{ route('admin.services.approve', $service) }}">
                    @csrf
                    <button class="bg-green-500 hover:bg-green-600 text-white text-xs px-3 py-1.5 rounded-lg w-full">✓ قبول</button>
                  </form>
                  <button onclick="document.getElementById('svc-rej-{{$service->id}}').classList.toggle('hidden')"
                    class="bg-red-100 hover:bg-red-200 text-red-700 text-xs px-3 py-1.5 rounded-lg">✕ رفض</button>
                  <div id="svc-rej-{{$service->id}}" class="hidden mt-1">
                    <form method="POST" action="{{ route('admin.services.reject', $service) }}">
                      @csrf
                      <textarea name="rejection_reason" required rows="2" placeholder="سبب الرفض..."
                        class="w-full border rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:border-red-400 resize-none mb-1"></textarea>
                      <button class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1.5 rounded-lg w-full">تأكيد</button>
                    </form>
                  </div>
                @elseif($service->status === 'active')
                  <a href="{{ route('services.show', $service->slug) }}" target="_blank"
                     class="bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs px-3 py-1.5 rounded-lg text-center">👁 عرض</a>
                @else
                  <form method="POST" action="{{ route('admin.services.approve', $service) }}">
                    @csrf
                    <button class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs px-3 py-1.5 rounded-lg w-full">↩ تفعيل</button>
                  </form>
                @endif
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      <div class="p-4">{{ $services->links() }}</div>
    @endif
  </div>
</div>
@endsection

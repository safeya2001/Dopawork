@extends('layouts.admin')
@section('title', 'طلبات التحقق من الهوية')
@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">🪪 طلبات التحقق من الهوية</h1>
    <span class="text-sm text-gray-500">{{ $verifications->total() }} طلب</span>
  </div>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 mb-5 text-sm">✅ {{ session('success') }}</div>
  @endif

  {{-- Status Filter --}}
  <div class="flex gap-2 mb-5 flex-wrap">
    <a href="{{ route('admin.verifications.index') }}"
       class="{{ !request('status') ? 'bg-gray-800 text-white' : 'bg-white text-gray-600 border border-gray-200' }} px-4 py-2 rounded-xl text-sm font-medium">
      الكل
    </a>
    @foreach(['pending'=>'⏳ معلقة','approved'=>'✅ مقبولة','rejected'=>'❌ مرفوضة'] as $s => $label)
      <a href="{{ route('admin.verifications.index', ['status'=>$s]) }}"
         class="{{ request('status')===$s ? 'bg-primary-600 text-white' : 'bg-white text-gray-600 border border-gray-200' }} px-4 py-2 rounded-xl text-sm font-medium">
        {{ $label }}
      </a>
    @endforeach
  </div>

  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    @if($verifications->isEmpty())
      <div class="p-12 text-center text-gray-400 text-sm">لا توجد طلبات</div>
    @else
      <table class="w-full text-sm">
        <thead>
          <tr class="bg-gray-50 text-xs text-gray-500 border-b">
            <th class="px-5 py-3 text-start">المستخدم</th>
            <th class="px-5 py-3 text-start">نوع الوثيقة</th>
            <th class="px-5 py-3 text-start">الوثائق المرفوعة</th>
            <th class="px-5 py-3 text-start">التاريخ</th>
            <th class="px-5 py-3 text-start">الحالة</th>
            <th class="px-5 py-3 text-start">إجراء</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          @foreach($verifications as $v)
          <tr class="hover:bg-gray-50 align-top">
            <td class="px-5 py-4">
              <p class="font-semibold text-gray-900">{{ $v->user->name }}</p>
              <p class="text-xs text-gray-400">{{ $v->user->email }}</p>
              <p class="text-xs text-gray-400">{{ $v->user->role }}</p>
              @if($v->document_number)
                <p class="text-xs text-gray-500 mt-1">رقم: {{ $v->document_number }}</p>
              @endif
            </td>

            <td class="px-5 py-4 text-gray-600 text-xs">
              {{ str_replace('_', ' ', ucfirst($v->document_type)) }}
              @if($v->document_expiry)
                <p class="text-gray-400 mt-1">تنتهي: {{ $v->document_expiry->format('Y-m-d') }}</p>
              @endif
            </td>

            {{-- Document images --}}
            <td class="px-5 py-4">
              <div class="flex flex-wrap gap-2">
                @if($v->front_image)
                  <a href="{{ route('admin.verifications.document', [$v, 'front_image']) }}" target="_blank"
                     class="group relative block w-16 h-16 rounded-lg overflow-hidden border border-gray-200 hover:border-primary-400 transition-colors">
                    @if(str_ends_with(strtolower($v->front_image), '.pdf'))
                      <div class="w-full h-full bg-red-50 flex flex-col items-center justify-center">
                        <span class="text-xl">📄</span>
                        <span class="text-xs text-red-600 font-medium">PDF</span>
                      </div>
                    @else
                      <img src="{{ route('admin.verifications.document', [$v, 'front_image']) }}"
                           class="w-full h-full object-cover" alt="الوجه الأمامي"
                           onerror="this.parentElement.innerHTML='<div class=\'w-full h-full bg-gray-100 flex items-center justify-center text-xs text-gray-400\'>خطأ</div>'">
                    @endif
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all flex items-end justify-center pb-0.5">
                      <span class="text-white text-[9px] opacity-0 group-hover:opacity-100 font-medium bg-black/50 px-1 rounded">أمامي</span>
                    </div>
                  </a>
                @endif

                @if($v->back_image)
                  <a href="{{ route('admin.verifications.document', [$v, 'back_image']) }}" target="_blank"
                     class="group relative block w-16 h-16 rounded-lg overflow-hidden border border-gray-200 hover:border-primary-400 transition-colors">
                    @if(str_ends_with(strtolower($v->back_image), '.pdf'))
                      <div class="w-full h-full bg-red-50 flex flex-col items-center justify-center">
                        <span class="text-xl">📄</span>
                        <span class="text-xs text-red-600 font-medium">PDF</span>
                      </div>
                    @else
                      <img src="{{ route('admin.verifications.document', [$v, 'back_image']) }}"
                           class="w-full h-full object-cover" alt="الوجه الخلفي"
                           onerror="this.parentElement.innerHTML='<div class=\'w-full h-full bg-gray-100 flex items-center justify-center text-xs text-gray-400\'>خطأ</div>'">
                    @endif
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all flex items-end justify-center pb-0.5">
                      <span class="text-white text-[9px] opacity-0 group-hover:opacity-100 font-medium bg-black/50 px-1 rounded">خلفي</span>
                    </div>
                  </a>
                @endif

                @if($v->selfie_image)
                  <a href="{{ route('admin.verifications.document', [$v, 'selfie_image']) }}" target="_blank"
                     class="group relative block w-16 h-16 rounded-lg overflow-hidden border border-gray-200 hover:border-primary-400 transition-colors">
                    <img src="{{ route('admin.verifications.document', [$v, 'selfie_image']) }}"
                         class="w-full h-full object-cover" alt="سيلفي"
                         onerror="this.parentElement.innerHTML='<div class=\'w-full h-full bg-gray-100 flex items-center justify-center text-xs text-gray-400\'>خطأ</div>'">
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all flex items-end justify-center pb-0.5">
                      <span class="text-white text-[9px] opacity-0 group-hover:opacity-100 font-medium bg-black/50 px-1 rounded">سيلفي</span>
                    </div>
                  </a>
                @endif

                @if(!$v->front_image && !$v->back_image && !$v->selfie_image)
                  <span class="text-xs text-gray-400 italic">لا توجد ملفات</span>
                @endif
              </div>
              <a href="{{ route('admin.verifications.show', $v) }}"
                 class="inline-block mt-2 text-primary-600 hover:underline text-xs">🔍 عرض التفاصيل</a>
            </td>

            <td class="px-5 py-4 text-gray-400 text-xs whitespace-nowrap">{{ $v->created_at->format('Y/m/d') }}<br>{{ $v->created_at->format('H:i') }}</td>

            <td class="px-5 py-4">
              @php
                $sc = ['pending'=>['bg-yellow-100','text-yellow-700','معلق'],
                       'approved'=>['bg-green-100','text-green-700','موافق'],
                       'rejected'=>['bg-red-100','text-red-700','مرفوض']][$v->status] ?? ['bg-gray-100','text-gray-700',$v->status];
              @endphp
              <span class="text-xs px-2 py-1 rounded-full {{ $sc[0] }} {{ $sc[1] }} font-medium">{{ $sc[2] }}</span>
              @if($v->status === 'approved' && $v->reviewed_at)
                <p class="text-xs text-gray-400 mt-1">{{ $v->reviewed_at->format('Y/m/d') }}</p>
              @endif
              @if($v->status === 'rejected' && $v->rejection_reason_ar)
                <p class="text-xs text-red-500 mt-1 max-w-[140px]">{{ Str::limit($v->rejection_reason_ar, 50) }}</p>
              @endif
            </td>

            <td class="px-5 py-4">
              @if($v->status === 'pending')
                <div class="flex flex-col gap-2">
                  <form method="POST" action="{{ route('admin.verifications.approve', $v) }}">
                    @csrf
                    <button class="bg-green-500 hover:bg-green-600 text-white text-xs px-3 py-1.5 rounded-lg w-full">✓ قبول</button>
                  </form>
                  <button onclick="document.getElementById('rej-{{$v->id}}').classList.toggle('hidden')"
                    class="bg-red-100 hover:bg-red-200 text-red-700 text-xs px-3 py-1.5 rounded-lg">✕ رفض</button>
                </div>
                <div id="rej-{{$v->id}}" class="hidden mt-2 space-y-1">
                  <form method="POST" action="{{ route('admin.verifications.reject', $v) }}">
                    @csrf
                    <input name="rejection_reason" required placeholder="Reason (English)..." class="w-full border rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:border-red-400">
                    <input name="rejection_reason_ar" required placeholder="السبب بالعربية..." class="w-full border rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:border-red-400 mt-1">
                    <button class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1.5 rounded-lg w-full mt-1">تأكيد الرفض</button>
                  </form>
                </div>
              @else
                <span class="text-xs text-gray-400">—</span>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      <div class="p-4">{{ $verifications->links() }}</div>
    @endif
  </div>
</div>
@endsection

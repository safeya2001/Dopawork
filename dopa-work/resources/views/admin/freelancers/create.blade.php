@extends('layouts.admin')
@section('title', 'إضافة فريلانسر جديد')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8" dir="rtl">

  <div class="flex items-center gap-3 mb-6">
    <a href="{{ route('admin.freelancers.index') }}"
       class="text-gray-400 hover:text-gray-700 text-sm">← العودة للقائمة</a>
    <span class="text-gray-300">|</span>
    <h1 class="text-xl font-bold text-gray-900">➕ إضافة فريلانسر جديد</h1>
  </div>

  @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 mb-5">
      <p class="text-red-700 text-sm font-medium mb-1">⚠️ يوجد أخطاء في البيانات:</p>
      <ul class="list-disc list-inside text-red-600 text-sm space-y-0.5">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.freelancers.store') }}"
        class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
    @csrf

    <p class="text-xs text-gray-400 bg-blue-50 border border-blue-100 rounded-xl px-4 py-2">
      💡 سيتم إنشاء الحساب بدور <strong>فريلانسر</strong> وحالة <strong>نشط</strong> مباشرةً.
    </p>

    {{-- Name / Name AR --}}
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">الاسم (EN) <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name') }}" required
               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400"
               placeholder="Ahmed Ali">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">الاسم (AR)</label>
        <input type="text" name="name_ar" value="{{ old('name_ar') }}"
               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400"
               placeholder="أحمد علي">
      </div>
    </div>

    {{-- Email --}}
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">البريد الإلكتروني <span class="text-red-500">*</span></label>
      <input type="email" name="email" value="{{ old('email') }}" required
             class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400"
             placeholder="freelancer@example.com">
    </div>

    {{-- Phone --}}
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">رقم الهاتف</label>
      <input type="text" name="phone" value="{{ old('phone') }}"
             class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400"
             placeholder="+962799000000">
    </div>

    {{-- Password --}}
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">كلمة المرور <span class="text-red-500">*</span></label>
      <input type="password" name="password" required minlength="8"
             class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400"
             placeholder="8 أحرف على الأقل">
      <p class="text-xs text-gray-400 mt-1">أرسل كلمة المرور للفريلانسر بعد الإنشاء</p>
    </div>

    {{-- City --}}
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">المدينة</label>
      <input type="text" name="city" value="{{ old('city') }}"
             class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400"
             placeholder="عمّان">
    </div>

    {{-- Bio --}}
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">نبذة (AR)</label>
      <textarea name="bio_ar" rows="3"
                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400 resize-none"
                placeholder="نبذة مختصرة عن المستقل...">{{ old('bio_ar') }}</textarea>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">نبذة (EN)</label>
      <textarea name="bio" rows="3"
                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400 resize-none"
                placeholder="Brief description about the freelancer...">{{ old('bio') }}</textarea>
    </div>

    {{-- Actions --}}
    <div class="flex gap-3 pt-2">
      <button type="submit"
              class="flex-1 bg-primary-600 hover:bg-primary-700 text-white py-2.5 rounded-xl text-sm font-medium">
        ✅ إنشاء الحساب
      </button>
      <a href="{{ route('admin.freelancers.index') }}"
         class="flex-1 text-center border border-gray-200 text-gray-600 py-2.5 rounded-xl text-sm">
        إلغاء
      </a>
    </div>
  </form>
</div>
@endsection

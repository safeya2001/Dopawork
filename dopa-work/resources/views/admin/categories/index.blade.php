@extends('layouts.admin')
@section('title', 'إدارة الفئات')
@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">🗂️ إدارة الفئات</h1>
    <a href="{{ route('admin.categories.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition-colors">
      + إضافة فئة
    </a>
  </div>

  @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 mb-5 text-sm">✅ {{ session('success') }}</div>@endif

  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    @if($categories->isEmpty())
      <div class="p-12 text-center text-gray-400 text-sm">لا توجد فئات</div>
    @else
      <table class="w-full text-sm">
        <thead><tr class="bg-gray-50 text-xs text-gray-500 uppercase border-b">
          <th class="px-5 py-3 text-start">الاسم</th>
          <th class="px-5 py-3 text-start">الاسم بالعربية</th>
          <th class="px-5 py-3 text-start">الأيقونة</th>
          <th class="px-5 py-3 text-start">الفئات الفرعية</th>
          <th class="px-5 py-3 text-start">الحالة</th>
          <th class="px-5 py-3 text-start">إجراء</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-50">
          @foreach($categories as $cat)
          <tr class="hover:bg-gray-50">
            <td class="px-5 py-4 font-semibold text-gray-900">{{ $cat->name }}</td>
            <td class="px-5 py-4 text-gray-700">{{ $cat->name_ar }}</td>
            <td class="px-5 py-4 text-2xl">{{ $cat->icon }}</td>
            <td class="px-5 py-4 text-gray-500 text-xs">{{ $cat->children->count() }} فئة فرعية</td>
            <td class="px-5 py-4">
              <span class="{{ $cat->is_active?'bg-green-100 text-green-700':'bg-gray-100 text-gray-500' }} text-xs px-2 py-1 rounded-full">
                {{ $cat->is_active?'نشطة':'معطلة' }}
              </span>
            </td>
            <td class="px-5 py-4">
              <div class="flex gap-2">
                <a href="{{ route('admin.categories.edit', $cat) }}" class="bg-blue-100 text-blue-700 text-xs px-3 py-1.5 rounded-lg hover:bg-blue-200">تعديل</a>
                <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}">
                  @csrf @method('DELETE')
                  <button class="bg-red-100 text-red-700 text-xs px-3 py-1.5 rounded-lg hover:bg-red-200"
                    onclick="return confirm('حذف هذه الفئة؟')">حذف</button>
                </form>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>
</div>
@endsection

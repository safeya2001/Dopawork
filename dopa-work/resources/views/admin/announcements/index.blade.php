@extends('layouts.admin')
@section('title', app()->getLocale()==='ar' ? 'الإشعارات والإعلانات' : 'Announcements')

@section('content')
@php $ar = app()->getLocale()==='ar'; @endphp
<div class="max-w-3xl mx-auto px-4 py-8">
  <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ $ar ? 'إرسال إشعار / إعلان' : 'Send Announcement' }}</h1>

  @if(session('success'))
    <div class="mb-5 rounded-xl bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm font-medium">{{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="mb-5 rounded-xl bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
      <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.announcements.send') }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4 mb-8">
    @csrf

    <div>
      <label class="block text-xs text-gray-500 mb-1">{{ $ar ? 'الجمهور المستهدف' : 'Target Audience' }}</label>
      <select name="audience" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
        <option value="all">{{ $ar ? 'الكل' : 'Everyone' }}</option>
        <option value="freelancers">{{ $ar ? 'المستقلون فقط' : 'Freelancers Only' }}</option>
        <option value="clients">{{ $ar ? 'العملاء فقط' : 'Clients Only' }}</option>
      </select>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-xs text-gray-500 mb-1">{{ $ar ? 'العنوان (إنجليزي)' : 'Title (English)' }}</label>
        <input type="text" name="title" maxlength="200" placeholder="Announcement title..."
               class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('title') border-red-400 @enderror">
        @error('title')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="block text-xs text-gray-500 mb-1">{{ $ar ? 'العنوان (عربي)' : 'Title (Arabic)' }}</label>
        <input type="text" name="title_ar" maxlength="200" dir="rtl" placeholder="عنوان الإعلان..."
               class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('title_ar') border-red-400 @enderror">
        @error('title_ar')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-xs text-gray-500 mb-1">{{ $ar ? 'نص الإشعار (إنجليزي)' : 'Body (English)' }}</label>
        <textarea name="body" rows="4" maxlength="1000" placeholder="Announcement body..."
                  class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none @error('body') border-red-400 @enderror"></textarea>
        @error('body')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="block text-xs text-gray-500 mb-1">{{ $ar ? 'نص الإشعار (عربي)' : 'Body (Arabic)' }}</label>
        <textarea name="body_ar" rows="4" maxlength="1000" dir="rtl" placeholder="نص الإشعار..."
                  class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none @error('body_ar') border-red-400 @enderror"></textarea>
        @error('body_ar')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
      </div>
    </div>

    <div class="flex justify-end">
      <button type="submit"
              class="px-6 py-2.5 text-sm font-medium rounded-xl bg-primary-600 text-white hover:bg-primary-700 transition-colors">
        🔔 {{ $ar ? 'إرسال الإشعار' : 'Send Announcement' }}
      </button>
    </div>
  </form>

  {{-- Recent Announcements --}}
  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-4 border-b border-gray-100">
      <h2 class="font-semibold text-gray-900 text-sm">{{ $ar ? 'الإشعارات الأخيرة' : 'Recent Announcements' }}</h2>
    </div>
    <div class="divide-y divide-gray-50">
      @forelse($recent as $n)
      <div class="px-4 py-3">
        <p class="text-sm font-medium text-gray-900">{{ $ar ? ($n->title_ar ?: $n->title) : ($n->title ?: $n->title_ar) }}</p>
        <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ $ar ? ($n->body_ar ?: $n->body) : ($n->body ?: $n->body_ar) }}</p>
        <p class="text-xs text-gray-300 mt-1">{{ $n->created_at->diffForHumans() }}</p>
      </div>
      @empty
      <p class="px-4 py-6 text-center text-sm text-gray-400">{{ $ar ? 'لا توجد إشعارات بعد' : 'No announcements yet' }}</p>
      @endforelse
    </div>
  </div>

</div>
@endsection

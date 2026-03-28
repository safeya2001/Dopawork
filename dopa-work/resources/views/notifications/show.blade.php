@extends('layouts.app')
@section('title', app()->getLocale() === 'ar' ? ($notification->title_ar ?: $notification->title) : ($notification->title ?: $notification->title_ar))

@section('content')
@php $ar = app()->getLocale()==='ar'; @endphp
<div class="max-w-2xl mx-auto px-4 py-10">

  {{-- Back --}}
  <a href="{{ route('notifications.index') }}"
     class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-primary-600 mb-6">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
    </svg>
    {{ $ar ? 'العودة للإشعارات' : 'Back to Notifications' }}
  </a>

  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

    {{-- Header --}}
    <div class="px-6 py-5 border-b border-gray-100 flex items-start gap-4">
      <div class="shrink-0 w-12 h-12 rounded-xl flex items-center justify-center text-2xl
        {{ $notification->type === 'announcement'       ? 'bg-blue-100'   :
           ($notification->type === 'identity_approved' ? 'bg-green-100'  :
           ($notification->type === 'identity_rejected' ? 'bg-red-100'    :
           ($notification->type === 'order_placed'      ? 'bg-orange-100' :
           ($notification->type === 'payment'           ? 'bg-emerald-100': 'bg-gray-100')))) }}">
        {{ $notification->type === 'announcement'       ? '📢' :
           ($notification->type === 'identity_approved' ? '✅' :
           ($notification->type === 'identity_rejected' ? '❌' :
           ($notification->type === 'order_placed'      ? '📦' :
           ($notification->type === 'payment'           ? '💳' : '🔔')))) }}
      </div>
      <div class="flex-1 min-w-0">
        <h1 class="text-lg font-bold text-gray-900 leading-snug">
          {{ app()->getLocale() === 'ar' ? ($notification->title_ar ?: $notification->title) : ($notification->title ?: $notification->title_ar) }}
        </h1>
        <div class="flex items-center gap-3 mt-1.5">
          <span class="text-xs text-gray-400">{{ $notification->created_at->format('Y/m/d — H:i') }}</span>
          <span class="text-xs text-gray-300">•</span>
          <span class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
          @if($notification->is_read)
            <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">{{ $ar ? 'مقروء' : 'Read' }}</span>
          @else
            <span class="text-xs bg-orange-100 text-orange-600 px-2 py-0.5 rounded-full">{{ $ar ? 'جديد' : 'New' }}</span>
          @endif
        </div>
      </div>
    </div>

    {{-- Body --}}
    <div class="px-6 py-6">
      <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed whitespace-pre-wrap">{{ app()->getLocale() === 'ar' ? ($notification->body_ar ?: $notification->body) : ($notification->body ?: $notification->body_ar) }}</div>
    </div>

    {{-- Action button if exists --}}
    @if($notification->action_url)
      <div class="px-6 pb-6">
        <a href="{{ $notification->action_url }}"
           class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium px-5 py-2.5 rounded-xl transition-colors">
          {{ $ar ? 'الانتقال للصفحة' : 'Go to page' }}
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
          </svg>
        </a>
      </div>
    @endif

  </div>

</div>
@endsection

@extends('layouts.app')
@section('title', app()->getLocale()==='ar' ? 'الرسائل' : 'Messages')

@section('content')
@php $isAr = app()->getLocale() === 'ar'; $me = auth()->id(); @endphp

<div class="max-w-3xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-gray-900">
            💬 {{ $isAr ? 'الرسائل' : 'Messages' }}
        </h1>
        <span class="text-sm text-gray-400">{{ $conversations->total() }} {{ $isAr ? 'محادثة' : 'conversations' }}</span>
    </div>

    @if($conversations->isEmpty())
        {{-- Empty State --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
            <div class="text-6xl mb-4">💬</div>
            <h3 class="text-lg font-semibold text-gray-700 mb-2">
                {{ $isAr ? 'لا توجد رسائل بعد' : 'No messages yet' }}
            </h3>
            <p class="text-sm text-gray-400 mb-6">
                {{ $isAr ? 'تواصل مع مستقل لبدء محادثة' : 'Contact a freelancer to start a conversation' }}
            </p>
            <a href="{{ route('freelancers.index') }}"
               class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-medium px-5 py-2.5 rounded-xl text-sm transition-colors">
                {{ $isAr ? 'تصفح المستقلين' : 'Browse Freelancers' }}
            </a>
        </div>

    @else
        {{-- Conversations List --}}
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden divide-y divide-gray-50">
            @foreach($conversations as $conv)
                @php $other = $conv->getOtherParticipant($me); @endphp
                <a href="{{ route('messages.show', $conv) }}"
                   class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 transition-colors group">

                    {{-- Avatar --}}
                    <div class="relative shrink-0">
                        <img src="{{ $other?->avatar ? Storage::url($other->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($other?->name ?? '?').'&color=3b82f6&background=dbeafe&size=48' }}"
                             class="w-12 h-12 rounded-full object-cover border-2 border-gray-100" alt="">
                        {{-- Unread badge --}}
                        @php
                            $unread = $conv->messages->where('sender_id', '!=', $me)->where('is_read', false)->count();
                        @endphp
                        @if($unread > 0)
                            <span class="absolute -top-1 -right-1 w-5 h-5 bg-primary-600 text-white text-xs font-bold rounded-full flex items-center justify-center">
                                {{ $unread > 9 ? '9+' : $unread }}
                            </span>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <p class="font-semibold text-gray-900 text-sm truncate">{{ $other?->name ?? '—' }}</p>
                            <span class="text-xs text-gray-400 shrink-0">
                                {{ $conv->last_message_at?->diffForHumans() ?? $conv->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 truncate mt-0.5">
                            {{ $conv->latestMessage?->body ?? ($isAr ? 'ابدأ المحادثة...' : 'Start the conversation...') }}
                        </p>
                        @if($conv->service)
                            <span class="inline-block mt-1 text-xs bg-primary-50 text-primary-600 px-2 py-0.5 rounded-md">
                                📌 {{ $conv->service->title }}
                            </span>
                        @endif
                    </div>

                    {{-- Arrow --}}
                    <svg class="w-4 h-4 text-gray-300 shrink-0 group-hover:text-primary-400 transition-colors {{ $isAr ? 'rotate-180' : '' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($conversations->hasPages())
            <div class="mt-4">{{ $conversations->links() }}</div>
        @endif
    @endif
</div>
@endsection

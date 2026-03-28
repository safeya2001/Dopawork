@extends('layouts.app')
@section('title', app()->getLocale()==='ar' ? 'محادثة' : 'Conversation')

@section('content')
@php $isAr = app()->getLocale() === 'ar'; $me = auth()->id(); $other = $conversation->getOtherParticipant($me); @endphp

<div class="max-w-3xl mx-auto flex flex-col" style="height: calc(100dvh - 64px - 64px);">

    {{-- ===== HEADER ===== --}}
    <div class="bg-white border-b border-gray-100 px-4 py-3 flex items-center gap-3 shrink-0">
        <a href="{{ route('messages.index') }}" class="p-2 rounded-xl hover:bg-gray-100 transition-colors {{ $isAr ? 'rotate-180' : '' }}">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>

        <img src="{{ $other?->avatar ? Storage::url($other->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($other?->name ?? '?').'&color=3b82f6&background=dbeafe&size=40' }}"
             class="w-10 h-10 rounded-full object-cover border-2 border-gray-100 shrink-0" alt="">

        <div class="flex-1 min-w-0">
            <p class="font-semibold text-gray-900 text-sm truncate">{{ $other?->name }}</p>
            @if($conversation->service)
                <p class="text-xs text-gray-400 truncate">📌 {{ $conversation->service->title }}</p>
            @elseif($other?->freelancerProfile)
                <p class="text-xs text-gray-400 truncate">{{ $other->freelancerProfile->professional_title ?? $other->freelancerProfile->professional_title_ar }}</p>
            @endif
        </div>

        @if($conversation->order)
            <a href="{{ auth()->user()->isFreelancer() ? route('freelancer.orders.show', $conversation->order) : route('client.orders.show', $conversation->order) }}"
               class="text-xs bg-primary-50 text-primary-600 font-medium px-3 py-1.5 rounded-xl hover:bg-primary-100 transition-colors shrink-0">
                {{ $isAr ? 'الطلب' : 'Order' }} #{{ $conversation->order->order_number }}
            </a>
        @endif
    </div>

    {{-- ===== MESSAGES AREA ===== --}}
    <div class="flex-1 overflow-y-auto px-4 py-4 space-y-3 bg-gray-50" id="messagesArea">

        @if($messages->isEmpty())
            <div class="text-center py-10">
                <div class="text-5xl mb-3">👋</div>
                <p class="text-sm text-gray-400">{{ $isAr ? 'ابدأ المحادثة بإرسال رسالة' : 'Start the conversation by sending a message' }}</p>
            </div>
        @endif

        @foreach($messages as $msg)
            @php $isMine = $msg->sender_id === $me; @endphp

            <div class="flex items-end gap-2 {{ $isMine ? 'flex-row-reverse' : 'flex-row' }}">

                {{-- Avatar (other party only) --}}
                @if(!$isMine)
                    <img src="{{ $other?->avatar ? Storage::url($other->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($other?->name ?? '?').'&color=3b82f6&background=dbeafe&size=32' }}"
                         class="w-7 h-7 rounded-full object-cover shrink-0 mb-1" alt="">
                @endif

                {{-- Bubble --}}
                <div class="max-w-xs lg:max-w-sm">
                    <div class="px-4 py-2.5 rounded-2xl text-sm leading-relaxed break-words
                        {{ $isMine
                            ? 'bg-primary-600 text-white rounded-br-sm'
                            : 'bg-white border border-gray-100 text-gray-800 rounded-bl-sm shadow-sm' }}">

                        {{-- Attachment --}}
                        @if($msg->attachment)
                            @php
                                $isImage = str_starts_with($msg->attachment_type ?? '', 'image/');
                                $fileUrl = Storage::url($msg->attachment);
                                $fileName = basename($msg->attachment);
                                $ext = strtoupper(pathinfo($fileName, PATHINFO_EXTENSION));
                                $fileIcons = ['PDF'=>'📄','DOC'=>'📝','DOCX'=>'📝','XLS'=>'📊','XLSX'=>'📊','ZIP'=>'🗜️','RAR'=>'🗜️'];
                                $fileIcon = $fileIcons[$ext] ?? '📎';
                            @endphp
                            @if($isImage)
                                <a href="{{ $fileUrl }}" target="_blank" class="{{ $msg->body ? 'mb-2 block' : '' }}">
                                    <img src="{{ $fileUrl }}" alt="attachment"
                                         class="rounded-xl max-w-full max-h-52 object-cover">
                                </a>
                            @else
                                <a href="{{ $fileUrl }}" target="_blank" download
                                   class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl
                                          {{ $isMine ? 'bg-white/15' : 'bg-primary-50' }}
                                          hover:opacity-80 transition-opacity {{ $msg->body ? 'mb-2' : '' }}">
                                    <span class="text-2xl leading-none">{{ $fileIcon }}</span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold truncate max-w-[160px] {{ $isMine ? 'text-white' : 'text-gray-800' }}">
                                            {{ $fileName }}
                                        </p>
                                        <p class="text-xs opacity-60 mt-0.5">{{ $ext }} · {{ $isAr ? 'اضغط للتحميل' : 'Tap to download' }}</p>
                                    </div>
                                    <svg class="w-4 h-4 shrink-0 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                </a>
                            @endif
                        @endif

                        {{-- Message body --}}
                        @if($msg->body)
                            {{ $msg->body }}
                        @endif
                    </div>
                    <p class="text-xs text-gray-400 mt-1 {{ $isMine ? 'text-end' : 'text-start' }}">
                        {{ $msg->created_at->format('h:i A') }}
                        @if($isMine)
                            · {{ $msg->is_read ? ($isAr ? 'تمت القراءة' : 'Read') : ($isAr ? 'تم الإرسال' : 'Sent') }}
                        @endif
                    </p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ===== INPUT AREA ===== --}}
    <div class="bg-white border-t border-gray-100 px-4 py-3 shrink-0">
        <form action="{{ route('messages.send', $conversation) }}" method="POST"
              enctype="multipart/form-data" class="flex items-end gap-3">
            @csrf

            {{-- Attachment button --}}
            <label for="attachment" class="p-2 rounded-xl text-gray-400 hover:text-primary-600 hover:bg-primary-50 cursor-pointer transition-colors shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                </svg>
                <input type="file" id="attachment" name="attachment" class="hidden" accept="image/*,.pdf,.doc,.docx">
            </label>

            {{-- Text input --}}
            <div class="flex-1">
            <textarea name="body" id="msgInput" rows="1" maxlength="2000"
                    placeholder="{{ $isAr ? 'اكتب رسالتك...' : 'Write a message...' }}"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-100 resize-none transition-all {{ $isAr ? 'text-right' : 'text-left' }}"
                    oninput="autoResize(this)"></textarea>
            </div>

            {{-- Send button --}}
            <button type="submit"
                class="w-11 h-11 bg-primary-600 hover:bg-primary-700 text-white rounded-2xl flex items-center justify-center shrink-0 transition-colors shadow-sm shadow-primary-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
            </button>
        </form>

        {{-- Attachment preview --}}
        <div id="attachPreview" class="hidden mt-2 text-xs text-primary-600 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
            <span id="attachName"></span>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-scroll to bottom
    const area = document.getElementById('messagesArea');
    if (area) area.scrollTop = area.scrollHeight;

    // Auto-resize textarea
    function autoResize(el) {
        el.style.height = 'auto';
        el.style.height = Math.min(el.scrollHeight, 120) + 'px';
    }

    // Send on Enter (Shift+Enter for newline)
    document.getElementById('msgInput')?.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            this.closest('form').submit();
        }
    });

    // Show attachment filename
    document.getElementById('attachment')?.addEventListener('change', function() {
        const preview = document.getElementById('attachPreview');
        const name = document.getElementById('attachName');
        if (this.files.length > 0) {
            name.textContent = this.files[0].name;
            preview.classList.remove('hidden');
        } else {
            preview.classList.add('hidden');
        }
    });
</script>
@endpush
@endsection

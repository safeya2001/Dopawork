@extends('layouts.app')
@section('title', app()->getLocale()==='ar' ? 'الإشعارات' : 'Notifications')

@section('content')
@php $ar = app()->getLocale()==='ar'; @endphp
<div class="max-w-3xl mx-auto px-4 py-10">

  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">🔔 {{ $ar ? 'الإشعارات' : 'Notifications' }}</h1>
    @if($notifications->total() > 0)
      <button onclick="markAllReadPage()"
              class="text-sm text-primary-600 hover:underline font-medium">
        {{ $ar ? 'تحديد الكل كمقروء' : 'Mark all as read' }}
      </button>
    @endif
  </div>

  {{-- Filter tabs --}}
  <div class="flex gap-2 mb-6">
    <a href="{{ route('notifications.index') }}"
       class="{{ !request('filter') || request('filter') === 'all' ? 'bg-primary-600 text-white' : 'bg-white text-gray-600 border border-gray-200' }} px-4 py-2 rounded-xl text-sm font-medium">
      {{ $ar ? 'الكل' : 'All' }}
    </a>
    <a href="{{ route('notifications.index', ['filter' => 'unread']) }}"
       class="{{ request('filter') === 'unread' ? 'bg-primary-600 text-white' : 'bg-white text-gray-600 border border-gray-200' }} px-4 py-2 rounded-xl text-sm font-medium">
      {{ $ar ? 'غير مقروءة' : 'Unread' }}
      @php $unreadCount = auth()->user()->notifications()->where('is_read', false)->count(); @endphp
      @if($unreadCount > 0)
        <span class="ms-1 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5">{{ $unreadCount }}</span>
      @endif
    </a>
  </div>

  {{-- List --}}
  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden divide-y divide-gray-50" id="notif-page-list">
    @forelse($notifications as $n)
      <a href="{{ route('notifications.show', $n) }}"
         class="flex items-start gap-4 px-5 py-4 hover:bg-gray-50 transition-colors {{ !$n->is_read ? 'bg-orange-50/40' : '' }}"
         id="notif-row-{{ $n->id }}">

        {{-- Icon by type --}}
        <div class="shrink-0 w-10 h-10 rounded-xl flex items-center justify-center text-xl
          {{ $n->type === 'announcement' ? 'bg-blue-100' :
             ($n->type === 'identity_approved' ? 'bg-green-100' :
             ($n->type === 'identity_rejected' ? 'bg-red-100' : 'bg-gray-100')) }}">
          {{ $n->type === 'announcement' ? '📢' :
             ($n->type === 'identity_approved' ? '✅' :
             ($n->type === 'identity_rejected' ? '❌' :
             ($n->type === 'order_placed' ? '📦' :
             ($n->type === 'payment' ? '💳' : '🔔')))) }}
        </div>

        <div class="flex-1 min-w-0">
          <div class="flex items-start justify-between gap-2">
            <p class="text-sm font-semibold text-gray-900">
              {{ app()->getLocale() === 'ar' ? ($n->title_ar ?: $n->title) : ($n->title ?: $n->title_ar) }}
            </p>
            @if(!$n->is_read)
              <span class="shrink-0 w-2 h-2 bg-orange-500 rounded-full mt-1.5"></span>
            @endif
          </div>
          <p class="text-sm text-gray-600 mt-0.5">
            {{ app()->getLocale() === 'ar' ? ($n->body_ar ?: $n->body) : ($n->body ?: $n->body_ar) }}
          </p>
          <p class="text-xs text-gray-400 mt-1">{{ $n->created_at->diffForHumans() }}</p>
        </div>

      </a>
    @empty
      <div class="p-16 text-center text-gray-400">
        <span class="text-5xl block mb-3">📭</span>
        <p class="text-sm">{{ $ar ? 'لا توجد إشعارات' : 'No notifications yet' }}</p>
      </div>
    @endforelse
  </div>

  {{-- Pagination --}}
  @if($notifications->hasPages())
    <div class="mt-4">{{ $notifications->links() }}</div>
  @endif

</div>

<script>
function markReadPage(id) {
    fetch(`/notifications/${id}/read`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    }).then(() => {
        const row = document.getElementById('notif-row-' + id);
        if (row) {
            row.classList.remove('bg-orange-50/40');
            row.querySelector('.bg-orange-500')?.remove();
            row.querySelector('button[onclick*="markReadPage"]')?.remove();
        }
    });
}

function markAllReadPage() {
    fetch('{{ route("notifications.markAllRead") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    }).then(() => location.reload());
}
</script>
@endsection

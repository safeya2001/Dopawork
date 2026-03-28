@extends('layouts.app')
@section('title', $order->order_number)

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">

  {{-- Breadcrumb --}}
  <nav class="text-sm text-gray-400 mb-5 flex items-center gap-2">
    <a href="{{ route('client.dashboard') }}" class="hover:text-primary-600">{{ app()->getLocale()==='ar'?'لوحتي':'Dashboard' }}</a>
    <span>/</span>
    <a href="{{ route('client.orders.index') }}" class="hover:text-primary-600">{{ app()->getLocale()==='ar'?'الطلبات':'Orders' }}</a>
    <span>/</span>
    <span class="text-gray-600">{{ $order->order_number }}</span>
  </nav>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Main --}}
    <div class="lg:col-span-2 space-y-5">

      {{-- Header card --}}
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-start justify-between flex-wrap gap-3">
          <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $order->order_number }}</h1>
            <p class="text-gray-500 text-sm mt-1">{{ $order->title }}</p>
          </div>
          @include('components.status-badge', ['status' => $order->status])
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4 pt-4 border-t border-gray-50 text-sm">
          <div>
            <p class="text-gray-400 text-xs">{{ app()->getLocale()==='ar'?'المبلغ':'Amount' }}</p>
            <p class="font-bold text-primary-700">{{ number_format($order->total_amount,3) }} JOD</p>
          </div>
          <div>
            <p class="text-gray-400 text-xs">{{ app()->getLocale()==='ar'?'الموعد':'Deadline' }}</p>
            <p class="font-semibold text-gray-800">{{ $order->deadline?->format('Y/m/d') ?? '—' }}</p>
          </div>
          <div>
            <p class="text-gray-400 text-xs">{{ app()->getLocale()==='ar'?'التعديلات':'Revisions' }}</p>
            <p class="font-semibold text-gray-800">{{ $order->revisions_used }}/{{ $order->revisions_allowed }}</p>
          </div>
          <div>
            <p class="text-gray-400 text-xs">{{ app()->getLocale()==='ar'?'التاريخ':'Date' }}</p>
            <p class="font-semibold text-gray-800">{{ $order->created_at->format('Y/m/d') }}</p>
          </div>
        </div>
      </div>

      {{-- Requirements --}}
      @if($order->requirements)
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
          <h3 class="font-semibold text-gray-900 mb-2">{{ app()->getLocale()==='ar'?'المتطلبات':'Requirements' }}</h3>
          <p class="text-gray-600 text-sm whitespace-pre-line">{{ $order->requirements }}</p>
        </div>
      @endif

      {{-- Milestones --}}
      @if($order->milestones->count())
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
          <h3 class="font-semibold text-gray-900 mb-3">{{ app()->getLocale()==='ar'?'المراحل':'Milestones' }}</h3>
          <div class="space-y-3">
            @foreach($order->milestones as $ms)
              <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                <div class="flex items-center gap-3">
                  <span class="{{ $ms->status==='approved'?'text-green-500':($ms->status==='delivered'?'text-blue-500':'text-gray-300') }} text-lg">
                    {{ $ms->status==='approved'?'✓':($ms->status==='delivered'?'📦':'○') }}
                  </span>
                  <span class="text-sm text-gray-700">{{ $ms->title }}</span>
                </div>
                <span class="text-sm font-semibold text-primary-700">{{ number_format($ms->amount,3) }} JOD</span>
              </div>
            @endforeach
          </div>
        </div>
      @endif

      {{-- Actions --}}
      @if(in_array($order->status, ['delivered']))
        <div class="bg-green-50 border border-green-200 rounded-2xl p-5">
          <h3 class="font-semibold text-green-900 mb-2">
            📦 {{ app()->getLocale()==='ar'?'تم تسليم الطلب — يرجى المراجعة':'Order Delivered — Please Review' }}
          </h3>
          <p class="text-sm text-green-700 mb-4">
            {{ app()->getLocale()==='ar' ? 'راجع التسليم وافق عليه لصرف المبلغ للمستقل.' : 'Review the delivery and accept it to release payment to the freelancer.' }}
          </p>
          <div class="flex gap-3 flex-wrap">
            <form method="POST" action="{{ route('client.orders.complete', $order) }}">
              @csrf
              <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-medium transition-colors"
                onclick="return confirm('{{ app()->getLocale()==='ar'?'هل أنت متأكد من قبول التسليم؟':'Accept this delivery?'}}')">
                ✓ {{ app()->getLocale()==='ar'?'قبول التسليم':'Accept Delivery' }}
              </button>
            </form>
            @if($order->revisions_used < $order->revisions_allowed)
              <button onclick="document.getElementById('revisionForm').classList.toggle('hidden')"
                      class="border border-orange-400 text-orange-600 hover:bg-orange-50 px-5 py-2.5 rounded-xl text-sm font-medium transition-colors">
                ↩ {{ app()->getLocale()==='ar'?'طلب تعديل':'Request Revision' }}
              </button>
            @endif
          </div>
          <div id="revisionForm" class="hidden mt-4">
            <form method="POST" action="{{ route('client.orders.revision', $order) }}">
              @csrf
              <textarea name="note" rows="3" required
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-primary-400 resize-none"
                placeholder="{{ app()->getLocale()==='ar'?'اوصف التعديل المطلوب...':'Describe the revision needed...' }}"></textarea>
              <button type="submit" class="mt-2 bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-xl text-sm font-medium">
                {{ app()->getLocale()==='ar'?'إرسال طلب التعديل':'Submit Revision Request' }}
              </button>
            </form>
          </div>
        </div>
      @endif

      @if($order->status === 'pending')
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
          <button onclick="document.getElementById('cancelForm').classList.toggle('hidden')"
                  class="border border-red-300 text-red-600 hover:bg-red-50 px-4 py-2 rounded-xl text-sm font-medium transition-colors">
            {{ app()->getLocale()==='ar'?'إلغاء الطلب':'Cancel Order' }}
          </button>
          <div id="cancelForm" class="hidden mt-4">
            <form method="POST" action="{{ route('client.orders.cancel', $order) }}">
              @csrf
              <textarea name="reason" rows="2" required
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-red-400 resize-none"
                placeholder="{{ app()->getLocale()==='ar'?'سبب الإلغاء...':'Reason for cancellation...' }}"></textarea>
              <button type="submit" class="mt-2 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-xl text-sm font-medium">
                {{ app()->getLocale()==='ar'?'تأكيد الإلغاء':'Confirm Cancel' }}
              </button>
            </form>
          </div>
        </div>
      @endif

      @if($order->status === 'completed')
        {{-- Receipt downloads --}}
        <div class="flex gap-3 flex-wrap">
          <a href="{{ route('client.orders.receipt', $order) }}"
             class="bg-primary-600 text-white px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-primary-700 transition-colors">
            📄 {{ app()->getLocale()==='ar'?'تحميل الإيصال':'Download Receipt' }}
          </a>
          <a href="{{ route('client.orders.receipt', ['order'=>$order,'lang'=>'ar']) }}"
             class="border border-gray-200 text-gray-600 px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition-colors">
            📄 {{ app()->getLocale()==='ar'?'إيصال عربي':'Arabic Receipt' }}
          </a>
        </div>

        {{-- Review Section --}}
        @if($order->review)
          <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-5">
            <h3 class="font-semibold text-yellow-900 mb-3">
              ⭐ {{ app()->getLocale()==='ar'?'تقييمك للمستقل':'Your Review' }}
            </h3>
            <div class="flex items-center gap-1 mb-2">
              @for($i = 1; $i <= 5; $i++)
                <span class="{{ $i <= $order->review->rating ? 'text-yellow-400' : 'text-gray-300' }} text-xl">★</span>
              @endfor
              <span class="text-sm text-gray-500 ms-2">{{ $order->review->rating }}/5</span>
            </div>
            @if($order->review->comment)
              <p class="text-sm text-gray-700 mt-2">{{ $order->review->comment }}</p>
            @endif
          </div>
        @else
          <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5">
            <h3 class="font-semibold text-blue-900 mb-1">
              ⭐ {{ app()->getLocale()==='ar'?'قيّم تجربتك':'Rate Your Experience' }}
            </h3>
            <p class="text-sm text-blue-700 mb-4">
              {{ app()->getLocale()==='ar'?'شاركنا رأيك في العمل المنجز. تقييمك يساعد المجتمع.':'Share your feedback on the completed work. Your review helps the community.' }}
            </p>
            <form method="POST" action="{{ route('client.orders.review', $order) }}" id="reviewForm">
              @csrf
              {{-- Overall Rating --}}
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  {{ app()->getLocale()==='ar'?'التقييم العام':'Overall Rating' }} <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-2" id="starRating">
                  @for($i = 1; $i <= 5; $i++)
                    <button type="button" data-star="{{ $i }}"
                      class="star-btn text-3xl text-gray-300 hover:text-yellow-400 transition-colors focus:outline-none">★</button>
                  @endfor
                </div>
                <input type="hidden" name="rating" id="ratingInput" required>
              </div>
              {{-- Sub-ratings --}}
              <div class="grid grid-cols-3 gap-3 mb-4">
                @foreach([
                  ['name'=>'communication_rating','ar'=>'التواصل','en'=>'Communication'],
                  ['name'=>'quality_rating','ar'=>'جودة العمل','en'=>'Quality'],
                  ['name'=>'delivery_rating','ar'=>'الالتزام بالموعد','en'=>'Delivery'],
                ] as $sub)
                  <div class="bg-white rounded-xl border border-gray-100 p-3 text-center">
                    <p class="text-xs text-gray-500 mb-2">{{ app()->getLocale()==='ar'?$sub['ar']:$sub['en'] }}</p>
                    <div class="flex justify-center gap-1" data-group="{{ $sub['name'] }}">
                      @for($i = 1; $i <= 5; $i++)
                        <button type="button" data-val="{{ $i }}"
                          class="sub-star text-lg text-gray-300 hover:text-yellow-400 transition-colors focus:outline-none">★</button>
                      @endfor
                    </div>
                    <input type="hidden" name="{{ $sub['name'] }}" class="sub-input-{{ $sub['name'] }}">
                  </div>
                @endforeach
              </div>
              {{-- Comment --}}
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  {{ app()->getLocale()==='ar'?'تعليق (اختياري)':'Comment (optional)' }}
                </label>
                <textarea name="comment" rows="3" maxlength="2000"
                  class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-primary-400 resize-none"
                  placeholder="{{ app()->getLocale()==='ar'?'شاركنا تجربتك مع هذا المستقل...':'Share your experience with this freelancer...' }}"></textarea>
              </div>
              <button type="submit" id="reviewSubmit"
                class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                disabled>
                {{ app()->getLocale()==='ar'?'إرسال التقييم':'Submit Review' }}
              </button>
            </form>
            <script>
            (function(){
              const stars = document.querySelectorAll('#starRating .star-btn');
              const input = document.getElementById('ratingInput');
              const submitBtn = document.getElementById('reviewSubmit');
              let selected = 0;
              stars.forEach(btn => {
                btn.addEventListener('mouseover', () => {
                  stars.forEach((s,i) => s.classList.toggle('text-yellow-400', i < parseInt(btn.dataset.star)));
                  stars.forEach((s,i) => s.classList.toggle('text-gray-300', i >= parseInt(btn.dataset.star)));
                });
                btn.addEventListener('click', () => {
                  selected = parseInt(btn.dataset.star);
                  input.value = selected;
                  submitBtn.disabled = false;
                  stars.forEach((s,i) => {
                    s.classList.toggle('text-yellow-400', i < selected);
                    s.classList.toggle('text-gray-300', i >= selected);
                  });
                });
              });
              document.getElementById('starRating').addEventListener('mouseleave', () => {
                stars.forEach((s,i) => {
                  s.classList.toggle('text-yellow-400', i < selected);
                  s.classList.toggle('text-gray-300', i >= selected);
                });
              });
              // Sub-ratings
              document.querySelectorAll('[data-group]').forEach(group => {
                const name = group.dataset.group;
                const subStars = group.querySelectorAll('.sub-star');
                const hiddenInput = document.querySelector('.sub-input-' + name);
                let subSelected = 0;
                subStars.forEach(btn => {
                  btn.addEventListener('mouseover', () => {
                    subStars.forEach((s,i) => s.classList.toggle('text-yellow-400', i < parseInt(btn.dataset.val)));
                    subStars.forEach((s,i) => s.classList.toggle('text-gray-300', i >= parseInt(btn.dataset.val)));
                  });
                  btn.addEventListener('click', () => {
                    subSelected = parseInt(btn.dataset.val);
                    hiddenInput.value = subSelected;
                    subStars.forEach((s,i) => {
                      s.classList.toggle('text-yellow-400', i < subSelected);
                      s.classList.toggle('text-gray-300', i >= subSelected);
                    });
                  });
                });
                group.addEventListener('mouseleave', () => {
                  subStars.forEach((s,i) => {
                    s.classList.toggle('text-yellow-400', i < subSelected);
                    s.classList.toggle('text-gray-300', i >= subSelected);
                  });
                });
              });
            })();
            </script>
          </div>
        @endif
      @endif
    </div>

    {{-- Sidebar --}}
    <div class="space-y-5">
      {{-- Freelancer --}}
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <h3 class="font-semibold text-gray-900 mb-3 text-sm">{{ app()->getLocale()==='ar'?'المستقل':'Freelancer' }}</h3>
        <div class="flex items-center gap-3">
          <img src="https://ui-avatars.com/api/?name={{ urlencode($order->freelancer?->name) }}&size=44&color=3b82f6&background=dbeafe" class="w-11 h-11 rounded-full">
          <div>
            <p class="font-semibold text-gray-900 text-sm">{{ $order->freelancer?->name }}</p>
            <p class="text-xs text-gray-500">{{ $order->freelancer?->freelancerProfile?->display_title }}</p>
          </div>
        </div>
      </div>

      {{-- Escrow --}}
      @if($order->escrow)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
          <h3 class="font-semibold text-gray-900 mb-3 text-sm">🔒 {{ app()->getLocale()==='ar'?'الضمان':'Escrow' }}</h3>
          <div class="space-y-2 text-sm">
            <div class="flex justify-between text-gray-600">
              <span>{{ app()->getLocale()==='ar'?'الحالة':'Status' }}</span>
              @include('components.status-badge', ['status' => $order->escrow->status])
            </div>
            <div class="flex justify-between text-gray-600">
              <span>{{ app()->getLocale()==='ar'?'المبلغ':'Amount' }}</span>
              <span class="font-semibold">{{ number_format($order->escrow->amount,3) }} JOD</span>
            </div>
          </div>
        </div>
      @endif

      {{-- Messages --}}
      @if($order->conversation)
        <a href="{{ route('messages.show', $order->conversation) }}"
           class="block bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:border-primary-200 transition-colors">
          <p class="font-semibold text-gray-900 text-sm">💬 {{ app()->getLocale()==='ar'?'المحادثة':'Conversation' }}</p>
          <p class="text-xs text-gray-400 mt-1">{{ app()->getLocale()==='ar'?'فتح المحادثة':'Open chat' }} →</p>
        </a>
      @endif
    </div>
  </div>
</div>
@endsection

<div class="bg-white rounded-2xl overflow-hidden group card-lift border border-gray-100/80">

    {{-- Cover Image --}}
    <div class="relative h-44 overflow-hidden" style="background:linear-gradient(135deg,#fff7ed 0%,#ffedd5 100%);">
        @if($service->cover_image)
            <img src="{{ Storage::url($service->cover_image) }}"
                 alt="{{ $service->display_title }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        @else
            <div class="w-full h-full flex flex-col items-center justify-center gap-2">
                <span class="text-5xl">{{ $service->category->icon ?? '💼' }}</span>
            </div>
        @endif

        {{-- Featured badge --}}
        @if($service->is_featured)
        <span class="absolute top-2.5 {{ app()->getLocale()==='ar' ? 'left-2.5' : 'right-2.5' }} inline-flex items-center gap-1 bg-amber-400 text-amber-900 text-[10px] font-black px-2 py-0.5 rounded-full shadow">
            ⭐ {{ app()->getLocale()==='ar' ? 'مميز' : 'Featured' }}
        </span>
        @endif

        {{-- Category tag --}}
        @if($service->category)
        <span class="absolute bottom-2.5 {{ app()->getLocale()==='ar' ? 'right-2.5' : 'left-2.5' }} bg-black/40 text-white text-[10px] font-medium px-2 py-0.5 rounded-full backdrop-blur-sm">
            {{ app()->getLocale()==='ar' ? ($service->category->name_ar ?? $service->category->name) : $service->category->name }}
        </span>
        @endif
    </div>

    <div class="p-4">

        {{-- Freelancer Info --}}
        <div class="flex items-center gap-2.5 mb-3">
            <img src="{{ $service->user->avatar ? Storage::url($service->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($service->user->name).'&color=ffffff&background=ea580c&bold=true&size=32' }}"
                 class="w-8 h-8 rounded-full object-cover ring-2 ring-orange-100 shrink-0" alt="">
            <div class="min-w-0 flex-1">
                <p class="text-xs font-bold text-gray-900 truncate">{{ $service->user->name }}</p>
                @if($service->user->freelancerProfile?->display_title)
                    <p class="text-[10px] text-gray-400 truncate">{{ $service->user->freelancerProfile->display_title }}</p>
                @endif
            </div>
            @if($service->user->freelancerProfile?->is_verified)
            <span class="shrink-0 w-4.5 h-4.5 w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-black text-white"
                  style="background:#ea580c;" title="{{ app()->getLocale()==='ar' ? 'موثق' : 'Verified' }}">✓</span>
            @endif
        </div>

        {{-- Title --}}
        <a href="{{ route('services.show', $service->slug) }}" class="block mb-3">
            <h3 class="text-sm font-semibold text-gray-900 hover:text-orange-600 leading-snug line-clamp-2 transition-colors duration-150">
                {{ $service->display_title }}
            </h3>
        </a>

        {{-- Rating --}}
        <div class="mb-3 min-h-[20px]">
        @if($service->reviews_count > 0)
            <div class="flex items-center gap-1">
                <span class="text-amber-400 text-sm leading-none">★</span>
                <span class="text-xs font-bold text-gray-800">{{ number_format($service->rating, 1) }}</span>
                <span class="text-[10px] text-gray-400">({{ $service->reviews_count }})</span>
            </div>
        @endif
        </div>

        {{-- Price + CTA --}}
        <div class="border-t border-gray-50 pt-3 flex items-end justify-between gap-2">
            <div>
                <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">
                    {{ app()->getLocale()==='ar' ? 'يبدأ من' : 'Starting at' }}
                </p>
                <p class="text-xl font-black leading-tight" style="color:#ea580c;">
                    {{ number_format($service->min_price, 3) }}<span class="text-xs font-semibold text-gray-400 ms-1">JOD</span>
                </p>
            </div>
            <a href="{{ route('services.show', $service->slug) }}"
               class="shrink-0 text-xs font-bold text-white px-4 py-2 rounded-xl transition-all hover:-translate-y-0.5 active:scale-95"
               style="background:linear-gradient(135deg,#f97316,#ea580c);box-shadow:0 4px 12px rgba(234,88,12,0.3);">
                {{ app()->getLocale()==='ar' ? 'عرض' : 'View' }}
            </a>
        </div>

    </div>
</div>

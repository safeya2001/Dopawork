<nav id="main-nav" class="bg-white border-b border-gray-100 sticky top-0 z-50 transition-shadow duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <div class="flex items-center gap-2">
                @php
                    $logoUrl = route('home');
                    if (Auth::check()) {
                        $role = Auth::user()->role;
                        if ($role === 'super_admin' || $role === 'admin') {
                            $logoUrl = route('admin.dashboard');
                        } elseif ($role === 'freelancer') {
                            $logoUrl = route('freelancer.dashboard');
                        } else {
                            $logoUrl = route('client.dashboard');
                        }
                    }
                @endphp
                <a href="{{ $logoUrl }}" class="flex items-center gap-0.5" dir="ltr">
                    {{-- dopa logo wordmark --}}
                    <span class="font-black text-2xl tracking-tight leading-none" style="color:#f97316; font-family:'Inter','Cairo',sans-serif;">dopa</span><span class="font-medium text-xl text-gray-500 tracking-tight leading-none" style="font-family:'Inter','Cairo',sans-serif;">work</span>
                </a>
            </div>

            {{-- Center Nav --}}
            <div class="hidden md:flex items-center gap-6">
                <a href="{{ route('services.index') }}" class="text-gray-600 hover:text-primary-600 font-medium text-sm transition-colors">
                    {{ app()->getLocale() === 'ar' ? 'تصفح الخدمات' : 'Browse Services' }}
                </a>
                <a href="{{ route('freelancers.index') }}" class="text-gray-600 hover:text-primary-600 font-medium text-sm transition-colors">
                    {{ app()->getLocale() === 'ar' ? 'المستقلون' : 'Freelancers' }}
                </a>
            </div>

            {{-- Right side --}}
            <div class="flex items-center gap-3">

                {{-- Language Switcher --}}
                <div class="relative">
                    @if(app()->getLocale() === 'ar')
                        <a href="{{ route('set.locale', 'en') }}" class="text-sm text-gray-600 hover:text-primary-600 font-medium px-3 py-1.5 rounded-lg border border-gray-200 hover:border-primary-300 transition-all">
                            EN
                        </a>
                    @else
                        <a href="{{ route('set.locale', 'ar') }}" class="text-sm text-gray-600 hover:text-primary-600 font-medium px-3 py-1.5 rounded-lg border border-gray-200 hover:border-primary-300 transition-all">
                            عربي
                        </a>
                    @endif
                </div>

                @auth
                    {{-- Notifications Bell --}}
                    <div class="relative" id="notifWrapper">
                        <button onclick="toggleNotifDropdown()"
                                class="relative p-2 text-gray-500 hover:text-primary-600 transition-colors"
                                title="الإشعارات">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span id="notif-badge"
                                  class="absolute top-0.5 right-0.5 hidden min-w-[16px] h-4 px-0.5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                            </span>
                        </button>

                        {{-- Dropdown --}}
                        <div id="notifDropdown"
                             class="hidden absolute {{ app()->getLocale() === 'ar' ? 'left-0' : 'right-0' }} mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 z-50 overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 bg-gray-50">
                                <p class="text-sm font-bold text-gray-800">🔔 {{ app()->getLocale()==='ar' ? 'الإشعارات' : 'Notifications' }}</p>
                                <button onclick="markAllRead()" class="text-xs text-primary-600 hover:underline">{{ app()->getLocale()==='ar' ? 'تحديد الكل كمقروء' : 'Mark all read' }}</button>
                            </div>
                            <div id="notif-list" class="max-h-80 overflow-y-auto divide-y divide-gray-50">
                                <p class="text-center text-gray-400 text-xs py-8">{{ app()->getLocale()==='ar' ? 'جاري التحميل...' : 'Loading...' }}</p>
                            </div>
                            <div class="px-4 py-2 border-t border-gray-100 text-center bg-gray-50">
                                <a href="{{ route('notifications.index') }}" class="text-xs text-primary-600 hover:underline font-medium">{{ app()->getLocale()==='ar' ? 'عرض كل الإشعارات' : 'View all notifications' }}</a>
                            </div>
                        </div>
                    </div>

                    <script>
                    function toggleNotifDropdown() {
                        const dd = document.getElementById('notifDropdown');
                        const isHidden = dd.classList.contains('hidden');
                        // close user menu if open
                        document.getElementById('userMenuDropdown')?.classList.add('hidden');
                        if (isHidden) {
                            dd.classList.remove('hidden');
                            loadNotifications();
                        } else {
                            dd.classList.add('hidden');
                        }
                    }

                    function loadNotifications() {
                        fetch('{{ route("notifications.fetch") }}')
                            .then(r => r.json())
                            .then(data => {
                                const badge = document.getElementById('notif-badge');
                                if (data.unread > 0) {
                                    badge.textContent = data.unread > 99 ? '99+' : data.unread;
                                    badge.classList.remove('hidden');
                                    badge.classList.add('flex');
                                } else {
                                    badge.classList.add('hidden');
                                }

                                const list = document.getElementById('notif-list');
                                if (data.notifications.length === 0) {
                                    list.innerHTML = '<p class="text-center text-gray-400 text-xs py-8">لا توجد إشعارات</p>';
                                    return;
                                }

                                list.innerHTML = data.notifications.map(n => `
                                    <a href="/notifications/${n.id}"
                                       class="block px-4 py-3 hover:bg-gray-50 transition-colors ${n.is_read ? '' : 'bg-orange-50/60'}">
                                        <div class="flex items-start gap-2">
                                            ${!n.is_read ? '<span class="mt-1.5 w-2 h-2 bg-orange-500 rounded-full shrink-0"></span>' : '<span class="mt-1.5 w-2 h-2 shrink-0"></span>'}
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-semibold text-gray-900 truncate">${n.title}</p>
                                                <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">${n.body}</p>
                                                <p class="text-[10px] text-gray-400 mt-1">${n.created_at}</p>
                                            </div>
                                        </div>
                                    </a>
                                `).join('');
                            });
                    }

                    function markRead(id, el) {
                        fetch(`/notifications/${id}/read`, {
                            method: 'POST',
                            headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'}
                        });
                        el.classList.remove('bg-blue-50/50');
                        el.querySelector('span.bg-blue-500')?.classList.replace('bg-blue-500', 'bg-transparent');
                        // lower badge
                        const badge = document.getElementById('notif-badge');
                        const cur = parseInt(badge.textContent) || 0;
                        if (cur - 1 <= 0) badge.classList.add('hidden');
                        else badge.textContent = cur - 1;
                    }

                    function markAllRead() {
                        fetch('{{ route("notifications.markAllRead") }}', {
                            method: 'POST',
                            headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'}
                        }).then(() => {
                            document.getElementById('notif-badge').classList.add('hidden');
                        document.querySelectorAll('#notif-list .bg-orange-50\/60').forEach(el => {
                                el.classList.remove('bg-orange-50/60');
                            });
                            document.querySelectorAll('#notif-list span.bg-orange-500').forEach(el => {
                                el.classList.replace('bg-orange-500','bg-transparent');
                            });
                        });
                    }

                    // Load badge count on page load
                    document.addEventListener('DOMContentLoaded', function() {
                        fetch('{{ route("notifications.fetch") }}')
                            .then(r => r.json())
                            .then(data => {
                                const badge = document.getElementById('notif-badge');
                                if (data.unread > 0) {
                                    badge.textContent = data.unread > 99 ? '99+' : data.unread;
                                    badge.classList.remove('hidden');
                                    badge.classList.add('flex');
                                }
                            });
                    });

                    // Close notif dropdown when clicking outside
                    document.addEventListener('click', function(e) {
                        if (!document.getElementById('notifWrapper')?.contains(e.target)) {
                            document.getElementById('notifDropdown')?.classList.add('hidden');
                        }
                    });
                    </script>

                    {{-- User Menu --}}
                    <div class="relative" id="userMenuWrapper">
                        <button onclick="toggleUserMenu()" class="flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-primary-600">
                            <img src="{{ auth()->user()->avatar ? Storage::url(auth()->user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&color=ffffff&background=ea580c&bold=true' }}"
                                 class="w-8 h-8 rounded-full object-cover ring-2 ring-orange-100" alt="">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div id="userMenuDropdown" class="absolute {{ app()->getLocale() === 'ar' ? 'left-0' : 'right-0' }} mt-2 w-52 bg-white rounded-xl shadow-lg border border-gray-100 py-2 hidden z-50">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                            </div>
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    🛡️ {{ app()->getLocale() === 'ar' ? 'لوحة الإدارة' : 'Admin Panel' }}
                                </a>
                            @elseif(auth()->user()->isFreelancer())
                                <a href="{{ route('freelancer.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    💼 {{ app()->getLocale() === 'ar' ? 'لوحتي' : 'My Dashboard' }}
                                </a>
                                <a href="{{ route('freelancer.projects.browse') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    🔍 {{ app()->getLocale() === 'ar' ? 'تصفح المشاريع' : 'Browse Projects' }}
                                </a>
                                <a href="{{ route('freelancer.proposals.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    📋 {{ app()->getLocale() === 'ar' ? 'عروضي' : 'My Proposals' }}
                                </a>
                                <a href="{{ route('freelancer.contracts.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    🤝 {{ app()->getLocale() === 'ar' ? 'عقودي النشطة' : 'Active Contracts' }}
                                </a>
                            @else
                                <a href="{{ route('client.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    📊 {{ app()->getLocale() === 'ar' ? 'لوحتي' : 'My Dashboard' }}
                                </a>
                                <a href="{{ route('client.projects.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    📋 {{ app()->getLocale() === 'ar' ? 'مشاريعي' : 'My Projects' }}
                                </a>
                            @endif
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                👤 {{ app()->getLocale() === 'ar' ? 'ملفي الشخصي' : 'My Profile' }}
                            </a>
                            <a href="{{ route('wallet.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                💰 {{ app()->getLocale() === 'ar' ? 'محفظتي' : 'My Wallet' }}
                                <span class="ms-auto text-xs font-bold text-primary-600">{{ number_format(auth()->user()->wallet_balance, 3) }} JOD</span>
                            </a>
                            <a href="{{ route('messages.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                💬 {{ app()->getLocale() === 'ar' ? 'الرسائل' : 'Messages' }}
                            </a>
                            <div class="border-t border-gray-100 mt-1 pt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 text-start">
                                        🚪 {{ app()->getLocale() === 'ar' ? 'تسجيل الخروج' : 'Sign Out' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600 px-4 py-2">
                        {{ app()->getLocale() === 'ar' ? 'تسجيل الدخول' : 'Sign In' }}
                    </a>
                    <a href="{{ route('register') }}" class="bg-primary-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors">
                        {{ app()->getLocale() === 'ar' ? 'ابدأ الآن' : 'Get Started' }}
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<script>
function toggleUserMenu() {
    const dropdown = document.getElementById('userMenuDropdown');
    dropdown.classList.toggle('hidden');
}
document.addEventListener('click', function(e) {
    const wrapper = document.getElementById('userMenuWrapper');
    if (wrapper && !wrapper.contains(e.target)) {
        document.getElementById('userMenuDropdown')?.classList.add('hidden');
    }
});
// Scroll shadow
window.addEventListener('scroll', function(){
    const nav = document.getElementById('main-nav');
    if (!nav) return;
    if (window.scrollY > 10) {
        nav.style.boxShadow = '0 4px 24px rgba(0,0,0,0.07)';
    } else {
        nav.style.boxShadow = 'none';
    }
});
</script>

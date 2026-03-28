<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') | {{ app()->getLocale() === 'ar' ? config('platform.name_ar', 'دوبا وورك') : config('app.name', 'Dopa Work') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: {{ app()->getLocale()==='ar' ? "'Cairo'" : "'Inter'" }}, sans-serif; }
        [dir="rtl"] .ms-auto { margin-right: auto; margin-left: 0; }

        /* ── Sidebar base ── */
        #admin-desktop-sidebar {
            display: none; /* hidden on mobile by default */
            flex-direction: column;
            width: 260px;
            min-width: 260px;
            flex-shrink: 0;
            transition: width .25s cubic-bezier(.4,0,.2,1), min-width .25s cubic-bezier(.4,0,.2,1);
            background: #0f172a;
            position: sticky;
            top: 64px;
            height: calc(100vh - 64px);
            overflow-y: auto;
            box-shadow: 4px 0 24px rgba(0,0,0,.3);
        }
        @media (min-width: 1024px) {
            #admin-desktop-sidebar { display: flex; }
        }
        #admin-desktop-sidebar.collapsed {
            width: 68px;
            min-width: 68px;
        }

        /* Scrollbar */
        #admin-desktop-sidebar::-webkit-scrollbar { width: 4px; }
        #admin-desktop-sidebar::-webkit-scrollbar-track { background: #0f172a; }
        #admin-desktop-sidebar::-webkit-scrollbar-thumb { background: #334155; border-radius:4px; }

        /* ── Sidebar links ── */
        .sidebar-link {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 12px; border-radius: 10px;
            font-size: .875rem; font-weight: 500;
            color: #94a3b8; white-space: nowrap; overflow: hidden;
            transition: background .15s, color .15s;
            position: relative;
            text-decoration: none;
        }
        .sidebar-link:hover  { background: rgba(255,255,255,.07); color: #e2e8f0; }
        .sidebar-link.active { background: #2563eb; color: #fff; font-weight: 600; }

        .sidebar-link .link-icon  { flex-shrink:0; width:22px; text-align:center; font-size:1.1rem; line-height:1; }
        .sidebar-link .link-label { flex:1; transition: opacity .2s, width .2s; overflow:hidden; }
        .sidebar-link .link-badge { flex-shrink:0; background:#ef4444; color:#fff; font-size:.6rem; font-weight:700; min-width:18px; height:18px; padding:0 4px; border-radius:9px; display:flex; align-items:center; justify-content:center; }

        /* Collapsed state */
        #admin-desktop-sidebar.collapsed .link-label  { opacity:0; width:0; }
        #admin-desktop-sidebar.collapsed .link-badge  { opacity:0; width:0; padding:0; min-width:0; }
        #admin-desktop-sidebar.collapsed .sidebar-section { opacity:0; height:0; padding:0; margin:0; overflow:hidden; }
        #admin-desktop-sidebar.collapsed .sidebar-link { justify-content:center; gap:0; padding:10px 0; }
        #admin-desktop-sidebar.collapsed .logo-text { display:none; }
        #admin-desktop-sidebar.collapsed .user-info  { display:none; }

        /* Tooltip on collapsed */
        #admin-desktop-sidebar.collapsed .sidebar-link { position: relative; }
        #admin-desktop-sidebar.collapsed .sidebar-link:hover::after {
            content: attr(data-label);
            position: absolute;
            left: calc(100% + 10px);
            top: 50%; transform: translateY(-50%);
            background: #1e293b; color: #e2e8f0;
            font-size: .75rem; white-space: nowrap;
            padding: 4px 10px; border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,.4);
            pointer-events: none; z-index: 9999;
        }
        [dir="rtl"] #admin-desktop-sidebar.collapsed .sidebar-link:hover::after {
            left: auto; right: calc(100% + 10px);
        }

        .sidebar-section {
            font-size: .6rem; font-weight: 700; letter-spacing: .1em;
            text-transform: uppercase; color: #475569;
            padding: 16px 14px 4px;
        }

        /* Toggle button */
        #sidebar-toggle {
            position: absolute;
            right: -14px;
            top: 22px;
            width: 28px; height: 28px;
            background: #2563eb; color: #fff;
            border-radius: 50%; border: 2px solid #0f172a;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; z-index: 20;
            transition: transform .25s;
            font-size: .75rem; line-height:1;
            box-shadow: 0 2px 8px rgba(0,0,0,.4);
        }
        [dir="rtl"] #sidebar-toggle { right: auto; left: -14px; }
        #admin-desktop-sidebar.collapsed #sidebar-toggle { transform: rotate(180deg); }
        [dir="rtl"] #admin-desktop-sidebar.collapsed #sidebar-toggle { transform: rotate(0deg); }

        /* Mobile drawer scrollbar */
        #admin-drawer::-webkit-scrollbar { width: 4px; }
        #admin-drawer::-webkit-scrollbar-track { background: #0f172a; }
        #admin-drawer::-webkit-scrollbar-thumb { background: #334155; border-radius:4px; }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-900 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">

{{-- Top Navbar --}}
@include('layouts.nav')

{{-- Flash messages (global) --}}
@if(session('success'))
<div id="flash-success"
     class="fixed top-20 inset-x-0 z-50 flex justify-center pointer-events-none">
    <div class="bg-green-600 text-white text-sm px-5 py-2.5 rounded-xl shadow-lg pointer-events-auto flex items-center gap-2">
        ✅ {{ session('success') }}
        <button onclick="document.getElementById('flash-success').remove()" class="ms-2 opacity-70 hover:opacity-100">✕</button>
    </div>
</div>
<script>setTimeout(()=>{const e=document.getElementById('flash-success');if(e)e.remove()},5000)</script>
@endif
@if(session('error'))
<div id="flash-error"
     class="fixed top-20 inset-x-0 z-50 flex justify-center pointer-events-none">
    <div class="bg-red-600 text-white text-sm px-5 py-2.5 rounded-xl shadow-lg pointer-events-auto flex items-center gap-2">
        ⚠️ {{ session('error') }}
        <button onclick="document.getElementById('flash-error').remove()" class="ms-2 opacity-70 hover:opacity-100">✕</button>
    </div>
</div>
<script>setTimeout(()=>{const e=document.getElementById('flash-error');if(e)e.remove()},6000)</script>
@endif

@php
use App\Models\WalletTransaction;
use App\Models\IdentityVerification;
use App\Models\Dispute;
use App\Models\Service;
use App\Models\Order;

$_pendingVerif    = IdentityVerification::where('status', 'pending')->count();
$_openDisputes    = Dispute::where('status', 'open')->count();
$_pendingWithdraw = WalletTransaction::where('type','withdrawal')->where('status','pending')->count();
$_pendingDeposit  = WalletTransaction::where('type','deposit')->where('status','pending')->count();
$_pendingServices = Service::where('status','pending_review')->count();

$_navGroups = [
    [
        'label' => 'عام',
        'items' => [
            ['route'=>'admin.dashboard', 'icon'=>'📊', 'label'=>'لوحة التحليلات', 'badge'=>0],
        ],
    ],
    [
        'label' => 'المستخدمون',
        'items' => [
            ['route'=>'admin.users.index',       'icon'=>'👥', 'label'=>'كل المستخدمين',    'badge'=>0],
            ['route'=>'admin.clients.index',     'icon'=>'🛒', 'label'=>'العملاء',          'badge'=>0],
            ['route'=>'admin.freelancers.index', 'icon'=>'💼', 'label'=>'المستقلون',        'badge'=>0],
            ['route'=>'admin.verifications.index','icon'=>'🪪','label'=>'التحقق من الهوية','badge'=>$_pendingVerif],
        ],
    ],
    [
        'label' => 'المنصة',
        'items' => [
            ['route'=>'admin.services.index',  'icon'=>'🛠️', 'label'=>'الخدمات',   'badge'=>$_pendingServices],
            ['route'=>'admin.orders.index',    'icon'=>'📦',  'label'=>'الطلبات',   'badge'=>0],
            ['route'=>'admin.disputes.index',  'icon'=>'⚖️', 'label'=>'النزاعات',  'badge'=>$_openDisputes],
            ['route'=>'admin.categories.index','icon'=>'🏷️', 'label'=>'التصنيفات', 'badge'=>0],
        ],
    ],
    [
        'label' => 'المالية',
        'items' => [
            ['route'=>'admin.escrow.index',      'icon'=>'🔒', 'label'=>'الضمان',      'badge'=>0],
            ['route'=>'admin.deposits.index',    'icon'=>'💳', 'label'=>'الإيداعات',   'badge'=>$_pendingDeposit],
            ['route'=>'admin.withdrawals.index', 'icon'=>'💸', 'label'=>'السحوبات',    'badge'=>$_pendingWithdraw],
            ['route'=>'admin.reports.index',     'icon'=>'📈', 'label'=>'التقارير',    'badge'=>0],
        ],
    ],
    [
        'label' => 'الإعدادات',
        'items' => [
            ['route'=>'admin.announcements.index','icon'=>'📣','label'=>'الإشعارات', 'badge'=>0],
            ['route'=>'admin.content.index',     'icon'=>'📄', 'label'=>'المحتوى',   'badge'=>0],
            ['route'=>'admin.settings.index',    'icon'=>'⚙️', 'label'=>'الإعدادات', 'badge'=>0],
        ],
    ],
];
@endphp

{{-- Layout wrapper --}}
<div class="flex min-h-[calc(100vh-4rem)]">

    {{-- ═══ SIDEBAR — Desktop (collapsible) ═══ --}}
    <aside id="admin-desktop-sidebar">

        {{-- Toggle button --}}
        <button id="sidebar-toggle" onclick="adminToggleSidebar()" title="طي / توسيع">&#8249;</button>

        {{-- Logo area --}}
        <div class="px-4 py-4 border-b border-slate-700/50 flex items-center gap-3 overflow-hidden">
            <div class="w-9 h-9 bg-orange-500 rounded-xl flex items-center justify-center shrink-0 shadow-lg">
                <span class="text-white font-black text-sm" style="font-family:'Cairo',sans-serif;">d</span>
            </div>
            <div class="logo-text overflow-hidden">
                <p class="text-sm font-black text-white whitespace-nowrap" dir="ltr" style="font-family:'Cairo',sans-serif;"><span style="color:#f97316;">dopa</span><span class="text-slate-300 font-semibold"> work</span></p>
                <p class="text-[10px] text-slate-400 whitespace-nowrap">Admin Panel</p>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-2 pb-4" style="overflow-y:auto;overflow-x:hidden;">
            @foreach($_navGroups as $_group)
            <p class="sidebar-section">{{ $_group['label'] }}</p>
            @foreach($_group['items'] as $_item)
            <a href="{{ route($_item['route']) }}"
               data-label="{{ $_item['label'] }}"
               class="sidebar-link {{ request()->routeIs($_item['route']) ? 'active' : '' }}">
                <span class="link-icon">{{ $_item['icon'] }}</span>
                <span class="link-label">{{ $_item['label'] }}</span>
                @if($_item['badge'] > 0)
                    <span class="link-badge">{{ $_item['badge'] }}</span>
                @endif
            </a>
            @endforeach
            @endforeach
        </nav>

        {{-- User info + logout --}}
        <div class="p-3 border-t border-slate-700/50 overflow-hidden">
            <div class="flex items-center gap-3 px-2 py-2 rounded-xl bg-slate-800/60">
                <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-white text-sm font-bold shrink-0">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="user-info min-w-0 flex-1">
                    <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] text-slate-400">{{ auth()->user()->role }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="logout-btn shrink-0">
                    @csrf
                    <button type="submit" title="تسجيل الخروج"
                            class="w-7 h-7 flex items-center justify-center text-slate-400 hover:text-red-400 hover:bg-red-400/10 rounded-lg transition-colors text-base">
                        ⏻
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Mobile sidebar backdrop --}}
    <div id="admin-backdrop" onclick="adminSidebarClose()"
         class="fixed inset-0 bg-black/40 z-40 lg:hidden hidden"></div>

    {{-- Mobile sidebar drawer --}}
    <aside id="admin-drawer"
           class="admin-sidebar fixed top-0 bottom-0 z-50 w-72 bg-slate-900 shadow-2xl overflow-y-auto lg:hidden flex-col hidden
                  {{ app()->getLocale()==='ar' ? 'right-0' : 'left-0' }}">
        <div class="flex items-center justify-between px-4 py-4 border-b border-slate-700/60">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 bg-orange-500 rounded-lg flex items-center justify-center text-white font-bold text-xs" style="font-family:'Cairo',sans-serif;">d</div>
                <p class="text-sm font-bold text-white">لوحة الإدارة</p>
            </div>
            <button onclick="adminSidebarClose()" class="text-slate-400 hover:text-white text-xl leading-none">✕</button>
        </div>
        <nav class="flex-1 px-3 pb-4">
            @foreach($_navGroups as $_group)
            <p class="sidebar-section">{{ $_group['label'] }}</p>
            @foreach($_group['items'] as $_item)
            <a href="{{ route($_item['route']) }}" onclick="adminSidebarClose()"
               class="sidebar-link {{ request()->routeIs($_item['route']) ? 'active' : '' }}">
                <span class="link-icon">{{ $_item['icon'] }}</span>
                <span class="link-label">{{ $_item['label'] }}</span>
                @if($_item['badge'] > 0)
                    <span class="link-badge">{{ $_item['badge'] }}</span>
                @endif
            </a>
            @endforeach
            @endforeach
        </nav>
    </aside>

    <script>
    // ── Mobile drawer ──
    function adminSidebarOpen(){
        document.getElementById('admin-drawer').classList.remove('hidden');
        document.getElementById('admin-drawer').classList.add('flex');
        document.getElementById('admin-backdrop').classList.remove('hidden');
        document.body.style.overflow='hidden';
    }
    function adminSidebarClose(){
        document.getElementById('admin-drawer').classList.add('hidden');
        document.getElementById('admin-drawer').classList.remove('flex');
        document.getElementById('admin-backdrop').classList.add('hidden');
        document.body.style.overflow='';
    }

    // ── Desktop collapse/expand ──
    const SIDEBAR_KEY = 'adminSidebarCollapsed';
    const sidebar = document.getElementById('admin-desktop-sidebar');

    function adminToggleSidebar(){
        const collapsed = sidebar.classList.toggle('collapsed');
        localStorage.setItem(SIDEBAR_KEY, collapsed ? '1' : '0');
    }

    // Restore saved state on load
    (function(){
        if(localStorage.getItem(SIDEBAR_KEY) === '1'){
            sidebar.classList.add('collapsed');
        }
    })();
    </script>

    {{-- ═══ MAIN CONTENT ═══ --}}
    <div class="flex-1 min-w-0 flex flex-col">

        {{-- Mobile top bar with hamburger --}}
        <div class="lg:hidden flex items-center gap-3 px-4 py-3 bg-white border-b border-gray-200 sticky top-16 z-30">
            <button onclick="adminSidebarOpen()"
                    class="flex flex-col gap-1.5 p-1.5 text-gray-600 hover:text-gray-900">
                <span class="block w-5 h-0.5 bg-current rounded"></span>
                <span class="block w-5 h-0.5 bg-current rounded"></span>
                <span class="block w-5 h-0.5 bg-current rounded"></span>
            </button>
            <span class="text-sm font-semibold text-gray-700">@yield('title', 'الإدارة')</span>
        </div>

        <main class="flex-1 p-4 md:p-6">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') | {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased" style="background:#F8FBFD; color:#2C3E50;">
@php
    /**
     * StarAdmin Soft-UI Navigation Groups
     */
    $menuCategories = [
        [
            'group' => 'Operasional Proyek',
            'items' => [
                [
                    'label' => 'Proyek',
                    'route' => 'projects.index',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>',
                    'active_routes' => ['projects.*'],
                ],
                [
                    'label' => 'Jadwal Proyek',
                    'route' => 'schedules.index',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
                    'active_routes' => ['schedules.*'],
                ],
            ],
        ],
        [
            'group' => 'Logistik & SDM',
            'items' => [
                [
                    'label' => 'Supplier & Pengadaan',
                    'route' => 'procurement.index',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M5 7l1 13h12l1-13M9 7V4h6v3"/>',
                    'active_routes' => ['procurement.*', 'purchase-orders.*', 'suppliers.*'],
                ],
                [
                    'label' => 'Material & Inventaris',
                    'route' => 'materials.index',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
                    'active_routes' => ['materials.*'],
                ],
                [
                    'label' => 'Riwayat Mutasi Stok',
                    'route' => 'inventory.transactions',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16"/>',
                    'active_routes' => ['inventory.*'],
                ],
                [
                    'label' => 'Penugasan & Kehadiran',
                    'route' => 'workforce.index',
                    'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 shrink-0">
  <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
  <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
  <path d="m9 14 2 2 4-4"></path>
</svg>',
                    'active_routes' => ['workforce.*'],
                ],
                [
                    'label' => 'Tenaga Kerja',
                    'route' => 'workers.index',
                    'icon'  => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>',
                    'active_routes' => ['workers.*'],
                ],
            ],
        ],
        [
            'group' => 'Administrasi',
            'items' => [
                [
                    'label' => 'Hak Akses Pengguna',
                    'route' => 'users.roles.index',
                    'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 shrink-0">
  <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
  <circle cx="12" cy="11" r="1"></circle>
  <path d="M12 12v2.5"></path>
</svg>',
                    'active_routes' => ['users.*'],
                ],
            ],
        ],
        [
            'group' => 'Keuangan & Analitik',
            'items' => [
                [
                    'label' => 'Arus Kas',
                    'route' => 'finance.index',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M5 7l1 13h12l1-13"/>',
                    'active_routes' => ['finance.*'],
                ],
                [
                    'label' => 'Anggaran & Biaya',
                    'route' => 'budgets.index',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>',
                    'active_routes' => ['budgets.*'],
                ],
                [
                    'label' => 'Laporan',
                    'route' => 'reports.index',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                    'active_routes' => ['reports.*'],
                ],
            ],
        ],
    ];

    // Menu ditampilkan sesuai role, sementara otorisasi utama tetap di middleware route.
    $allowedMenuRoutes = match (Auth::user()->role ?? null) {
        'project_manager' => ['projects.index', 'schedules.index', 'workers.index', 'workforce.index', 'reports.index'],
        'logistics' => ['procurement.index', 'materials.index', 'inventory.transactions'],
        'finance' => ['finance.index', 'budgets.index', 'workforce.index', 'reports.index'],
        'field_staff' => ['workforce.index'],
        'superadmin' => null,
        default => [],
    };

    if ($allowedMenuRoutes !== null) {
        $menuCategories = array_values(array_filter(array_map(function ($category) use ($allowedMenuRoutes) {
            $category['items'] = array_values(array_filter(
                $category['items'],
                fn ($item) => in_array($item['route'], $allowedMenuRoutes, true)
            ));
            return $category;
        }, $menuCategories), fn ($category) => count($category['items']) > 0));
    }
@endphp

<div class="min-h-screen h-screen flex overflow-hidden bg-[#F8FBFD]">

    {{-- ═══════════════════════════════════════════════════════
         SIDEBAR — StarAdmin Soft-UI Canvas Blended (w-60 / 15rem)
         ═══════════════════════════════════════════════════════ --}}
    <aside id="sidebar" class="hidden lg:flex fixed lg:static inset-y-0 left-0 z-30 w-60 min-w-[15rem] max-w-[15rem] h-screen shrink-0 flex-col bg-[#F8FBFD] border-r border-[#EEF4F8] select-none overflow-y-auto">

        {{-- Header Workspace Ringkas --}}
        <div class="px-5 py-5 flex items-center justify-between">
            <div class="flex items-center min-w-0">
                <div class="w-6 h-6 rounded-md bg-[#136F9A] text-white flex items-center justify-center shrink-0 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <span class="text-[14px] font-bold text-[#2C3E50] tracking-tight ml-2.5 truncate">BuildSync ERP</span>
            </div>
        </div>

        {{-- Navigation Menu --}}
        <nav class="flex-1 overflow-y-auto py-1">
            {{-- Dashboard (Item Utama langsung di bawah Header Workspace) --}}
            @php
                $isDashboardActive = Route::has('dashboard') && request()->routeIs('dashboard');
            @endphp
            <div class="mb-2">
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 text-[13px] transition-all {{ $isDashboardActive ? 'rounded-r-full mr-4 py-2.5 px-5 bg-[#ED7132]/12 text-[#ED7132] font-bold shadow-xs' : 'text-[#7F7F7F] hover:text-[#2C3E50] font-medium py-2 px-5' }}">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-4 h-4 shrink-0 stroke-[1.75] {{ $isDashboardActive ? 'text-[#ED7132]' : 'text-[#7F7F7F]' }}"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v9a1 1 0 001 1h4v-5h4v5h4a1 1 0 001-1v-9"/>
                    </svg>
                    <span>Dashboard</span>
                </a>
            </div>

            {{-- Kategori Menu (StarAdmin Category Labels + Collapsible Chevron) --}}
            @foreach ($menuCategories as $cat)
                <div class="sidebar-group mb-1">
                    <button type="button"
                            class="w-full px-5 py-1.5 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] hover:text-[#2C3E50] flex items-center justify-between cursor-pointer transition-colors mt-4 mb-1 group focus:outline-none text-left"
                            onclick="toggleSidebarGroup(this)"
                            aria-expanded="true">
                        <span>{{ $cat['group'] }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-3 h-3 text-[#7F7F7F] transition-transform duration-150 group-hover:text-[#2C3E50] shrink-0"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div class="space-y-0.5">
                        @foreach ($cat['items'] as $item)
                            @php
                                $activeRoutes = $item['active_routes'] ?? [$item['route']];
                                $isActive     = Route::has($item['route']) && request()->routeIs($activeRoutes);
                            @endphp

                            @if (Route::has($item['route']))
                                <a href="{{ route($item['route']) }}"
                                   class="flex items-center gap-3 text-[13px] transition-all {{ $isActive ? 'rounded-r-full mr-4 py-2.5 px-5 bg-[#ED7132]/12 text-[#ED7132] font-bold shadow-xs' : 'text-[#7F7F7F] hover:text-[#2C3E50] font-medium py-2 px-5' }}">
                                    @if (str_starts_with(trim($item['icon']), '<svg'))
                                        {!! preg_replace('/class="([^"]*)"/', 'class="$1 ' . ($isActive ? 'text-[#ED7132]' : 'text-[#7F7F7F]') . '"', $item['icon'], 1) !!}
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"
                                             class="w-4 h-4 shrink-0 {{ $isActive ? 'text-[#ED7132]' : 'text-[#7F7F7F]' }}">
                                            {!! $item['icon'] !!}
                                        </svg>
                                    @endif
                                    <span class="truncate">{{ $item['label'] }}</span>
                                </a>
                            @else
                                <span class="flex items-center gap-3 text-[13px] py-2 px-5 text-slate-400 cursor-not-allowed">
                                    @if (str_starts_with(trim($item['icon']), '<svg'))
                                        {!! preg_replace('/class="([^"]*)"/', 'class="$1 text-slate-400 opacity-50"', $item['icon'], 1) !!}
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"
                                             class="w-4 h-4 shrink-0 text-slate-400 opacity-50">
                                            {!! $item['icon'] !!}
                                        </svg>
                                    @endif
                                    <span class="truncate">{{ $item['label'] }}</span>
                                    <span class="ml-auto text-[10px] text-slate-400">(segera)</span>
                                </span>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>

        {{-- Footer User & Logout (StarAdmin Soft Profile Bar) --}}
        <div class="mt-auto shrink-0 p-4 border-t border-[#EEF4F8]">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                    <div class="w-7 h-7 rounded-full bg-[#136F9A] text-white text-xs font-bold flex items-center justify-center shrink-0 select-none shadow-xs">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-[12px] font-semibold text-[#2C3E50] truncate leading-tight">
                            {{ Auth::user()->name ?? 'Administrator' }}
                        </p>
                        <p class="text-[10px] text-[#7F7F7F] truncate">
                            {{ Auth::user()->email ?? 'admin@buildsync.id' }}
                        </p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                    @csrf
                    <button type="submit"
                            title="Keluar / Logout"
                            class="text-[#7F7F7F] hover:text-[#ED7132] p-1.5 rounded-md hover:bg-white transition-colors flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 stroke-[1.75]" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ═══════════════════════════════════════════════════════
         MAIN CONTENT AREA
         ═══════════════════════════════════════════════════════ --}}
    <div class="flex-1 flex flex-col h-screen min-w-0 overflow-y-auto">

        {{-- Top Header (StarAdmin Soft Topbar) --}}
        <header class="sticky top-0 z-10 flex shrink-0 items-center justify-between px-6 py-4 bg-[#F8FBFD] border-b border-[#EEF4F8]"
                style="min-height:64px;">
            <div class="flex items-center gap-3">
                {{-- Mobile menu toggle --}}
                <button type="button"
                        class="lg:hidden rounded-lg px-2.5 py-1.5 text-xs font-medium border border-slate-200 bg-white text-[#2C3E50] hover:bg-slate-50"
                        onclick="document.getElementById('sidebar').classList.toggle('hidden'); document.getElementById('sidebar').classList.toggle('flex');">
                    Menu
                </button>

                {{-- Header Sapaan/Judul Utama (Kombinasi font tipis dan tebal) --}}
                @if (request()->routeIs('dashboard'))
                    @php
                        $hour = now()->timezone('Asia/Jakarta')->format('H');
                        if ($hour >= 5 && $hour < 11) {
                            $greeting = 'Selamat Pagi';
                        } elseif ($hour >= 11 && $hour < 15) {
                            $greeting = 'Selamat Siang';
                        } elseif ($hour >= 15 && $hour < 18) {
                            $greeting = 'Selamat Sore';
                        } else {
                            $greeting = 'Selamat Malam';
                        }
                    @endphp
                    <h1 class="text-2xl tracking-tight">
                        <span class="font-light text-[#7F7F7F]">{{ $greeting }},</span>
                        <span class="font-bold text-[#2C3E50]">{{ Auth::user()->name ?? 'Administrator' }}</span>
                    </h1>
                @else
                    <div class="flex flex-col">
                        <div class="flex items-center gap-1.5 text-xs text-[#7F7F7F]">
                            @hasSection('breadcrumbs')
                                @yield('breadcrumbs')
                            @elseif (View::hasSection('breadcrumb'))
                                @yield('breadcrumb')
                            @endif
                        </div>
                        <h1 class="text-xl font-bold tracking-tight text-[#2C3E50]">
                            @yield('title', 'Dashboard')
                        </h1>
                    </div>
                @endif
            </div>

            {{-- Right side: Soft capsule pill badge/filter --}}
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 rounded-lg bg-white border border-slate-200/70 px-3 py-1.5 text-xs text-[#2C3E50] shadow-[0_1px_3px_rgba(44,62,80,0.03)]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-[#7F7F7F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="font-medium tabular">{{ now()->translatedFormat('d M Y') }}</span>
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 min-w-0 p-6 lg:p-8">
            @yield('content')
        </main>
    </div>
</div>

{{-- Mobile sidebar overlay backdrop --}}
<div id="sidebar-backdrop" class="fixed inset-0 z-10 hidden bg-black/40 lg:hidden"
     onclick="document.getElementById('sidebar').classList.add('hidden'); document.getElementById('sidebar').classList.remove('flex'); this.classList.add('hidden');"></div>

<script>
// Toggle collapsible group di sidebar
function toggleSidebarGroup(btn) {
    const list = btn.nextElementSibling;
    const chevron = btn.querySelector('svg');
    if (!list) return;

    const isHidden = list.classList.toggle('hidden');
    if (chevron) {
        chevron.classList.toggle('-rotate-90', isHidden);
    }
    btn.setAttribute('aria-expanded', isHidden ? 'false' : 'true');
}

// Mobile sidebar: tampilkan backdrop saat sidebar dibuka
document.addEventListener('DOMContentLoaded', function () {
    const btn      = document.querySelector('[onclick*="sidebar"]');
    const sidebar  = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');
    if (btn && sidebar && backdrop) {
        btn.addEventListener('click', function () {
            if (!sidebar.classList.contains('hidden')) {
                backdrop.classList.remove('hidden');
            } else {
                backdrop.classList.add('hidden');
            }
        });
    }
});
</script>
@stack('scripts')
</body>
</html>

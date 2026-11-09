<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') | {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased" style="background:#f8fafc; color:#0f172a;">
@php
    /**
     * Linear Workspace Sidebar Navigation Groups
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
                    'label' => 'Material & Inventaris',
                    'route' => 'materials.index',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
                    'active_routes' => ['materials.*'],
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
            'group' => 'Keuangan & Analitik',
            'items' => [
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
@endphp

<div class="min-h-screen h-screen flex overflow-hidden bg-slate-50">

    {{-- ═══════════════════════════════════════════════════════
         SIDEBAR — Linear Workspace Sidebar (w-60 / 15rem)
         ═══════════════════════════════════════════════════════ --}}
    <aside id="sidebar" class="hidden lg:flex fixed lg:static inset-y-0 left-0 z-30 w-60 min-w-[15rem] max-w-[15rem] h-screen shrink-0 flex-col bg-slate-900 border-r border-slate-800 select-none overflow-y-auto">

        {{-- Header Workspace Ringkas (1 Baris ala Linear) --}}
        <div class="px-3.5 py-3.5 flex items-center justify-between">
            <div class="flex items-center min-w-0">
                <div class="w-5 h-5 rounded-[5px] bg-blue-600 text-white flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <span class="text-[13px] font-semibold text-white tracking-tight ml-2 truncate">BuildSync ERP</span>
            </div>
        </div>

        {{-- Navigation Menu --}}
        <nav class="flex-1 overflow-y-auto py-1">
            {{-- Dashboard (Item Utama langsung di bawah Header Workspace) --}}
            @php
                $isDashboardActive = Route::has('dashboard') && request()->routeIs('dashboard');
            @endphp
            <div class="px-2.5 mb-3">
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-[13px] transition-colors {{ $isDashboardActive ? 'bg-slate-800/90 text-white font-medium' : 'text-slate-400 hover:bg-slate-800/40 hover:text-slate-200 font-normal' }}">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-4 h-4 shrink-0 stroke-[1.75] {{ $isDashboardActive ? 'text-white' : 'text-slate-400' }}"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v9a1 1 0 001 1h4v-5h4v5h4a1 1 0 001-1v-9"/>
                    </svg>
                    <span>Dashboard</span>
                </a>
            </div>

            {{-- Kategori Menu (Title Case + Collapsible Chevron) --}}
            @foreach ($menuCategories as $cat)
                <div class="sidebar-group mb-1">
                    <button type="button"
                            class="w-full px-2.5 py-1 text-[12px] font-medium text-slate-400 hover:text-slate-200 flex items-center gap-1 cursor-pointer transition-colors mt-4 mb-0.5 group focus:outline-none text-left"
                            onclick="toggleSidebarGroup(this)"
                            aria-expanded="true">
                        <span>{{ $cat['group'] }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-3 h-3 text-slate-500 transition-transform duration-150 group-hover:text-slate-400 shrink-0"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div class="space-y-0.5 px-2.5">
                        @foreach ($cat['items'] as $item)
                            @php
                                $activeRoutes = $item['active_routes'] ?? [$item['route']];
                                $isActive     = Route::has($item['route']) && request()->routeIs($activeRoutes);
                            @endphp

                            @if (Route::has($item['route']))
                                <a href="{{ route($item['route']) }}"
                                   class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-[13px] transition-colors {{ $isActive ? 'bg-slate-800/90 text-white font-medium' : 'text-slate-400 hover:bg-slate-800/40 hover:text-slate-200 font-normal' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"
                                         class="w-4 h-4 shrink-0 {{ $isActive ? 'text-white' : 'text-slate-400' }}">
                                        {!! $item['icon'] !!}
                                    </svg>
                                    <span class="truncate">{{ $item['label'] }}</span>
                                </a>
                            @else
                                <span class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-[13px] text-slate-600 cursor-not-allowed">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"
                                         class="w-4 h-4 shrink-0 text-slate-600 opacity-50">
                                        {!! $item['icon'] !!}
                                    </svg>
                                    <span class="truncate">{{ $item['label'] }}</span>
                                    <span class="ml-auto text-[10px] text-slate-600">(segera)</span>
                                </span>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>

        {{-- Footer User & Logout (Minimalis Menyatu) --}}
        <div class="mt-auto shrink-0 p-2.5 border-t border-slate-800/80">
            <div class="flex items-center justify-between gap-2 px-1 py-0.5">
                <div class="flex items-center gap-2 min-w-0 flex-1">
                    <div class="w-6 h-6 rounded-full bg-blue-600 text-white text-[11px] font-semibold flex items-center justify-center shrink-0 select-none">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <span class="text-[12px] font-medium text-slate-200 truncate">
                        {{ Auth::user()->name ?? 'Pengguna' }}
                    </span>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                    @csrf
                    <button type="submit"
                            title="Keluar / Logout"
                            class="text-slate-400 hover:text-red-400 p-1 rounded hover:bg-slate-800 transition-colors flex items-center justify-center">
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

        {{-- Top Header --}}
        <header class="sticky top-0 z-10 flex shrink-0 items-center justify-between px-5 py-3 bg-white border-b border-slate-200"
                style="height:52px;">
            <div class="flex items-center gap-3">
                {{-- Mobile menu toggle --}}
                <button type="button"
                        class="lg:hidden rounded px-2 py-1 text-xs font-medium border border-slate-200 text-slate-600 hover:bg-slate-50"
                        onclick="document.getElementById('sidebar').classList.toggle('hidden'); document.getElementById('sidebar').classList.toggle('flex');">
                    Menu
                </button>

                {{-- Breadcrumbs (slot, disupply tiap view) --}}
                <div class="flex items-center gap-1 text-sm">
                    @hasSection('breadcrumbs')
                        @yield('breadcrumbs')
                    @elseif (View::hasSection('breadcrumb'))
                        @yield('breadcrumb')
                    @else
                        <span class="font-semibold text-slate-900">
                            @yield('title', 'Dashboard')
                        </span>
                    @endif
                </div>
            </div>

            {{-- Right side: current date --}}
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-500 tabular">
                    {{ now()->translatedFormat('d M Y') }}
                </span>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 min-w-0 p-6">
            @yield('content')
        </main>
    </div>
</div>

{{-- Mobile sidebar overlay backdrop --}}
<div id="sidebar-backdrop" class="fixed inset-0 z-10 hidden bg-black/40 lg:hidden"
     onclick="document.getElementById('sidebar').classList.add('hidden'); document.getElementById('sidebar').classList.remove('flex'); this.classList.add('hidden');"></div>

<script>
// Toggle collapsible group di sidebar (Linear Style)
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
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
     * Grouped sidebar menu:
     * 4 kategori fungsional, masing-masing dengan items.
     * Modul "Jadwal" dikonsolidasi — sub-navigasi via tabs di dalam halaman.
     */
    $menuGroups = [
        [
            'group' => 'Ringkasan',
            'items' => [
                [
                    'label' => 'Dashboard',
                    'route' => 'dashboard',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l9-9 9 9M5 10v9a1 1 0 001 1h4v-5h4v5h4a1 1 0 001-1v-9"/>',
                ],
            ],
        ],
        [
            'group' => 'Operasional Proyek',
            'items' => [
                [
                    'label' => 'Proyek',
                    'route' => 'projects.index',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>',
                    'active_routes' => ['projects.*'],
                ],
                [
                    'label' => 'Jadwal Proyek',
                    'route' => 'schedules.index',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
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
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
                    'active_routes' => ['materials.*'],
                ],
                [
                    'label' => 'Tenaga Kerja',
                    'route' => 'workers.index',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4 0a4 4 0 110-8 4 4 0 010 8zm6-4a3 3 0 11-6 0 3 3 0 016 0zM5 8a3 3 0 11-6 0 3 3 0 016 0z"/>',
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
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>',
                    'active_routes' => ['budgets.*'],
                ],
                [
                    'label' => 'Laporan',
                    'route' => 'reports.index',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                    'active_routes' => ['reports.*'],
                ],
            ],
        ],
    ];
@endphp

<div class="min-h-screen h-screen flex overflow-hidden bg-slate-50">

    {{-- ═══════════════════════════════════════════════════════
         SIDEBAR — Lebar tetap 256px (w-64), locked fixed layout
         ═══════════════════════════════════════════════════════ --}}
    <aside id="sidebar" class="hidden lg:flex fixed lg:static inset-y-0 left-0 z-30 w-64 min-w-[16rem] max-w-[16rem] h-screen shrink-0 flex-col bg-slate-900 overflow-y-auto border-r border-slate-800">

        {{-- Logo / App Name --}}
        <div class="px-5 py-4" style="border-bottom:1px solid #1e293b;">
            <div class="flex items-center gap-2.5">
                {{-- App icon –– konstruksi (hard-hat / bangunan) --}}
                <div class="flex h-8 w-8 items-center justify-center rounded"
                     style="background:#2563eb;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold leading-tight text-white">BuildSync ERP</p>
                    <p class="text-[10px] leading-tight" style="color:#64748b;">Sistem Manajemen Konstruksi</p>
                </div>
            </div>
        </div>

        {{-- Navigation Groups --}}
        <nav class="flex-1 overflow-y-auto px-3 py-3">
            @foreach ($menuGroups as $group)
                <div class="mb-4">
                    {{-- Group label --}}
                    <p class="mb-1 px-2 text-[10px] font-semibold uppercase tracking-widest"
                       style="color:#475569;">
                        {{ $group['group'] }}
                    </p>

                    @foreach ($group['items'] as $item)
                        @php
                            // Tentukan apakah link ini "aktif"
                            $activeRoutes = $item['active_routes'] ?? [$item['route']];
                            $isActive     = Route::has($item['route'])
                                            && request()->routeIs($activeRoutes);
                        @endphp

                        @if (Route::has($item['route']))
                            <a href="{{ route($item['route']) }}"
                               class="sidebar-link mb-0.5 flex items-center gap-2.5 rounded px-2.5 py-1.5 text-[13px] {{ $isActive ? 'bg-slate-800 text-white font-medium' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    {!! $item['icon'] !!}
                                </svg>
                                <span>{{ $item['label'] }}</span>
                            </a>
                        @else
                            <span class="mb-0.5 flex items-center gap-2.5 rounded px-2 py-1.5 text-[13px] cursor-not-allowed"
                                  style="color:#334155;">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="h-4 w-4 shrink-0 opacity-40" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    {!! $item['icon'] !!}
                                </svg>
                                {{ $item['label'] }}
                                <span class="ml-auto text-[10px]" style="color:#334155;">(segera)</span>
                            </span>
                        @endif
                    @endforeach
                </div>
            @endforeach
        </nav>

        {{-- ── User Footer Card ───────────────────────────────── --}}
        <div class="mt-auto shrink-0 px-3 py-3 border-t border-slate-800">
            <div class="flex items-center gap-2.5 rounded px-2 py-2 bg-slate-800/80">
                {{-- Avatar --}}
                <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-[11px] font-bold"
                     style="background:#1d4ed8; color:#eff6ff;">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-[12px] font-medium leading-tight text-white">
                        {{ Auth::user()->name ?? 'Pengguna' }}
                    </p>
                    <p class="truncate text-[10px] leading-tight" style="color:#64748b;">
                        {{ Auth::user()->email ?? '' }}
                    </p>
                </div>
                {{-- Logout button --}}
                <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                    @csrf
                    <button type="submit"
                            title="Keluar"
                            class="flex items-center justify-center rounded p-1 transition-colors"
                            style="color:#64748b;"
                            onmouseover="this.style.color='#ef4444'; this.style.background='rgba(239,68,68,.1)';"
                            onmouseout="this.style.color='#64748b'; this.style.background='';">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
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
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') | {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
</head>
<body class="antialiased bg-[#F8FAFC] dark:bg-[#09090B] text-[#0B2545] dark:text-[#F4F4F5] font-sans">
@php
    /**
     * StarAdmin Soft-UI Navigation Groups
     */
    $menuCategories = [
        [
            'group' => __('nav.group_project_ops'),
            'items' => [
                [
                    'label' => __('nav.projects'),
                    'route' => 'projects.index',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>',
                    'active_routes' => ['projects.*'],
                ],
                [
                    'label' => __('nav.schedules'),
                    'route' => 'schedules.index',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
                    'active_routes' => ['schedules.*'],
                ],
            ],
        ],
        [
            'group' => __('nav.group_logistics_hr'),
            'items' => [
                [
                    'label' => __('nav.procurement'),
                    'route' => 'procurement.index',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M5 7l1 13h12l1-13M9 7V4h6v3"/>',
                    'active_routes' => ['procurement.*', 'purchase-orders.*', 'suppliers.*'],
                ],
                [
                    'label' => __('nav.materials'),
                    'route' => 'materials.index',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
                    'active_routes' => ['materials.*'],
                ],
                [
                    'label' => __('nav.stock_mutations'),
                    'route' => 'inventory.transactions',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16"/>',
                    'active_routes' => ['inventory.*'],
                ],
                [
                    'label' => __('nav.workforce_assignment'),
                    'route' => 'workforce.index',
                    'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 shrink-0">
  <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
  <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
  <path d="m9 14 2 2 4-4"></path>
</svg>',
                    'active_routes' => ['workforce.*'],
                ],
                [
                    'label' => __('nav.workers'),
                    'route' => 'workers.index',
                    'icon'  => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>',
                    'active_routes' => ['workers.*'],
                ],
            ],
        ],
        [
            'group' => __('nav.group_administration'),
            'items' => [
                [
                    'label' => __('nav.users'),
                    'route' => 'users.roles.index',
                    'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 shrink-0">
  <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
  <circle cx="12" cy="11" r="1"></circle>
  <path d="M12 12v2.5"></path>
</svg>',
                    'active_routes' => ['users.*'],
                ],
                [
                    'label' => __('nav.settings'),
                    'route' => 'settings.index',
                    'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 shrink-0">
  <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path>
  <circle cx="12" cy="12" r="3"></circle>
</svg>',
                    'active_routes' => ['settings.*'],
                ],
            ],
        ],
        [
            'group' => __('nav.group_finance_analytics'),
            'items' => [
                [
                    'label' => __('nav.finance'),
                    'route' => 'finance.index',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M5 7l1 13h12l1-13"/>',
                    'active_routes' => ['finance.*'],
                ],
                [
                    'label' => __('nav.budgets'),
                    'route' => 'budgets.index',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>',
                    'active_routes' => ['budgets.*'],
                ],
                [
                    'label' => __('nav.reports'),
                    'route' => 'reports.index',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                    'active_routes' => ['reports.*'],
                ],
            ],
        ],
    ];

    // Menu ditampilkan sesuai role, sementara otorisasi utama tetap di middleware route.
    $allowedMenuRoutes = match (Auth::user()->role ?? null) {
        'project_manager' => ['projects.index', 'schedules.index', 'workers.index', 'workforce.index', 'reports.index', 'settings.index'],
        'logistics' => ['procurement.index', 'materials.index', 'inventory.transactions', 'settings.index'],
        'finance' => ['finance.index', 'budgets.index', 'workforce.index', 'reports.index', 'settings.index'],
        'field_staff' => ['workforce.index', 'settings.index'],
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

<div class="app-shell min-h-screen h-screen flex overflow-hidden">

    {{-- ═══════════════════════════════════════════════════════
         SIDEBAR — Clean Minimal White (#FFFFFF) & Neutral Dark (#111113) Architecture
         ═══════════════════════════════════════════════════════ --}}
    <aside id="sidebar" class="sidebar hidden lg:flex fixed lg:static inset-y-0 left-0 z-30">

        {{-- Header Workspace Ringkas (Height: 64px / h-16, divider perfectly aligned with topbar) --}}
        <div class="sidebar-header">
            <div class="flex items-center min-w-0">
                <div class="w-7 h-7 rounded-md bg-[#1D4E89] bg-[#136F9A] text-white flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div class="ml-2.5 min-w-0">
                    <span class="text-[15px] font-semibold text-[#0B2545] dark:text-[#F4F4F5] tracking-tight block truncate">BuildSync ERP</span>
                    <span class="text-xs text-[#64748B] dark:text-[#A1A1AA] font-medium block">Enterprise Suite</span>
                </div>
            </div>
        </div>

        {{-- Navigation Menu (Dedicated Single Scrollable Area with Hidden Scrollbar) --}}
        <nav class="sidebar-nav space-y-1">
            {{-- Dashboard (Item Utama langsung di bawah Header Workspace) --}}
            @php
                $isDashboardActive = Route::has('dashboard') && request()->routeIs('dashboard');
            @endphp
            <div class="mb-2">
                <a href="{{ route('dashboard') }}"
                   class="sidebar-item {{ $isDashboardActive ? 'sidebar-item-active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-4 h-4 shrink-0 stroke-[1.75] {{ $isDashboardActive ? 'text-[#1D4E89] dark:text-[#93C5FD]' : 'text-[#64748B] dark:text-[#71717A]' }}"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v9a1 1 0 001 1h4v-5h4v5h4a1 1 0 001-1v-9"/>
                    </svg>
                    <span>{{ __('nav.dashboard') }}</span>
                </a>
            </div>

            {{-- Kategori Menu (Collapsible Groups) --}}
            @foreach ($menuCategories as $cat)
                <div class="sidebar-group">
                    <button type="button"
                            class="sidebar-group-btn group focus:outline-none"
                            onclick="toggleSidebarGroup(this)"
                            aria-expanded="true">
                        <span>{{ $cat['group'] }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-3.5 h-3.5 text-[#64748B]/70 transition-transform duration-150 group-hover:text-[#0B2545] dark:group-hover:text-white shrink-0"
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
                                   class="sidebar-item {{ $isActive ? 'sidebar-item-active' : '' }}">
                                    @if (str_starts_with(trim($item['icon']), '<svg'))
                                        {!! preg_replace('/class="([^"]*)"/', 'class="$1 ' . ($isActive ? 'text-[#1D4E89] dark:text-[#93C5FD]' : 'text-[#64748B] dark:text-[#71717A]') . '"', $item['icon'], 1) !!}
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"
                                             class="w-4 h-4 shrink-0 {{ $isActive ? 'text-[#1D4E89] dark:text-[#93C5FD]' : 'text-[#64748B] dark:text-[#71717A]' }}">
                                            {!! $item['icon'] !!}
                                        </svg>
                                    @endif
                                    <span class="truncate">{{ $item['label'] }}</span>
                                </a>
                            @else
                                <span class="flex items-center gap-3 text-sm py-2 px-3 text-[#64748B]/40 cursor-not-allowed">
                                    @if (str_starts_with(trim($item['icon']), '<svg'))
                                        {!! preg_replace('/class="([^"]*)"/', 'class="$1 text-[#64748B]/30"', $item['icon'], 1) !!}
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"
                                             class="w-4 h-4 shrink-0 text-[#64748B]/30">
                                            {!! $item['icon'] !!}
                                        </svg>
                                    @endif
                                    <span class="truncate">{{ $item['label'] }}</span>
                                    <span class="ml-auto text-xs text-[#64748B]/40">{{ __('nav.soon') }}</span>
                                </span>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>

        {{-- Footer User & Logout (Permanently Pinned at Bottom) --}}
        <div class="sidebar-user-footer">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                    <div class="w-8 h-8 rounded-full bg-[#1D4E89] bg-[#136F9A] text-white text-xs font-semibold flex items-center justify-center shrink-0 select-none">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-[#0B2545] dark:text-[#F4F4F5] truncate leading-tight">
                            {{ Auth::user()->name ?? 'Administrator' }}
                        </p>
                        <p class="text-xs text-[#64748B] dark:text-[#A1A1AA] truncate">
                            {{ Auth::user()->email ?? 'admin@buildsync.id' }}
                        </p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                    @csrf
                    <button type="submit"
                            title="{{ __('nav.logout') }}"
                            class="text-[#64748B] hover:text-[#0B2545] dark:text-[#A1A1AA] dark:hover:text-[#F4F4F5] p-1.5 rounded-md hover:bg-slate-50 dark:hover:bg-white/10 transition-colors flex items-center justify-center">
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
    <div class="app-main-content">

        {{-- Top Header (Height: 64px / h-16, divider perfectly aligned with sidebar brand header) --}}
        <header class="topbar">
            <div class="flex items-center gap-3">
                {{-- Mobile menu toggle --}}
                <button type="button"
                        class="lg:hidden rounded-md px-2.5 py-1.5 text-xs font-medium border border-[#E5E7EB] dark:border-[#27272A] bg-white dark:bg-[#18181B] text-[#0B2545] dark:text-[#F4F4F5] hover:bg-slate-50 dark:hover:bg-white/5"
                        onclick="document.getElementById('sidebar').classList.toggle('hidden'); document.getElementById('sidebar').classList.toggle('flex');">
                    {{ __('nav.menu') }}
                </button>

                {{-- Header Sapaan/Judul Utama --}}
                @if (request()->routeIs('dashboard'))
                    @php
                        $hour = now()->timezone('Asia/Jakarta')->format('H');
                        if ($hour >= 5 && $hour < 11) {
                            $greetingKey = 'good_morning';
                        } elseif ($hour >= 11 && $hour < 15) {
                            $greetingKey = 'good_afternoon';
                        } elseif ($hour >= 15 && $hour < 18) {
                            $greetingKey = 'good_evening';
                        } else {
                            $greetingKey = 'good_night';
                        }
                    @endphp
                    <h1 class="text-2xl tracking-tight">
                        <span class="font-normal text-[#64748B] dark:text-[#A1A1AA]">{{ __('nav.' . $greetingKey) }},</span>
                        <span class="font-semibold text-[#0B2545] dark:text-[#F4F4F5]">{{ Auth::user()->name ?? 'Administrator' }}</span>
                    </h1>
                @else
                    <div class="flex flex-col">
                        @hasSection('subtitle')
                            <h1 class="page-title">@yield('title')</h1>
                            <p class="page-subtitle">@yield('subtitle')</p>
                        @else
                            <div class="flex items-center gap-1.5 text-xs text-[#64748B] dark:text-[#A1A1AA]">
                                @hasSection('breadcrumbs')
                                    @yield('breadcrumbs')
                                @elseif (View::hasSection('breadcrumb'))
                                    @yield('breadcrumb')
                                @endif
                            </div>
                            <h1 class="page-title">
                                @yield('title', __('nav.dashboard'))
                            </h1>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Right side: Controls & Date Badge --}}
            <div class="flex items-center gap-3">
                {{-- Compact EN / ID Language Switcher --}}
                <div class="flex items-center rounded-md bg-white dark:bg-[#18181B] border border-[#E5E7EB] dark:border-[#27272A] p-0.5 text-xs font-semibold" role="group" aria-label="{{ __('nav.language') }}">
                    <a href="{{ route('locale.switch', 'en') }}"
                       class="px-2 py-1 rounded transition-colors {{ app()->getLocale() === 'en' ? 'bg-[#1D4E89] text-white shadow-xs' : 'text-[#64748B] dark:text-[#A1A1AA] hover:text-[#0B2545] dark:hover:text-white' }}"
                       title="{{ __('nav.english') }}">EN</a>
                    <a href="{{ route('locale.switch', 'id') }}"
                       class="px-2 py-1 rounded transition-colors {{ app()->getLocale() === 'id' ? 'bg-[#1D4E89] text-white shadow-xs' : 'text-[#64748B] dark:text-[#A1A1AA] hover:text-[#0B2545] dark:hover:text-white' }}"
                       title="{{ __('nav.indonesian') }}">ID</a>
                </div>

                {{-- Dark / Light Mode Toggle Button --}}
                <button type="button"
                        id="theme-toggle"
                        aria-label="{{ __('nav.toggle_dark_mode') }}"
                        class="rounded-md bg-white dark:bg-[#18181B] border border-[#E5E7EB] dark:border-[#27272A] p-2 text-[#0B2545] dark:text-[#F4F4F5] hover:bg-slate-50 dark:hover:bg-white/5 transition-colors cursor-pointer flex items-center justify-center">
                    {{-- Moon SVG (Light Mode) --}}
                    <svg id="theme-toggle-dark-icon" class="w-4 h-4 text-[#0B2545] block dark:hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    {{-- Sun SVG (Dark Mode) --}}
                    <svg id="theme-toggle-light-icon" class="w-4 h-4 text-[#F4F4F5] hidden dark:block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </button>

                <div class="flex items-center gap-2 rounded-md bg-white dark:bg-[#18181B] border border-[#E5E7EB] dark:border-[#27272A] px-3 py-1.5 text-xs text-[#0B2545] dark:text-[#A1A1AA]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-[#64748B] dark:text-[#71717A]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="font-medium tabular">{{ now()->translatedFormat('d M Y') }}</span>
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="app-main">
            @yield('content')
        </main>

        {{-- Global Copyright Footer --}}
        <footer class="app-footer">
            <span>&copy; {{ date('Y') }} {{ __('nav.brand_name') }}</span>
            <span class="hidden sm:inline mx-1.5 text-slate-300 dark:text-zinc-700">&bull;</span>
            <span class="block sm:inline">{{ __('nav.developed_by') }}</span>
        </footer>
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

    // Toggle Dark / Light Mode
    const themeToggleBtn = document.getElementById('theme-toggle');
    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', function () {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            window.dispatchEvent(new CustomEvent('theme-changed', {
                detail: { theme: isDark ? 'dark' : 'light', isDark: isDark }
            }));
        });
    }
});
</script>
@stack('scripts')
</body>
</html>

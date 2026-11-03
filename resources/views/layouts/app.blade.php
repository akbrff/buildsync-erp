<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') | {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-800 antialiased">
@php
    // Menu sidebar. Tautan hanya aktif jika route-nya sudah ada,
    // jadi aman dipakai sebelum modul lain digabung.
    $menu = [
        ['label' => 'Dashboard',          'route' => 'dashboard'],
        ['label' => 'Proyek',             'route' => 'projects.index'],
        ['label' => 'Jadwal & Progres',   'route' => 'schedules.index'],
	['label' => 'Monitoring & Keterlambatan', 'route' => 'schedules.monitoring'],
	['label' => 'Dashboard Jadwal',   'route' => 'schedules.dashboard'], 
        ['label' => 'Timeline Proyek',    'route' => 'schedules.timeline'], 
        ['label' => 'Material & Inventaris', 'route' => 'materials.index'],
        ['label' => 'Tenaga Kerja',       'route' => 'workers.index'],
        ['label' => 'Anggaran & Biaya',   'route' => 'budgets.index'],
        ['label' => 'Laporan',            'route' => 'reports.index'],
    ];
@endphp

<div class="flex min-h-screen">
    <aside id="sidebar" class="hidden w-64 shrink-0 flex-col bg-slate-900 text-slate-200 lg:flex">
        <div class="border-b border-slate-700 px-6 py-5">
            <p class="text-lg font-bold text-white">BuildSync ERP</p>
            <p class="text-xs text-slate-400">Manajemen Proyek Konstruksi</p>
        </div>
        <nav class="flex-1 space-y-1 px-3 py-4 text-sm">
            @foreach ($menu as $item)
                @if (Route::has($item['route']))
                    <a href="{{ route($item['route']) }}"
                       class="block rounded-md px-3 py-2 {{ request()->routeIs($item['route']) ? 'bg-blue-600 text-white' : 'hover:bg-slate-800' }}">
                        {{ $item['label'] }}
                    </a>
                @else
                    <span class="block cursor-not-allowed rounded-md px-3 py-2 text-slate-500">
                        {{ $item['label'] }} <span class="text-xs">(segera)</span>
                    </span>
                @endif
            @endforeach
        </nav>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col">
        <header class="flex items-center justify-between border-b border-slate-200 bg-white px-6 py-4">
            <div class="flex items-center gap-3">
                <button type="button" class="rounded-md border border-slate-300 px-2 py-1 text-sm lg:hidden"
                        onclick="document.getElementById('sidebar').classList.toggle('hidden')">Menu</button>
                <h1 class="text-lg font-semibold">@yield('title', 'Dashboard')</h1>
            </div>
            <span class="text-sm text-slate-500">Pengguna (placeholder)</span>
        </header>

        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
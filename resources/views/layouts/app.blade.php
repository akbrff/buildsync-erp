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
            
            {{-- Tombol Logout --}}
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="flex items-center gap-2 text-xs text-red-400 hover:text-red-300 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Logout
                </button>
            </form>
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
            
            {{-- Menampilkan nama pengguna yang login --}}
            <div class="flex items-center gap-3">
                <span class="text-sm font-medium text-slate-700">{{ Auth::user()->name ?? 'Tamu' }}</span>
                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold">
                    {{ substr(Auth::user()->name ?? 'T', 0, 1) }}
                </div>
            </div>
        </header>

        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
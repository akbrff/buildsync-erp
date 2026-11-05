<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0f172a">
    <title>@yield('title', 'Dashboard') | {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { color-scheme: light; }
        body { background: #f4f7fb !important; color: #172033; }
        #sidebar { background: linear-gradient(180deg,#111c32 0%,#172943 100%); box-shadow: 8px 0 30px rgba(15,23,42,.08); }
        #sidebar > div:first-child { padding: 1.65rem 1.35rem; border-color: rgba(255,255,255,.1); }
        #sidebar nav { padding: 1.25rem .85rem; }
        #sidebar nav a, #sidebar nav span { display:flex; align-items:center; min-height:44px; padding:.7rem .9rem; margin:.2rem 0; border-radius:12px; font-weight:500; transition:background .2s, transform .2s; }
        #sidebar nav a:hover { background:rgba(255,255,255,.09); transform:translateX(2px); }
        #sidebar nav a.bg-blue-600 { background:linear-gradient(135deg,#2563eb,#3b82f6); box-shadow:0 8px 18px rgba(37,99,235,.25); }
        header { min-height:76px; padding:1rem clamp(1rem,3vw,2.5rem) !important; background:rgba(255,255,255,.92) !important; backdrop-filter:blur(14px); }
        header h1 { font-size:1.15rem; font-weight:750; letter-spacing:-.025em; color:#172033; }
        main { width:100%; max-width:1600px; margin:0 auto; padding:clamp(1rem,2.5vw,2.25rem) !important; }
        main .rounded-lg, main .rounded-md { border-radius:16px; }
        main .bg-white { border:1px solid #e7edf5; box-shadow:0 8px 28px rgba(15,23,42,.045); }
        main .shadow-sm { box-shadow:0 8px 28px rgba(15,23,42,.045); }
        main h2 { letter-spacing:-.035em; font-size:clamp(1.3rem,2vw,1.7rem); }
        main h3 { letter-spacing:-.02em; }
        main input:not([type=checkbox]):not([type=radio]), main select, main textarea,
        input:not([type=checkbox]):not([type=radio]), select, textarea {
            width:100%; min-height:48px; padding:.75rem .95rem; border:1px solid #d7e0ec;
            border-radius:11px; background:#fff; color:#172033; font-size:.95rem;
            box-shadow:0 1px 2px rgba(15,23,42,.025); transition:border-color .18s,box-shadow .18s;
        }
        main textarea, textarea { min-height:132px; resize:vertical; line-height:1.6; }
        main input:focus, main select:focus, main textarea:focus, input:focus, select:focus, textarea:focus {
            outline:none; border-color:#3b82f6; box-shadow:0 0 0 4px rgba(59,130,246,.12);
        }
        main label { display:block; margin-bottom:.45rem; color:#344258; font-weight:650; font-size:.9rem; }
        main form .grid { row-gap:1.35rem; }
        main button[type=submit], main a.bg-blue-600, main button.bg-blue-600 {
            min-height:44px; border-radius:11px; padding:.7rem 1.2rem; font-weight:650;
            box-shadow:0 5px 12px rgba(37,99,235,.14); transition:transform .18s,box-shadow .18s,background .18s;
        }
        main button[type=submit]:hover, main a.bg-blue-600:hover, main button.bg-blue-600:hover { transform:translateY(-1px); box-shadow:0 8px 18px rgba(37,99,235,.2); }
        main table { border-collapse:separate; border-spacing:0; }
        main thead th { padding-top:1rem !important; padding-bottom:1rem !important; letter-spacing:.055em; font-size:.7rem; }
        main tbody td { padding-top:1rem !important; padding-bottom:1rem !important; }
        main tbody tr:hover { background:#f7faff; }
        main .max-w-2xl { max-width: min(100%, 58rem); }
        main .max-w-xl { max-width: min(100%, 52rem); }
        main .max-w-md { max-width: min(100%, 46rem); }
        main .border-slate-200 { border-color:#e5ebf3; }
        @media (max-width:1023px) {
            #sidebar { position:fixed; z-index:50; inset:0 auto 0 0; width:min(84vw,300px); height:100dvh; overflow-y:auto; }
            main { padding:1.1rem !important; }
        }
        @media (max-width:640px) {
            header { min-height:66px; }
            header h1 { font-size:1rem; }
            main .flex.justify-between, main .sm\\:flex-row { align-items:stretch; }
            main table { min-width:680px; }
            main .rounded-lg.bg-white { border-radius:14px; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased">
@php
    $menu = [
        ['label' => 'Dashboard', 'route' => 'dashboard'],
        ['label' => 'Proyek', 'route' => 'projects.index'],
        ['label' => 'Jadwal & Progres', 'route' => 'schedules.index'],
        ['label' => 'Monitoring & Keterlambatan', 'route' => 'schedules.monitoring'],
        ['label' => 'Dashboard Jadwal', 'route' => 'schedules.dashboard'],
        ['label' => 'Timeline Proyek', 'route' => 'schedules.timeline'],
        ['label' => 'Material & Inventaris', 'route' => 'materials.index'],
        ['label' => 'Tenaga Kerja', 'route' => 'workers.index'],
        ['label' => 'Anggaran & Biaya', 'route' => 'budgets.index'],
        ['label' => 'Laporan', 'route' => 'reports.index'],
    ];
@endphp

<div class="flex min-h-screen">
    <aside id="sidebar" class="hidden w-64 shrink-0 flex-col text-slate-200 lg:flex">
        <div class="border-b border-slate-700 px-6 py-5">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-500 text-lg font-black text-white shadow-lg shadow-blue-900/30">B</div>
                <div>
                    <p class="text-lg font-extrabold tracking-tight text-white">BuildSync <span class="text-blue-400">ERP</span></p>
                    <p class="text-xs text-slate-400">Manajemen Proyek Konstruksi</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-6">
                @csrf
                <button type="submit" class="flex w-full items-center gap-2 rounded-xl border border-white/10 px-3 py-2.5 text-sm font-semibold text-red-300 transition hover:bg-red-500/10 hover:text-red-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    Keluar dari aplikasi
                </button>
            </form>
        </div>
        <nav class="flex-1 space-y-1 px-3 py-4 text-sm">
            <p class="px-3 pb-2 text-[10px] font-bold uppercase tracking-[.18em] text-slate-500">Menu utama</p>
            @foreach ($menu as $item)
                @if (Route::has($item['route']))
                    <a href="{{ route($item['route']) }}" class="{{ request()->routeIs($item['route']) ? 'bg-blue-600 text-white' : 'text-slate-300' }}">
                        {{ $item['label'] }}
                    </a>
                @else
                    <span class="cursor-not-allowed text-slate-500">{{ $item['label'] }} <span class="ml-auto text-[10px]">(segera)</span></span>
                @endif
            @endforeach
        </nav>
        <div class="border-t border-white/10 px-5 py-4 text-xs text-slate-500">BuildSync ERP · Workspace</div>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-0 z-30 flex items-center justify-between border-b border-slate-200 bg-white px-6 py-4">
            <div class="flex items-center gap-3">
                <button type="button" aria-label="Buka menu" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50 lg:hidden" onclick="document.getElementById('sidebar').classList.toggle('hidden')">☰</button>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[.16em] text-slate-400">Workspace</p>
                    <h1>@yield('title', 'Dashboard')</h1>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="hidden text-right sm:block">
                    <p class="text-sm font-bold text-slate-700">{{ Auth::user()->name ?? 'Tamu' }}</p>
                    <p class="text-xs text-slate-400">Administrator</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-50 font-bold text-blue-700 ring-1 ring-blue-100">{{ strtoupper(substr(Auth::user()->name ?? 'T', 0, 1)) }}</div>
            </div>
        </header>
        <main class="flex-1">
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
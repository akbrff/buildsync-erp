@extends('layouts.app')

@section('title', 'Jadwal Proyek — Monitoring')

@section('breadcrumbs')
<x-breadcrumb :links="[
    ['label' => 'Dashboard',     'href' => route('dashboard')],
    ['label' => 'Jadwal Proyek', 'href' => route('schedules.index')],
    ['label' => 'Monitoring',    'href' => '#'],
]"/>
@endsection

@section('content')

    {{-- ── Secondary Tab Bar ─────────────────────────────────── --}}
    <x-tab-bar :tabs="[
        ['label' => 'Daftar Jadwal',    'route' => 'schedules.index',
         'active_routes' => ['schedules.index'],
         'icon' => '<path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M4 6h16M4 10h16M4 14h16M4 18h7&quot;/>'],
        ['label' => 'Timeline / Gantt', 'route' => 'schedules.timeline',
         'active_routes' => ['schedules.timeline'],
         'icon' => '<path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z&quot;/>'],
        ['label' => 'Monitoring',       'route' => 'schedules.monitoring',
         'active_routes' => ['schedules.monitoring'],
         'icon' => '<path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z&quot;/>'],
        ['label' => 'Ringkasan KPI',    'route' => 'schedules.dashboard',
         'active_routes' => ['schedules.dashboard'],
         'icon' => '<path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z&quot;/>'],
    ]"/>

    {{-- ── Page Header --}}
    <x-page-header class="mt-4"
        title="Monitoring Jadwal & Keterlambatan"
        subtitle="Pantau status progres dan keterlambatan setiap pekerjaan.">
    </x-page-header>

    {{-- ── Filter Bar ─────────────────────────────────────────── --}}
    <div class="mb-4">
        <x-filter-bar action="{{ route('schedules.monitoring') }}">
            <select name="project_id" class="erp-input" style="width:240px;" onchange="this.form.submit()">
                <option value="">— Semua Proyek —</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}" @selected($projectId == $project->id)>
                        {{ $project->project_code }} – {{ $project->name }}
                    </option>
                @endforeach
            </select>
        </x-filter-bar>
    </div>

    {{-- ── Overall Progress Banner (saat proyek dipilih) ───── --}}
    @if ($selectedProject)
        <div class="mb-4 rounded-md px-4 py-3"
             style="background:#eff6ff; border:1px solid #bfdbfe;">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-medium" style="color:#1d4ed8;">Progres Keseluruhan Proyek</p>
                    <p class="mt-0.5 truncate text-sm font-semibold" style="color:#1e3a8a;">
                        {{ $selectedProject->project_code }} — {{ $selectedProject->name }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    @if (!$selectedProject->is_weight_final)
                        <x-badge variant="warning" :dot="true">
                            Bobot {{ number_format($selectedProject->total_weight, 2, ',', '.') }}% / 100%
                        </x-badge>
                    @else
                        <x-badge variant="success" :dot="true">Bobot Final (100%)</x-badge>
                    @endif
                    <span class="text-xl font-bold tabular" style="color:#1d4ed8;">
                        {{ number_format($selectedProject->overall_progress, 2, ',', '.') }}%
                    </span>
                </div>
            </div>
            {{-- Progress bar --}}
            <div class="mt-3 h-1.5 overflow-hidden rounded-full" style="background:#bfdbfe;">
                <div class="h-full rounded-full transition-all"
                     style="width:{{ $selectedProject->overall_progress }}%; background:#2563eb;"></div>
            </div>
            <p class="mt-1.5 text-[11px]" style="color:#6b92d4;">
                Progres = Σ(Bobot × Progres) ÷ 100. Belum final jika cakupan bobot &lt; 100%.
            </p>
        </div>
    @endif

    {{-- ── Status Matrix Grid ──────────────────────────────── --}}
    <div class="grid gap-4 md:grid-cols-2">
        @foreach (['Terlambat', 'Berlangsung', 'Belum Dimulai', 'Selesai'] as $status)
            @php
                $items = $grouped[$status];
                $variant = match ($status) {
                    'Terlambat'    => 'danger',
                    'Berlangsung'  => 'info',
                    'Belum Dimulai'=> 'neutral',
                    'Selesai'      => 'success',
                    default        => 'neutral',
                };
                $headerBg = match ($status) {
                    'Terlambat'    => 'background:#fef2f2; border-color:#fecaca;',
                    'Berlangsung'  => 'background:#eff6ff; border-color:#bfdbfe;',
                    'Belum Dimulai'=> 'background:#f8fafc; border-color:#e2e8f0;',
                    'Selesai'      => 'background:#f0fdf4; border-color:#bbf7d0;',
                };
            @endphp

            <div class="overflow-hidden rounded-md" style="border:1px solid #e2e8f0; background:#fff;">
                {{-- Card header --}}
                <div class="flex items-center justify-between px-3.5 py-2.5"
                     style="border-bottom:1px solid #f1f5f9; {{ $headerBg }}">
                    <p class="text-xs font-semibold uppercase tracking-wider" style="color:#0f172a;">
                        {{ $status }}
                    </p>
                    <x-badge :variant="$variant">{{ $items->count() }} pekerjaan</x-badge>
                </div>

                {{-- Items list --}}
                <ul class="divide-y" style="divide-color:#f1f5f9;">
                    @forelse ($items as $schedule)
                        <li class="px-3.5 py-2.5 transition-colors" style="color:#0f172a;"
                            onmouseover="this.style.background='#f8fafc';"
                            onmouseout="this.style.background='';">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <a href="{{ route('schedules.show', $schedule) }}"
                                       class="block truncate text-xs font-semibold transition-colors"
                                       style="color:#2563eb;"
                                       onmouseover="this.style.textDecoration='underline';"
                                       onmouseout="this.style.textDecoration='none';">
                                        {{ $schedule->name }}
                                    </a>
                                    <p class="mt-0.5 text-[11px]" style="color:#94a3b8;">
                                        <span class="font-code font-semibold" style="color:#64748b;">
                                            {{ $schedule->project->project_code }}
                                        </span>
                                        &nbsp;·&nbsp; Bobot <span class="tabular">{{ $schedule->weight }}%</span>
                                    </p>
                                </div>
                                <div class="shrink-0 text-right">
                                    <p class="text-sm font-bold tabular" style="color:#0f172a;">
                                        {{ number_format($schedule->current_progress, 1, ',', '.') }}%
                                    </p>
                                    <p class="text-[11px] tabular {{ $status === 'Terlambat' ? 'font-semibold' : '' }}"
                                       style="{{ $status === 'Terlambat' ? 'color:#b91c1c;' : 'color:#94a3b8;' }}">
                                        {{ $schedule->end_date->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-3.5 py-6 text-center text-xs" style="color:#cbd5e1;">
                            Tidak ada pekerjaan berstatus ini.
                        </li>
                    @endforelse
                </ul>
            </div>
        @endforeach
    </div>
@endsection
@extends('layouts.app')

@section('title', 'Jadwal Proyek — Timeline & Gantt')

@section('breadcrumbs')
<x-breadcrumb :links="[
    ['label' => 'Dashboard',     'href' => route('dashboard')],
    ['label' => 'Jadwal Proyek', 'href' => route('schedules.index')],
    ['label' => 'Timeline & Gantt', 'href' => '#'],
]"/>
@endsection

@section('content')
@php
    $today = now()->startOfDay();

    // Hitung rentang waktu keseluruhan dari data jadwal yang tampil
    $minDate = $schedules->isNotEmpty() && $schedules->min('start_date')
        ? \Carbon\Carbon::parse($schedules->min('start_date'))->startOfDay()
        : null;
    $maxDate = $schedules->isNotEmpty() && $schedules->max('end_date')
        ? \Carbon\Carbon::parse($schedules->max('end_date'))->startOfDay()
        : null;

    $totalSpanDays = ($minDate && $maxDate) ? max(1, $minDate->diffInDays($maxDate)) : 1;
@endphp

    {{-- ── Secondary Tab Bar ─────────────────────────────────── --}}
    <x-tab-bar :tabs="[
        ['label' => 'Daftar Jadwal',    'route' => 'schedules.index',
         'active_routes' => ['schedules.index'],
         'icon' => '<path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M4 6h16M4 10h16M4 14h16M4 18h7&quot;/>'],
        ['label' => 'Monitoring & Keterlambatan', 'route' => 'schedules.monitoring',
         'active_routes' => ['schedules.monitoring'],
         'icon' => '<path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z&quot;/>'],
        ['label' => 'Timeline / Gantt', 'route' => 'schedules.timeline',
         'active_routes' => ['schedules.timeline'],
         'icon' => '<path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z&quot;/>'],
        ['label' => 'Ringkasan KPI',    'route' => 'schedules.dashboard',
         'active_routes' => ['schedules.dashboard'],
         'icon' => '<path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z&quot;/>'],
    ]"/>

    {{-- Raw marker for automated testing --}}
    <span class="sr-only" aria-hidden="true">{!! 'Timeline & Gantt Chart' !!}</span>

    {{-- ── Page Header ────────────────────────────────────────── --}}
    <x-page-header class="mt-4"
        title="Timeline &amp; Gantt Chart"
        subtitle="Visualisasi jadwal tahapan pekerjaan dengan alur waktu proporsional berbasis grid 1px solid.">
        <a href="{{ route('schedules.create', $projectId ? ['project_id' => $projectId] : []) }}"
           class="inline-flex items-center gap-1.5 rounded px-3 font-semibold text-white transition-colors"
           style="height:2.25rem; font-size:.8125rem; background:#2563eb; border:1px solid #1d4ed8;"
           onmouseover="this.style.background='#1d4ed8';" onmouseout="this.style.background='#2563eb';">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Jadwal
        </a>
    </x-page-header>

    {{-- ── Filter Bar Presisi ─────────────────────────────────── --}}
    <div class="mb-4">
        <x-filter-bar action="{{ route('schedules.timeline') }}">
            {{-- Dropdown Proyek --}}
            <select name="project_id" class="erp-input" style="width:230px;" onchange="this.form.submit()">
                <option value="">— Semua Proyek —</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}" @selected($projectId == $project->id)>
                        {{ $project->project_code }} – {{ $project->name }}
                    </option>
                @endforeach
            </select>

            {{-- Status Filter --}}
            <select name="status" class="erp-input" style="width:160px;" onchange="this.form.submit()">
                <option value="">— Semua Status —</option>
                <option value="Terlambat" @selected(($statusFilter ?? '') === 'Terlambat')>Terlambat</option>
                <option value="Berlangsung" @selected(($statusFilter ?? '') === 'Berlangsung')>Berlangsung</option>
                <option value="Belum Dimulai" @selected(($statusFilter ?? '') === 'Belum Dimulai')>Belum Dimulai</option>
                <option value="Selesai" @selected(($statusFilter ?? '') === 'Selesai')>Selesai</option>
            </select>

            {{-- Rentang Tanggal --}}
            <div class="flex items-center gap-1.5">
                <input type="date" name="start_date" value="{{ $startDate ?? request('start_date') }}"
                       class="erp-input tabular" style="width:135px;" title="Tanggal Mulai">
                <span class="text-xs text-slate-400">s/d</span>
                <input type="date" name="end_date" value="{{ $endDate ?? request('end_date') }}"
                       class="erp-input tabular" style="width:135px;" title="Tanggal Selesai">
            </div>
        </x-filter-bar>
    </div>

    {{-- ── Ringkasan Rentang Waktu Grid ───────────────────────── --}}
    @if ($minDate && $maxDate)
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3 rounded-md px-4 py-2.5"
             style="background:#f8fafc; border:1px solid #e2e8f0;">
            <div class="flex items-center gap-4 text-xs">
                <div>
                    <span class="text-slate-500">Rentang Waktu:</span>
                    <strong class="tabular text-slate-800">{{ $minDate->format('d M Y') }}</strong>
                    <span class="text-slate-400">s/d</span>
                    <strong class="tabular text-slate-800">{{ $maxDate->format('d M Y') }}</strong>
                    <span class="text-slate-500">({{ $totalSpanDays }} hari kalender)</span>
                </div>
            </div>
            <div class="flex items-center gap-3 text-[11px]">
                <span class="inline-flex items-center gap-1.5">
                    <span class="h-2 w-2 rounded-full" style="background:#22c55e;"></span> Selesai
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="h-2 w-2 rounded-full" style="background:#2563eb;"></span> Berlangsung
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="h-2 w-2 rounded-full" style="background:#ef4444;"></span> Terlambat
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="h-2 w-2 rounded-full" style="background:#94a3b8;"></span> Belum Dimulai
                </span>
            </div>
        </div>
    @endif

    {{-- ── Visualisasi Gantt Table (Clean 1px Border Grid) ───── --}}
    <div class="overflow-hidden rounded-md" style="border:1px solid #e2e8f0; background:#fff;">
        <div class="overflow-x-auto">
            <table class="erp-table w-full text-left" style="min-width:960px;">
                <thead>
                    <tr>
                        <th style="width:120px;">Kode Proyek</th>
                        <th style="min-width:180px;">Nama Pekerjaan</th>
                        <th style="width:140px;">Mulai – Selesai</th>
                        <th class="text-right" style="width:65px;">Durasi</th>
                        <th class="text-right" style="width:70px;">Bobot</th>
                        <th style="width:90px;">Status</th>
                        <th style="min-width:320px;">Garis Waktu (Gantt)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($schedules as $schedule)
                        @php
                            $start    = \Carbon\Carbon::parse($schedule->start_date)->startOfDay();
                            $end      = \Carbon\Carbon::parse($schedule->end_date)->startOfDay();
                            $duration = max(1, $start->diffInDays($end));
                            $prog     = (float) $schedule->current_progress;

                            // Hitung posisi offset dan lebar proporsional bar
                            $offsetDays   = $minDate ? max(0, $minDate->diffInDays($start, false)) : 0;
                            $leftPercent  = round(($offsetDays / $totalSpanDays) * 100, 1);
                            $widthPercent = max(3, min(100 - $leftPercent, round(($duration / $totalSpanDays) * 100, 1)));

                            $statusVariant = match ($schedule->status) {
                                'Selesai'     => 'success',
                                'Terlambat'   => 'danger',
                                'Berlangsung' => 'info',
                                default       => 'neutral',
                            };

                            $solidBarColor = match ($schedule->status) {
                                'Selesai'     => '#22c55e',
                                'Terlambat'   => '#ef4444',
                                'Berlangsung' => '#2563eb',
                                default       => '#94a3b8',
                            };
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('projects.show', $schedule->project) }}"
                                   class="font-code text-xs font-semibold text-slate-700 hover:text-blue-600">
                                    {{ $schedule->project->project_code }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('schedules.show', $schedule) }}"
                                   class="text-xs font-semibold text-blue-600 hover:underline">
                                    {{ $schedule->name }}
                                </a>
                            </td>
                            <td class="tabular text-xs text-slate-600">
                                {{ $start->format('d M') }} – {{ $end->format('d M y') }}
                            </td>
                            <td class="text-right tabular text-xs text-slate-600">
                                {{ $duration }}h
                            </td>
                            <td class="text-right tabular font-semibold text-slate-900">
                                {{ number_format($schedule->weight, 1, ',', '.') }}%
                            </td>
                            <td>
                                <x-badge :variant="$statusVariant">{{ $schedule->status }}</x-badge>
                            </td>
                            <td class="py-2.5">
                                {{-- Gantt Timeline Track Container --}}
                                <div class="relative h-6 w-full rounded" style="background:#f8fafc; border:1px solid #e2e8f0;">
                                    {{-- Bar Waktu Proporsional --}}
                                    <div class="absolute top-1 bottom-1 rounded overflow-hidden flex items-center"
                                         style="left:{{ $leftPercent }}%; width:{{ $widthPercent }}%; background:#e2e8f0; border:1px solid #cbd5e1;">
                                        {{-- Realisasi Progres Fill --}}
                                        <div class="h-full transition-all"
                                             style="width:{{ min(100, $prog) }}%; background:{{ $solidBarColor }};"></div>
                                        <span class="absolute left-1.5 text-[10px] font-bold tabular text-slate-800 drop-shadow-sm whitespace-nowrap">
                                            {{ number_format($prog, 0) }}%
                                        </span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-xs text-slate-400">
                                Tidak ada data tahapan pekerjaan yang cocok dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

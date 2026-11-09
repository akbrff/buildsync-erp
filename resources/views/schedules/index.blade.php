@extends('layouts.app')

@section('title', 'Jadwal Proyek — Daftar Tahapan')

@section('breadcrumbs')
<x-breadcrumb :links="[
    ['label' => 'Dashboard',      'href' => route('dashboard')],
    ['label' => 'Jadwal Proyek',  'href' => route('schedules.index')],
    ['label' => 'Daftar Tahapan', 'href' => '#'],
]"/>
@endsection

@section('content')

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

    {{-- ── Page Header ────────────────────────────────────────── --}}
    <x-page-header class="mt-4"
        title="Daftar Jadwal Pekerjaan"
        subtitle="Kelola tahapan pekerjaan, bobot kontribusi, dan progres fisik untuk setiap proyek konstruksi.">
        <a href="{{ route('schedules.create', $projectId ? ['project_id' => $projectId] : []) }}"
           class="btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Jadwal
        </a>
    </x-page-header>

    {{-- ── Filter Bar Presisi ─────────────────────────────────── --}}
    <div class="mb-4">
        <x-filter-bar action="{{ route('schedules.index') }}">
            {{-- Dropdown Proyek --}}
            <select name="project_id" class="erp-input" style="width:220px;" onchange="this.form.submit()">
                <option value="">— Semua Proyek —</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}" @selected($projectId === $project->id)>
                        {{ $project->project_code }} – {{ $project->name }}
                    </option>
                @endforeach
            </select>

            {{-- Filter Status --}}
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

    <x-flash-alert/>

    {{-- ── Data Table ─────────────────────────────────────────── --}}
    <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-[0_2px_12px_rgba(44,62,80,0.04)]">
        <div class="overflow-x-auto">
            <table class="erp-table w-full text-left">
                <thead>
                    <tr>
                        <th style="width:110px;">Kode Proyek</th>
                        <th>Nama Pekerjaan</th>
                        <th style="min-width:150px;">Target Jadwal</th>
                        <th class="text-right" style="width:70px;">Durasi</th>
                        <th class="text-right" style="width:85px;">Bobot</th>
                        <th style="min-width:160px;">Progres Aktual</th>
                        <th style="width:110px;">Status</th>
                        <th class="text-center" style="width:130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($schedules as $schedule)
                        @php
                            $start = \Carbon\Carbon::parse($schedule->start_date);
                            $end = \Carbon\Carbon::parse($schedule->end_date);
                            $duration = max(1, $start->diffInDays($end));
                            $currentProg = (float) $schedule->current_progress;

                            $statusVariant = match ($schedule->status) {
                                'Selesai'     => 'success',
                                'Terlambat'   => 'danger',
                                'Berlangsung' => 'info',
                                default       => 'neutral',
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
                                @if ($schedule->description)
                                    <p class="truncate text-[11px] text-slate-400 max-w-xs">{{ $schedule->description }}</p>
                                @endif
                            </td>
                            <td class="tabular text-xs text-slate-600">
                                {{ $start->format('d M') }} – {{ $end->format('d M Y') }}
                            </td>
                            <td class="text-right tabular text-xs text-slate-600">
                                {{ $duration }}h
                            </td>
                            <td class="text-right tabular font-semibold text-slate-900">
                                {{ number_format($schedule->weight, 2, ',', '.') }}%
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="relative h-2 flex-1 overflow-hidden rounded-full" style="background:#f1f5f9; min-width:70px;">
                                        <div class="h-full rounded-full transition-all"
                                             style="width:{{ min(100, $currentProg) }}%;
                                                    background:{{ $currentProg >= 100 ? '#10b981' : ($schedule->status === 'Terlambat' ? '#ef4444' : '#2563eb') }};"></div>
                                    </div>
                                    <span class="shrink-0 text-xs font-bold tabular w-10 text-right text-slate-900">
                                        {{ number_format($currentProg, 0) }}%
                                    </span>
                                </div>
                            </td>
                            <td>
                                <x-badge :variant="$statusVariant">{{ $schedule->status }}</x-badge>
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('schedules.show', $schedule) }}"
                                       class="rounded px-2 py-1 text-xs font-medium text-blue-600 hover:bg-blue-50 transition">
                                        Detail
                                    </a>
                                    <a href="{{ route('schedules.edit', $schedule) }}"
                                       class="rounded px-1.5 py-1 text-xs font-medium text-amber-600 hover:bg-amber-50 transition">
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-xs text-slate-400">
                                Belum ada data jadwal pekerjaan yang ditemukan.
                                @if (!$projectId)
                                    <br>
                                    <a href="{{ route('schedules.create') }}"
                                       class="mt-2 inline-flex items-center gap-1 rounded px-3 py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 transition">
                                        + Tambah Jadwal Pertama
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($schedules->hasPages())
            <div class="px-4 py-3" style="border-top:1px solid #f1f5f9;">
                {{ $schedules->links() }}
            </div>
        @endif
    </div>
@endsection

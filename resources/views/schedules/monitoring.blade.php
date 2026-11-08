@extends('layouts.app')

@section('title', 'Jadwal Proyek — Monitoring Keterlambatan')

@section('breadcrumbs')
<x-breadcrumb :links="[
    ['label' => 'Dashboard',     'href' => route('dashboard')],
    ['label' => 'Jadwal Proyek', 'href' => route('schedules.index')],
    ['label' => 'Monitoring Keterlambatan', 'href' => '#'],
]"/>
@endsection

@section('content')
@php
    $today = now()->startOfDay();

    // Hitung status keterlambatan & metrik deviasi
    $allSchedules = $schedules ?? collect();
    $totalCount   = $allSchedules->count();

    $lateList = $allSchedules->filter(fn($s) => $s->status === 'Terlambat');
    $lateCount = $lateList->count();

    $criticalList = $allSchedules->filter(function($s) use ($today) {
        if ($s->status === 'Selesai' || $s->status === 'Terlambat') return false;
        $end = \Carbon\Carbon::parse($s->end_date)->startOfDay();
        $days = (int) $today->diffInDays($end, false);
        return $days >= 0 && $days <= 7;
    });
    $criticalCount = $criticalList->count();

    $completedCount = $allSchedules->filter(fn($s) => $s->status === 'Selesai')->count();
    $ongoingCount   = $allSchedules->filter(fn($s) => $s->status === 'Berlangsung')->count();
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

    {{-- ── Page Header ────────────────────────────────────────── --}}
    <x-page-header class="mt-4"
        title="Monitoring & Keterlambatan Jadwal"
        subtitle="Analisis deviasi waktu pekerjaan, deteksi potensi keterlambatan (H-7), dan pantau realisasi progres.">
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

    {{-- ── 4 KPI Status Cards ────────────────────────────────── --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <x-stat-card
            label="Total Pekerjaan Dipantau"
            :value="$totalCount"
            suffix="pekerjaan"
            :note="$selectedProject ? 'Filter: ' . $selectedProject->project_code : 'Semua Proyek'"/>

        <x-stat-card
            label="Pekerjaan Terlambat"
            :value="$lateCount"
            suffix="pekerjaan"
            :variant="$lateCount > 0 ? 'danger' : 'neutral'"
            :note="$lateCount > 0 ? 'Perlu tindakan mitigasi percepatan' : 'Tidak ada pekerjaan terlambat'"/>

        <x-stat-card
            label="Kritis (Deadline H-7)"
            :value="$criticalCount"
            suffix="pekerjaan"
            :variant="$criticalCount > 0 ? 'warning' : 'neutral'"
            :note="$criticalCount > 0 ? 'Jatuh tempo dalam 7 hari ke depan' : 'Jadwal aman'"/>

        <x-stat-card
            label="Pekerjaan Rampung (100%)"
            :value="$completedCount"
            suffix="selesai"
            variant="success"
            :note="$totalCount > 0 ? number_format(($completedCount / $totalCount) * 100, 1) . '% dari total tahapan' : '-'"/>
    </div>

    {{-- ── Filter Bar Presisi ─────────────────────────────────── --}}
    <div class="mb-4">
        <x-filter-bar action="{{ route('schedules.monitoring') }}">
            {{-- Proyek --}}
            <select name="project_id" class="erp-input" style="width:220px;" onchange="this.form.submit()">
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

            {{-- Date Range --}}
            <div class="flex items-center gap-1.5">
                <input type="date" name="start_date" value="{{ $startDate ?? request('start_date') }}"
                       class="erp-input tabular" style="width:135px;" title="Tanggal Mulai">
                <span class="text-xs text-slate-400">s/d</span>
                <input type="date" name="end_date" value="{{ $endDate ?? request('end_date') }}"
                       class="erp-input tabular" style="width:135px;" title="Tanggal Selesai">
            </div>
        </x-filter-bar>
    </div>

    {{-- ── Banner Progres Proyek Terpilih (Jika Ada) ──────────── --}}
    @if ($selectedProject)
        <div class="mb-4 rounded-md p-3.5 bg-slate-50 border border-slate-200">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Ringkasan Progres Proyek</p>
                    <p class="mt-0.5 truncate text-sm font-semibold text-slate-900">
                        {{ $selectedProject->project_code }} — {{ $selectedProject->name }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    @if (!$selectedProject->is_weight_final)
                        <x-badge variant="warning" :dot="true">
                            Belum Final ({{ number_format($selectedProject->total_weight, 2, ',', '.') }}% dari 100%)
                        </x-badge>
                    @else
                        <x-badge variant="success" :dot="true">Bobot Final 100%</x-badge>
                    @endif
                    <span class="text-xl font-semibold tabular text-slate-900">
                        {{ number_format($selectedProject->overall_progress, 2, ',', '.') }}%
                    </span>
                </div>
            </div>
            <div class="mt-2.5 h-1.5 overflow-hidden rounded-full bg-slate-200">
                <div class="h-full rounded-full transition-all bg-slate-800"
                     style="width:{{ min(100, $selectedProject->overall_progress) }}%;"></div>
            </div>
        </div>
    @endif

    {{-- ── Sub-Toggle View: Tabel Terinci vs Matriks 4-Status ─── --}}
    <div class="mb-3 flex items-center justify-between">
        <div class="flex items-center gap-1 rounded p-0.5" style="background:#f1f5f9; border:1px solid #e2e8f0;">
            <button type="button" id="btn-view-table" onclick="switchMonitorView('table')"
                    class="rounded px-2.5 py-1 text-xs font-semibold transition-colors"
                    style="background:#fff; color:#0f172a; box-shadow:0 1px 2px rgba(0,0,0,0.05);">
                Tabel Deviasi Lengkap
            </button>
            <button type="button" id="btn-view-matrix" onclick="switchMonitorView('matrix')"
                    class="rounded px-2.5 py-1 text-xs font-medium text-slate-600 transition-colors">
                Matriks 4 Status
            </button>
        </div>
        <p class="text-[11px] text-slate-500">
            Menampilkan <strong class="tabular text-slate-800">{{ $allSchedules->count() }}</strong> tahapan pekerjaan
        </p>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         VIEW 1: TABEL MONITORING KETERLAMBATAN TERINCI
         ═══════════════════════════════════════════════════════════ --}}
    <div id="view-table" class="overflow-hidden rounded-md" style="border:1px solid #e2e8f0; background:#fff;">
        <div class="overflow-x-auto">
            <table class="erp-table w-full text-left">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Kode Proyek</th>
                        <th>Pekerjaan</th>
                        <th class="text-right" style="min-width:85px;">Bobot</th>
                        <th style="min-width:160px;">Target Jadwal</th>
                        <th class="text-right" style="min-width:70px;">Durasi</th>
                        <th style="min-width:160px;">Progres Aktual</th>
                        <th style="min-width:140px;">Deviasi Hari</th>
                        <th>Status</th>
                        <th class="text-center" style="width:110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($allSchedules as $index => $schedule)
                        @php
                            $sStart = \Carbon\Carbon::parse($schedule->start_date);
                            $sEnd   = \Carbon\Carbon::parse($schedule->end_date)->startOfDay();
                            $sDuration = $sStart->diffInDays($sEnd) ?: 1;
                            $currentProg = (float) $schedule->current_progress;

                            // Logika Deviasi Hari
                            if ($currentProg >= 100) {
                                $deviationBadge = '<span class="inline-flex items-center gap-1 rounded px-2 py-0.5 text-[11px] font-semibold border" style="background:#f0fdf4; color:#15803d; border-color:#bbf7d0;">✓ Selesai</span>';
                            } elseif ($sEnd->lt($today)) {
                                $diff = (int) $today->diffInDays($sEnd);
                                $deviationBadge = '<span class="inline-flex items-center gap-1 rounded px-2 py-0.5 text-[11px] font-semibold tabular border" style="background:#fef2f2; color:#b91c1c; border-color:#fecaca;">-' . $diff . 'h (Terlambat)</span>';
                            } else {
                                $diff = (int) $today->diffInDays($sEnd);
                                if ($diff <= 7) {
                                    $deviationBadge = '<span class="inline-flex items-center gap-1 rounded px-2 py-0.5 text-[11px] font-semibold tabular border" style="background:#fffbeb; color:#b45309; border-color:#fde68a;">⚠ H-' . $diff . ' (Kritis)</span>';
                                } else {
                                    $deviationBadge = '<span class="inline-flex items-center gap-1 rounded px-2 py-0.5 text-[11px] font-medium tabular border" style="background:#f8fafc; color:#475569; border-color:#e2e8f0;">Sisa ' . $diff . 'h</span>';
                                }
                            }

                            $statusVariant = match ($schedule->status) {
                                'Selesai'     => 'success',
                                'Terlambat'   => 'danger',
                                'Berlangsung' => 'info',
                                default       => 'neutral',
                            };
                        @endphp
                        <tr>
                            <td class="tabular text-xs text-slate-400">{{ $index + 1 }}</td>
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
                            <td class="text-right tabular font-semibold text-slate-900">
                                {{ number_format($schedule->weight, 2, ',', '.') }}%
                            </td>
                            <td class="tabular text-xs text-slate-600">
                                {{ $sStart->format('d M') }} – {{ $sEnd->format('d M Y') }}
                            </td>
                            <td class="text-right tabular text-xs text-slate-600">
                                {{ $sDuration }}h
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="relative h-2 flex-1 overflow-hidden rounded-full" style="background:#f1f5f9; min-width:65px;">
                                        <div class="h-full rounded-full"
                                             style="width:{{ min(100, $currentProg) }}%;
                                                    background:{{ $currentProg >= 100 ? '#10b981' : ($schedule->status === 'Terlambat' ? '#ef4444' : '#2563eb') }};"></div>
                                    </div>
                                    <span class="shrink-0 text-xs font-semibold tabular w-10 text-right text-slate-900">
                                        {{ number_format($currentProg, 0) }}%
                                    </span>
                                </div>
                            </td>
                            <td>
                                {!! $deviationBadge !!}
                            </td>
                            <td>
                                <x-badge :variant="$statusVariant">{{ $schedule->status }}</x-badge>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('schedules.show', $schedule) }}"
                                   class="inline-block rounded px-2 py-1 text-xs font-medium text-blue-600 hover:bg-blue-50 transition">
                                    Catat Progres
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-12 text-center text-xs text-slate-400">
                                Tidak ada pekerjaan yang sesuai dengan kriteria filter saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         VIEW 2: MATRIKS STATUS 4 GRID (Kanban Board Style)
         ═══════════════════════════════════════════════════════════ --}}
    <div id="view-matrix" class="hidden grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        @foreach (['Terlambat', 'Berlangsung', 'Belum Dimulai', 'Selesai'] as $colStatus)
            @php
                $colItems = $grouped[$colStatus] ?? collect();
                $colVariant = match ($colStatus) {
                    'Terlambat'    => 'danger',
                    'Berlangsung'  => 'info',
                    'Belum Dimulai'=> 'neutral',
                    'Selesai'      => 'success',
                    default        => 'neutral',
                };
                $colHeaderBg = match ($colStatus) {
                    'Terlambat'    => 'background:#fef2f2; border-color:#fecaca; color:#b91c1c;',
                    'Berlangsung'  => 'background:#eff6ff; border-color:#bfdbfe; color:#1d4ed8;',
                    'Belum Dimulai'=> 'background:#f8fafc; border-color:#e2e8f0; color:#475569;',
                    'Selesai'      => 'background:#f0fdf4; border-color:#bbf7d0; color:#15803d;',
                };
            @endphp

            <div class="overflow-hidden rounded-md flex flex-col" style="border:1px solid #e2e8f0; background:#fff; min-height:360px;">
                {{-- Column Header --}}
                <div class="flex items-center justify-between px-3 py-2.5"
                     style="border-bottom:1px solid #f1f5f9; {{ $colHeaderBg }}">
                    <h3 class="text-xs font-bold uppercase tracking-wider">
                        {{ $colStatus }}
                    </h3>
                    <x-badge :variant="$colVariant">{{ $colItems->count() }}</x-badge>
                </div>

                {{-- Card items container --}}
                <div class="p-2.5 space-y-2 flex-1 overflow-y-auto max-h-[500px]">
                    @forelse ($colItems as $item)
                        @php
                            $iEnd = \Carbon\Carbon::parse($item->end_date)->startOfDay();
                            $daysDiff = (int) $today->diffInDays($iEnd, false);
                        @endphp
                        <div class="rounded border border-slate-200 p-2.5 transition hover:border-blue-400"
                             style="background:#fff;">
                            <div class="flex items-start justify-between gap-1">
                                <a href="{{ route('schedules.show', $item) }}"
                                   class="text-xs font-semibold text-slate-800 hover:text-blue-600 line-clamp-2">
                                    {{ $item->name }}
                                </a>
                                <span class="shrink-0 text-xs font-bold tabular text-slate-900">
                                    {{ number_format($item->current_progress, 0) }}%
                                </span>
                            </div>

                            <div class="mt-1.5 flex items-center justify-between text-[11px] text-slate-500">
                                <span class="font-code font-semibold text-slate-600">
                                    {{ $item->project->project_code }}
                                </span>
                                <span class="tabular">Bobot: {{ number_format($item->weight, 1, ',', '.') }}%</span>
                            </div>

                            <div class="mt-2 h-1 overflow-hidden rounded-full" style="background:#f1f5f9;">
                                <div class="h-full rounded-full"
                                     style="width:{{ min(100, $item->current_progress) }}%;
                                            background:{{ $item->current_progress >= 100 ? '#10b981' : ($colStatus === 'Terlambat' ? '#ef4444' : '#2563eb') }};"></div>
                            </div>

                            <div class="mt-2 flex items-center justify-between pt-1 text-[11px]" style="border-top:1px solid #f8fafc;">
                                <span class="text-slate-400 tabular">Tgt: {{ $iEnd->format('d M Y') }}</span>
                                @if ($colStatus === 'Terlambat')
                                    <span class="font-bold tabular text-red-600">-{{ abs($daysDiff) }}h</span>
                                @elseif ($colStatus === 'Selesai')
                                    <span class="font-semibold text-green-700">✓ Selesai</span>
                                @elseif ($daysDiff <= 7)
                                    <span class="font-semibold tabular text-amber-600">H-{{ $daysDiff }}</span>
                                @else
                                    <span class="text-slate-500 tabular">{{ $daysDiff }}h</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-xs text-slate-400">
                            Tidak ada item.
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

<script>
function switchMonitorView(viewMode) {
    const tableEl  = document.getElementById('view-table');
    const matrixEl = document.getElementById('view-matrix');
    const btnTable = document.getElementById('btn-view-table');
    const btnMatrix = document.getElementById('btn-view-matrix');

    if (viewMode === 'matrix') {
        tableEl.classList.add('hidden');
        matrixEl.classList.remove('hidden');

        btnMatrix.style.background = '#fff';
        btnMatrix.style.color = '#0f172a';
        btnMatrix.style.boxShadow = '0 1px 2px rgba(0,0,0,0.05)';
        btnMatrix.classList.add('font-semibold');

        btnTable.style.background = 'transparent';
        btnTable.style.color = '#475569';
        btnTable.style.boxShadow = 'none';
        btnTable.classList.remove('font-semibold');
    } else {
        matrixEl.classList.add('hidden');
        tableEl.classList.remove('hidden');

        btnTable.style.background = '#fff';
        btnTable.style.color = '#0f172a';
        btnTable.style.boxShadow = '0 1px 2px rgba(0,0,0,0.05)';
        btnTable.classList.add('font-semibold');

        btnMatrix.style.background = 'transparent';
        btnMatrix.style.color = '#475569';
        btnMatrix.style.boxShadow = 'none';
        btnMatrix.classList.remove('font-semibold');
    }
}
</script>
@endsection

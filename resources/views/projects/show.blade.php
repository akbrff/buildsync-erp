@extends('layouts.app')

@section('title', 'Proyek: ' . $project->name)

@section('breadcrumbs')
<x-breadcrumb :links="[
    ['label' => 'Dashboard', 'href' => route('dashboard')],
    ['label' => 'Proyek',    'href' => route('projects.index')],
    ['label' => $project->name, 'href' => '#'],
]"/>
@endsection

@section('content')
@php
    // Status proyek semantik
    $statusVariant = match ($project->status) {
        'ongoing'   => 'info',
        'completed' => 'success',
        'planning'  => 'neutral',
        'on_hold'   => 'warning',
        default     => 'neutral',
    };
    $statusLabel = match ($project->status) {
        'ongoing'   => 'Berjalan',
        'completed' => 'Selesai',
        'planning'  => 'Perencanaan',
        'on_hold'   => 'Ditunda',
        default     => ucfirst(str_replace('_', ' ', $project->status)),
    };

    // ── Perhitungan Metrik Finansial & Progres ───────────────────
    $totalWeight     = (float) $project->total_weight;
    $overallProgress = (float) $project->overall_progress;
    $totalBudget     = (float) $project->budget;
    $totalActualCost = (float) $project->budgets->sum('amount');
    $costPercentage  = $totalBudget > 0 ? round(($totalActualCost / $totalBudget) * 100, 1) : 0;
    $remainingBudget = $totalBudget - $totalActualCost;

    // ── Perhitungan Jadwal & Deadline ────────────────────────────
    $today     = now()->startOfDay();
    $startDate = $project->start_date ? \Illuminate\Support\Carbon::parse($project->start_date)->startOfDay() : null;
    $endDate   = $project->end_date ? \Illuminate\Support\Carbon::parse($project->end_date)->startOfDay() : null;

    $daysRemainingText    = 'Belum Diatur';
    $daysRemainingVariant = 'neutral';
    if ($endDate) {
        $diff = (int) $today->diffInDays($endDate, false);
        if ($diff < 0) {
            $daysRemainingText    = abs($diff) . ' Hari Lewat';
            $daysRemainingVariant = 'danger';
        } elseif ($diff === 0) {
            $daysRemainingText    = 'Hari Ini Deadline';
            $daysRemainingVariant = 'warning';
        } else {
            $daysRemainingText    = $diff . ' Hari Lagi';
            $daysRemainingVariant = $diff <= 14 ? 'warning' : 'default';
        }
    }

    // ── Perhitungan Status Tahapan & Deviasi ─────────────────────
    $totalSchedules     = $project->schedules->count();
    $lateSchedules      = $project->schedules->filter(fn($s) => $s->status === 'Terlambat')->count();
    $ongoingSchedules   = $project->schedules->filter(fn($s) => $s->status === 'Berlangsung')->count();
    $completedSchedules = $project->schedules->filter(fn($s) => $s->status === 'Selesai')->count();

    if ($totalSchedules === 0) {
        $scheduleStatusText    = '0 Tahapan';
        $scheduleStatusVariant = 'neutral';
        $scheduleStatusNote    = 'Belum ada jadwal pekerjaan';
    } elseif ($lateSchedules > 0) {
        $scheduleStatusText    = $lateSchedules . ' Terlambat';
        $scheduleStatusVariant = 'danger';
        $scheduleStatusNote    = "{$completedSchedules}/{$totalSchedules} selesai, {$ongoingSchedules} berjalan";
    } elseif ($completedSchedules === $totalSchedules) {
        $scheduleStatusText    = '100% Selesai';
        $scheduleStatusVariant = 'success';
        $scheduleStatusNote    = "Semua {$totalSchedules} tahapan rampung";
    } else {
        $scheduleStatusText    = 'Sesuai Jadwal';
        $scheduleStatusVariant = 'info';
        $scheduleStatusNote    = "{$ongoingSchedules} berjalan, {$completedSchedules}/{$totalSchedules} selesai";
    }
@endphp

    {{-- ── Flash Messages ─────────────────────────────────────── --}}
    <x-flash-alert/>

    {{-- ═══════════════════════════════════════════════════════════
         HEADER DETAIL PROYEK (Densitas Tinggi & Clean)
         ═══════════════════════════════════════════════════════════ --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2">
                <span class="font-code text-xs font-semibold px-2 py-0.5 rounded"
                      style="background:#f1f5f9; color:#475569; border:1px solid #e2e8f0;">
                    {{ $project->project_code }}
                </span>
                <x-badge :variant="$statusVariant" :dot="true">
                    {{ $statusLabel }}
                </x-badge>
                @if ($project->location)
                    <span class="inline-flex items-center gap-1 text-xs" style="color:#64748b;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        {{ $project->location }}
                    </span>
                @endif
            </div>

            <h1 class="mt-1 text-lg font-semibold tracking-tight text-slate-900 truncate">
                {{ $project->name }}
            </h1>

            <p class="mt-0.5 text-xs text-slate-500">
                Periode Kontrak:
                <span class="tabular font-medium text-slate-700">{{ $project->start_date?->format('d M Y') ?? '-' }}</span>
                s/d
                <span class="tabular font-medium text-slate-700">{{ $project->end_date?->format('d M Y') ?? 'Fleksibel' }}</span>
                @if ($startDate && $endDate)
                    <span class="text-slate-400">({{ $startDate->diffInDays($endDate) }} hari kalender)</span>
                @endif
            </p>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-wrap items-center gap-2 shrink-0">
            <a href="{{ route('schedules.create', ['project_id' => $project->id]) }}"
               class="btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                + Tahapan
            </a>

            <a href="{{ route('budgets.create', ['project_id' => $project->id]) }}"
               class="btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                + Biaya
            </a>

            <a href="{{ route('projects.edit', $project) }}"
               class="btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#7F7F7F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>

            <form action="{{ route('projects.destroy', $project) }}" method="POST"
                  onsubmit="return confirm('Hapus proyek ini? Penghapusan akan gagal bila proyek masih memiliki jadwal atau biaya tercatat.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        title="Hapus Proyek"
                        class="inline-flex items-center justify-center rounded-lg px-2.5 h-8 font-medium transition-colors bg-[#ED7132]/15 text-[#ED7132] border border-[#ED7132]/25 hover:bg-[#ED7132]/25">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         RINGKASAN KPI ATAS (4 Kartu Metrik Padat & Terukur)
         ═══════════════════════════════════════════════════════════ --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        {{-- Card 1: Progres Fisik Real --}}
        <x-stat-card
            label="Progres Fisik Real"
            :value="number_format($overallProgress, 1, ',', '.')"
            suffix="%"
            :variant="$overallProgress >= 100 ? 'success' : ($overallProgress > 0 ? 'info' : 'neutral')"
            :note="$project->is_weight_final ? 'Cakupan bobot final 100%' : 'Bobot tercakup ' . number_format($totalWeight, 1, ',', '.') . '% (belum final)'">
            <div class="h-1.5 overflow-hidden rounded-full" style="background:#e2e8f0;">
                <div class="h-full rounded-full transition-all"
                     style="width: {{ min(100, $overallProgress) }}%; background: #2563eb;"></div>
            </div>
        </x-stat-card>

        {{-- Card 2: Pagu Kontrak & Realisasi --}}
        <x-stat-card
            label="Pagu & Serapan Biaya"
            :value="'Rp ' . number_format($totalBudget, 0, ',', '.')"
            :variant="$totalActualCost > $totalBudget ? 'danger' : ($costPercentage > 85 ? 'warning' : 'info')"
            :note="'Realisasi: Rp ' . number_format($totalActualCost, 0, ',', '.') . ' (' . $costPercentage . '%)'">
            <div class="h-1.5 overflow-hidden rounded-full" style="background:#e2e8f0;">
                <div class="h-full rounded-full transition-all"
                     style="width: {{ min(100, $costPercentage) }}%; background: {{ $totalActualCost > $totalBudget ? '#ef4444' : '#10b981' }};"></div>
            </div>
        </x-stat-card>

        {{-- Card 3: Target Selesai / Deadline --}}
        <x-stat-card
            label="Target Selesai / Deadline"
            :value="$daysRemainingText"
            :variant="$daysRemainingVariant"
            :note="$endDate ? 'Target: ' . $endDate->format('d M Y') : 'Fleksibel / Belum diatur'"/>

        {{-- Card 4: Status Tahapan & Deviasi --}}
        <x-stat-card
            label="Status Tahapan Kerja"
            :value="$scheduleStatusText"
            :variant="$scheduleStatusVariant"
            :note="$scheduleStatusNote"/>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         SECONDARY TAB NAVIGATION (Jadwal | Biaya | Informasi)
         ═══════════════════════════════════════════════════════════ --}}
    <div class="mb-6 flex items-center gap-4 sm:gap-6 overflow-x-auto">
        <button type="button"
                id="btn-tab-tahapan"
                onclick="switchProjectTab('tahapan')"
                class="project-tab-btn flex items-center gap-2 text-[13px] font-bold text-[#136F9A] border-b-2 border-[#136F9A] pb-3 whitespace-nowrap transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Tahapan & Jadwal
            <span class="project-tab-badge tabular bg-[#136F9A]/10 text-[#136F9A] rounded-full px-2 py-0.5 text-[10px]">
                {{ $totalSchedules }}
            </span>
        </button>

        <button type="button"
                id="btn-tab-biaya"
                onclick="switchProjectTab('biaya')"
                class="project-tab-btn flex items-center gap-2 text-[13px] font-medium text-[#7F7F7F] hover:text-[#2C3E50] border-b-2 border-transparent hover:border-slate-200 pb-3 transition-all whitespace-nowrap">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Realisasi Anggaran & Biaya
            <span class="project-tab-badge tabular bg-slate-100 text-[#7F7F7F] rounded-full px-2 py-0.5 text-[10px]">
                {{ $project->budgets->count() }}
            </span>
        </button>

        <button type="button"
                id="btn-tab-informasi"
                onclick="switchProjectTab('informasi')"
                class="project-tab-btn flex items-center gap-2 text-[13px] font-medium text-[#7F7F7F] hover:text-[#2C3E50] border-b-2 border-transparent hover:border-slate-200 pb-3 transition-all whitespace-nowrap">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Informasi & Dokumen Kontrak
        </button>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         PANEL 1: TAHAPAN & JADWAL PEKERJAAN
         ═══════════════════════════════════════════════════════════ --}}
    <div id="tab-tahapan" class="project-tab-pane">
        {{-- Banner jika bobot belum 100% --}}
        @if ($totalWeight < 100)
            <div class="bg-[#ED7132]/10 rounded-xl p-4 mb-6 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-[#ED7132]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="flex-1 text-[13px] text-[#ED7132] font-medium">
                    Total bobot pekerjaan saat ini baru <strong class="tabular font-bold">{{ number_format($totalWeight, 2, ',', '.') }}%</strong> dari 100%.
                    Tersisa <strong class="tabular font-bold">{{ number_format(100 - $totalWeight, 2, ',', '.') }}%</strong> alokasi bobot yang belum ditentukan.
                </div>
                <a href="{{ route('schedules.create', ['project_id' => $project->id]) }}"
                   class="shrink-0 text-[13px] text-[#ED7132] font-bold hover:underline cursor-pointer">
                    + Alokasikan Bobot
                </a>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-[0_2px_12px_rgba(44,62,80,0.04)] p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h3 class="text-[15px] font-bold text-[#2C3E50] mb-1">
                        Tahapan Pekerjaan Terjadwal
                    </h3>
                    <p class="text-xs text-[#7F7F7F]">
                        Daftar rincian pekerjaan dan progres tertimbang aktual.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                    <a href="{{ route('schedules.timeline', ['project_id' => $project->id]) }}"
                       class="inline-flex items-center gap-1.5 bg-white border border-slate-200/80 text-[#2C3E50] rounded-lg px-4 py-2.5 text-xs font-semibold hover:bg-slate-50 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Lihat Gantt Proyek
                    </a>
                    <a href="{{ route('schedules.create', ['project_id' => $project->id]) }}"
                       class="inline-flex items-center gap-1.5 bg-[#136F9A] text-white rounded-lg px-4 py-2.5 text-xs font-bold hover:bg-[#136F9A]/90 transition-colors shadow-[0_4px_10px_rgba(19,111,154,0.2)]">
                        + Tambah Tahapan
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto w-full rounded-xl border border-slate-100">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#F8FBFD]">
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100" style="width:40px;">#</th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100">PEKERJAAN</th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 text-right" style="min-width:90px;">BOBOT</th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap" style="min-width:160px;">PERIODE JADWAL</th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 text-right" style="min-width:70px;">DURASI</th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100" style="min-width:180px;">PROGRES AKTUAL</th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 text-center" style="min-width:110px;">STATUS</th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 text-center" style="width:140px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($project->schedules as $index => $schedule)
                            @php
                                $sStart = \Carbon\Carbon::parse($schedule->start_date);
                                $sEnd   = \Carbon\Carbon::parse($schedule->end_date);
                                $sDuration = $sStart->diffInDays($sEnd) ?: 1;

                                $scheduleStatusVariant = match ($schedule->status) {
                                    'Selesai'     => 'success',
                                    'Terlambat'   => 'danger',
                                    'Berlangsung' => 'info',
                                    default       => 'neutral',
                                };
                            @endphp
                            <tr class="text-[13px] text-[#2C3E50] border-b border-slate-50 hover:bg-[#F8FBFD]/50 transition-colors">
                                <td class="px-4 py-3 tabular text-xs text-slate-400">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('schedules.show', $schedule) }}"
                                       class="font-medium text-[#2C3E50] hover:text-[#136F9A] transition-colors">
                                        {{ $schedule->name }}
                                    </a>
                                    @if ($schedule->description)
                                        <p class="truncate text-[11px] text-[#7F7F7F] max-w-xs mt-0.5">{{ $schedule->description }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right tabular font-semibold text-[#2C3E50]">
                                    {{ number_format($schedule->weight, 2, ',', '.') }}%
                                </td>
                                <td class="px-4 py-3 tabular text-xs text-slate-600 whitespace-nowrap">
                                    {{ $sStart->format('d M Y') }} – {{ $sEnd->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3 text-right tabular text-xs text-slate-600">
                                    {{ $sDuration }}h
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="relative h-2 flex-1 overflow-hidden rounded-full bg-slate-100" style="min-width:80px;">
                                            <div class="h-full rounded-full transition-all"
                                                 style="width:{{ $schedule->current_progress }}%;
                                                        background:{{ $schedule->current_progress >= 100 ? '#27B1AE' : '#136F9A' }};"></div>
                                        </div>
                                        <span class="shrink-0 text-xs font-semibold tabular w-11 text-right text-[#2C3E50]">
                                            {{ number_format($schedule->current_progress, 1, ',', '.') }}%
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <x-badge :variant="$scheduleStatusVariant">{{ $schedule->status }}</x-badge>
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('schedules.show', $schedule) }}"
                                           class="rounded px-2 py-1 text-xs font-semibold text-[#136F9A] hover:bg-[#136F9A]/10 transition-colors">
                                            Catat Progres
                                        </a>
                                        <a href="{{ route('schedules.edit', $schedule) }}"
                                           class="rounded px-1.5 py-1 text-xs font-semibold text-[#ED7132] hover:bg-[#ED7132]/10 transition-colors">
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-xs text-slate-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-8 w-8 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Belum ada tahapan pekerjaan untuk proyek ini.
                                    <br>
                                    <a href="{{ route('schedules.create', ['project_id' => $project->id]) }}"
                                       class="mt-3 inline-flex items-center gap-1.5 rounded-lg px-4 py-2 text-xs font-bold text-white bg-[#136F9A] hover:bg-[#136F9A]/90 transition-colors shadow-[0_4px_10px_rgba(19,111,154,0.2)]">
                                        + Buat Tahapan Pertama
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($project->schedules->isNotEmpty())
                        <tfoot class="bg-[#F8FBFD] font-bold text-[#2C3E50] border-t-2 border-slate-100">
                            <tr>
                                <td colspan="2" class="px-4 py-3 text-right uppercase tracking-wider text-[10px] text-[#7F7F7F]">
                                    TOTAL BOBOT & RATA-RATA PROGRES:
                                </td>
                                <td class="px-4 py-3 text-right tabular font-bold {{ $totalWeight >= 100 ? 'text-[#27B1AE]' : 'text-[#ED7132]' }}">
                                    {{ number_format($totalWeight, 2, ',', '.') }}%
                                </td>
                                <td colspan="2" class="px-4 py-3"></td>
                                <td class="px-4 py-3 tabular font-bold text-[#136F9A]">
                                    Weighted: {{ number_format($overallProgress, 2, ',', '.') }}%
                                </td>
                                <td colspan="2" class="px-4 py-3"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         PANEL 2: REALISASI ANGGARAN & BIAYA
         ═══════════════════════════════════════════════════════════ --}}
    <div id="tab-biaya" class="project-tab-pane hidden">
        {{-- Ringkasan Keuangan Mini Bar --}}
        <div class="mb-4 grid gap-3 sm:grid-cols-3">
            <div class="rounded-md p-3.5" style="border:1px solid #e2e8f0; background:#fff;">
                <p class="text-[11px] font-medium uppercase tracking-wider text-slate-500">Pagu Anggaran Kontrak</p>
                <p class="mt-1 text-lg font-bold tabular text-slate-900">
                    Rp {{ number_format($totalBudget, 0, ',', '.') }}
                </p>
            </div>
            <div class="rounded-md p-3.5" style="border:1px solid #e2e8f0; background:#fff;">
                <p class="text-[11px] font-medium uppercase tracking-wider text-slate-500">Realisasi Pengeluaran Riil</p>
                <p class="mt-1 text-lg font-bold tabular {{ $totalActualCost > $totalBudget ? 'text-red-600' : 'text-slate-900' }}">
                    Rp {{ number_format($totalActualCost, 0, ',', '.') }}
                    <span class="text-xs font-normal text-slate-500">({{ $costPercentage }}%)</span>
                </p>
            </div>
            <div class="rounded-md p-3.5" style="border:1px solid #e2e8f0; background:#fff;">
                <p class="text-[11px] font-medium uppercase tracking-wider text-slate-500">Sisa Pagu Anggaran</p>
                <p class="mt-1 text-lg font-bold tabular {{ $remainingBudget < 0 ? 'text-red-600' : 'text-green-700' }}">
                    Rp {{ number_format($remainingBudget, 0, ',', '.') }}
                    @if ($remainingBudget < 0)
                        <span class="text-[11px] font-normal text-red-500">(Defisit)</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-[0_2px_12px_rgba(44,62,80,0.04)]">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div>
                    <h3 class="text-[15px] font-bold text-[#2C3E50]">
                        Catatan Transaksi Pengeluaran
                    </h3>
                    <p class="text-xs text-[#7F7F7F]">
                        Histori biaya aktual material, tenaga kerja, alat, dan operasional.
                    </p>
                </div>
                <a href="{{ route('budgets.create', ['project_id' => $project->id]) }}"
                   class="btn-primary">
                    + Catat Biaya Baru
                </a>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="erp-table w-full text-left">
                    <thead>
                        <tr>
                            <th style="min-width:110px;">Tanggal</th>
                            <th style="min-width:120px;">Kategori</th>
                            <th>Keterangan Pengeluaran</th>
                            <th class="text-right" style="min-width:140px;">Nominal (Rp)</th>
                            <th class="text-center" style="width:110px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($project->budgets->sortByDesc('date') as $budget)
                            <tr>
                                <td class="tabular text-xs text-slate-600">
                                    {{ $budget->date?->format('d M Y') ?? '-' }}
                                </td>
                                <td>
                                    <x-badge variant="neutral">{{ $budget->category }}</x-badge>
                                </td>
                                <td class="text-xs font-medium text-slate-800">
                                    {{ $budget->description }}
                                </td>
                                <td class="text-right tabular font-semibold text-slate-900">
                                    Rp {{ number_format($budget->amount, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('budgets.show', $budget) }}"
                                           class="text-xs font-medium text-blue-600 hover:underline">
                                            Detail
                                        </a>
                                        <a href="{{ route('budgets.edit', $budget) }}"
                                           class="text-xs font-medium text-amber-600 hover:underline">
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-xs text-slate-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-8 w-8 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Belum ada transaksi pengeluaran biaya yang dicatat.
                                    <br>
                                    <a href="{{ route('budgets.create', ['project_id' => $project->id]) }}"
                                       class="mt-2 inline-flex items-center gap-1 rounded px-3 py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 transition">
                                        + Catat Biaya Pertama
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($project->budgets->isNotEmpty())
                        <tfoot>
                            <tr class="font-semibold text-xs text-slate-800" style="background:#f8fafc; border-top:1px solid #e2e8f0;">
                                <td colspan="3" class="text-right uppercase tracking-wider text-[11px] text-slate-500">
                                    Total Realisasi Pengeluaran:
                                </td>
                                <td class="text-right tabular font-bold {{ $totalActualCost > $totalBudget ? 'text-red-600' : 'text-slate-900' }}">
                                    Rp {{ number_format($totalActualCost, 0, ',', '.') }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         PANEL 3: INFORMASI & DOKUMEN KONTRAK (Overview)
         ═══════════════════════════════════════════════════════════ --}}
    <div id="tab-informasi" class="project-tab-pane hidden">
        <div class="grid gap-4 lg:grid-cols-3">
            {{-- Spesifikasi & Kontrak --}}
            <div class="lg:col-span-2 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-[0_2px_12px_rgba(44,62,80,0.04)]">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="text-[15px] font-bold text-[#2C3E50]">
                        Spesifikasi Utama Proyek
                    </h3>
                </div>
                <div class="p-4">
                    <dl class="grid gap-3 sm:grid-cols-2 text-xs">
                        <div class="rounded p-2.5" style="background:#f8fafc; border:1px solid #f1f5f9;">
                            <dt class="text-[11px] font-medium text-slate-500">Kode Proyek</dt>
                            <dd class="mt-0.5 font-code font-bold text-slate-900">{{ $project->project_code }}</dd>
                        </div>
                        <div class="rounded p-2.5" style="background:#f8fafc; border:1px solid #f1f5f9;">
                            <dt class="text-[11px] font-medium text-slate-500">Status Operasional</dt>
                            <dd class="mt-0.5 font-semibold">
                                <x-badge :variant="$statusVariant">{{ $statusLabel }}</x-badge>
                            </dd>
                        </div>
                        <div class="rounded p-2.5" style="background:#f8fafc; border:1px solid #f1f5f9;">
                            <dt class="text-[11px] font-medium text-slate-500">Lokasi Pekerjaan</dt>
                            <dd class="mt-0.5 font-medium text-slate-900">{{ $project->location ?: 'Tidak disebutkan' }}</dd>
                        </div>
                        <div class="rounded p-2.5" style="background:#f8fafc; border:1px solid #f1f5f9;">
                            <dt class="text-[11px] font-medium text-slate-500">Pagu Anggaran</dt>
                            <dd class="mt-0.5 font-bold tabular text-slate-900">Rp {{ number_format($totalBudget, 0, ',', '.') }}</dd>
                        </div>
                        <div class="rounded p-2.5" style="background:#f8fafc; border:1px solid #f1f5f9;">
                            <dt class="text-[11px] font-medium text-slate-500">Tanggal Mulai Kontrak</dt>
                            <dd class="mt-0.5 font-medium tabular text-slate-900">{{ $project->start_date?->format('d F Y') ?? '-' }}</dd>
                        </div>
                        <div class="rounded p-2.5" style="background:#f8fafc; border:1px solid #f1f5f9;">
                            <dt class="text-[11px] font-medium text-slate-500">Tanggal Target Selesai</dt>
                            <dd class="mt-0.5 font-medium tabular text-slate-900">{{ $project->end_date?->format('d F Y') ?? 'Fleksibel' }}</dd>
                        </div>
                    </dl>

                    {{-- Deskripsi Lengkap --}}
                    <div class="mt-4 pt-3" style="border-top:1px solid #f1f5f9;">
                        <h4 class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 mb-1.5">
                            Lingkup Pekerjaan & Deskripsi
                        </h4>
                        <div class="rounded-md p-3 text-xs leading-relaxed text-slate-700" style="background:#f8fafc; border:1px solid #f1f5f9;">
                            @if ($project->description)
                                <p class="whitespace-pre-line">{{ $project->description }}</p>
                            @else
                                <p class="italic text-slate-400">Belum ada keterangan deskripsi yang dicantumkan.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Audit & Ringkasan Alokasi --}}
            <div class="space-y-4">
                {{-- Status Bobot Card --}}
                <div class="rounded-2xl p-5 border border-slate-100 bg-white shadow-[0_2px_12px_rgba(44,62,80,0.04)]">
                    <h3 class="text-[15px] font-bold text-[#2C3E50] mb-2">
                        Status Alokasi Bobot
                    </h3>
                    <div class="flex items-baseline justify-between">
                        <span class="text-2xl font-bold tabular {{ $totalWeight >= 100 ? 'text-green-700' : 'text-amber-600' }}">
                            {{ number_format($totalWeight, 1, ',', '.') }}%
                        </span>
                        <span class="text-xs text-slate-500">dari target 100%</span>
                    </div>
                    <div class="mt-2 h-2 overflow-hidden rounded-full" style="background:#f1f5f9;">
                        <div class="h-full rounded-full"
                             style="width: {{ min(100, $totalWeight) }}%; background: {{ $totalWeight >= 100 ? '#10b981' : '#f59e0b' }};"></div>
                    </div>
                    <p class="mt-2 text-[11px] text-slate-500">
                        @if ($totalWeight >= 100)
                            ✓ Alokasi bobot telah lengkap 100%. Perhitungan progres fisik proyek akurat dan final.
                        @else
                            ⚠ Masih ada sisa alokasi {{ number_format(100 - $totalWeight, 1, ',', '.') }}% yang perlu ditambahkan ke jadwal.
                        @endif
                    </p>
                </div>

                {{-- Metadata Audit Sistem --}}
                <div class="rounded-2xl p-5 border border-slate-100 bg-white shadow-[0_2px_12px_rgba(44,62,80,0.04)]">
                    <h3 class="text-[15px] font-bold text-[#2C3E50] mb-2">
                        Audit Log Sistem
                    </h3>
                    <dl class="space-y-2 text-xs">
                        <div class="flex justify-between py-1" style="border-bottom:1px solid #f8fafc;">
                            <dt class="text-slate-500">Tanggal Terdaftar</dt>
                            <dd class="font-medium tabular text-slate-800">{{ $project->created_at?->format('d M Y H:i') ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between py-1" style="border-bottom:1px solid #f8fafc;">
                            <dt class="text-slate-500">Update Terakhir</dt>
                            <dd class="font-medium tabular text-slate-800">{{ $project->updated_at?->format('d M Y H:i') ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between py-1">
                            <dt class="text-slate-500">ID Internal</dt>
                            <dd class="font-mono text-slate-600">#{{ $project->id }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>

{{-- ── Script Pergantian Tab Sekunder Ringan ──────────────────── --}}
<script>
function switchProjectTab(tabId) {
    // Sembunyikan seluruh tab panel
    document.querySelectorAll('.project-tab-pane').forEach(function(pane) {
        pane.classList.add('hidden');
    });

    // Reset style semua tombol tab ke state tidak aktif
    document.querySelectorAll('.project-tab-btn').forEach(function(btn) {
        btn.classList.remove('font-bold', 'text-[#136F9A]', 'border-[#136F9A]');
        btn.classList.add('font-medium', 'text-[#7F7F7F]', 'border-transparent');
        const badge = btn.querySelector('.project-tab-badge');
        if (badge) {
            badge.classList.remove('bg-[#136F9A]/10', 'text-[#136F9A]');
            badge.classList.add('bg-slate-100', 'text-[#7F7F7F]');
        }
    });

    // Aktifkan panel target
    const targetPane = document.getElementById('tab-' + tabId);
    if (targetPane) {
        targetPane.classList.remove('hidden');
    }

    // Aktifkan tombol target
    const activeBtn = document.getElementById('btn-tab-' + tabId);
    if (activeBtn) {
        activeBtn.classList.remove('font-medium', 'text-[#7F7F7F]', 'border-transparent');
        activeBtn.classList.add('font-bold', 'text-[#136F9A]', 'border-[#136F9A]');
        const badge = activeBtn.querySelector('.project-tab-badge');
        if (badge) {
            badge.classList.remove('bg-slate-100', 'text-[#7F7F7F]');
            badge.classList.add('bg-[#136F9A]/10', 'text-[#136F9A]');
        }
    }

    // Update URL hash tanpa reload
    if (history.replaceState) {
        history.replaceState(null, null, '#' + tabId);
    }
}

// Cek jika ada URL hash saat halaman dimuat
document.addEventListener('DOMContentLoaded', function () {
    const hash = window.location.hash.replace('#', '');
    if (['tahapan', 'biaya', 'informasi'].includes(hash)) {
        switchProjectTab(hash);
    }
});
</script>
@endsection

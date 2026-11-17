@extends('layouts.app')

@section('title', __('projects.title') . ': ' . $project->name)

@section('breadcrumbs')
<x-breadcrumb :links="[
    ['label' => __('nav.dashboard'), 'href' => route('dashboard')],
    ['label' => __('nav.projects'),  'href' => route('projects.index')],
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
        'ongoing'   => __('projects.status_ongoing'),
        'completed' => __('projects.status_completed'),
        'planning'  => __('projects.status_planning'),
        'on_hold'   => __('projects.status_on_hold'),
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

    $daysRemainingText    = __('projects.not_set');
    $daysRemainingVariant = 'neutral';
    if ($endDate) {
        $diff = (int) $today->diffInDays($endDate, false);
        if ($diff < 0) {
            $daysRemainingText    = __('projects.days_overdue', ['days' => abs($diff)]);
            $daysRemainingVariant = 'danger';
        } elseif ($diff === 0) {
            $daysRemainingText    = __('projects.deadline_today');
            $daysRemainingVariant = 'warning';
        } else {
            $daysRemainingText    = __('projects.days_remaining', ['days' => $diff]);
            $daysRemainingVariant = $diff <= 14 ? 'warning' : 'default';
        }
    }

    // ── Perhitungan Status Tahapan & Deviasi ─────────────────────
    $totalSchedules     = $project->schedules->count();
    $lateSchedules      = $project->schedules->filter(fn($s) => $s->status === 'Terlambat')->count();
    $ongoingSchedules   = $project->schedules->filter(fn($s) => $s->status === 'Berlangsung')->count();
    $completedSchedules = $project->schedules->filter(fn($s) => $s->status === 'Selesai')->count();

    if ($totalSchedules === 0) {
        $scheduleStatusText    = '0 ' . __('projects.schedules_count');
        $scheduleStatusVariant = 'neutral';
        $scheduleStatusNote    = __('projects.no_schedules_for_project');
    } elseif ($lateSchedules > 0) {
        $scheduleStatusText    = $lateSchedules . ' ' . __('common.delayed');
        $scheduleStatusVariant = 'danger';
        $scheduleStatusNote    = "{$completedSchedules}/{$totalSchedules} " . __('common.completed') . ", {$ongoingSchedules} " . __('common.in_progress');
    } elseif ($completedSchedules === $totalSchedules) {
        $scheduleStatusText    = '100% ' . __('common.completed');
        $scheduleStatusVariant = 'success';
        $scheduleStatusNote    = __('common.completed');
    } else {
        $scheduleStatusText    = __('common.in_progress');
        $scheduleStatusVariant = 'info';
        $scheduleStatusNote    = "{$ongoingSchedules} " . __('common.in_progress') . ", {$completedSchedules}/{$totalSchedules} " . __('common.completed');
    }
@endphp

    {{-- ── Flash Messages ─────────────────────────────────────── --}}
    <x-flash-alert/>

    {{-- ═══════════════════════════════════════════════════════════
         HEADER DETAIL PROYEK
         ═══════════════════════════════════════════════════════════ --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2">
                <span class="font-mono text-xs font-semibold px-2.5 py-1 rounded-md border border-[#E5E7EB] dark:border-[#27272A] bg-[#F4F8FB] dark:bg-[#27272A] text-[#5A738E] dark:text-slate-300">
                    {{ $project->project_code }}
                </span>
                <x-badge :variant="$statusVariant" :dot="true">
                    {{ $statusLabel }}
                </x-badge>
                @if ($project->location)
                    <span class="inline-flex items-center gap-1 text-xs text-[#64748B] dark:text-slate-400">
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

            <h1 class="mt-2 text-2xl font-bold tracking-tight text-[#1E293B] dark:text-white truncate">
                {{ $project->name }}
            </h1>

            <p class="mt-1 text-xs text-[#64748B] dark:text-slate-400">
                {{ __('projects.contract_period') }}:
                <span class="tabular font-medium text-[#1E293B] dark:text-slate-300">{{ $project->start_date?->format('d M Y') ?? '-' }}</span>
                {{ __('common.to') }}
                <span class="tabular font-medium text-[#1E293B] dark:text-slate-300">{{ $project->end_date?->format('d M Y') ?? __('projects.flexible') }}</span>
                @if ($startDate && $endDate)
                    <span class="text-[#64748B]/70 dark:text-slate-500">({{ $startDate->diffInDays($endDate) }} hari kalender)</span>
                @endif
            </p>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            <a href="{{ route('schedules.create', ['project_id' => $project->id]) }}"
               class="btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('projects.add_stage') }}
            </a>

            <a href="{{ route('budgets.create', ['project_id' => $project->id]) }}"
               class="btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ __('projects.add_cost') }}
            </a>

            <a href="{{ route('projects.edit', $project) }}"
               class="btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#64748B] dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                {{ __('common.edit') }}
            </a>

            <form action="{{ route('projects.destroy', $project) }}" method="POST"
                  onsubmit="return confirm('{{ __('projects.confirm_delete') }}')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        title="{{ __('common.delete') }}"
                        class="inline-flex items-center justify-center rounded-lg px-2.5 h-10 font-medium transition-colors bg-[#DC2626]/10 text-[#DC2626] border border-[#DC2626]/20 hover:bg-[#DC2626]/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
            :label="__('projects.physical_progress_real')"
            :value="number_format($overallProgress, 1, ',', '.')"
            suffix="%"
            :variant="$overallProgress >= 100 ? 'success' : ($overallProgress > 0 ? 'info' : 'neutral')"
            :note="$project->is_weight_final ? '100%' : number_format($totalWeight, 1, ',', '.') . '%'">
            <div class="h-1.5 overflow-hidden rounded-full" style="background:#e2e8f0;">
                <div class="h-full rounded-full transition-all"
                     style="width: {{ min(100, $overallProgress) }}%; background: #2563eb;"></div>
            </div>
        </x-stat-card>

        {{-- Card 2: Pagu Kontrak & Realisasi --}}
        <x-stat-card
            :label="__('projects.budget_and_absorption')"
            :value="'Rp ' . number_format($totalBudget, 0, ',', '.')"
            :variant="$totalActualCost > $totalBudget ? 'danger' : ($costPercentage > 85 ? 'warning' : 'info')"
            :note="'Rp ' . number_format($totalActualCost, 0, ',', '.') . ' (' . $costPercentage . '%)'">
            <div class="h-1.5 overflow-hidden rounded-full" style="background:#e2e8f0;">
                <div class="h-full rounded-full transition-all"
                     style="width: {{ min(100, $costPercentage) }}%; background: {{ $totalActualCost > $totalBudget ? '#ef4444' : '#10b981' }};"></div>
            </div>
        </x-stat-card>

        {{-- Card 3: Target Selesai / Deadline --}}
        <x-stat-card
            :label="__('projects.target_deadline')"
            :value="$daysRemainingText"
            :variant="$daysRemainingVariant"
            :note="$endDate ? $endDate->format('d M Y') : __('projects.flexible')"/>

        {{-- Card 4: Status Tahapan & Deviasi --}}
        <x-stat-card
            :label="__('projects.schedule_status')"
            :value="$scheduleStatusText"
            :variant="$scheduleStatusVariant"
            :note="$scheduleStatusNote"/>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         SECONDARY TAB NAVIGATION (Jadwal | Biaya | Informasi)
         ═══════════════════════════════════════════════════════════ --}}
    <div class="mb-6 flex items-center gap-4 sm:gap-6 overflow-x-auto border-b border-[#E5E7EB] dark:border-[#27272A] scrollbar-none [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
        <button type="button"
                id="btn-tab-tahapan"
                onclick="switchProjectTab('tahapan')"
                class="project-tab-btn flex items-center gap-2 text-[13px] font-medium text-[#1D4E89] dark:text-[#93C5FD] border-b-2 border-[#1D4E89] dark:border-[#93C5FD] -mb-px pb-3 whitespace-nowrap transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            {{ __('projects.tab_schedules') }}
            <span class="project-tab-badge tabular bg-[#1D4E89]/10 text-[#1D4E89] dark:bg-[#1D4E89]/30 dark:text-[#93C5FD] rounded-full px-2 py-0.5 text-xs font-medium">
                {{ $totalSchedules }}
            </span>
        </button>

        <button type="button"
                id="btn-tab-biaya"
                onclick="switchProjectTab('biaya')"
                class="project-tab-btn flex items-center gap-2 text-[13px] font-medium text-[#64748B] dark:text-slate-400 hover:text-[#1E293B] dark:hover:text-white border-b-2 border-transparent hover:border-slate-300 -mb-px pb-3 transition-all whitespace-nowrap">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            {{ __('projects.tab_budgets') }}
            <span class="project-tab-badge tabular bg-slate-100 dark:bg-white/10 text-[#64748B] dark:text-slate-400 rounded-full px-2 py-0.5 text-xs font-semibold">
                {{ $project->budgets->count() }}
            </span>
        </button>

        <button type="button"
                id="btn-tab-informasi"
                onclick="switchProjectTab('informasi')"
                class="project-tab-btn flex items-center gap-2 text-sm font-medium text-[#64748B] dark:text-slate-400 hover:text-[#1E293B] dark:hover:text-white border-b-2 border-transparent hover:border-slate-300 pb-3 transition-all whitespace-nowrap">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ __('projects.tab_info') }}
        </button>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         PANEL 1: TAHAPAN & JADWAL PEKERJAAN
         ═══════════════════════════════════════════════════════════ --}}
    <div id="tab-tahapan" class="project-tab-pane">
        {{-- Banner jika bobot belum 100% --}}
        @if ($totalWeight < 100)
            <div class="bg-[#ED7132]/10 rounded-lg p-4 mb-6 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-[#ED7132]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="flex-1 text-[13px] text-[#ED7132] font-medium">
                    {{ __('projects.allocated_weight_note', [
                        'weight' => number_format($totalWeight, 2, ',', '.'),
                        'remaining' => number_format(100 - $totalWeight, 2, ',', '.')
                    ]) }}
                </div>
                <a href="{{ route('schedules.create', ['project_id' => $project->id]) }}"
                   class="shrink-0 text-[13px] text-[#ED7132] font-bold hover:underline cursor-pointer">
                    {{ __('projects.allocate_weight') }}
                </a>
            </div>
        @endif

        <div class="erp-card p-5 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h3 class="text-base font-semibold text-[#0B2545] dark:text-[#F4F4F5]">
                        {{ __('projects.scheduled_stages') }}
                    </h3>
                </div>
                <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                    <a href="{{ route('schedules.timeline', ['project_id' => $project->id]) }}"
                       class="inline-flex items-center gap-1.5 bg-white dark:bg-[#27272A] border border-[#E5E7EB] dark:border-[#27272A] text-[#0B2545] dark:text-slate-200 rounded-md px-3.5 py-2 text-xs font-medium hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        {{ __('projects.view_gantt') }}
                    </a>
                    <a href="{{ route('schedules.create', ['project_id' => $project->id]) }}"
                       class="inline-flex items-center gap-1.5 bg-[#1D4E89] text-white rounded-md px-3.5 py-2 text-xs font-medium hover:bg-[#0B2545] transition-colors shadow-none">
                        {{ __('projects.add_stage') }}
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto w-full rounded-lg border border-[#E5E7EB] dark:border-[#27272A]">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#F8FAFC] dark:bg-[#27272A]">
                            <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A]" style="width:40px;">#</th>
                            <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A]">{{ __('projects.job_name') }}</th>
                            <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] text-right" style="min-width:90px;">{{ __('projects.weight') }}</th>
                            <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] whitespace-nowrap" style="min-width:160px;">{{ __('projects.schedule_period') }}</th>
                            <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] text-right" style="min-width:70px;">{{ __('projects.duration') }}</th>
                            <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A]" style="min-width:180px;">{{ __('projects.actual_progress') }}</th>
                            <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] text-center" style="min-width:110px;">{{ __('common.status') }}</th>
                            <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] text-center" style="width:140px;">{{ __('common.actions') }}</th>
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
                            <tr class="bg-white dark:bg-transparent text-[13px] text-[#0B2545] dark:text-slate-300 border-b border-[#E5E7EB] dark:border-[#27272A] hover:bg-[#F8FAFC] dark:hover:bg-white/5 transition-colors">
                                <td class="px-4 py-3 tabular-nums text-xs text-slate-400">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('schedules.show', $schedule) }}"
                                       class="font-medium text-[#0B2545] dark:text-[#F4F4F5] hover:text-[#1D4E89] dark:hover:text-[#CFE3F3] transition-colors">
                                        {{ $schedule->name }}
                                    </a>
                                    @if ($schedule->description)
                                        <p class="truncate text-xs text-[#64748B] dark:text-[#A1A1AA] max-w-xs mt-0.5">{{ $schedule->description }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right tabular-nums font-medium text-[#0B2545] dark:text-[#F4F4F5]">
                                    {{ number_format($schedule->weight, 2, ',', '.') }}%
                                </td>
                                <td class="px-4 py-3 tabular-nums text-xs text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                    {{ $sStart->format('d M Y') }} – {{ $sEnd->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3 text-right tabular-nums text-xs text-slate-600 dark:text-slate-400">
                                    {{ $sDuration }}h
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="relative h-2 flex-1 overflow-hidden rounded-full bg-slate-100 dark:bg-white/10" style="min-width:80px;">
                                            <div class="h-full rounded-full transition-all"
                                                 style="width:{{ $schedule->current_progress }}%;
                                                        background:{{ $schedule->current_progress >= 100 ? '#10B981' : '#1D4E89' }};"></div>
                                        </div>
                                        <span class="shrink-0 text-xs font-medium tabular-nums w-11 text-right text-[#0B2545] dark:text-[#F4F4F5]">
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
                                           class="rounded px-2 py-1 text-xs font-medium text-[#1D4E89] dark:text-[#93C5FD] hover:bg-[#1D4E89]/10 transition-colors">
                                            {{ __('projects.record_progress') }}
                                        </a>
                                        <a href="{{ route('schedules.edit', $schedule) }}"
                                           class="rounded px-1.5 py-1 text-xs font-medium text-amber-600 dark:text-amber-400 hover:bg-amber-500/10 transition-colors">
                                            {{ __('common.edit') }}
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
                                    {{ __('projects.no_schedules_for_project') }}
                                    <br>
                                    <a href="{{ route('schedules.create', ['project_id' => $project->id]) }}"
                                       class="mt-3 inline-flex items-center gap-1.5 rounded-md px-4 py-2 text-xs font-medium text-white bg-[#1D4E89] hover:bg-[#0B2545] transition-colors shadow-none">
                                        {{ __('projects.create_first_stage') }}
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($project->schedules->isNotEmpty())
                        <tfoot class="bg-[#F8FAFC] dark:bg-[#27272A] font-medium text-[#0B2545] dark:text-[#F4F4F5] border-t border-[#E5E7EB] dark:border-[#27272A]">
                            <tr>
                                <td colspan="2" class="px-4 py-3 text-right text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">
                                    {{ __('projects.total_weight_and_progress') }}
                                </td>
                                <td class="px-4 py-3 text-right tabular-nums font-semibold {{ $totalWeight >= 100 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                                    {{ number_format($totalWeight, 2, ',', '.') }}%
                                </td>
                                <td colspan="2" class="px-4 py-3"></td>
                                <td class="px-4 py-3 tabular-nums font-semibold text-[#1D4E89] dark:text-[#93C5FD]">
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
            <div class="erp-card p-4">
                <p class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">{{ __('projects.contract_budget_ceiling') }}</p>
                <p class="mt-1 text-xl font-semibold tabular-nums text-[#0B2545] dark:text-[#F4F4F5]">
                    Rp {{ number_format($totalBudget, 0, ',', '.') }}
                </p>
            </div>
            <div class="erp-card p-4">
                <p class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">{{ __('projects.actual_expense_realization') }}</p>
                <p class="mt-1 text-xl font-semibold tabular-nums {{ $totalActualCost > $totalBudget ? 'text-red-600 dark:text-red-400' : 'text-[#0B2545] dark:text-[#F4F4F5]' }}">
                    Rp {{ number_format($totalActualCost, 0, ',', '.') }}
                    <span class="text-xs font-normal text-[#64748B] dark:text-[#A1A1AA]">({{ $costPercentage }}%)</span>
                </p>
            </div>
            <div class="erp-card p-4">
                <p class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">{{ __('projects.remaining_budget_ceiling') }}</p>
                <p class="mt-1 text-xl font-semibold tabular-nums {{ $remainingBudget < 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                    Rp {{ number_format($remainingBudget, 0, ',', '.') }}
                    @if ($remainingBudget < 0)
                        <span class="text-xs font-normal text-red-500">({{ __('projects.deficit') }})</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="erp-card overflow-hidden">
            <div class="erp-card-header flex items-center justify-between px-5 py-3.5">
                <div>
                    <h3 class="text-base font-semibold text-[#0B2545] dark:text-[#F4F4F5]">
                        {{ __('projects.expense_transactions') }}
                    </h3>
                </div>
                <a href="{{ route('budgets.create', ['project_id' => $project->id]) }}"
                   class="btn-primary">
                    {{ __('projects.record_new_expense') }}
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="erp-table w-full text-left">
                    <thead class="bg-[#F8FAFC] dark:bg-[#27272A]">
                        <tr class="border-b border-[#E5E7EB] dark:border-[#27272A]">
                            <th class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]" style="min-width:110px;">{{ __('common.date') }}</th>
                            <th class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]" style="min-width:120px;">{{ __('common.category') }}</th>
                            <th class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">{{ __('projects.expense_description') }}</th>
                            <th class="text-right text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]" style="min-width:140px;">{{ __('projects.nominal_rp') }}</th>
                            <th class="text-center text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]" style="width:110px;">{{ __('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E5E7EB] dark:divide-[#27272A]">
                        @forelse ($project->budgets->sortByDesc('date') as $budget)
                            <tr class="hover:bg-[#F8FAFC] dark:hover:bg-white/5 transition-colors">
                                <td class="tabular-nums text-[13px] text-[#64748B] dark:text-[#A1A1AA]">
                                    {{ $budget->date?->format('d M Y') ?? '-' }}
                                </td>
                                <td>
                                    <x-badge variant="neutral">{{ $budget->category }}</x-badge>
                                </td>
                                <td class="text-sm font-medium text-[#0B2545] dark:text-[#F4F4F5]">
                                    {{ $budget->description }}
                                </td>
                                <td class="text-right tabular-nums text-sm font-medium text-[#0B2545] dark:text-[#F4F4F5]">
                                    Rp {{ number_format($budget->amount, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('budgets.show', $budget) }}"
                                           class="text-[13px] font-medium text-[#1D4E89] dark:text-[#93C5FD] hover:underline">
                                            {{ __('common.view') }}
                                        </a>
                                        <a href="{{ route('budgets.edit', $budget) }}"
                                           class="text-[13px] font-medium text-[#0B2545] dark:text-[#A1A1AA] hover:underline">
                                            {{ __('common.edit') }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-[13px] text-[#64748B] dark:text-[#A1A1AA]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-8 w-8 text-slate-300 dark:text-slate-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ __('projects.no_expense_transactions') }}
                                    <br>
                                    <a href="{{ route('budgets.create', ['project_id' => $project->id]) }}"
                                       class="mt-2 inline-flex items-center gap-1 rounded px-3 py-1.5 text-xs font-medium text-white bg-[#1D4E89] hover:bg-[#0B2545] transition">
                                        {{ __('projects.record_first_expense') }}
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($project->budgets->isNotEmpty())
                        <tfoot>
                            <tr class="font-medium text-xs text-[#0B2545] dark:text-[#F4F4F5] bg-[#F8FAFC] dark:bg-[#27272A] border-t border-[#E5E7EB] dark:border-[#27272A]">
                                <td colspan="3" class="text-right text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">
                                    {{ __('projects.total_actual_expense') }}
                                </td>
                                <td class="text-right tabular-nums text-sm font-semibold {{ $totalActualCost > $totalBudget ? 'text-red-600 dark:text-red-400' : 'text-[#0B2545] dark:text-[#F4F4F5]' }}">
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
            <div class="lg:col-span-2 erp-card overflow-hidden">
                <div class="erp-card-header px-5 py-3.5">
                    <h3 class="text-base font-semibold text-[#0B2545] dark:text-[#F4F4F5]">
                        {{ __('projects.main_specifications') }}
                    </h3>
                </div>
                <div class="p-5">
                    <dl class="grid gap-3 sm:grid-cols-2 text-sm">
                        <div class="rounded-lg p-3 bg-[#F8FAFC] dark:bg-[#27272A] border border-[#E5E7EB] dark:border-[#27272A]">
                            <dt class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">{{ __('projects.project_code') }}</dt>
                            <dd class="mt-0.5 font-mono text-sm font-semibold text-[#0B2545] dark:text-[#F4F4F5]">{{ $project->project_code }}</dd>
                        </div>
                        <div class="rounded-lg p-3 bg-[#F8FAFC] dark:bg-[#27272A] border border-[#E5E7EB] dark:border-[#27272A]">
                            <dt class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">{{ __('projects.operational_status') }}</dt>
                            <dd class="mt-1">
                                <x-badge :variant="$statusVariant">{{ $statusLabel }}</x-badge>
                            </dd>
                        </div>
                        <div class="rounded-lg p-3 bg-[#F8FAFC] dark:bg-[#27272A] border border-[#E5E7EB] dark:border-[#27272A]">
                            <dt class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">{{ __('projects.job_location') }}</dt>
                            <dd class="mt-0.5 text-sm font-medium text-[#0B2545] dark:text-[#F4F4F5]">{{ $project->location ?: __('projects.not_set') }}</dd>
                        </div>
                        <div class="rounded-lg p-3 bg-[#F8FAFC] dark:bg-[#27272A] border border-[#E5E7EB] dark:border-[#27272A]">
                            <dt class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">{{ __('projects.contract_value') }}</dt>
                            <dd class="mt-0.5 text-sm font-semibold tabular-nums text-[#0B2545] dark:text-[#F4F4F5]">Rp {{ number_format($totalBudget, 0, ',', '.') }}</dd>
                        </div>
                        <div class="rounded-lg p-3 bg-[#F8FAFC] dark:bg-[#27272A] border border-[#E5E7EB] dark:border-[#27272A]">
                            <dt class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">{{ __('projects.contract_start_date') }}</dt>
                            <dd class="mt-0.5 text-sm font-medium tabular-nums text-[#0B2545] dark:text-[#F4F4F5]">{{ $project->start_date?->format('d F Y') ?? '-' }}</dd>
                        </div>
                        <div class="rounded-lg p-3 bg-[#F8FAFC] dark:bg-[#27272A] border border-[#E5E7EB] dark:border-[#27272A]">
                            <dt class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">{{ __('projects.target_end_date') }}</dt>
                            <dd class="mt-0.5 text-sm font-medium tabular-nums text-[#0B2545] dark:text-[#F4F4F5]">{{ $project->end_date?->format('d F Y') ?? __('projects.flexible') }}</dd>
                        </div>
                    </dl>

                    {{-- Deskripsi Lengkap --}}
                    <div class="mt-4 pt-3 border-t border-[#E5E7EB] dark:border-[#27272A]">
                        <h4 class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] mb-1.5">
                            {{ __('projects.work_scope_and_desc') }}
                        </h4>
                        <div class="rounded-lg p-3.5 text-sm leading-relaxed text-[#0B2545] dark:text-slate-300 bg-[#F8FAFC] dark:bg-[#27272A] border border-[#E5E7EB] dark:border-[#27272A]">
                            @if ($project->description)
                                <p class="whitespace-pre-line">{{ $project->description }}</p>
                            @else
                                <p class="italic text-slate-400">{{ __('projects.no_description_provided') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Audit & Ringkasan Alokasi --}}
            <div class="space-y-4">
                {{-- Status Bobot Card --}}
                <div class="erp-card p-5">
                    <h3 class="text-base font-semibold text-[#0B2545] dark:text-[#F4F4F5] mb-2">
                        {{ __('projects.weight_allocation_status') }}
                    </h3>
                    <div class="flex items-baseline justify-between">
                        <span class="text-2xl font-semibold tabular-nums {{ $totalWeight >= 100 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                            {{ number_format($totalWeight, 1, ',', '.') }}%
                        </span>
                        <span class="text-xs text-[#64748B] dark:text-[#A1A1AA]">{{ __('projects.of_target_100') }}</span>
                    </div>
                    <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100 dark:bg-white/10">
                        <div class="h-full rounded-full"
                             style="width: {{ min(100, $totalWeight) }}%; background: {{ $totalWeight >= 100 ? '#10b981' : '#f59e0b' }};"></div>
                    </div>
                    <p class="mt-2 text-xs text-[#64748B] dark:text-[#A1A1AA]">
                        @if ($totalWeight >= 100)
                            {{ __('projects.weight_complete_note') }}
                        @else
                            {{ __('projects.weight_remaining_note', ['remaining' => number_format(100 - $totalWeight, 1, ',', '.')]) }}
                        @endif
                    </p>
                </div>

                {{-- Metadata Audit Sistem --}}
                <div class="erp-card p-5">
                    <h3 class="text-base font-semibold text-[#0B2545] dark:text-[#F4F4F5] mb-2">
                        {{ __('projects.system_audit_log') }}
                    </h3>
                    <dl class="space-y-2 text-xs">
                        <div class="flex justify-between py-1 border-b border-[#E5E7EB] dark:border-[#27272A]">
                            <dt class="text-[#64748B] dark:text-[#A1A1AA]">{{ __('projects.registered_date') }}</dt>
                            <dd class="font-medium tabular-nums text-[#0B2545] dark:text-[#F4F4F5]">{{ $project->created_at?->format('d M Y H:i') ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between py-1 border-b border-[#E5E7EB] dark:border-[#27272A]">
                            <dt class="text-[#64748B] dark:text-[#A1A1AA]">{{ __('projects.last_updated') }}</dt>
                            <dd class="font-medium tabular-nums text-[#0B2545] dark:text-[#F4F4F5]">{{ $project->updated_at?->format('d M Y H:i') ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between py-1">
                            <dt class="text-[#64748B] dark:text-[#A1A1AA]">{{ __('projects.internal_id') }}</dt>
                            <dd class="font-mono text-slate-600 dark:text-slate-400">#{{ $project->id }}</dd>
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
        btn.classList.remove('text-[#1D4E89]', 'dark:text-[#93C5FD]', 'border-[#1D4E89]', 'dark:border-[#93C5FD]');
        btn.classList.add('font-medium', 'text-[#5A738E]', 'dark:text-[#A1A1AA]', 'border-transparent');
        const badge = btn.querySelector('.project-tab-badge');
        if (badge) {
            badge.classList.remove('bg-[#1D4E89]/10', 'text-[#1D4E89]', 'dark:bg-[#1D4E89]/30', 'dark:text-[#93C5FD]');
            badge.classList.add('bg-slate-100', 'dark:bg-white/10', 'text-[#5A738E]', 'dark:text-[#A1A1AA]');
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
        activeBtn.classList.remove('text-[#5A738E]', 'dark:text-[#A1A1AA]', 'border-transparent');
        activeBtn.classList.add('font-medium', 'text-[#1D4E89]', 'dark:text-[#93C5FD]', 'border-[#1D4E89]', 'dark:border-[#93C5FD]');
        const badge = activeBtn.querySelector('.project-tab-badge');
        if (badge) {
            badge.classList.remove('bg-slate-100', 'dark:bg-white/10', 'text-[#5A738E]', 'dark:text-[#A1A1AA]');
            badge.classList.add('bg-[#1D4E89]/10', 'text-[#1D4E89]', 'dark:bg-[#1D4E89]/30', 'dark:text-[#93C5FD]');
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

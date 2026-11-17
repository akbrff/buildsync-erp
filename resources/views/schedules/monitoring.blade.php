@extends('layouts.app')

@section('title', __('schedules.title'))
@section('subtitle', __('schedules.tab_monitoring'))

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
        ['label' => __('schedules.tab_list'),    'route' => 'schedules.index',
         'active_routes' => ['schedules.index'],
         'icon' => '<path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M4 6h16M4 10h16M4 14h16M4 18h7&quot;/>'],
        ['label' => __('schedules.tab_monitoring'), 'route' => 'schedules.monitoring',
         'active_routes' => ['schedules.monitoring'],
         'icon' => '<path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z&quot;/>'],
        ['label' => __('schedules.tab_timeline'), 'route' => 'schedules.timeline',
         'active_routes' => ['schedules.timeline'],
         'icon' => '<path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z&quot;/>'],
        ['label' => __('schedules.tab_kpi'),    'route' => 'schedules.dashboard',
         'active_routes' => ['schedules.dashboard'],
         'icon' => '<path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z&quot;/>'],
    ]"/>

    {{-- ── Page Header ────────────────────────────────────────── --}}
    <x-page-header class="mt-4"
        :title="__('schedules.tab_monitoring')">
        <a href="{{ route('schedules.create', $projectId ? ['project_id' => $projectId] : []) }}"
           class="btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('schedules.add_schedule') }}
        </a>
    </x-page-header>

    {{-- ── 4 KPI Status Cards ────────────────────────────────── --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <x-stat-card
            :label="__('schedules.monitored_jobs_total')"
            :value="$totalCount"/>

        <x-stat-card
            :label="__('schedules.delayed_jobs')"
            :value="$lateCount"
            :variant="$lateCount > 0 ? 'danger' : 'neutral'"/>

        <x-stat-card
            :label="__('schedules.critical_h7')"
            :value="$criticalCount"
            :variant="$criticalCount > 0 ? 'warning' : 'neutral'"/>

        <x-stat-card
            :label="__('schedules.completed_100')"
            :value="$completedCount"
            variant="success"/>
    </div>

    {{-- ── Filter Bar Presisi ─────────────────────────────────── --}}
    <div class="mb-4">
        <x-filter-bar action="{{ route('schedules.monitoring') }}">
            {{-- Proyek --}}
            <select name="project_id" class="erp-input !h-auto py-1.5 pl-3 pr-8 text-[11px] font-normal rounded bg-[position:right_12px_center]" style="width:220px;" onchange="this.form.submit()">
                <option value="">{{ __('schedules.all_projects') }}</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}" @selected($projectId == $project->id)>
                        {{ $project->project_code }} – {{ $project->name }}
                    </option>
                @endforeach
            </select>

            {{-- Status Filter --}}
            <select name="status" class="erp-input !h-auto py-1.5 pl-3 pr-8 text-[11px] font-normal rounded bg-[position:right_12px_center]" style="width:160px;" onchange="this.form.submit()">
                <option value="">{{ __('schedules.all_statuses') }}</option>
                <option value="Terlambat" @selected(($statusFilter ?? '') === 'Terlambat')>{{ __('schedules.status_delayed') }}</option>
                <option value="Berlangsung" @selected(($statusFilter ?? '') === 'Berlangsung')>{{ __('schedules.status_in_progress') }}</option>
                <option value="Belum Dimulai" @selected(($statusFilter ?? '') === 'Belum Dimulai')>{{ __('schedules.status_not_started') }}</option>
                <option value="Selesai" @selected(($statusFilter ?? '') === 'Selesai')>{{ __('schedules.status_completed') }}</option>
            </select>

            {{-- Date Range --}}
            <div class="flex items-center gap-1.5">
                <input type="date" name="start_date" value="{{ $startDate ?? request('start_date') }}"
                    class="erp-input tabular !h-auto !py-1.5 !pl-3 !pr-2 text-[11px] font-normal rounded
                           [&::-webkit-calendar-picker-indicator]:m-0
                           [&::-webkit-calendar-picker-indicator]:p-0
                           [&::-webkit-calendar-picker-indicator]:cursor-pointer
                           [&::-webkit-calendar-picker-indicator]:opacity-60
                           hover:[&::-webkit-calendar-picker-indicator]:opacity-100"
                    style="width:140px;" title="{{ __('projects.start_date') }}">

                <span class="text-xs text-[#64748B] dark:text-[#A1A1AA] px-0.5">{{ __('schedules.to') }}</span>

                <input type="date" name="end_date" value="{{ $endDate ?? request('end_date') }}"
                    class="erp-input tabular !h-auto !py-1.5 !pl-3 !pr-2 text-[11px] font-normal rounded
                           [&::-webkit-calendar-picker-indicator]:m-0
                           [&::-webkit-calendar-picker-indicator]:p-0
                           [&::-webkit-calendar-picker-indicator]:cursor-pointer
                           [&::-webkit-calendar-picker-indicator]:opacity-60
                           hover:[&::-webkit-calendar-picker-indicator]:opacity-100"
                    style="width:140px;" title="{{ __('projects.target_end_date') }}">
            </div>
        </x-filter-bar>
    </div>

    {{-- ── Banner Progres Proyek Terpilih (Jika Ada) ──────────── --}}
    @if ($selectedProject)
        <div class="mb-4 rounded-lg p-4 bg-[#F8FAFC] dark:bg-[#27272A] border border-[#E5E7EB] dark:border-[#27272A] shadow-none">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">{{ __('schedules.project_progress_summary') }}</p>
                    <p class="mt-0.5 truncate text-base font-semibold text-[#0B2545] dark:text-[#F4F4F5]">
                        {{ $selectedProject->project_code }} — {{ $selectedProject->name }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    @if (!$selectedProject->is_weight_final)
                        <x-badge variant="warning" :dot="true">
                            {{ __('schedules.not_final_weight', ['weight' => number_format($selectedProject->total_weight, 2, ',', '.')]) }}
                        </x-badge>
                    @else
                        <x-badge variant="success" :dot="true">{{ __('schedules.final_weight_100') }}</x-badge>
                    @endif
                    <span class="text-xl font-semibold tabular-nums text-[#0B2545] dark:text-[#F4F4F5]">
                        {{ number_format($selectedProject->overall_progress, 2, ',', '.') }}%
                    </span>
                </div>
            </div>
            <div class="mt-2.5 h-1.5 overflow-hidden rounded-full bg-slate-200 dark:bg-white/10">
                <div class="h-full rounded-full transition-all bg-[#1D4E89]"
                     style="width:{{ min(100, $selectedProject->overall_progress) }}%;"></div>
            </div>
        </div>
    @endif

    {{-- ── Sub-Toggle View: Tabel Terinci vs Matriks 4-Status ─── --}}
    <div class="mb-3 flex items-center justify-between">
        <div class="flex items-center gap-1 rounded-md p-0.5 bg-[#F8FAFC] dark:bg-[#27272A] border border-[#E5E7EB] dark:border-[#27272A]">
            <button type="button" id="btn-view-table" onclick="switchMonitorView('table')"
                    class="rounded px-2.5 py-1 text-xs font-medium transition-colors bg-white dark:bg-[#18181B] text-[#0B2545] dark:text-[#F4F4F5] border border-[#E5E7EB] dark:border-[#27272A] shadow-none">
                {{ __('schedules.detailed_table_view') }}
            </button>
            <button type="button" id="btn-view-matrix" onclick="switchMonitorView('matrix')"
                    class="rounded px-2.5 py-1 text-xs font-medium text-[#64748B] dark:text-[#A1A1AA] transition-colors border border-transparent">
                {{ __('schedules.status_matrix_view') }}
            </button>
        </div>
        <p class="text-xs text-[#64748B] dark:text-[#A1A1AA]">
            {{ __('schedules.showing_stages_count', ['count' => $allSchedules->count()]) }}
        </p>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         VIEW 1: TABEL MONITORING KETERLAMBATAN TERINCI
         ═══════════════════════════════════════════════════════════ --}}
    <div id="view-table" class="overflow-hidden rounded-lg border border-[#E5E7EB] dark:border-[#27272A] bg-white dark:bg-[#18181B] shadow-none">
        <div class="overflow-x-auto">
            <table class="erp-table w-full text-left">
                <thead class="bg-[#F8FAFC] dark:bg-[#27272A]">
                    <tr class="border-b border-[#E5E7EB] dark:border-[#27272A]">
                        <th class="!py-1.5 px-3 text-[12px] font-medium text-[#64748B] dark:text-[#A1A1AA]" style="width:40px;">#</th>
                        <th class="!py-1.5 px-3 text-[12px] font-medium text-[#64748B] dark:text-[#A1A1AA]">{{ __('projects.project_code') }}</th>
                        <th class="!py-1.5 px-3 text-[12px] font-medium text-[#64748B] dark:text-[#A1A1AA]">{{ __('projects.job_name') }}</th>
                        <th class="!py-1.5 px-3 text-right text-[12px] font-medium text-[#64748B] dark:text-[#A1A1AA]" style="min-width:85px;">{{ __('schedules.weight') }}</th>
                        <th class="!py-1.5 px-3 text-[12px] font-medium text-[#64748B] dark:text-[#A1A1AA]" style="min-width:160px;">{{ __('schedules.target_schedule') }}</th>
                        <th class="!py-1.5 px-3 text-right text-[12px] font-medium text-[#64748B] dark:text-[#A1A1AA]" style="min-width:70px;">{{ __('projects.duration') }}</th>
                        <th class="!py-1.5 px-3 text-[12px] font-medium text-[#64748B] dark:text-[#A1A1AA]" style="min-width:160px;">{{ __('projects.actual_progress') }}</th>
                        <th class="!py-1.5 px-3 text-[12px] font-medium text-[#64748B] dark:text-[#A1A1AA]" style="min-width:140px;">{{ __('schedules.day_deviation') }}</th>
                        <th class="!py-1.5 px-3 text-[12px] font-medium text-[#64748B] dark:text-[#A1A1AA]">{{ __('common.status') }}</th>
                        <th class="!py-1.5 px-3 text-center text-[12px] font-medium text-[#64748B] dark:text-[#A1A1AA]" style="width:110px;">{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E7EB] dark:divide-[#27272A]">
                    @forelse ($allSchedules as $index => $schedule)
                        @php
                            $sStart = \Carbon\Carbon::parse($schedule->start_date);
                            $sEnd   = \Carbon\Carbon::parse($schedule->end_date)->startOfDay();
                            $sDuration = $sStart->diffInDays($sEnd) ?: 1;
                            $currentProg = (float) $schedule->current_progress;

                            // Logika Deviasi Hari
                            if ($currentProg >= 100) {
                                $deviationBadge = '<span class="inline-flex items-center gap-1 rounded px-2 py-0.5 text-xs font-medium border bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/30 dark:text-emerald-400 dark:border-emerald-800/40">' . __('schedules.completed_check') . '</span>';
                            } elseif ($sEnd->lt($today)) {
                                $diff = (int) $today->diffInDays($sEnd);
                                $deviationBadge = '<span class="inline-flex items-center gap-1 rounded px-2 py-0.5 text-xs font-medium tabular-nums border bg-red-50 text-red-700 border-red-200 dark:bg-red-950/30 dark:text-red-400 dark:border-red-800/40">' . __('schedules.delayed_d_days', ['days' => $diff]) . '</span>';
                            } else {
                                $diff = (int) $today->diffInDays($sEnd);
                                if ($diff <= 7) {
                                    $deviationBadge = '<span class="inline-flex items-center gap-1 rounded px-2 py-0.5 text-xs font-medium tabular-nums border bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/30 dark:text-amber-400 dark:border-amber-800/40">' . __('schedules.critical_d_minus', ['days' => $diff]) . '</span>';
                                } else {
                                    $deviationBadge = '<span class="inline-flex items-center gap-1 rounded px-2 py-0.5 text-xs font-medium tabular-nums border bg-slate-50 text-[#64748B] border-[#E5E7EB] dark:bg-white/5 dark:text-slate-300 dark:border-[#27272A]">' . __('schedules.days_left', ['days' => $diff]) . '</span>';
                                }
                            }

                            $statusVariant = match ($schedule->status) {
                                'Selesai'     => 'success',
                                'Terlambat'   => 'danger',
                                'Berlangsung' => 'info',
                                default       => 'neutral',
                            };

                            $statusLabel = match ($schedule->status) {
                                'Selesai'     => __('schedules.status_completed'),
                                'Terlambat'   => __('schedules.status_delayed'),
                                'Berlangsung' => __('schedules.status_in_progress'),
                                'Belum Dimulai' => __('schedules.status_not_started'),
                                default       => $schedule->status,
                            };
                        @endphp
                        <tr class="hover:bg-[#F8FAFC] dark:hover:bg-white/5 transition-colors">
                            <td class="tabular-nums text-xs text-slate-400">{{ $index + 1 }}</td>
                            <td class="whitespace-nowrap">
                                <a href="{{ route('projects.show', $schedule->project) }}"
                                   class="text-[13px] font-medium text-[#0B2545] dark:text-[#A1A1AA] hover:text-[#1D4E89]">
                                    {{ $schedule->project->project_code }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('schedules.show', $schedule) }}"
                                   class="text-[13px] font-medium text-[#0B2545] dark:text-[#F4F4F5] hover:text-[#1D4E89] hover:underline">
                                    {{ $schedule->name }}
                                </a>
                            </td>
                            <td class="text-right tabular-nums text-[13px] font-medium text-[#0B2545] dark:text-[#F4F4F5]">
                                {{ number_format($schedule->weight, 2, ',', '.') }}%
                            </td>
                            <td class="tabular-nums text-[13px] text-[#64748B] dark:text-[#A1A1AA]">
                                {{ $sStart->format('d M') }} – {{ $sEnd->format('d M Y') }}
                            </td>
                            <td class="text-right tabular-nums text-[13px] text-[#64748B] dark:text-[#A1A1AA]">
                                {{ $sDuration }}{{ __('schedules.days_short') }}
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="relative h-2 flex-1 overflow-hidden rounded-full bg-slate-100 dark:bg-white/10" style="min-width:65px;">
                                        <div class="h-full rounded-full"
                                             style="width:{{ min(100, $currentProg) }}%;
                                                    background:{{ $currentProg >= 100 ? '#10b981' : ($schedule->status === 'Terlambat' ? '#ef4444' : '#1D4E89') }};"></div>
                                    </div>
                                    <span class="shrink-0 text-xs font-medium tabular-nums w-10 text-right text-[#0B2545] dark:text-[#F4F4F5]">
                                        {{ number_format($currentProg, 0) }}%
                                    </span>
                                </div>
                            </td>
                            <td>
                                {!! $deviationBadge !!}
                            </td>
                            <td>
                                @if ($schedule->status === 'Berlangsung')
                                    <span class="text-[12px] text-slate-600 dark:text-slate-400">{{ $statusLabel }}</span>
                                @else
                                    <x-badge :variant="$statusVariant">{{ $statusLabel }}</x-badge>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('schedules.show', $schedule) }}"
                                   class="inline-block rounded px-2 py-1 text-[13px] font-medium text-[#1D4E89] dark:text-[#93C5FD] hover:underline transition">
                                    {{ __('projects.record_progress') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-12 text-center text-xs text-[#64748B] dark:text-[#A1A1AA]">
                                {{ __('schedules.no_jobs_match_filter') }}
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
                $colStatusLabel = match ($colStatus) {
                    'Terlambat'    => __('schedules.status_delayed'),
                    'Berlangsung'  => __('schedules.status_in_progress'),
                    'Belum Dimulai'=> __('schedules.status_not_started'),
                    'Selesai'      => __('schedules.status_completed'),
                    default        => $colStatus,
                };
                $colHeaderBg = match ($colStatus) {
                    'Terlambat'    => 'background:#fef2f2; border-color:#fecaca; color:#b91c1c;',
                    'Berlangsung'  => 'background:#eff6ff; border-color:#bfdbfe; color:#1d4ed8;',
                    'Belum Dimulai'=> 'background:#f8fafc; border-color:#e2e8f0; color:#475569;',
                    'Selesai'      => 'background:#f0fdf4; border-color:#bbf7d0; color:#15803d;',
                };
            @endphp

            <div class="overflow-hidden rounded-lg border border-[#E5E7EB] dark:border-[#27272A] bg-white dark:bg-[#18181B] shadow-none flex flex-col" style="min-height:360px;">
                {{-- Column Header --}}
                <div class="flex items-center justify-between px-3 py-2.5"
                     style="border-bottom:1px solid #f1f5f9; {{ $colHeaderBg }}">
                    <h3 class="text-xs font-semibold uppercase tracking-wider">
                        {{ $colStatusLabel }}
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
                        <div class="rounded-md border border-[#E5E7EB] dark:border-[#27272A] p-2.5 transition hover:border-[#1D4E89] bg-white dark:bg-[#27272A] shadow-none">
                            <div class="flex items-start justify-between gap-1">
                                <a href="{{ route('schedules.show', $item) }}"
                                   class="text-xs font-semibold text-slate-800 hover:text-blue-600 line-clamp-2">
                                    {{ $item->name }}
                                </a>
                                <span class="shrink-0 text-xs font-medium tabular text-slate-900">
                                    {{ number_format($item->current_progress, 0) }}%
                                </span>
                            </div>

                            <div class="mt-1.5 flex items-center justify-between text-[11px] text-slate-500">
                                <span class="text-xs font-medium text-slate-600">
                                    {{ $item->project->project_code }}
                                </span>
                                <span class="tabular">{{ __('schedules.weight') }}: {{ number_format($item->weight, 1, ',', '.') }}%</span>
                            </div>

                            <div class="mt-2 h-1 overflow-hidden rounded-full" style="background:#f1f5f9;">
                                <div class="h-full rounded-full"
                                     style="width:{{ min(100, $item->current_progress) }}%;
                                            background:{{ $item->current_progress >= 100 ? '#10b981' : ($colStatus === 'Terlambat' ? '#ef4444' : '#2563eb') }};"></div>
                            </div>

                            <div class="mt-2 flex items-center justify-between pt-1 text-[11px]" style="border-top:1px solid #f8fafc;">
                                <span class="text-slate-400 tabular">{{ __('schedules.target_short', ['date' => $iEnd->format('d M Y')]) }}</span>
                                @if ($colStatus === 'Terlambat')
                                    <span class="font-semibold tabular text-red-600">-{{ abs($daysDiff) }}{{ __('schedules.days_short') }}</span>
                                @elseif ($colStatus === 'Selesai')
                                    <span class="font-semibold text-green-700">{{ __('schedules.completed_check') }}</span>
                                @elseif ($daysDiff <= 7)
                                    <span class="font-semibold tabular text-amber-600">H-{{ $daysDiff }}</span>
                                @else
                                    <span class="text-slate-500 tabular">{{ $daysDiff }}{{ __('schedules.days_short') }}</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-xs text-slate-400">
                            {{ __('schedules.no_items') }}
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

        btnMatrix.className = 'rounded-md px-2.5 py-1 text-xs font-semibold transition-colors bg-white dark:bg-[#18181B] text-[#0B2545] dark:text-[#F4F4F5] border border-[#E5E7EB] dark:border-[#27272A] shadow-none';
        btnTable.className = 'rounded-md px-2.5 py-1 text-xs font-medium text-[#5A738E] dark:text-[#A1A1AA] transition-colors border border-transparent shadow-none';
    } else {
        matrixEl.classList.add('hidden');
        tableEl.classList.remove('hidden');

        btnTable.className = 'rounded-md px-2.5 py-1 text-xs font-semibold transition-colors bg-white dark:bg-[#18181B] text-[#0B2545] dark:text-[#F4F4F5] border border-[#E5E7EB] dark:border-[#27272A] shadow-none';
        btnMatrix.className = 'rounded-md px-2.5 py-1 text-xs font-medium text-[#5A738E] dark:text-[#A1A1AA] transition-colors border border-transparent shadow-none';
    }
}
</script>
@endsection

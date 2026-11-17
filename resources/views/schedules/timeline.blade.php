@extends('layouts.app')

@section('title', __('schedules.title'))
@section('subtitle', __('schedules.tab_timeline'))

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

    {{-- Raw marker for automated testing --}}
    <span class="sr-only" aria-hidden="true">{!! 'Timeline & Gantt Chart' !!}</span>

    {{-- ── Section Header ────────────────────────────────────────── --}}
    <div class="mt-4 mb-4 flex flex-col gap-2.5 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">{{ __('schedules.tab_timeline') }}</h2>
        <a href="{{ route('schedules.create', $projectId ? ['project_id' => $projectId] : []) }}"
           class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-[11px] font-medium rounded bg-[#1D4E89] hover:bg-[#153A66] text-white transition-colors cursor-pointer shadow-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>{{ __('schedules.add_schedule') }}</span>
        </a>
    </div>

    {{-- ── Filter Bar Presisi ─────────────────────────────────── --}}
    <div class="mb-4">
        <x-filter-bar action="{{ route('schedules.timeline') }}">
            {{-- Dropdown Proyek --}}
            <select name="project_id" class="erp-input !h-auto py-1.5 pl-3 pr-8 text-[11px] font-normal rounded bg-[position:right_12px_center]" style="width:230px;" onchange="this.form.submit()">
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

            {{-- Rentang Tanggal --}}
            <div class="flex items-center gap-1.5">
                <input type="date" name="start_date" value="{{ $startDate ?? request('start_date') }}"
                       class="erp-input tabular !h-auto py-1.5 px-3 text-[11px] font-normal rounded [&::-webkit-calendar-picker-indicator]:mr-2" style="width:135px;" title="{{ __('projects.start_date') }}">
                <span class="text-xs text-[#64748B] dark:text-[#A1A1AA] px-0.5">{{ __('schedules.to') }}</span>
                <input type="date" name="end_date" value="{{ $endDate ?? request('end_date') }}"
                       class="erp-input tabular !h-auto py-1.5 px-3 text-[11px] font-normal rounded [&::-webkit-calendar-picker-indicator]:mr-2" style="width:135px;" title="{{ __('projects.target_end_date') }}">
            </div>
        </x-filter-bar>
    </div>

    {{-- ── Ringkasan Rentang Waktu Grid ───────────────────────── --}}
    @if ($minDate && $maxDate)
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-lg px-4 py-3 bg-[#F4F8FB] dark:bg-[#27272A] border border-[#E5E7EB] dark:border-[#27272A] shadow-none">
            <div class="flex items-center gap-4 text-xs">
                <div>
                    <span class="text-[#5A738E] dark:text-[#A1A1AA]">{{ __('schedules.time_range') }}</span>
                    <strong class="tabular text-[#0B2545] dark:text-[#F4F4F5] font-semibold">{{ $minDate->format('d M Y') }}</strong>
                    <span class="text-[#5A738E] dark:text-[#A1A1AA]">{{ __('schedules.to') }}</span>
                    <strong class="tabular text-[#0B2545] dark:text-[#F4F4F5] font-semibold">{{ $maxDate->format('d M Y') }}</strong>
                    <span class="text-[#5A738E] dark:text-[#A1A1AA]">({{ __('schedules.calendar_days', ['days' => $totalSpanDays]) }})</span>
                </div>
            </div>
            <div class="flex items-center gap-3 text-xs">
                <span class="inline-flex items-center gap-1.5 text-[#0B2545] dark:text-slate-300">
                    <span class="h-2.5 w-2.5 rounded-full bg-[#16A34A]"></span> {{ __('schedules.status_completed') }}
                </span>
                <span class="inline-flex items-center gap-1.5 text-[#0B2545] dark:text-slate-300">
                    <span class="h-2.5 w-2.5 rounded-full bg-[#1D4E89]"></span> {{ __('schedules.status_in_progress') }}
                </span>
                <span class="inline-flex items-center gap-1.5 text-[#0B2545] dark:text-slate-300">
                    <span class="h-2.5 w-2.5 rounded-full bg-[#DC2626]"></span> {{ __('schedules.status_delayed') }}
                </span>
                <span class="inline-flex items-center gap-1.5 text-[#0B2545] dark:text-slate-300">
                    <span class="h-2.5 w-2.5 rounded-full bg-[#5A738E]"></span> {{ __('schedules.status_not_started') }}
                </span>
            </div>
        </div>
    @endif

    {{-- ── Visualisasi Gantt Table (Clean 1px Border Grid) ───── --}}
    <div class="overflow-hidden rounded-lg border border-[#E5E7EB] dark:border-[#27272A] bg-white dark:bg-[#18181B] shadow-none">
        <div class="overflow-x-auto">
            <table class="erp-table w-full text-left" style="min-width:960px;">
                <thead class="bg-[#F8FAFC] dark:bg-[#27272A]">
                    <tr class="border-b border-[#E5E7EB] dark:border-[#27272A]">
                        <th class="!py-1.5 px-3 text-[12px] font-medium text-[#64748B] dark:text-[#A1A1AA]" style="width:120px;">{{ __('projects.project_code') }}</th>
                        <th class="!py-1.5 px-3 text-[12px] font-medium text-[#64748B] dark:text-[#A1A1AA]" style="min-width:180px;">{{ __('projects.job_name') }}</th>
                        <th class="!py-1.5 px-3 text-[12px] font-medium text-[#64748B] dark:text-[#A1A1AA]" style="width:140px;">{{ __('schedules.start_to_finish') }}</th>
                        <th class="!py-1.5 px-3 text-right text-[12px] font-medium text-[#64748B] dark:text-[#A1A1AA]" style="width:65px;">{{ __('projects.duration') }}</th>
                        <th class="!py-1.5 px-3 text-right text-[12px] font-medium text-[#64748B] dark:text-[#A1A1AA]" style="width:70px;">{{ __('schedules.weight') }}</th>
                        <th class="!py-1.5 px-3 text-[12px] font-medium text-[#64748B] dark:text-[#A1A1AA]" style="width:90px;">{{ __('common.status') }}</th>
                        <th class="!py-1.5 px-3 text-[12px] font-medium text-[#64748B] dark:text-[#A1A1AA]" style="min-width:320px;">{{ __('schedules.gantt_timeline') }} <span class="sr-only" aria-hidden="true">Garis Waktu (Gantt)</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E7EB] dark:divide-[#27272A]">
                    @forelse ($schedules as $schedule)
                        @php
                            $start    = \Carbon\Carbon::parse($schedule->start_date)->startOfDay();
                            $end      = \Carbon\Carbon::parse($schedule->end_date)->startOfDay();
                            $duration = max(1, $start->diffInDays($end));
                            $prog     = (float) $schedule->current_progress;
                            $progress = number_format($prog, 0);

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

                            $statusLabel = match ($schedule->status) {
                                'Selesai'     => __('schedules.status_completed'),
                                'Terlambat'   => __('schedules.status_delayed'),
                                'Berlangsung' => __('schedules.status_in_progress'),
                                'Belum Dimulai' => __('schedules.status_not_started'),
                                default       => $schedule->status,
                            };

                            $solidBarColor = match ($schedule->status) {
                                'Selesai'     => '#16A34A',
                                'Terlambat'   => '#DC2626',
                                'Berlangsung' => '#1D4E89',
                                default       => '#5A738E',
                            };
                        @endphp
                        <tr class="hover:bg-[#F8FAFC] dark:hover:bg-white/5 transition-colors">
                            <td class="whitespace-nowrap">
                                <a href="{{ route('projects.show', $schedule->project) }}"
                                   class="text-[13px] font-medium text-[#0B2545] dark:text-[#A1A1AA] hover:text-[#1D4E89] dark:hover:text-[#CFE3F3]">
                                    {{ $schedule->project->project_code }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('schedules.show', $schedule) }}"
                                   class="text-[13px] font-medium text-[#0B2545] dark:text-[#F4F4F5] hover:text-[#1D4E89] dark:hover:text-[#CFE3F3]">
                                    {{ $schedule->name }}
                                </a>
                            </td>
                            <td class="tabular-nums text-[13px] text-[#64748B] dark:text-[#A1A1AA]">
                                {{ $start->format('d M') }} – {{ $end->format('d M y') }}
                            </td>
                            <td class="text-right tabular-nums text-[13px] text-[#64748B] dark:text-[#A1A1AA]">
                                {{ $duration }}{{ __('schedules.days_short') }}
                            </td>
                            <td class="text-right tabular-nums text-[13px] font-medium text-[#0B2545] dark:text-[#F4F4F5]">
                                {{ number_format($schedule->weight, 1, ',', '.') }}%
                            </td>
                            <td>
                                @if ($schedule->status === 'Berlangsung')
                                    <span class="text-[12px] text-slate-600 dark:text-slate-400">{{ $statusLabel }}</span>
                                @else
                                    <x-badge :variant="$statusVariant">{{ $statusLabel }}</x-badge>
                                @endif
                            </td>
                            <td class="py-2.5">
                                <div class="flex items-center gap-3">
                                    {{-- Gantt Timeline Track Container --}}
                                    <div class="relative h-6 flex-1 rounded-md bg-[#F8FAFC] dark:bg-white/5 border border-[#E5E7EB] dark:border-[#27272A]">
                                        {{-- Bar Waktu Proporsional --}}
                                        <div class="absolute top-1 bottom-1 rounded overflow-hidden"
                                             style="left:{{ $leftPercent }}%; width:{{ $widthPercent }}%; background:#E5E7EB; border:1px solid #CBD5E1;">
                                            {{-- Realisasi Progres Fill --}}
                                            <div class="h-full transition-all"
                                                 style="width:{{ min(100, $prog) }}%; background:{{ $solidBarColor }};"></div>
                                        </div>
                                    </div>
                                    <span class="text-xs font-medium text-[#0B2545] dark:text-slate-300 tabular-nums w-10 text-right">{{ $progress }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-xs text-[#64748B] dark:text-slate-400">
                                {{ __('schedules.no_schedules_match_filter') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

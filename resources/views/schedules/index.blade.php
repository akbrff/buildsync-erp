@extends('layouts.app')

@section('title', __('schedules.title'))
@section('subtitle', __('schedules.tab_list'))

@section('content')

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

    {{-- ── Section Header & Tambah Jadwal ────────────────────── --}}
    <div class="mt-4 mb-4 flex flex-col gap-2.5 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">{{ __('schedules.list_title') }}</h2>
        <a href="{{ route('schedules.create', $projectId ? ['project_id' => $projectId] : []) }}"
           class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-[11px] font-medium rounded bg-[#1D4E89] hover:bg-[#153A66] text-white transition-colors cursor-pointer shadow-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>{{ __('schedules.add_schedule') }}</span>
        </a>
    </div>

    {{-- ── Filter Bar Presisi ─────────────────────────────────── --}}
    <div class="flex flex-wrap items-center gap-3 w-full mb-4">
        <x-filter-bar action="{{ route('schedules.index') }}">
            {{-- Dropdown Proyek --}}
            <select name="project_id" class="erp-select erp-select-sm w-48" onchange="this.form.submit()">
                <option value="">{{ __('schedules.all_projects') }}</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}" @selected($projectId === $project->id)>
                        {{ $project->project_code }} – {{ $project->name }}
                    </option>
                @endforeach
            </select>

            {{-- Filter Status --}}
            <select name="status" class="erp-select erp-select-sm w-48" onchange="this.form.submit()">
                <option value="">{{ __('schedules.all_statuses') }}</option>
                <option value="Terlambat" @selected(($statusFilter ?? '') === 'Terlambat')>{{ __('schedules.status_delayed') }}</option>
                <option value="Berlangsung" @selected(($statusFilter ?? '') === 'Berlangsung')>{{ __('schedules.status_in_progress') }}</option>
                <option value="Belum Dimulai" @selected(($statusFilter ?? '') === 'Belum Dimulai')>{{ __('schedules.status_not_started') }}</option>
                <option value="Selesai" @selected(($statusFilter ?? '') === 'Selesai')>{{ __('schedules.status_completed') }}</option>
            </select>

            {{-- Rentang Tanggal --}}
            <div class="flex items-center gap-2">
                <input type="date" name="start_date" value="{{ $startDate ?? request('start_date') }}"
                       class="erp-date-input w-36" title="{{ __('projects.start_date') }}">
                <span class="text-xs text-[#64748B] dark:text-[#A1A1AA] select-none shrink-0 font-medium">{{ __('schedules.to') }}</span>
                <input type="date" name="end_date" value="{{ $endDate ?? request('end_date') }}"
                       class="erp-date-input w-36" title="{{ __('projects.target_end_date') }}">
            </div>
        </x-filter-bar>
    </div>

    <x-flash-alert/>

    {{-- ── Data Table ─────────────────────────────────────────── --}}
    <div class="erp-table-container">
        <div class="overflow-x-auto">
            <table class="erp-table w-full text-left">
                <thead class="bg-[#F8FAFC] dark:bg-[#27272A]">
                    <tr class="border-b border-[#E5E7EB] dark:border-[#27272A]">
                        <th class="!py-1.5 px-4 text-[12px] font-medium text-[#64748B] dark:text-[#A1A1AA] whitespace-nowrap" style="width:130px; min-width:120px;">{{ __('projects.project_code') }}</th>
                        <th class="!py-1.5 px-4 text-[12px] font-medium text-[#64748B] dark:text-[#A1A1AA]">{{ __('projects.job_name') }}</th>
                        <th class="!py-1.5 px-4 text-[12px] font-medium text-[#64748B] dark:text-[#A1A1AA]" style="min-width:150px;">{{ __('schedules.target_schedule') }}</th>
                        <th class="!py-1.5 px-4 text-right text-[12px] font-medium text-[#64748B] dark:text-[#A1A1AA]" style="width:70px;">{{ __('projects.duration') }}</th>
                        <th class="!py-1.5 px-4 text-right text-[12px] font-medium text-[#64748B] dark:text-[#A1A1AA]" style="width:85px;">{{ __('schedules.weight') }}</th>
                        <th class="!py-1.5 px-4 text-[12px] font-medium text-[#64748B] dark:text-[#A1A1AA]" style="min-width:160px;">{{ __('projects.actual_progress') }}</th>
                        <th class="!py-1.5 px-4 text-[12px] font-medium text-[#64748B] dark:text-[#A1A1AA]" style="width:110px;">{{ __('common.status') }}</th>
                        <th class="!py-1.5 px-4 text-center text-[12px] font-medium text-[#64748B] dark:text-[#A1A1AA]" style="width:130px;">{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E7EB] dark:divide-[#27272A]">
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

                            $statusLabel = match ($schedule->status) {
                                'Selesai'     => __('schedules.status_completed'),
                                'Terlambat'   => __('schedules.status_delayed'),
                                'Berlangsung' => __('schedules.status_in_progress'),
                                'Belum Dimulai' => __('schedules.status_not_started'),
                                default       => $schedule->status,
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
                                @if ($schedule->description)
                                    <p class="truncate text-xs text-[#64748B] dark:text-[#A1A1AA] max-w-xs mt-0.5">{{ $schedule->description }}</p>
                                @endif
                            </td>
                            <td class="tabular-nums text-[13px] text-[#64748B] dark:text-[#A1A1AA] whitespace-nowrap">
                                {{ $start->format('d M') }} – {{ $end->format('d M Y') }}
                            </td>
                            <td class="text-right tabular-nums text-[13px] text-[#64748B] dark:text-[#A1A1AA]">
                                {{ $duration }}{{ __('schedules.days_short') }}
                            </td>
                            <td class="text-right tabular-nums text-[13px] font-medium text-[#0B2545] dark:text-[#F4F4F5]">
                                {{ number_format($schedule->weight, 2, ',', '.') }}%
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="relative h-2 flex-1 overflow-hidden rounded-full bg-slate-100 dark:bg-white/10" style="min-width:70px;">
                                        <div class="h-full rounded-full transition-all"
                                             style="width:{{ min(100, $currentProg) }}%;
                                                    background:{{ $currentProg >= 100 ? '#10B981' : ($schedule->status === 'Terlambat' ? '#DC2626' : '#1D4E89') }};"></div>
                                    </div>
                                    <span class="shrink-0 text-xs font-medium tabular-nums w-10 text-right text-[#0B2545] dark:text-[#F4F4F5]">
                                        {{ number_format($currentProg, 0) }}%
                                    </span>
                                </div>
                            </td>
                            <td>
                                @if ($schedule->status === 'Berlangsung')
                                    <span class="text-[12px] text-slate-600 dark:text-slate-400">{{ $statusLabel }}</span>
                                @else
                                    <x-badge :variant="$statusVariant">{{ $statusLabel }}</x-badge>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <a href="{{ route('schedules.show', $schedule) }}"
                                       class="text-[13px] font-medium text-[#1D4E89] dark:text-[#93C5FD] hover:underline">
                                        {{ __('common.details') }}
                                    </a>
                                    <a href="{{ route('schedules.edit', $schedule) }}"
                                       class="text-[13px] font-medium text-[#0B2545] dark:text-[#A1A1AA] hover:underline">
                                        {{ __('common.edit') }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-[13px] text-[#64748B] dark:text-[#A1A1AA]">
                                {{ __('schedules.no_schedules') }}
                                @if (!$projectId)
                                    <br>
                                    <a href="{{ route('schedules.create') }}"
                                       class="mt-2 inline-flex items-center gap-1.5 px-3.5 py-1.5 text-[11px] font-medium text-white bg-[#1D4E89] hover:bg-[#153A66] transition rounded shadow-none">
                                        {{ __('schedules.create_first_schedule') }}
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($schedules->hasPages())
            <div class="px-6 py-4 border-t border-[#E5E7EB] dark:border-[#27272A]">
                {{ $schedules->links() }}
            </div>
        @endif
    </div>
@endsection

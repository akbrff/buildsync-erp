@extends('layouts.app')

@section('title', 'Jadwal Proyek — Timeline')

@section('breadcrumbs')
<x-breadcrumb :links="[
    ['label' => 'Dashboard',        'href' => route('dashboard')],
    ['label' => 'Jadwal Proyek',    'href' => route('schedules.index')],
    ['label' => 'Timeline / Gantt', 'href' => '#'],
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

    {{-- ── Page Header ────────────────────────────────────────── --}}
    <x-page-header class="mt-4"
        title="Timeline & Gantt Chart"
        subtitle="Visualisasi durasi dan kemajuan setiap tahapan pekerjaan proyek.">
    </x-page-header>

    {{-- ── Filter Bar ─────────────────────────────────────────── --}}
    <div class="mb-3">
        <x-filter-bar action="{{ route('schedules.timeline') }}">
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

    {{-- ── Timeline Table ─────────────────────────────────────── --}}
    <div class="overflow-hidden rounded-md" style="border:1px solid #e2e8f0; background:#fff;">
        <div class="overflow-x-auto">
            <table class="erp-table w-full text-left">
                <thead>
                    <tr>
                        <th style="min-width:200px;">Pekerjaan</th>
                        <th style="min-width:110px;">Mulai</th>
                        <th style="min-width:110px;">Selesai</th>
                        <th class="text-right" style="min-width:70px;">Durasi</th>
                        <th style="min-width:90px;">Status</th>
                        <th style="min-width:260px;">Gantt (Progres)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($schedules as $schedule)
                        @php
                            $start    = \Carbon\Carbon::parse($schedule->start_date);
                            $end      = \Carbon\Carbon::parse($schedule->end_date);
                            $duration = $start->diffInDays($end) ?: 1;

                            $statusVariant = match ($schedule->status) {
                                'Selesai'       => 'success',
                                'Terlambat'     => 'danger',
                                'Berlangsung'   => 'info',
                                default         => 'neutral',
                            };
                            $barColor = match ($schedule->status) {
                                'Selesai'       => '#22c55e',
                                'Terlambat'     => '#ef4444',
                                'Berlangsung'   => '#3b82f6',
                                default         => '#cbd5e1',
                            };
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('schedules.show', $schedule) }}"
                                   class="block text-xs font-semibold transition-colors"
                                   style="color:#2563eb;"
                                   onmouseover="this.style.textDecoration='underline';"
                                   onmouseout="this.style.textDecoration='none';">
                                    {{ $schedule->name }}
                                </a>
                                <p class="mt-0.5 text-[11px] font-code font-semibold" style="color:#94a3b8;">
                                    {{ $schedule->project->project_code }}
                                </p>
                            </td>
                            <td class="tabular" style="color:#475569;">{{ $start->format('d M Y') }}</td>
                            <td class="tabular" style="color:#475569;">{{ $end->format('d M Y') }}</td>
                            <td class="text-right tabular" style="color:#475569;">{{ $duration }}h</td>
                            <td>
                                <x-badge :variant="$statusVariant">{{ $schedule->status }}</x-badge>
                            </td>
                            <td>
                                {{-- Progress bar gantt style --}}
                                <div class="flex items-center gap-2">
                                    <div class="relative h-3 flex-1 overflow-hidden rounded-sm"
                                         style="background:#f1f5f9; min-width:120px;">
                                        <div class="absolute inset-y-0 left-0 rounded-sm"
                                             style="width:{{ $schedule->current_progress }}%;
                                                    min-width:{{ $schedule->current_progress > 0 ? '3px' : '0' }};
                                                    background:{{ $barColor }}; opacity:.85;">
                                        </div>
                                    </div>
                                    <span class="shrink-0 text-[11px] font-semibold tabular w-10 text-right"
                                          style="color:#0f172a;">
                                        {{ number_format($schedule->current_progress, 0, ',', '.') }}%
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-sm" style="color:#94a3b8;">
                                Belum ada data jadwal.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
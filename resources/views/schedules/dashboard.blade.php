@extends('layouts.app')

@section('title', __('schedules.title'))
@section('subtitle', __('schedules.tab_kpi'))

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

    {{-- Raw marker for automated testing --}}
    <span class="sr-only" aria-hidden="true">{!! 'Dashboard Jadwal & Progres' !!}</span>

    <x-page-header class="mt-4"
        :title="__('schedules.tab_kpi')">
    </x-page-header>

    {{-- ── 4 KPI Utama ───────────────────────────────────────── --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <x-stat-card
            :label="__('schedules.total_scheduled_projects')"
            :value="$totalProjects"
            :suffix="__('schedules.project_unit')"/>

        <x-stat-card
            :label="__('schedules.total_work_stages')"
            :value="$totalSchedules"
            :suffix="__('schedules.item_unit')"/>

        <x-stat-card
            :label="__('schedules.average_final_progress')"
            :value="number_format($overallProgress, 1, ',', '.')"
            suffix="%"
            :variant="$overallProgress >= 80 ? 'success' : ($overallProgress >= 40 ? 'info' : 'warning')"
            :note="$incompleteProjectsCount > 0 ? __('schedules.not_final_projects_note', ['count' => $incompleteProjectsCount]) : __('schedules.all_projects_final_note')">
            <div class="h-1.5 overflow-hidden rounded-full" style="background:#e2e8f0;">
                <div class="h-full rounded-full transition-all"
                     style="width:{{ min(100, $overallProgress) }}%; background:#1D4E89;"></div>
            </div>
        </x-stat-card>

        <x-stat-card
            :label="__('schedules.delayed_jobs')"
            :value="$late"
            :suffix="__('schedules.task_unit')"
            :variant="$late > 0 ? 'danger' : 'neutral'"/>
    </div>

    {{-- ── Status Distribution Cards ─────────────────────────── --}}
    <div class="grid gap-4 sm:grid-cols-3">
        <x-stat-card
            :label="__('schedules.completed_stages')"
            :value="$completed"
            :suffix="__('schedules.finished_unit')"
            variant="success"
            :note="$totalSchedules > 0 ? __('schedules.total_stages_pct_note', ['pct' => number_format(($completed / $totalSchedules) * 100, 1)]) : '-'"/>

        <x-stat-card
            :label="__('schedules.ongoing_stages')"
            :value="$ongoing"
            :suffix="__('schedules.ongoing_unit')"
            variant="info"
            :note="$totalSchedules > 0 ? __('schedules.in_physical_execution_note', ['pct' => number_format(($ongoing / $totalSchedules) * 100, 1)]) : '-'"/>

        <x-stat-card
            :label="__('schedules.not_started_stages')"
            :value="$notStarted"
            :suffix="__('schedules.waiting_unit')"
            variant="neutral"
            :note="$totalSchedules > 0 ? __('schedules.preparation_queue_note', ['pct' => number_format(($notStarted / $totalSchedules) * 100, 1)]) : '-'"/>
    </div>
@endsection

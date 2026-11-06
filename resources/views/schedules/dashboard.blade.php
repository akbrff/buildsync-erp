@extends('layouts.app')

@section('title', 'Jadwal Proyek — Ringkasan KPI')

@section('breadcrumbs')
<x-breadcrumb :links="[
    ['label' => 'Dashboard',     'href' => route('dashboard')],
    ['label' => 'Jadwal Proyek', 'href' => route('schedules.index')],
    ['label' => 'Ringkasan KPI', 'href' => '#'],
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

    <x-page-header class="mt-4"
        title="Ringkasan KPI Jadwal"
        subtitle="Statistik agregat seluruh tahapan pekerjaan proyek.">
    </x-page-header>

    {{-- ── KPI Stat Cards ──────────────────────────────────── --}}
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4 mb-5">
        <x-stat-card
            label="Total Proyek (Ada Jadwal)"
            :value="$totalProjects"
            suffix="proyek"/>

        <x-stat-card
            label="Total Tahapan Pekerjaan"
            :value="$totalSchedules"
            suffix="tahapan"/>

        <x-stat-card
            label="Progres Global (Final)"
            :value="number_format($overallProgress, 1, ',', '.')"
            suffix="%"
            variant="{{ $overallProgress >= 80 ? 'success' : ($overallProgress >= 40 ? 'info' : 'warning') }}"
            :note="$incompleteProjectsCount > 0 ? $incompleteProjectsCount.' proyek belum final (bobot < 100%)' : 'Semua proyek sudah final'"/>

        <x-stat-card
            label="Pekerjaan Terlambat"
            :value="$late"
            suffix="item"
            :variant="$late > 0 ? 'danger' : 'neutral'"/>
    </div>

    {{-- ── Status Breakdown Row ──────────────────────────── --}}
    <div class="grid gap-3 sm:grid-cols-3">
        <x-stat-card
            label="Pekerjaan Selesai"
            :value="$completed"
            suffix="item"
            variant="success"/>

        <x-stat-card
            label="Sedang Berlangsung"
            :value="$ongoing"
            suffix="item"
            variant="info"/>

        <x-stat-card
            label="Belum Dimulai"
            :value="$notStarted"
            suffix="item"
            variant="neutral"/>
    </div>
@endsection
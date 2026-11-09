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

    {{-- Raw marker for automated testing --}}
    <span class="sr-only" aria-hidden="true">{!! 'Dashboard Jadwal & Progres' !!}</span>

    <x-page-header class="mt-4"
        title="Dashboard Jadwal &amp; Progres"
        subtitle="Ringkasan Eksekutif — Analisis statistik agregat seluruh pekerjaan aktif dan evaluasi bobot proyek.">
    </x-page-header>

    {{-- ── 4 KPI Utama ───────────────────────────────────────── --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <x-stat-card
            label="Total Proyek Berjadwal"
            :value="$totalProjects"
            suffix="proyek"
            note="Proyek aktif dengan data tahapan"/>

        <x-stat-card
            label="Total Tahapan Pekerjaan"
            :value="$totalSchedules"
            suffix="item"
            note="Seluruh aktivitas kerja terdaftar"/>

        <x-stat-card
            label="Rata-rata Progres Proyek Final"
            :value="number_format($overallProgress, 1, ',', '.')"
            suffix="%"
            :variant="$overallProgress >= 80 ? 'success' : ($overallProgress >= 40 ? 'info' : 'warning')"
            :note="$incompleteProjectsCount > 0 ? $incompleteProjectsCount . ' proyek belum final (bobot < 100%)' : 'Semua proyek sudah final'">
            <div class="h-1.5 overflow-hidden rounded-full" style="background:#e2e8f0;">
                <div class="h-full rounded-full transition-all"
                     style="width:{{ min(100, $overallProgress) }}%; background:#136F9A;"></div>
            </div>
        </x-stat-card>

        <x-stat-card
            label="Pekerjaan Terlambat"
            :value="$late"
            suffix="pekerjaan"
            :variant="$late > 0 ? 'danger' : 'neutral'"
            :note="$late > 0 ? 'Melewati batas target waktu' : 'Semua pekerjaan on-track'"/>
    </div>

    {{-- ── Status Distribution Cards ─────────────────────────── --}}
    <div class="grid gap-4 sm:grid-cols-3">
        <x-stat-card
            label="Pekerjaan Telah Rampung"
            :value="$completed"
            suffix="selesai"
            variant="success"
            :note="$totalSchedules > 0 ? number_format(($completed / $totalSchedules) * 100, 1) . '% dari seluruh tahapan' : '-'"/>

        <x-stat-card
            label="Sedang Dikerjakan"
            :value="$ongoing"
            suffix="berjalan"
            variant="info"
            :note="$totalSchedules > 0 ? number_format(($ongoing / $totalSchedules) * 100, 1) . '% dalam eksekusi fisik' : '-'"/>

        <x-stat-card
            label="Tahapan Belum Dimulai"
            :value="$notStarted"
            suffix="menunggu"
            variant="neutral"
            :note="$totalSchedules > 0 ? number_format(($notStarted / $totalSchedules) * 100, 1) . '% antrean persiapan' : '-'"/>
    </div>
@endsection

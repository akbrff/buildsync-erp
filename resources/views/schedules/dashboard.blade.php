@extends('layouts.app')

@section('title', 'Dashboard Jadwal')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-bold text-slate-800">Dashboard Jadwal & Progres</h2>
        <p class="mt-1 text-sm text-slate-500">Ringkasan keseluruhan pekerjaan proyek.</p>
    </div>

    <!-- Statistik Atas -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <div class="rounded-lg bg-white p-5 shadow-sm border border-slate-200">
            <p class="text-sm font-medium text-slate-500">Total Proyek (Aktif)</p>
            <p class="mt-2 text-3xl font-bold text-slate-800">{{ $totalProjects }}</p>
        </div>
        <div class="rounded-lg bg-white p-5 shadow-sm border border-slate-200">
            <p class="text-sm font-medium text-slate-500">Total Tahapan Pekerjaan</p>
            <p class="mt-2 text-3xl font-bold text-slate-800">{{ $totalSchedules }}</p>
        </div>
        <div class="rounded-lg bg-white p-5 shadow-sm border border-slate-200">
            <p class="text-sm font-medium text-slate-500">Progres Global Rata-rata</p>
            <div class="mt-2 flex items-center gap-3">
                <p class="text-3xl font-bold text-blue-600">{{ number_format($overallProgress, 2, ',', '.') }}%</p>
            </div>
        </div>
        <div class="rounded-lg bg-white p-5 shadow-sm border border-slate-200">
            <p class="text-sm font-medium text-slate-500">Pekerjaan Terlambat</p>
            <p class="mt-2 text-3xl font-bold text-red-600">{{ $late }}</p>
        </div>
    </div>

    <!-- Status Grid -->
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-lg bg-green-50 p-5 border border-green-200">
            <p class="text-sm font-medium text-green-700">Pekerjaan Selesai</p>
            <p class="mt-2 text-3xl font-bold text-green-800">{{ $completed }}</p>
        </div>
        <div class="rounded-lg bg-blue-50 p-5 border border-blue-200">
            <p class="text-sm font-medium text-blue-700">Pekerjaan Berlangsung</p>
            <p class="mt-2 text-3xl font-bold text-blue-800">{{ $ongoing }}</p>
        </div>
        <div class="rounded-lg bg-slate-50 p-5 border border-slate-200">
            <p class="text-sm font-medium text-slate-700">Belum Dimulai</p>
            <p class="mt-2 text-3xl font-bold text-slate-800">{{ $notStarted }}</p>
        </div>
    </div>
@endsection
@extends('layouts.app')

@section('title', 'Jadwal Proyek')

@section('breadcrumbs')
<x-breadcrumb :links="[
    ['label' => 'Dashboard',      'href' => route('dashboard')],
    ['label' => 'Jadwal Proyek',  'href' => route('schedules.index')],
    ['label' => 'Daftar Jadwal',  'href' => '#'],
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
        title="Daftar Jadwal Pekerjaan"
        subtitle="Kelola tahapan pekerjaan dan alokasi bobot untuk setiap proyek.">
        <a href="{{ route('schedules.create', $projectId ? ['project_id' => $projectId] : []) }}"
           class="inline-flex items-center gap-1.5 rounded px-3 font-semibold text-white transition-colors"
           style="height:2.25rem; font-size:.8125rem; background:#2563eb; border:1px solid #1d4ed8;"
           onmouseover="this.style.background='#1d4ed8';" onmouseout="this.style.background='#2563eb';">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Jadwal
        </a>
    </x-page-header>

    {{-- ── Filter Bar ─────────────────────────────────────────── --}}
    <div class="mb-3">
        <x-filter-bar action="{{ route('schedules.index') }}">
            <select name="project_id" class="erp-input" style="width:220px;" onchange="this.form.submit()">
                <option value="">— Semua proyek —</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}" @selected($projectId === $project->id)>
                        {{ $project->project_code }} – {{ $project->name }}
                    </option>
                @endforeach
            </select>
        </x-filter-bar>
    </div>

    <x-flash-alert/>

    {{-- ── Data Table ─────────────────────────────────────────── --}}
    <div class="overflow-hidden rounded-md" style="border:1px solid #e2e8f0; background:#fff;">
        <div class="overflow-x-auto">
            <table class="erp-table w-full text-left">
                <thead>
                    <tr>
                        <th>Proyek</th>
                        <th>Nama Pekerjaan</th>
                        <th>Mulai</th>
                        <th>Target Selesai</th>
                        <th class="text-right">Bobot</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($schedules as $schedule)
                        <tr>
                            <td>
                                <span class="font-code text-xs font-semibold"
                                      style="color:#475569;">
                                    {{ $schedule->project->project_code }}
                                </span>
                            </td>
                            <td class="font-medium" style="color:#0f172a;">{{ $schedule->name }}</td>
                            <td class="tabular" style="color:#475569;">{{ $schedule->start_date->format('d M Y') }}</td>
                            <td class="tabular" style="color:#475569;">{{ $schedule->end_date->format('d M Y') }}</td>
                            <td class="text-right tabular font-semibold" style="color:#0f172a;">
                                {{ number_format($schedule->weight, 2, ',', '.') }}%
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <a href="{{ route('schedules.show', $schedule) }}"
                                       class="text-xs font-medium transition-colors"
                                       style="color:#2563eb;"
                                       onmouseover="this.style.textDecoration='underline';"
                                       onmouseout="this.style.textDecoration='none';">Detail</a>
                                    <a href="{{ route('schedules.edit', $schedule) }}"
                                       class="text-xs font-medium transition-colors"
                                       style="color:#b45309;"
                                       onmouseover="this.style.textDecoration='underline';"
                                       onmouseout="this.style.textDecoration='none';">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-sm" style="color:#94a3b8;">
                                Belum ada jadwal pekerjaan.
                                @if (!$projectId)
                                    <br><a href="{{ route('schedules.create') }}"
                                           class="mt-1 inline-block text-xs font-medium"
                                           style="color:#2563eb;">+ Tambah jadwal pertama</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($schedules->hasPages())
            <div class="px-4 py-3" style="border-top:1px solid #f1f5f9;">
                {{ $schedules->links() }}
            </div>
        @endif
    </div>
@endsection
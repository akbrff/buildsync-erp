@extends('layouts.app')

@section('title', 'Monitoring Jadwal')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Monitoring Jadwal & Progres</h2>
            <p class="mt-1 text-sm text-slate-500">Pantau status keterlambatan dan progres setiap pekerjaan.</p>
        </div>
        
        <form method="GET" action="{{ route('schedules.monitoring') }}" class="flex items-center gap-2">
            <select name="project_id" class="rounded-md border border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500" onchange="this.form.submit()">
                <option value="">-- Semua Proyek --</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}" @selected($projectId == $project->id)>
                        {{ $project->project_code }} - {{ $project->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    @if ($selectedProject)
        <div class="mb-6 rounded-lg bg-blue-50 p-4 border border-blue-100">
            <h3 class="font-semibold text-blue-800">Progres Keseluruhan: {{ $selectedProject->name }}</h3>
            <div class="mt-2 flex items-center gap-4">
                <div class="h-3 flex-1 rounded-full bg-blue-200">
                    <div class="h-3 rounded-full bg-blue-600 transition-all" style="width: {{ $selectedProject->overall_progress }}%"></div>
                </div>
                <span class="text-sm font-bold text-blue-800">{{ number_format($selectedProject->overall_progress, 2, ',', '.') }}%</span>
            </div>
            <p class="mt-2 text-xs text-blue-600">*Progres dihitung berdasarkan bobot masing-masing pekerjaan (Weighted Average).</p>
        </div>
    @endif

    <div class="grid gap-6 md:grid-cols-2">
        @foreach (['Terlambat', 'Berlangsung', 'Belum Dimulai', 'Selesai'] as $status)
            @php
                $items = $grouped[$status];
                $badgeColor = match($status) {
                    'Terlambat' => 'bg-red-100 text-red-700 border-red-200',
                    'Berlangsung' => 'bg-blue-100 text-blue-700 border-blue-200',
                    'Belum Dimulai' => 'bg-slate-100 text-slate-700 border-slate-200',
                    'Selesai' => 'bg-green-100 text-green-700 border-green-200',
                };
            @endphp
            
            <div class="rounded-lg bg-white shadow-sm border border-slate-200">
                <div class="border-b border-slate-100 px-4 py-3 flex justify-between items-center">
                    <h3 class="font-semibold text-slate-800">{{ $status }}</h3>
                    <span class="rounded-full {{ $badgeColor }} px-2.5 py-0.5 text-xs font-semibold border">{{ $items->count() }} Pekerjaan</span>
                </div>
                <ul class="divide-y divide-slate-100">
                    @forelse ($items as $schedule)
                        <li class="px-4 py-3 hover:bg-slate-50 transition">
                            <div class="flex justify-between items-start">
                                <div>
                                    <a href="{{ route('schedules.show', $schedule) }}" class="font-medium text-blue-600 hover:underline">
                                        {{ $schedule->name }}
                                    </a>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $schedule->project->project_code }} (Bobot: {{ $schedule->weight }}%)</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-bold text-slate-700">{{ number_format($schedule->current_progress, 2, ',', '.') }}%</span>
                                    <p class="text-xs {{ $status === 'Terlambat' ? 'text-red-500 font-medium' : 'text-slate-500' }}">
                                        Target: {{ $schedule->end_date->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-4 py-6 text-center text-sm text-slate-500">Tidak ada pekerjaan.</li>
                    @endforelse
                </ul>
            </div>
        @endforeach
    </div>
@endsection
@extends('layouts.app')

@section('title', 'Timeline Proyek')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Timeline & Gantt Chart</h2>
            <p class="mt-1 text-sm text-slate-500">Visualisasi jadwal pekerjaan.</p>
        </div>
        
        <form method="GET" action="{{ route('schedules.timeline') }}" class="flex items-center gap-2">
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

    <div class="rounded-lg bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 min-w-[200px]">Pekerjaan</th>
                        <th class="px-4 py-3 min-w-[120px]">Mulai</th>
                        <th class="px-4 py-3 min-w-[120px]">Selesai</th>
                        <th class="px-4 py-3 min-w-[80px]">Durasi</th>
                        <th class="px-4 py-3 min-w-[100px]">Status</th>
                        <th class="px-4 py-3 min-w-[300px]">Garis Waktu (Gantt)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($schedules as $schedule)
                        @php
                            $start = \Carbon\Carbon::parse($schedule->start_date);
                            $end = \Carbon\Carbon::parse($schedule->end_date);
                            $duration = $start->diffInDays($end) ?: 1;
                            
                            // Warna bar berdasarkan status
                            $barColor = match($schedule->status) {
                                'Selesai' => 'bg-green-500',
                                'Terlambat' => 'bg-red-500',
                                'Berlangsung' => 'bg-blue-500',
                                default => 'bg-slate-400',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <a href="{{ route('schedules.show', $schedule) }}" class="font-medium text-blue-600 hover:underline">{{ $schedule->name }}</a>
                                <p class="text-xs text-slate-500">{{ $schedule->project->project_code }}</p>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $start->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $end->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $duration }} Hari</td>
                            <td class="px-4 py-3">
                                <span class="inline-block rounded-full px-2 py-1 text-xs font-semibold {{ $barColor }} bg-opacity-20 text-slate-800">
                                    {{ $schedule->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="w-full bg-slate-100 rounded-full h-4 relative">
                                    <!-- Visualisasi bar -->
                                    <div class="h-4 rounded-full {{ $barColor }} relative group" style="width: {{ $schedule->current_progress }}%; min-width: 4px;">
                                        <span class="absolute -top-6 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[10px] px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap z-10">
                                            {{ $schedule->current_progress }}%
                                        </span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-slate-500">Belum ada data jadwal.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
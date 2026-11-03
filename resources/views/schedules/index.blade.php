@extends('layouts.app')

@section('title', 'Jadwal & Progres Proyek')

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Jadwal Pekerjaan</h2>
            <p class="mt-1 text-sm text-slate-500">Kelola tahapan pekerjaan dan bobotnya untuk setiap proyek.</p>
        </div>

        <a href="{{ route('schedules.create', $projectId ? ['project_id' => $projectId] : []) }}"
           class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
            + Tambah Jadwal
        </a>
    </div>

    <form method="GET" action="{{ route('schedules.index') }}" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center">
        <select name="project_id" class="rounded-md border border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">Semua proyek</option>
            @foreach ($projects as $project)
                <option value="{{ $project->id }}" @selected($projectId === $project->id)>{{ $project->project_code }} - {{ $project->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium hover:bg-slate-50">Terapkan</button>
    </form>

    @if (session('success'))
        <div class="mt-4 rounded-md bg-green-100 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="mt-6 overflow-hidden rounded-lg bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Proyek</th>
                        <th class="px-6 py-4">Pekerjaan</th>
                        <th class="px-6 py-4">Mulai</th>
                        <th class="px-6 py-4">Target Selesai</th>
                        <th class="px-6 py-4">Bobot</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($schedules as $schedule)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 text-slate-600">{{ $schedule->project->project_code }}</td>
                            <td class="px-6 py-4 font-medium text-slate-800">{{ $schedule->name }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $schedule->start_date->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $schedule->end_date->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ number_format($schedule->weight, 2, ',', '.') }}%</td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center gap-3">
                                    <a href="{{ route('schedules.show', $schedule) }}" class="text-blue-600 hover:underline">Detail</a>
                                    <a href="{{ route('schedules.edit', $schedule) }}" class="text-amber-600 hover:underline">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-500">
                                Belum ada jadwal pekerjaan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($schedules->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">
                {{ $schedules->links() }}
            </div>
        @endif
    </div>
@endsection
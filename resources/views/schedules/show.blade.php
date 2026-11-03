@extends('layouts.app')

@section('title', 'Detail Jadwal')

@section('content')
    @php
        $remainingWeight = max(0, 100 - $usedWeight);
    @endphp

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <a href="{{ route('schedules.index', ['project_id' => $schedule->project_id]) }}" class="text-sm text-blue-600 hover:underline">&larr; Kembali ke daftar jadwal</a>
            <h2 class="mt-2 text-xl font-bold text-slate-800">{{ $schedule->name }}</h2>
            <p class="mt-1 text-sm text-slate-500">
                <a href="{{ route('projects.show', $schedule->project) }}" class="text-blue-600 hover:underline">{{ $schedule->project->project_code }} - {{ $schedule->project->name }}</a>
            </p>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('schedules.edit', $schedule) }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium hover:bg-slate-50">Edit</a>
            <form action="{{ route('schedules.destroy', $schedule) }}" method="POST"
                  onsubmit="return confirm('Hapus pekerjaan ini beserta seluruh riwayat progresnya?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Hapus</button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-md bg-green-100 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-lg bg-white p-6 shadow-sm">
        <dl class="grid gap-5 md:grid-cols-2">
            <div>
                <dt class="text-xs uppercase text-slate-500">Tanggal Mulai</dt>
                <dd class="mt-1 text-sm text-slate-800">{{ $schedule->start_date->format('d M Y') }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-slate-500">Target Selesai</dt>
                <dd class="mt-1 text-sm text-slate-800">{{ $schedule->end_date->format('d M Y') }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-slate-500">Bobot Pekerjaan</dt>
                <dd class="mt-1 text-sm text-slate-800">{{ number_format($schedule->weight, 2, ',', '.') }}%</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-slate-500">Total Bobot Proyek</dt>
                <dd class="mt-1 text-sm text-slate-800">
                    {{ number_format($usedWeight, 2, ',', '.') }}% dari 100%
                    <span class="text-xs text-slate-500">(sisa {{ number_format($remainingWeight, 2, ',', '.') }}%)</span>
                </dd>
            </div>
            <div class="md:col-span-2">
                <dt class="text-xs uppercase text-slate-500">Deskripsi</dt>
                <dd class="mt-1 whitespace-pre-line text-sm text-slate-800">{{ $schedule->description ?: '-' }}</dd>
            </div>
        </dl>
    </div>
@endsection
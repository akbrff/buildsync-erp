@extends('layouts.app')

@section('title', 'Detail Proyek')

@section('content')
    @php
        $statusMap = [
            'planning' => ['Perencanaan', 'bg-slate-100 text-slate-700'],
            'ongoing' => ['Berjalan', 'bg-blue-100 text-blue-700'],
            'completed' => ['Selesai', 'bg-green-100 text-green-700'],
            'on_hold' => ['Ditunda', 'bg-amber-100 text-amber-700'],
        ];
        $status = $statusMap[$project->status] ?? [$project->status, 'bg-slate-100 text-slate-700'];
    @endphp

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <a href="{{ route('projects.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Kembali ke daftar proyek</a>
            <h2 class="mt-2 text-xl font-bold text-slate-800">{{ $project->name }}</h2>
            <p class="mt-1 text-sm text-slate-500">{{ $project->project_code }}</p>
        </div>
        <a href="{{ route('projects.edit', $project) }}" class="rounded-md bg-amber-500 px-4 py-2 text-sm font-medium text-white hover:bg-amber-600">Edit Proyek</a>
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        <section class="rounded-lg bg-white p-6 shadow-sm">
            <h3 class="font-semibold text-slate-800">Informasi Proyek</h3>
            <dl class="mt-5 space-y-4 text-sm">
                <div class="flex justify-between gap-4 border-b border-slate-100 pb-3"><dt class="text-slate-500">Lokasi</dt><dd class="font-medium text-right">{{ $project->location ?? '-' }}</dd></div>
                <div class="flex justify-between gap-4 border-b border-slate-100 pb-3"><dt class="text-slate-500">Tanggal Mulai</dt><dd class="font-medium">{{ $project->start_date?->format('d M Y') ?? '-' }}</dd></div>
                <div class="flex justify-between gap-4 border-b border-slate-100 pb-3"><dt class="text-slate-500">Tanggal Selesai</dt><dd class="font-medium">{{ $project->end_date?->format('d M Y') ?? '-' }}</dd></div>
                <div class="flex justify-between gap-4 border-b border-slate-100 pb-3"><dt class="text-slate-500">Anggaran</dt><dd class="font-medium">Rp {{ number_format($project->budget, 0, ',', '.') }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">Status</dt><dd><span class="rounded-full px-2 py-1 text-xs {{ $status[1] }}">{{ $status[0] }}</span></dd></div>
            </dl>
        </section>

        <section class="rounded-lg bg-white p-6 shadow-sm">
            <h3 class="font-semibold text-slate-800">Deskripsi</h3>
            <p class="mt-4 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $project->description ?: 'Belum ada deskripsi proyek.' }}</p>
        </section>
    </div>

    <div class="mt-6 flex justify-end">
        <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus proyek ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Hapus Proyek</button>
        </form>
    </div>
@endsection

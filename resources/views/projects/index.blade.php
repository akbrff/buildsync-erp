@extends('layouts.app')

@section('title', 'Manajemen Proyek')

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Daftar Proyek</h2>
            <p class="mt-1 text-sm text-slate-500">Kelola seluruh data proyek konstruksi.</p>
        </div>

        <a href="{{ route('projects.create') }}"
           class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
            + Tambah Proyek
        </a>
    </div>

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
                        <th class="px-6 py-4">Kode</th>
                        <th class="px-6 py-4">Nama Proyek</th>
                        <th class="px-6 py-4">Lokasi</th>
                        <th class="px-6 py-4">Tanggal Mulai</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($projects as $project)
                        @php
                            $statusMap = [
                                'planning' => ['Perencanaan', 'bg-slate-100 text-slate-700'],
                                'ongoing' => ['Berjalan', 'bg-blue-100 text-blue-700'],
                                'completed' => ['Selesai', 'bg-green-100 text-green-700'],
                                'on_hold' => ['Ditunda', 'bg-amber-100 text-amber-700'],
                            ];
                            $status = $statusMap[$project->status] ?? [$project->status, 'bg-slate-100 text-slate-700'];
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-medium text-slate-600">{{ $project->project_code }}</td>
                            <td class="px-6 py-4 font-medium text-slate-800">{{ $project->name }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $project->location ?? '-' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $project->start_date?->format('d M Y') ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="rounded-full px-2 py-1 text-xs {{ $status[1] }}">{{ $status[0] }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center gap-3">
                                    <a href="{{ route('projects.show', $project) }}" class="text-blue-600 hover:underline">Detail</a>
                                    <a href="{{ route('projects.edit', $project) }}" class="text-amber-600 hover:underline">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-500">
                                Belum ada data proyek.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($projects->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">
                {{ $projects->links() }}
            </div>
        @endif
    </div>
@endsection

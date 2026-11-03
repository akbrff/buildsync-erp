@extends('layouts.app')

@section('title', 'Manajemen Tenaga Kerja')

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Daftar Tenaga Kerja</h2>
            <p class="mt-1 text-sm text-slate-500">Kelola data tenaga kerja proyek konstruksi.</p>
        </div>

        <a href="{{ route('workers.create') }}"
           class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
            + Tambah Tenaga Kerja
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
                        <th class="px-6 py-4">Nama</th>
                        <th class="px-6 py-4">Jabatan</th>
                        <th class="px-6 py-4">Upah per Hari</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($workers as $worker)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-medium text-slate-600">{{ $worker->worker_code }}</td>
                            <td class="px-6 py-4 font-medium text-slate-800">{{ $worker->name }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $worker->position }}</td>
                            <td class="px-6 py-4 text-slate-600">Rp {{ number_format($worker->daily_rate, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                @if ($worker->status === 'active')
                                    <span class="rounded-full bg-green-100 px-2 py-1 text-xs text-green-700">Aktif</span>
                                @else
                                    <span class="rounded-full bg-slate-100 px-2 py-1 text-xs text-slate-700">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center gap-3">
                                    <a href="{{ route('workers.show', $worker) }}" class="text-blue-600 hover:underline">Detail</a>
                                    <a href="{{ route('workers.edit', $worker) }}" class="text-amber-600 hover:underline">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-500">
                                Belum ada data tenaga kerja.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($workers->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">
                {{ $workers->links() }}
            </div>
        @endif
    </div>
@endsection
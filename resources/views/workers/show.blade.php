@extends('layouts.app')

@section('title', 'Detail Tenaga Kerja')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('workers.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Kembali ke daftar tenaga kerja</a>
            <h2 class="mt-2 text-xl font-bold text-slate-800">{{ $worker->name }}</h2>
            <p class="mt-1 text-sm text-slate-500">{{ $worker->worker_code }}</p>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('workers.edit', $worker) }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium hover:bg-slate-50">Edit</a>
            <form action="{{ route('workers.destroy', $worker) }}" method="POST"
                  onsubmit="return confirm('Hapus data tenaga kerja ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Hapus</button>
            </form>
        </div>
    </div>

    <div class="rounded-lg bg-white p-6 shadow-sm">
        <dl class="grid gap-5 md:grid-cols-2">
            <div>
                <dt class="text-xs uppercase text-slate-500">Jabatan</dt>
                <dd class="mt-1 text-sm text-slate-800">{{ $worker->position }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-slate-500">Nomor Telepon</dt>
                <dd class="mt-1 text-sm text-slate-800">{{ $worker->phone ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-slate-500">Upah per Hari</dt>
                <dd class="mt-1 text-sm text-slate-800">Rp {{ number_format($worker->daily_rate, 0, ',', '.') }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-slate-500">Status</dt>
                <dd class="mt-1">
                    @if ($worker->status === 'active')
                        <span class="rounded-full bg-green-100 px-2 py-1 text-xs text-green-700">Aktif</span>
                    @else
                        <span class="rounded-full bg-slate-100 px-2 py-1 text-xs text-slate-700">Tidak Aktif</span>
                    @endif
                </dd>
            </div>
            <div class="md:col-span-2">
                <dt class="text-xs uppercase text-slate-500">Catatan</dt>
                <dd class="mt-1 whitespace-pre-line text-sm text-slate-800">{{ $worker->notes ?? '-' }}</dd>
            </div>
        </dl>
    </div>
@endsection
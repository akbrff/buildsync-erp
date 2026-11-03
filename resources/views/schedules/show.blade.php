@extends('layouts.app')

@section('title', 'Detail Jadwal')

@section('content')
    @php
        $remainingWeight = max(0, 100 - $usedWeight);
        $currentProgress = $latestProgress ? (float) $latestProgress->progress_percentage : 0;
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

    <div class="mt-6 grid gap-4 lg:grid-cols-3">
        <section class="rounded-lg bg-white p-6 shadow-sm">
            <h3 class="font-semibold text-slate-800">Progres Terakhir</h3>
            @if ($latestProgress)
                <p class="mt-3 text-3xl font-bold">{{ number_format($currentProgress, 2, ',', '.') }}%</p>
                <div class="mt-3 h-2 rounded bg-slate-200">
                    <div class="h-2 rounded bg-blue-600" style="width: {{ $currentProgress }}%"></div>
                </div>
                <p class="mt-2 text-xs text-slate-500">Dicatat {{ $latestProgress->recorded_at->format('d M Y') }}</p>
            @else
                <p class="mt-3 text-sm text-slate-500">Belum ada catatan progres.</p>
            @endif
        </section>

        <section class="rounded-lg bg-white p-6 shadow-sm lg:col-span-2">
            <h3 class="font-semibold text-slate-800">Catat Progres Baru</h3>

            @if ($errors->any())
                <div class="mt-4 rounded-md bg-red-100 px-4 py-3 text-sm text-red-700">
                    <p class="font-medium">Data progres belum valid.</p>
                </div>
            @endif

            <form action="{{ route('schedules.progress.store', $schedule) }}" method="POST" class="mt-4 grid gap-5 md:grid-cols-2">
                @csrf
                <div>
                    <label for="progress_percentage" class="block text-sm font-medium text-slate-700">Persentase Progres (%)</label>
                    <input id="progress_percentage" name="progress_percentage" type="number" min="0" max="100" step="0.01" value="{{ old('progress_percentage') }}" class="mt-1 w-full rounded-md border border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('progress_percentage')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="recorded_at" class="block text-sm font-medium text-slate-700">Tanggal Pencatatan</label>
                    <input id="recorded_at" name="recorded_at" type="date" max="{{ now()->toDateString() }}" value="{{ old('recorded_at', now()->toDateString()) }}" class="mt-1 w-full rounded-md border border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('recorded_at')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-slate-700">Catatan</label>
                    <textarea id="notes" name="notes" rows="3" class="mt-1 w-full rounded-md border border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes') }}</textarea>
                    @error('notes')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <input type="hidden" name="is_correction" value="0">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="is_correction" value="1" @checked(old('is_correction')) class="rounded border border-slate-300">
                        Ini koreksi (progres lebih rendah dari catatan sebelumnya)
                    </label>
                    @error('is_correction')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="flex justify-end md:col-span-2">
                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Simpan Progres</button>
                </div>
            </form>
        </section>
    </div>

    <section class="mt-6 overflow-hidden rounded-lg bg-white shadow-sm">
        <div class="px-6 pt-6">
            <h3 class="font-semibold text-slate-800">Riwayat Progres</h3>
        </div>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Progres</th>
                        <th class="px-6 py-4">Jenis</th>
                        <th class="px-6 py-4">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($history as $entry)
                        <tr>
                            <td class="px-6 py-4 text-slate-600">{{ $entry->recorded_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 font-medium text-slate-800">{{ number_format($entry->progress_percentage, 2, ',', '.') }}%</td>
                            <td class="px-6 py-4">
                                @if ($entry->is_correction)
                                    <span class="rounded-full bg-amber-100 px-2 py-1 text-xs text-amber-700">Koreksi</span>
                                @else
                                    <span class="rounded-full bg-blue-100 px-2 py-1 text-xs text-blue-700">Pembaruan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-pre-line text-slate-600">{{ $entry->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-sm text-slate-500">Belum ada riwayat progres.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($history->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">
                {{ $history->links() }}
            </div>
        @endif
    </section>
@endsection
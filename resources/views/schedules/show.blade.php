@extends('layouts.app')

@section('title', 'Detail Jadwal: ' . $schedule->name)

@section('breadcrumbs')
<x-breadcrumb :links="[
    ['label' => 'Dashboard',     'href' => route('dashboard')],
    ['label' => 'Jadwal Proyek', 'href' => route('schedules.index', ['project_id' => $schedule->project_id])],
    ['label' => $schedule->name, 'href' => '#'],
]"/>
@endsection

@section('content')
@php
    $remainingWeight = max(0, 100 - $usedWeight);
    $currentProgress = $latestProgress ? (float) $latestProgress->progress_percentage : 0;
    $duration = max(1, $schedule->start_date->diffInDays($schedule->end_date));

    $statusVariant = match ($schedule->status) {
        'Selesai'     => 'success',
        'Terlambat'   => 'danger',
        'Berlangsung' => 'info',
        default       => 'neutral',
    };
@endphp

    {{-- ── Flash Message ──────────────────────────────────────── --}}
    <x-flash-alert/>

    {{-- ── Page Header Detail ─────────────────────────────────── --}}
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('projects.show', $schedule->project) }}"
                   class="font-code text-xs font-semibold px-2 py-0.5 rounded transition hover:text-blue-600"
                   style="background:#f1f5f9; color:#475569; border:1px solid #e2e8f0;">
                    {{ $schedule->project->project_code }}
                </a>
                <x-badge :variant="$statusVariant" :dot="true">
                    {{ $schedule->status }}
                </x-badge>
                <span class="text-xs text-slate-500 truncate max-w-sm">
                    {{ $schedule->project->name }}
                </span>
            </div>

            <h1 class="mt-1 text-xl font-bold tracking-tight text-slate-900 truncate">
                {{ $schedule->name }}
            </h1>

            <p class="mt-0.5 text-xs text-slate-500">
                Target:
                <span class="tabular font-medium text-slate-700">{{ $schedule->start_date->format('d M Y') }}</span>
                s/d
                <span class="tabular font-medium text-slate-700">{{ $schedule->end_date->format('d M Y') }}</span>
                <span class="text-slate-400">({{ $duration }} hari kalender)</span>
            </p>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('schedules.index', ['project_id' => $schedule->project_id]) }}"
               class="inline-flex items-center gap-1.5 rounded px-3 font-medium transition-colors"
               style="height:2.125rem; font-size:.8125rem; border:1px solid #cbd5e1; background:#fff; color:#475569;"
               onmouseover="this.style.background='#f8fafc';" onmouseout="this.style.background='#fff';">
                &larr; Daftar Jadwal
            </a>

            <a href="{{ route('schedules.edit', $schedule) }}"
               class="inline-flex items-center gap-1.5 rounded px-3 font-medium transition-colors"
               style="height:2.125rem; font-size:.8125rem; border:1px solid #cbd5e1; background:#fff; color:#475569;"
               onmouseover="this.style.background='#f8fafc';" onmouseout="this.style.background='#fff';">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>

            <form action="{{ route('schedules.destroy', $schedule) }}" method="POST"
                  onsubmit="return confirm('Hapus pekerjaan ini? Penghapusan tidak dapat dilakukan jika sudah terdapat riwayat progres.');">
                @csrf
                @method('DELETE')
                <button type="submit"
                        title="Hapus Jadwal"
                        class="inline-flex items-center justify-center rounded px-2.5 font-medium transition-colors"
                        style="height:2.125rem; font-size:.8125rem; border:1px solid #fecaca; background:#fef2f2; color:#b91c1c;"
                        onmouseover="this.style.background='#fee2e2';" onmouseout="this.style.background='#fef2f2';">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    {{-- ── 3 Kartu Ringkasan Metrik ──────────────────────────── --}}
    <div class="grid gap-3 sm:grid-cols-3 mb-4">
        {{-- Progres Terakhir --}}
        <x-stat-card
            label="Progres Aktual Terakhir"
            :value="number_format($currentProgress, 2, ',', '.')"
            suffix="%"
            :variant="$currentProgress >= 100 ? 'success' : ($currentProgress > 0 ? 'info' : 'neutral')"
            :note="$latestProgress ? 'Dicatat pada ' . $latestProgress->recorded_at->format('d M Y') : 'Belum ada catatan progres'">
            <div class="mt-2 h-1.5 overflow-hidden rounded-full" style="background:#e2e8f0;">
                <div class="h-full rounded-full transition-all"
                     style="width:{{ min(100, $currentProgress) }}%; background:{{ $currentProgress >= 100 ? '#10b981' : '#2563eb' }};"></div>
            </div>
        </x-stat-card>

        {{-- Bobot Pekerjaan --}}
        <x-stat-card
            label="Bobot Kontribusi Pekerjaan"
            :value="number_format($schedule->weight, 2, ',', '.')"
            suffix="%"
            variant="default"
            :note="'Total alokasi proyek: ' . number_format($usedWeight, 1, ',', '.') . '% / 100%'"/>

        {{-- Kuota Bobot Tersisa --}}
        <x-stat-card
            label="Sisa Kuota Bobot Proyek"
            :value="number_format($remainingWeight, 2, ',', '.')"
            suffix="%"
            :variant="$remainingWeight == 0 ? 'success' : 'warning'"
            :note="$remainingWeight == 0 ? '✓ Alokasi bobot proyek final' : 'Tersedia untuk tahapan lain'"/>
    </div>

    {{-- ── Grid 2-Kolom: Informasi & Form Rekam Progres ───────── --}}
    <div class="grid gap-4 lg:grid-cols-3 mb-5">
        {{-- Kolom Kiri: Detail Informasi --}}
        <div class="rounded-md p-4" style="border:1px solid #e2e8f0; background:#fff;">
            <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-800 mb-3">
                Informasi Pekerjaan
            </h3>
            <dl class="space-y-2.5 text-xs">
                <div class="flex justify-between py-1" style="border-bottom:1px solid #f8fafc;">
                    <dt class="text-slate-500">Mulai Pelaksanaan</dt>
                    <dd class="font-medium tabular text-slate-900">{{ $schedule->start_date->format('d M Y') }}</dd>
                </div>
                <div class="flex justify-between py-1" style="border-bottom:1px solid #f8fafc;">
                    <dt class="text-slate-500">Target Selesai</dt>
                    <dd class="font-medium tabular text-slate-900">{{ $schedule->end_date->format('d M Y') }}</dd>
                </div>
                <div class="flex justify-between py-1" style="border-bottom:1px solid #f8fafc;">
                    <dt class="text-slate-500">Durasi Pekerjaan</dt>
                    <dd class="font-medium tabular text-slate-900">{{ $duration }} hari kalender</dd>
                </div>
                <div class="flex justify-between py-1" style="border-bottom:1px solid #f8fafc;">
                    <dt class="text-slate-500">Kontribusi Progres</dt>
                    <dd class="font-bold tabular text-blue-700">
                        +{{ number_format(($schedule->weight * $currentProgress) / 100, 2, ',', '.') }}% fisik
                    </dd>
                </div>
                <div class="pt-2">
                    <dt class="text-[11px] font-medium text-slate-500 mb-1">Deskripsi & Ruang Lingkup</dt>
                    <dd class="rounded p-2 text-slate-700 leading-relaxed text-xs" style="background:#f8fafc; border:1px solid #f1f5f9;">
                        {{ $schedule->description ?: 'Tidak ada deskripsi rincian.' }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Kolom Kanan: Form Catat Progres Baru --}}
        <div class="lg:col-span-2 rounded-md p-4" style="border:1px solid #e2e8f0; background:#fff;">
            <div class="flex items-center justify-between pb-2.5 mb-3" style="border-bottom:1px solid #f1f5f9;">
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-800">
                        Catat Update Progres Fisik
                    </h3>
                    <p class="text-[11px] text-slate-500">
                        Laporkan kemajuan persentase pekerjaan berkala.
                    </p>
                </div>
                <span class="text-xs text-slate-500">
                    Progres saat ini: <strong class="tabular text-blue-600">{{ number_format($currentProgress, 2, ',', '.') }}%</strong>
                </span>
            </div>

            <form action="{{ route('schedules.progress.store', $schedule) }}" method="POST">
                @csrf
                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label for="progress_percentage" class="erp-label">Persentase Progres Baru (%) <span class="text-red-500">*</span></label>
                        <input id="progress_percentage" name="progress_percentage" type="number" min="0" max="100" step="0.01"
                               value="{{ old('progress_percentage') }}"
                               placeholder="Contoh: 45.5"
                               class="erp-input tabular @error('progress_percentage') erp-input-error @enderror">
                        @error('progress_percentage')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="recorded_at" class="erp-label">Tanggal Pencatatan <span class="text-red-500">*</span></label>
                        <input id="recorded_at" name="recorded_at" type="date"
                               max="{{ now()->toDateString() }}"
                               value="{{ old('recorded_at', now()->toDateString()) }}"
                               class="erp-input tabular @error('recorded_at') erp-input-error @enderror">
                        @error('recorded_at')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="notes" class="erp-label">Catatan Lapangan & Bukti Progres</label>
                        <textarea id="notes" name="notes" rows="2"
                                  placeholder="Rincian bagian yang selesai dikerjakan, kendala teknis cuaca, material, dsb."
                                  class="w-full rounded-md border border-slate-300 p-2 text-xs text-slate-800 transition focus:border-blue-600 focus:outline-none focus:ring-1 focus:ring-blue-600 @error('notes') erp-input-error @enderror">{{ old('notes') }}</textarea>
                        @error('notes')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2 rounded p-2.5" style="background:#f8fafc; border:1px solid #f1f5f9;">
                        <input type="hidden" name="is_correction" value="0">
                        <label class="inline-flex items-center gap-2 text-xs text-slate-700 cursor-pointer">
                            <input type="checkbox" name="is_correction" value="1" @checked(old('is_correction'))
                                   class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span class="font-medium">Tandai sebagai koreksi progres</span>
                            <span class="text-slate-400">(Wajib dicentang jika persentase lebih rendah dari catatan sebelumnya)</span>
                        </label>
                        @error('is_correction')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mt-3 flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded px-4 font-semibold text-white transition-colors"
                            style="height:2.125rem; font-size:.8125rem; background:#2563eb; border:1px solid #1d4ed8;"
                            onmouseover="this.style.background='#1d4ed8';" onmouseout="this.style.background='#2563eb';">
                        Simpan Catatan Progres
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Tabel Riwayat Progres ───────────────────────────────── --}}
    <div class="overflow-hidden rounded-md" style="border:1px solid #e2e8f0; background:#fff;">
        <div class="px-4 py-3" style="border-bottom:1px solid #f1f5f9;">
            <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-800">
                Riwayat Rekam Progres
            </h3>
            <p class="text-[11px] text-slate-500">
                Log audit catatan persentase kemajuan pekerjaan dari waktu ke waktu.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="erp-table w-full text-left">
                <thead>
                    <tr>
                        <th style="width:130px;">Tanggal Rekam</th>
                        <th class="text-right" style="width:110px;">Progres Fisik</th>
                        <th style="width:110px;">Jenis Entri</th>
                        <th>Catatan Lapangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($history as $entry)
                        <tr>
                            <td class="tabular text-xs text-slate-700 font-medium">
                                {{ $entry->recorded_at->format('d M Y') }}
                            </td>
                            <td class="text-right tabular font-bold text-slate-900">
                                {{ number_format($entry->progress_percentage, 2, ',', '.') }}%
                            </td>
                            <td>
                                @if ($entry->is_correction)
                                    <x-badge variant="warning">Koreksi</x-badge>
                                @else
                                    <x-badge variant="info">Pembaruan</x-badge>
                                @endif
                            </td>
                            <td class="text-xs text-slate-600 whitespace-pre-line">
                                {{ $entry->notes ?: '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-10 text-center text-xs text-slate-400">
                                Belum ada riwayat progres yang dicatat untuk pekerjaan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($history->hasPages())
            <div class="px-4 py-3" style="border-top:1px solid #f1f5f9;">
                {{ $history->links() }}
            </div>
        @endif
    </div>
@endsection

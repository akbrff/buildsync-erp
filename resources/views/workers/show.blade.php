@extends('layouts.app')

@section('title', 'Detail Tenaga Kerja: ' . $worker->name)

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'SDM & Manpower', 'url' => route('workers.index')],
        ['label' => $worker->worker_code],
    ]" />
@endsection

@section('content')
<div class="space-y-4">
    {{-- Page Header --}}
    <x-page-header
        title="{{ $worker->name }}"
        subtitle="Profil kualifikasi spesialisasi tenaga kerja, status penugasan, dan kalkulasi estimasi upah.">
        <x-slot:badges>
            <span class="font-code rounded border border-slate-300 bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-700">
                {{ $worker->worker_code }}
            </span>
            @if ($worker->status === 'active')
                <x-badge variant="success" dot>Aktif</x-badge>
            @else
                <x-badge variant="neutral" dot>Tidak Aktif</x-badge>
            @endif
        </x-slot:badges>

        <x-slot:actions>
            <a href="{{ route('workers.index') }}"
               class="rounded border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors shadow-2xs">
                &larr; Kembali
            </a>
            <a href="{{ route('workers.edit', $worker) }}"
               class="inline-flex items-center gap-1.5 rounded bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-700 transition-colors shadow-2xs">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Tenaga Kerja
            </a>
            <form action="{{ route('workers.destroy', $worker) }}"
                  method="POST"
                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus data tenaga kerja [{{ $worker->worker_code }}] {{ $worker->name }}?');"
                  class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="rounded border border-red-200 bg-white px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50 hover:border-red-300 transition-colors shadow-2xs">
                    Hapus
                </button>
            </form>
        </x-slot:actions>
    </x-page-header>

    {{-- Flash Notifications --}}
    <x-flash-alert />

    {{-- Ringkasan 4 Kartu Metrik SDM --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        <x-stat-card
            label="Tarif Upah Harian"
            :value="'Rp ' . number_format($worker->daily_rate, 0, ',', '.')"
            subtext="Rate standar per hari (8 jam kerja)"
            color="navy"
        />

        <x-stat-card
            label="Status Ketersediaan"
            :value="$worker->status === 'active' ? 'Siap Kerja' : 'Standby'"
            :subtext="$worker->status === 'active' ? 'Aktif dalam roster lapangan' : 'Tidak aktif / istirahat'"
            :color="$worker->status === 'active' ? 'emerald' : 'amber'"
            :badge="$worker->status === 'active' ? 'Aktif' : 'Non-Aktif'"
            :badgeVariant="$worker->status === 'active' ? 'success' : 'neutral'"
        />

        <x-stat-card
            label="Estimasi Mingguan (6 Hari)"
            :value="'Rp ' . number_format($weeklyWageEst, 0, ',', '.')"
            subtext="Beban payroll per minggu kerja"
            color="navy"
        />

        <x-stat-card
            label="Estimasi Bulanan (25 Hari)"
            :value="'Rp ' . number_format($monthlyWageEst, 0, ',', '.')"
            subtext="Proyeksi beban upah bulanan"
            color="navy"
        />
    </div>

    {{-- Profil Lengkap & Kualifikasi --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        {{-- Detail Profil (2 Cols) --}}
        <div class="space-y-4 lg:col-span-2">
            <div class="rounded-md border border-slate-200 bg-white shadow-xs overflow-hidden">
                <div class="border-b border-slate-200 px-4 py-2.5 bg-slate-50 flex items-center justify-between">
                    <h3 class="text-xs font-semibold text-slate-800 uppercase tracking-wider">Biodata & Informasi Kepegawaian</h3>
                    <span class="text-[11px] text-slate-400">ID: #{{ $worker->id }}</span>
                </div>
                <div class="p-4">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-xs">
                        <div class="border-b border-slate-100 pb-2">
                            <dt class="text-slate-400 font-medium uppercase text-[10px] tracking-wider">Nomor Induk Karyawan / Kode</dt>
                            <dd class="mt-0.5 font-code font-semibold text-slate-800 text-sm">{{ $worker->worker_code }}</dd>
                        </div>
                        <div class="border-b border-slate-100 pb-2">
                            <dt class="text-slate-400 font-medium uppercase text-[10px] tracking-wider">Nama Lengkap</dt>
                            <dd class="mt-0.5 font-semibold text-slate-800 text-sm">{{ $worker->name }}</dd>
                        </div>
                        <div class="border-b border-slate-100 pb-2">
                            <dt class="text-slate-400 font-medium uppercase text-[10px] tracking-wider">Jabatan / Spesialisasi Keahlian</dt>
                            <dd class="mt-0.5 font-medium text-slate-800">
                                <span class="inline-flex items-center rounded border border-slate-200 bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-700">
                                    {{ $worker->position }}
                                </span>
                            </dd>
                        </div>
                        <div class="border-b border-slate-100 pb-2">
                            <dt class="text-slate-400 font-medium uppercase text-[10px] tracking-wider">Kontak Telepon / WhatsApp</dt>
                            <dd class="mt-0.5 tabular font-medium text-slate-800">
                                {{ $worker->phone ?: 'Tidak dicantumkan' }}
                            </dd>
                        </div>
                        <div class="border-b border-slate-100 pb-2">
                            <dt class="text-slate-400 font-medium uppercase text-[10px] tracking-wider">Tanggal Terdaftar</dt>
                            <dd class="mt-0.5 text-slate-600 tabular">{{ $worker->created_at ? $worker->created_at->format('d M Y, H:i') : '-' }}</dd>
                        </div>
                        <div class="border-b border-slate-100 pb-2">
                            <dt class="text-slate-400 font-medium uppercase text-[10px] tracking-wider">Pembaruan Terakhir</dt>
                            <dd class="mt-0.5 text-slate-600 tabular">{{ $worker->updated_at ? $worker->updated_at->format('d M Y, H:i') : '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Catatan & Kualifikasi Lapangan (1 Col) --}}
        <div>
            <div class="rounded-md border border-slate-200 bg-white shadow-xs overflow-hidden">
                <div class="border-b border-slate-200 px-4 py-2.5 bg-slate-50">
                    <h3 class="text-xs font-semibold text-slate-800 uppercase tracking-wider">Catatan Kualifikasi & Sertifikasi</h3>
                </div>
                <div class="p-4">
                    @if($worker->notes)
                        <div class="rounded border border-slate-100 bg-slate-50/70 p-3 text-xs leading-relaxed text-slate-700 whitespace-pre-line">
                            {{ $worker->notes }}
                        </div>
                    @else
                        <p class="text-xs text-slate-400 italic">Tidak ada catatan sertifikasi atau keterangan tambahan.</p>
                    @endif

                    <div class="mt-4 pt-3 border-t border-slate-100">
                        <span class="text-[11px] text-slate-400 block mb-2 font-medium uppercase tracking-wider">Aksi Cepat</span>
                        <div class="flex flex-col gap-2">
                            <a href="{{ route('workers.edit', $worker) }}"
                               class="w-full text-center rounded border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                                Edit Biodata & Upah
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

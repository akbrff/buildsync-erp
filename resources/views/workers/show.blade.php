@extends('layouts.app')

@section('title', 'Detail Tenaga Kerja: ' . $worker->name)

@section('breadcrumbs')
    <x-breadcrumb :links="[
        ['label' => 'Dashboard', 'href' => route('dashboard')],
        ['label' => 'SDM & Tenaga Kerja', 'href' => route('workers.index')],
        ['label' => $worker->worker_code, 'href' => '#'],
    ]" />
@endsection

@section('content')
    {{-- Page Header --}}
    <x-page-header
        title="{{ $worker->name }}">
        <x-slot:badges>
            <span class="font-code rounded border border-slate-300 bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-700">
                {{ $worker->worker_code }}
            </span>
            @if ($worker->status === 'active')
                <x-badge variant="success">Aktif</x-badge>
            @else
                <x-badge variant="neutral">Tidak Aktif</x-badge>
            @endif
        </x-slot:badges>

        <x-slot:actions>
            <a href="{{ route('workers.index') }}"
               class="btn-secondary">
                &larr; Kembali
            </a>
            <a href="{{ route('workers.edit', $worker) }}"
               class="btn-primary">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Tenaga Kerja
            </a>
            <form action="{{ route('workers.destroy', $worker) }}"
                  method="POST"
                  onsubmit="return confirm('Yakin ingin menghapus data tenaga kerja ini?');"
                  class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-md border border-red-200 dark:border-red-900/50 bg-red-50 dark:bg-red-950/30 px-3 py-2 text-xs font-semibold text-red-700 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors">
                    Hapus
                </button>
            </form>
        </x-slot:actions>
    </x-page-header>

    {{-- Flash Notifications --}}
    <x-flash-alert />

    {{-- Ringkasan 4 Kartu Metrik SDM --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4 mb-6">
        <x-stat-card
            label="Tarif Upah Harian"
            :value="'Rp ' . number_format($worker->daily_rate, 0, ',', '.')"
            color="navy"
        />

        <x-stat-card
            label="Status Ketersediaan"
            :value="$worker->status === 'active' ? 'Siap Kerja' : 'Standby'"
            :color="$worker->status === 'active' ? 'emerald' : 'amber'"
            :badge="$worker->status === 'active' ? 'Aktif' : 'Non-Aktif'"
            :badgeVariant="$worker->status === 'active' ? 'success' : 'neutral'"
        />

        <x-stat-card
            label="Estimasi Mingguan (6 Hari)"
            :value="'Rp ' . number_format($weeklyWageEst, 0, ',', '.')"
            color="navy"
        />

        <x-stat-card
            label="Estimasi Bulanan (25 Hari)"
            :value="'Rp ' . number_format($monthlyWageEst, 0, ',', '.')"
            color="navy"
        />
    </div>

    {{-- Profil Lengkap & Kualifikasi --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        {{-- Detail Profil (2 Cols) --}}
        <div class="space-y-4 lg:col-span-2">
            <div class="rounded-lg border border-[#E5E7EB] dark:border-[#27272A] bg-white dark:bg-[#18181B] shadow-none overflow-hidden">
                <div class="border-b border-[#E5E7EB] dark:border-[#27272A] px-5 py-3.5 bg-slate-50/50 dark:bg-white/5 flex items-center justify-between">
                    <h3 class="text-[15px] font-bold text-[#1E293B] dark:text-white">Biodata & Informasi Kepegawaian</h3>
                    <span class="text-xs text-[#64748B] dark:text-slate-400 font-mono">ID #{{ $worker->id }}</span>
                </div>
                <div class="p-5">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-xs">
                        <div class="border-b border-slate-100 dark:border-[#27272A] pb-2.5">
                            <dt class="text-[#64748B] dark:text-[#A1A1AA] font-medium text-[13px]">Nomor Induk Karyawan / Kode</dt>
                            <dd class="mt-0.5 font-mono font-semibold text-[#0B2545] dark:text-slate-100 text-sm">{{ $worker->worker_code }}</dd>
                        </div>
                        <div class="border-b border-slate-100 dark:border-[#27272A] pb-2.5">
                            <dt class="text-[#64748B] dark:text-[#A1A1AA] font-medium text-[13px]">Nama Lengkap</dt>
                            <dd class="mt-0.5 font-semibold text-[#0B2545] dark:text-slate-100 text-sm">{{ $worker->name }}</dd>
                        </div>
                        <div class="border-b border-slate-100 dark:border-[#27272A] pb-2.5">
                            <dt class="text-[#64748B] dark:text-[#A1A1AA] font-medium text-[13px]">Jabatan / Spesialisasi Keahlian</dt>
                            <dd class="mt-0.5 font-medium text-[#0B2545] dark:text-slate-200">
                                <span class="inline-flex items-center rounded border border-[#E5E7EB] dark:border-[#27272A] bg-[#F8FAFC] dark:bg-white/5 px-2 py-0.5 text-xs font-medium text-[#0B2545] dark:text-slate-300">
                                    {{ $worker->position }}
                                </span>
                            </dd>
                        </div>
                        <div class="border-b border-slate-100 dark:border-[#27272A] pb-2.5">
                            <dt class="text-[#64748B] dark:text-[#A1A1AA] font-medium text-[13px]">Kontak Telepon / WhatsApp</dt>
                            <dd class="mt-0.5 tabular-nums font-medium text-[#0B2545] dark:text-slate-200">
                                @if($worker->phone)
                                    {{ $worker->formatted_phone ?? $worker->phone }}
                                @else
                                    <span class="text-slate-300 dark:text-slate-600 font-mono">-</span>
                                @endif
                            </dd>
                        </div>
                        <div class="border-b border-slate-100 dark:border-[#27272A] pb-2.5">
                            <dt class="text-[#64748B] dark:text-[#A1A1AA] font-medium text-[13px]">Tanggal Terdaftar</dt>
                            <dd class="mt-0.5 text-[#64748B] dark:text-[#A1A1AA] tabular-nums">
                                @if($worker->created_at)
                                    {{ $worker->created_at->format('d M Y, H:i') }}
                                @else
                                    <span class="text-slate-300 dark:text-slate-600 font-mono">-</span>
                                @endif
                            </dd>
                        </div>
                        <div class="border-b border-slate-100 dark:border-[#27272A] pb-2.5">
                            <dt class="text-[#64748B] dark:text-[#A1A1AA] font-medium text-[13px]">Pembaruan Terakhir</dt>
                            <dd class="mt-0.5 text-[#64748B] dark:text-[#A1A1AA] tabular-nums">
                                @if($worker->updated_at)
                                    {{ $worker->updated_at->format('d M Y, H:i') }}
                                @else
                                    <span class="text-slate-300 dark:text-slate-600 font-mono">-</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Catatan & Kualifikasi Lapangan (1 Col) --}}
        <div>
            <div class="rounded-lg border border-[#E5E7EB] dark:border-[#27272A] bg-white dark:bg-[#18181B] shadow-none overflow-hidden">
                <div class="border-b border-[#E5E7EB] dark:border-[#27272A] px-5 py-3.5 bg-slate-50/50 dark:bg-white/5">
                    <h3 class="text-[15px] font-bold text-[#1E293B] dark:text-white">Catatan Kualifikasi & Sertifikasi</h3>
                </div>
                <div class="p-5">
                    @if($worker->notes)
                        <div class="rounded-md border border-[#E5E7EB] dark:border-[#27272A] bg-slate-50/70 dark:bg-white/5 p-3.5 text-xs leading-relaxed text-slate-700 dark:text-slate-300 whitespace-pre-line">
                            {{ $worker->notes }}
                        </div>
                    @else
                        <p class="text-xs text-slate-400 italic">Tidak ada catatan sertifikasi atau keterangan tambahan.</p>
                    @endif

                    <div class="mt-4 pt-3.5 border-t border-[#E5E7EB] dark:border-[#27272A]">
                        <span class="text-[11px] text-slate-400 block mb-2 font-medium uppercase tracking-wider">Aksi Cepat</span>
                        <div class="flex flex-col gap-2">
                            <a href="{{ route('workers.edit', $worker) }}"
                               class="w-full text-center rounded-md border border-slate-200 dark:border-[#27272A] bg-white dark:bg-[#27272A] px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                Edit Biodata & Upah
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Manajemen Tenaga Kerja & Mandor')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'SDM & Manpower', 'url' => route('workers.index')],
        ['label' => 'Daftar Tenaga Kerja'],
    ]" />
@endsection

@section('content')
<div class="space-y-4">
    {{-- Page Header --}}
    <x-page-header
        title="Manajemen SDM & Tenaga Kerja"
        subtitle="Alokasi mandor lapangan, tukang spesialis, helper proyek, serta kontrol tarif upah harian.">
        <x-slot:actions>
            <a href="{{ route('workers.create') }}"
               class="inline-flex items-center gap-1.5 rounded-md px-3.5 py-1.5 text-xs font-semibold text-white shadow-xs transition-colors"
               style="background-color: var(--erp-navy); border: 1px solid var(--erp-navy);"
               onmouseover="this.style.backgroundColor='#1e293b';"
               onmouseout="this.style.backgroundColor='var(--erp-navy)';">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Tenaga Kerja
            </a>
        </x-slot:actions>
    </x-page-header>

    {{-- Flash Notifications --}}
    <x-flash-alert />

    {{-- Ringkasan KPI SDM --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        <x-stat-card
            label="Total Pekerja Terdaftar"
            :value="$totalWorkers"
            unit="Orang"
            subtext="Database seluruh tenaga kerja proyek"
            color="navy"
        />

        <x-stat-card
            label="Tenaga Kerja Aktif"
            :value="$activeWorkers"
            unit="Orang"
            subtext="Tersedia & aktif penugasan"
            badge="Siap Kerja"
            badgeVariant="success"
            color="emerald"
        />

        <x-stat-card
            label="Standby / Non-Aktif"
            :value="$inactiveWorkers"
            unit="Orang"
            subtext="Menunggu tahapan proyek baru"
            badge="Standby"
            badgeVariant="neutral"
            color="amber"
        />

        <x-stat-card
            label="Estimasi Upah Harian"
            :value="'Rp ' . number_format($totalDailyWages, 0, ',', '.')"
            subtext="Total komitmen payroll per hari aktif"
            color="navy"
        />
    </div>

    {{-- Filter Bar & Table Panel --}}
    <div class="rounded-md border border-slate-200 bg-white shadow-xs">
        {{-- Toolbar: Filter & Search --}}
        <div class="border-b border-slate-200 p-3 bg-slate-50/50">
            <x-filter-bar
                action="{{ route('workers.index') }}"
                :searchable="true"
                searchName="q"
                searchPlaceholder="Cari NIK, nama pekerja, kontak...">

                {{-- Filter Posisi / Keahlian --}}
                <select name="position" class="erp-input" style="width: auto; min-width: 170px;" onchange="this.form.submit()">
                    <option value="">Semua Jabatan / Keahlian</option>
                    @foreach ($positions as $pos)
                        <option value="{{ $pos }}" @selected(request('position') === $pos)>{{ $pos }}</option>
                    @endforeach
                </select>

                {{-- Filter Status --}}
                <select name="status" class="erp-input" style="width: auto; min-width: 130px;" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Tidak Aktif</option>
                </select>
            </x-filter-bar>
        </div>

        {{-- Tabel Tenaga Kerja Berdensitas Tinggi --}}
        <div class="overflow-x-auto">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th class="w-32">NIK / Kode</th>
                        <th>Nama Lengkap</th>
                        <th>Jabatan & Kualifikasi</th>
                        <th>Kontak Telepon</th>
                        <th class="text-right">Upah per Hari</th>
                        <th class="text-center w-28">Status</th>
                        <th class="text-right w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($workers as $worker)
                        <tr class="hover:bg-slate-50/70">
                            {{-- Kode NIK --}}
                            <td class="font-code font-semibold text-slate-700 whitespace-nowrap">
                                <a href="{{ route('workers.show', $worker) }}" class="hover:text-blue-600 hover:underline">
                                    {{ $worker->worker_code }}
                                </a>
                            </td>

                            {{-- Nama Pekerja --}}
                            <td>
                                <div class="font-semibold text-slate-900 leading-snug">
                                    {{ $worker->name }}
                                </div>
                                @if($worker->notes)
                                    <div class="text-[11px] text-slate-400 line-clamp-1 max-w-xs" title="{{ $worker->notes }}">
                                        {{ $worker->notes }}
                                    </div>
                                @endif
                            </td>

                            {{-- Jabatan --}}
                            <td class="whitespace-nowrap">
                                <span class="inline-flex items-center rounded border border-slate-200 bg-slate-50 px-2 py-0.5 text-xs font-medium text-slate-700">
                                    {{ $worker->position }}
                                </span>
                            </td>

                            {{-- No Telepon --}}
                            <td class="tabular whitespace-nowrap text-slate-600">
                                @if($worker->phone)
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        {{ $worker->phone }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">-</span>
                                @endif
                            </td>

                            {{-- Upah Harian --}}
                            <td class="text-right tabular font-bold text-slate-800 whitespace-nowrap">
                                Rp {{ number_format($worker->daily_rate, 0, ',', '.') }}
                            </td>

                            {{-- Status Badge --}}
                            <td class="text-center whitespace-nowrap">
                                @if ($worker->status === 'active')
                                    <x-badge variant="success" dot>Aktif</x-badge>
                                @else
                                    <x-badge variant="neutral" dot>Tidak Aktif</x-badge>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('workers.show', $worker) }}"
                                       class="rounded p-1 text-slate-500 hover:bg-slate-100 hover:text-slate-800 transition-colors"
                                       title="Detail Pekerja">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    <a href="{{ route('workers.edit', $worker) }}"
                                       class="rounded p-1 text-blue-600 hover:bg-blue-50 transition-colors"
                                       title="Edit Pekerja">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>

                                    <form action="{{ route('workers.destroy', $worker) }}"
                                          method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus data tenaga kerja [{{ $worker->worker_code }}] {{ $worker->name }}?');"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="rounded p-1 text-red-500 hover:bg-red-50 hover:text-red-700 transition-colors"
                                                title="Hapus Pekerja">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="h-8 w-8 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <p class="text-xs font-medium text-slate-600">Tidak ada data tenaga kerja yang sesuai filter.</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Ubah kriteria filter atau daftarkan tenaga kerja baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer Pagination --}}
        @if ($workers->hasPages())
            <div class="border-t border-slate-200 px-3.5 py-2 bg-slate-50/50">
                {{ $workers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

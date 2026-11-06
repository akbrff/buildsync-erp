@extends('layouts.app')

@section('title', 'Detail Material: ' . $material->code)

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Logistik & Gudang', 'url' => route('materials.index')],
        ['label' => $material->code],
    ]" />
@endsection

@section('content')
<div class="space-y-4">
    {{-- Page Header --}}
    <x-page-header
        title="{{ $material->name }}"
        subtitle="Spesifikasi teknis, kontrol batas reorder point, dan mutasi keluar masuk gudang.">
        <x-slot:badges>
            <span class="font-code rounded border border-slate-300 bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-700">
                {{ $material->code }}
            </span>
            @if ($statusLevel === 'critical')
                <x-badge variant="danger" dot>Stok Kritis (≤ Min)</x-badge>
            @elseif ($statusLevel === 'warning')
                <x-badge variant="warning" dot>Mendekati Reorder</x-badge>
            @else
                <x-badge variant="success" dot>Stok Aman</x-badge>
            @endif
        </x-slot:badges>

        <x-slot:actions>
            <a href="{{ route('materials.index') }}"
               class="rounded border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors shadow-2xs">
                &larr; Kembali
            </a>
            <a href="{{ route('materials.edit', $material) }}"
               class="inline-flex items-center gap-1.5 rounded bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-700 transition-colors shadow-2xs">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Material
            </a>
            <form action="{{ route('materials.destroy', $material) }}"
                  method="POST"
                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus material [{{ $material->code }}] {{ $material->name }}?');"
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

    {{-- Ringkasan 4 Kartu Metrik --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        <x-stat-card
            label="Stok Fisik Saat Ini"
            :value="number_format($material->stock, 0, ',', '.')"
            :unit="$material->unit"
            :subtext="'Ambang minimum: ' . number_format($material->min_stock, 0, ',', '.') . ' ' . $material->unit"
            :color="$statusLevel === 'critical' ? 'rose' : ($statusLevel === 'warning' ? 'amber' : 'emerald')"
        />

        <x-stat-card
            label="Batas Minimum Reorder"
            :value="number_format($material->min_stock, 0, ',', '.')"
            :unit="$material->unit"
            subtext="Safety buffer pemesanan ulang"
            color="navy"
        />

        <x-stat-card
            label="Harga Satuan"
            :value="'Rp ' . number_format($material->price, 0, ',', '.')"
            :unit="'/ ' . $material->unit"
            subtext="Standar biaya pengadaan"
            color="navy"
        />

        <x-stat-card
            label="Total Valuasi Nilai Stok"
            :value="'Rp ' . number_format($materialValuation, 0, ',', '.')"
            subtext="Akumulasi aset fisik tersimpan"
            color="emerald"
        />
    </div>

    {{-- Content Grid: Detail & Mutasi Cepat --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        {{-- Kolom Kiri: Spesifikasi & Meter Stok (2 Cols) --}}
        <div class="space-y-4 lg:col-span-2">
            {{-- Spesifikasi Lembar Teknis --}}
            <div class="rounded-md border border-slate-200 bg-white shadow-xs overflow-hidden">
                <div class="border-b border-slate-200 px-4 py-2.5 bg-slate-50 flex items-center justify-between">
                    <h3 class="text-xs font-semibold text-slate-800 uppercase tracking-wider">Spesifikasi Lembar Inventaris</h3>
                    <span class="text-[11px] text-slate-400">ID: #{{ $material->id }}</span>
                </div>
                <div class="p-4">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-xs">
                        <div class="border-b border-slate-100 pb-2">
                            <dt class="text-slate-400 font-medium uppercase text-[10px] tracking-wider">Kode SKU / Referensi</dt>
                            <dd class="mt-0.5 font-code font-semibold text-slate-800 text-sm">{{ $material->code }}</dd>
                        </div>
                        <div class="border-b border-slate-100 pb-2">
                            <dt class="text-slate-400 font-medium uppercase text-[10px] tracking-wider">Nama Resmi Material</dt>
                            <dd class="mt-0.5 font-semibold text-slate-800 text-sm">{{ $material->name }}</dd>
                        </div>
                        <div class="border-b border-slate-100 pb-2">
                            <dt class="text-slate-400 font-medium uppercase text-[10px] tracking-wider">Satuan Ukuran Standar</dt>
                            <dd class="mt-0.5 font-medium text-slate-700">{{ $material->unit }}</dd>
                        </div>
                        <div class="border-b border-slate-100 pb-2">
                            <dt class="text-slate-400 font-medium uppercase text-[10px] tracking-wider">Harga Satuan Pengadaan</dt>
                            <dd class="mt-0.5 tabular font-semibold text-slate-800">Rp {{ number_format($material->price, 0, ',', '.') }} / {{ $material->unit }}</dd>
                        </div>
                        <div class="border-b border-slate-100 pb-2">
                            <dt class="text-slate-400 font-medium uppercase text-[10px] tracking-wider">Tanggal Ditambahkan</dt>
                            <dd class="mt-0.5 text-slate-600 tabular">{{ $material->created_at ? $material->created_at->format('d M Y, H:i') : '-' }}</dd>
                        </div>
                        <div class="border-b border-slate-100 pb-2">
                            <dt class="text-slate-400 font-medium uppercase text-[10px] tracking-wider">Pembaruan Terakhir</dt>
                            <dd class="mt-0.5 text-slate-600 tabular">{{ $material->updated_at ? $material->updated_at->format('d M Y, H:i') : '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Meter Tingkat Ketersediaan Stok --}}
            <div class="rounded-md border border-slate-200 bg-white p-4 shadow-xs">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="text-xs font-semibold text-slate-800">Indikator Buffer Stok Fisik vs Ambang Reorder</h4>
                    <span class="text-xs tabular font-bold text-slate-700">
                        {{ $material->stock }} / {{ $material->min_stock }} {{ $material->unit }}
                    </span>
                </div>

                @php
                    $targetMax = max($material->min_stock * 2, $material->stock, 1);
                    $stockPercent = min(100, round(($material->stock / $targetMax) * 100));
                    $minPercent   = min(100, round(($material->min_stock / $targetMax) * 100));
                @endphp

                <div class="relative w-full h-3 rounded bg-slate-100 overflow-hidden border border-slate-200">
                    <div class="h-full transition-all duration-300 {{ $statusLevel === 'critical' ? 'bg-red-500' : ($statusLevel === 'warning' ? 'bg-amber-500' : 'bg-emerald-500') }}"
                         style="width: {{ $stockPercent }}%;"></div>
                </div>

                <div class="flex items-center justify-between mt-2 text-[11px] text-slate-500">
                    <span class="flex items-center gap-1">
                        <span class="inline-block h-2 w-2 rounded-full bg-red-400"></span>
                        Ambang Kritis (≤ {{ $material->min_stock }} {{ $material->unit }})
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="inline-block h-2 w-2 rounded-full bg-amber-400"></span>
                        Buffer Reorder (~{{ round($material->min_stock * 1.2) }} {{ $material->unit }})
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
                        Level Aman
                    </span>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Form Cepat Mutasi Stok Fisik (1 Col) --}}
        <div>
            <div class="rounded-md border border-slate-200 bg-white shadow-xs overflow-hidden">
                <div class="border-b border-slate-200 px-4 py-2.5 bg-slate-50">
                    <h3 class="text-xs font-semibold text-slate-800 uppercase tracking-wider">Catat Mutasi Masuk / Keluar</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Tambah stok kiriman suplier atau kurangi untuk proyek.</p>
                </div>

                <form method="POST" action="{{ route('materials.adjust', $material) }}" class="p-4 space-y-3.5">
                    @csrf

                    <div>
                        <label class="erp-label mb-1.5 block">Tipe Mutasi</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center gap-2 rounded border border-slate-200 p-2 cursor-pointer hover:bg-slate-50 transition-colors">
                                <input type="radio" name="type" value="in" checked class="text-emerald-600 focus:ring-emerald-500">
                                <div>
                                    <span class="text-xs font-semibold text-slate-800 block">Masuk (+)</span>
                                    <span class="text-[10px] text-slate-500">Penerimaan</span>
                                </div>
                            </label>
                            <label class="flex items-center gap-2 rounded border border-slate-200 p-2 cursor-pointer hover:bg-slate-50 transition-colors">
                                <input type="radio" name="type" value="out" class="text-amber-600 focus:ring-amber-500">
                                <div>
                                    <span class="text-xs font-semibold text-slate-800 block">Keluar (-)</span>
                                    <span class="text-[10px] text-slate-500">Pakai Proyek</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="amount" class="erp-label mb-1 block">Jumlah Unit</label>
                        <div class="relative">
                            <input type="number" id="amount" name="amount" min="1" required
                                   class="erp-input pr-14 tabular font-semibold" placeholder="Contoh: 25">
                            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-xs text-slate-400 font-medium">
                                {{ $material->unit }}
                            </span>
                        </div>
                        @error('amount')
                            <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="notes" class="erp-label mb-1 block">No. DO / Surat Jalan / Catatan</label>
                        <textarea id="notes" name="notes" rows="2" class="erp-input" placeholder="Contoh: Surat Jalan SJ-091/Supplier dari gudang utama"></textarea>
                        @error('notes')
                            <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                            class="w-full rounded bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white hover:bg-blue-700 transition-colors shadow-xs">
                        Konfirmasi Mutasi Stok
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

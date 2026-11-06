@extends('layouts.app')

@section('title', 'Edit Material: ' . $material->code)

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Logistik & Gudang', 'url' => route('materials.index')],
        ['label' => $material->code, 'url' => route('materials.show', $material)],
        ['label' => 'Edit Spesifikasi'],
    ]" />
@endsection

@section('content')
<div class="max-w-4xl space-y-4">
    {{-- Page Header --}}
    <x-page-header
        title="Perbarui Data Material"
        subtitle="Ubah kode SKU, nama resmi, harga satuan pengadaan, atau batas reorder point material.">
        <x-slot:badges>
            <span class="font-code rounded border border-slate-300 bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-700">
                {{ $material->code }}
            </span>
        </x-slot:badges>
        <x-slot:actions>
            <a href="{{ route('materials.show', $material) }}"
               class="rounded border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors shadow-2xs">
                &larr; Batal & Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    {{-- Flash Notifications --}}
    <x-flash-alert />

    {{-- Form Container --}}
    <div class="rounded-md border border-slate-200 bg-white shadow-xs overflow-hidden">
        <div class="border-b border-slate-200 px-5 py-3 bg-slate-50 flex items-center justify-between">
            <h3 class="text-xs font-semibold text-slate-800 uppercase tracking-wider">Perubahan Lembar Inventaris Material</h3>
            <span class="text-xs text-slate-400">ID #{{ $material->id }}</span>
        </div>

        <form method="POST" action="{{ route('materials.update', $material) }}" class="p-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Kode SKU --}}
                <div>
                    <label for="code" class="erp-label mb-1 block">Kode Material / SKU <span class="text-red-500">*</span></label>
                    <input type="text" id="code" name="code" value="{{ old('code', $material->code) }}" required
                           class="erp-input font-code uppercase" placeholder="Contoh: MAT-CMT-01">
                    <p class="mt-1 text-[11px] text-slate-400">Kode unik SKU pengenal material di gudang.</p>
                    @error('code')
                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nama Material --}}
                <div>
                    <label for="name" class="erp-label mb-1 block">Nama Material <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $material->name) }}" required
                           class="erp-input" placeholder="Contoh: Semen Portland Komposit (50kg)">
                    <p class="mt-1 text-[11px] text-slate-400">Nama lengkap item beserta spesifikasi ringkas.</p>
                    @error('name')
                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Satuan --}}
                <div>
                    <label for="unit" class="erp-label mb-1 block">Satuan Ukuran <span class="text-red-500">*</span></label>
                    <input type="text" id="unit" name="unit" value="{{ old('unit', $material->unit) }}" required list="units-list"
                           class="erp-input" placeholder="Contoh: Sak, Batang, m3, Pail, Kg, Lembar">
                    <datalist id="units-list">
                        <option value="Sak">
                        <option value="Batang">
                        <option value="m3">
                        <option value="Kg">
                        <option value="Pail">
                        <option value="Lembar">
                        <option value="Roll">
                        <option value="Unit">
                    </datalist>
                    <p class="mt-1 text-[11px] text-slate-400">Satuan dasar perhitungan stok fisik.</p>
                    @error('unit')
                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Harga Satuan --}}
                <div>
                    <label for="price" class="erp-label mb-1 block">Harga Satuan Pengadaan (Rp) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-xs text-slate-400 font-medium">Rp</span>
                        <input type="number" id="price" name="price" value="{{ old('price', (int)$material->price) }}" min="0" step="100" required
                               class="erp-input pl-9 tabular font-medium" placeholder="68000">
                    </div>
                    <p class="mt-1 text-[11px] text-slate-400">Estimasi harga beli per satu satuan unit.</p>
                    @error('price')
                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Stok Saat Ini --}}
                <div>
                    <label for="stock" class="erp-label mb-1 block">Koreksi Stok Fisik Gudang <span class="text-red-500">*</span></label>
                    <input type="number" id="stock" name="stock" value="{{ old('stock', $material->stock) }}" min="0" required
                           class="erp-input tabular font-medium" placeholder="0">
                    <p class="mt-1 text-[11px] text-slate-400">Gunakan fitur <em>Mutasi Masuk/Keluar</em> untuk audit transaksi.</p>
                    @error('stock')
                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Batas Minimal Reorder --}}
                <div>
                    <label for="min_stock" class="erp-label mb-1 block">Batas Minimum Reorder Point <span class="text-red-500">*</span></label>
                    <input type="number" id="min_stock" name="min_stock" value="{{ old('min_stock', $material->min_stock) }}" min="0" required
                           class="erp-input tabular font-medium" placeholder="10">
                    <p class="mt-1 text-[11px] text-slate-400">Ambang batas sistem memunculkan peringatan stok kritis.</p>
                    @error('min_stock')
                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Form Footer Actions --}}
            <div class="mt-6 border-t border-slate-200 pt-4 flex items-center justify-end gap-2.5">
                <a href="{{ route('materials.show', $material) }}"
                   class="rounded border border-slate-200 bg-white px-3.5 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="rounded bg-blue-600 px-4 py-1.5 text-xs font-semibold text-white hover:bg-blue-700 transition-colors shadow-xs">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

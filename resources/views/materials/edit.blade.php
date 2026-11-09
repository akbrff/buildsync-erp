@extends('layouts.app')

@section('title', 'Edit Material: ' . $material->code)

@section('breadcrumbs')
    <x-breadcrumb :links="[
        ['label' => 'Dashboard', 'href' => route('dashboard')],
        ['label' => 'Logistik & Material', 'href' => route('materials.index')],
        ['label' => $material->code, 'href' => route('materials.show', $material)],
        ['label' => 'Edit Spesifikasi', 'href' => '#'],
    ]" />
@endsection

@section('content')
<div class="max-w-4xl">
    {{-- Page Header --}}
    <x-page-header
        title="Perbarui Data Material"
        subtitle="Ubah kode SKU, nama resmi, harga satuan pengadaan, atau batas reorder point material.">
        <x-slot:badges>
            <x-badge variant="neutral">{{ $material->code }}</x-badge>
        </x-slot:badges>
        <x-slot:actions>
            <a href="{{ route('materials.show', $material) }}"
               class="btn-secondary">
                &larr; Batal & Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    {{-- Flash Notifications --}}
    <x-flash-alert />

    {{-- Form Container --}}
    <div class="rounded-2xl border border-slate-100 bg-white shadow-[0_2px_12px_rgba(44,62,80,0.04)] overflow-hidden">
        <div class="border-b border-[#EEF4F8] px-6 py-4 bg-[#F8FBFD] flex items-center justify-between">
            <h3 class="text-[15px] font-bold text-[#2C3E50]">Perubahan Lembar Inventaris Material</h3>
            <span class="text-xs text-[#7F7F7F]">ID #{{ $material->id }}</span>
        </div>

        <form method="POST" action="{{ route('materials.update', $material) }}" class="p-6">
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
                    <x-erp-currency-input name="price" id="price" :value="old('price', (int)$material->price)" required placeholder="68.000" />
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
            <div class="mt-6 border-t border-[#EEF4F8] pt-4 flex items-center justify-end gap-2.5">
                <a href="{{ route('materials.show', $material) }}"
                   class="btn-secondary">
                    Batal
                </a>
                <button type="submit"
                        class="btn-primary">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Tambah Material Baru')

@section('breadcrumbs')
    <x-breadcrumb :links="[
        ['label' => 'Dashboard', 'href' => route('dashboard')],
        ['label' => 'Logistik & Material', 'href' => route('materials.index')],
        ['label' => 'Tambah Material Baru', 'href' => '#'],
    ]" />
@endsection

@section('content')
<div class="max-w-4xl">
    {{-- Page Header --}}
    <x-page-header
        title="Registrasi Item Material Baru"
        subtitle="Daftarkan SKU baru ke katalog logistik, tentukan satuan dan batas aman minimum reorder point.">
        <x-slot:actions>
            <a href="{{ route('materials.index') }}"
               class="inline-flex items-center gap-1.5 rounded border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors shadow-2xs">
                &larr; Batal & Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    {{-- Flash Notifications --}}
    <x-flash-alert />

    {{-- Form Container --}}
    <div class="rounded-md border border-slate-200 bg-white shadow-xs overflow-hidden">
        <div class="border-b border-slate-200 px-5 py-3 bg-slate-50">
            <h3 class="text-xs font-semibold text-slate-600 uppercase tracking-wider">Formulir Spesifikasi & Inventaris Material</h3>
        </div>

        <form method="POST" action="{{ route('materials.store') }}" class="p-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Kode SKU --}}
                <div>
                    <label for="code" class="erp-label mb-1 block">Kode Material / SKU <span class="text-red-500">*</span></label>
                    <input type="text" id="code" name="code" value="{{ old('code') }}" required
                           class="erp-input font-code uppercase" placeholder="Contoh: MAT-CMT-01">
                    <p class="mt-1 text-[11px] text-slate-400">Kode unik SKU pengenal material di gudang.</p>
                    @error('code')
                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nama Material --}}
                <div>
                    <label for="name" class="erp-label mb-1 block">Nama Material <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="erp-input" placeholder="Contoh: Semen Portland Komposit (50kg)">
                    <p class="mt-1 text-[11px] text-slate-400">Nama lengkap item beserta spesifikasi ringkas.</p>
                    @error('name')
                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Satuan --}}
                <div>
                    <label for="unit" class="erp-label mb-1 block">Satuan Ukuran <span class="text-red-500">*</span></label>
                    <input type="text" id="unit" name="unit" value="{{ old('unit') }}" required list="units-list"
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
                    <x-erp-currency-input name="price" id="price" :value="old('price')" required placeholder="Contoh: 68.000" />
                    <p class="mt-1 text-[11px] text-slate-400">Estimasi harga beli per satu satuan unit.</p>
                    @error('price')
                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Stok Awal --}}
                <div>
                    <label for="stock" class="erp-label mb-1 block">Stok Awal Fisik Gudang <span class="text-red-500">*</span></label>
                    <input type="number" id="stock" name="stock" value="{{ old('stock', 0) }}" min="0" required
                           class="erp-input tabular font-medium" placeholder="0">
                    <p class="mt-1 text-[11px] text-slate-400">Jumlah fisik yang sudah tersedia saat registrasi.</p>
                    @error('stock')
                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Batas Minimal Reorder --}}
                <div>
                    <label for="min_stock" class="erp-label mb-1 block">Batas Minimum Reorder Point <span class="text-red-500">*</span></label>
                    <input type="number" id="min_stock" name="min_stock" value="{{ old('min_stock', 10) }}" min="0" required
                           class="erp-input tabular font-medium" placeholder="10">
                    <p class="mt-1 text-[11px] text-slate-400">Ambang peringatan sistem saat stok mencapai batas ini.</p>
                    @error('min_stock')
                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Form Footer Actions --}}
            <div class="mt-6 border-t border-slate-200 pt-4 flex items-center justify-end gap-2.5">
                <a href="{{ route('materials.index') }}"
                   class="rounded border border-slate-200 bg-white px-3.5 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="rounded bg-blue-600 px-4 py-1.5 text-xs font-semibold text-white hover:bg-blue-700 transition-colors shadow-xs">
                    Simpan Material Baru
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

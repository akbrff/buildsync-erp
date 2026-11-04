@extends('layouts.app')

@section('title', 'Tambah Material')

@section('content')
    <div class="mb-6">
        <a href="{{ route('materials.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Kembali</a>
        <h2 class="mt-2 text-xl font-bold text-slate-800">Tambah Material Baru</h2>
    </div>

    <div class="rounded-lg bg-white p-6 shadow-sm border border-slate-200 max-w-2xl">
        <form method="POST" action="{{ route('materials.store') }}" class="grid gap-5 sm:grid-cols-2">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-slate-700">Kode Material</label>
                <input type="text" name="code" value="{{ old('code') }}" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Contoh: MAT-001">
                @error('code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Nama Material</label>
                <input type="text" name="name" value="{{ old('name') }}" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Contoh: Semen Portland">
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Satuan</label>
                <input type="text" name="unit" value="{{ old('unit') }}" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Contoh: Sak, m3, Batang">
                @error('unit')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Harga Satuan (Rp)</label>
                <input type="number" name="price" value="{{ old('price') }}" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Contoh: 65000">
                @error('price')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Stok Saat Ini</label>
                <input type="number" name="stock" value="{{ old('stock', 0) }}" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                @error('stock')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Batas Minimal Stok</label>
                <input type="number" name="min_stock" value="{{ old('min_stock', 5) }}" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                @error('min_stock')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="sm:col-span-2 flex justify-end mt-2">
                <button type="submit" class="rounded-md bg-blue-600 px-6 py-2 text-sm font-medium text-white hover:bg-blue-700">Simpan Material</button>
            </div>
        </form>
    </div>
@endsection
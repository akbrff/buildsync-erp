@extends('layouts.app')

@section('title', 'Detail Material')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <a href="{{ route('materials.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Kembali</a>
            <h2 class="mt-2 text-xl font-bold text-slate-800">{{ $material->name }}</h2>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('materials.edit', $material) }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium hover:bg-slate-50">Edit</a>
            <form action="{{ route('materials.destroy', $material) }}" method="POST" onsubmit="return confirm('Hapus material ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Hapus</button>
            </form>
        </div>
    </div>

    <div class="rounded-lg bg-white p-6 shadow-sm border border-slate-200">
        <dl class="grid gap-5 sm:grid-cols-2">
            <div>
                <dt class="text-xs uppercase text-slate-500">Kode Material</dt>
                <dd class="mt-1 text-sm text-slate-800">{{ $material->code }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-slate-500">Satuan</dt>
                <dd class="mt-1 text-sm text-slate-800">{{ $material->unit }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-slate-500">Harga Satuan</dt>
                <dd class="mt-1 text-sm text-slate-800">Rp {{ number_format($material->price, 0, ',', '.') }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-slate-500">Stok Saat Ini</dt>
                <dd class="mt-1 text-sm font-bold {{ $material->stock <= $material->min_stock ? 'text-red-600' : 'text-slate-800' }}">
                    {{ $material->stock }} {{ $material->unit }}
                    @if($material->stock <= $material->min_stock)
                        <span class="ml-2 text-xs text-red-500">(Di bawah batas minimal)</span>
                    @endif
                </dd>
            </div>
        </dl>
    </div>
@endsection
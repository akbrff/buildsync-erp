@extends('layouts.app')

@section('title', 'Material & Inventaris')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Material & Inventaris</h2>
            <p class="mt-1 text-sm text-slate-500">Kelola stok dan harga material proyek.</p>
        </div>
        <a href="{{ route('materials.create') }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Tambah Material</a>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-md bg-green-100 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <div class="rounded-lg bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3">Kode</th>
                        <th class="px-6 py-3">Nama Material</th>
                        <th class="px-6 py-3">Harga Satuan</th>
                        <th class="px-6 py-3">Stok</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($materials as $material)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-medium">{{ $material->code }}</td>
                            <td class="px-6 py-4">
                                {{ $material->name }}
                                @if($material->stock <= $material->min_stock)
                                    <span class="ml-2 rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-600">Stok Menipis!</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">Rp {{ number_format($material->price, 0, ',', '.') }} / {{ $material->unit }}</td>
                            <td class="px-6 py-4">{{ $material->stock }} {{ $material->unit }}</td>
                            <td class="px-6 py-4 text-right flex justify-end items-center gap-3">
                                <a href="{{ route('materials.show', $material) }}" class="text-slate-600 hover:underline">Detail</a>
                                <a href="{{ route('materials.edit', $material) }}" class="text-blue-600 hover:underline">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-500">Belum ada data material.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 px-6 py-4">
            {{ $materials->links() }}
        </div>
    </div>
@endsection
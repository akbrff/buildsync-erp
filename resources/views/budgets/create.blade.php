@extends('layouts.app')

@section('title', 'Catat Biaya')

@section('content')
    <div class="mb-6">
        <a href="{{ route('budgets.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Kembali</a>
        <h2 class="mt-2 text-xl font-bold text-slate-800">Catat Pengeluaran Biaya</h2>
    </div>

    <div class="rounded-lg bg-white p-6 shadow-sm border border-slate-200 max-w-2xl">
        <form method="POST" action="{{ route('budgets.store') }}" class="grid gap-5 sm:grid-cols-2">
            @csrf
            
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-slate-700">Proyek</label>
                <select name="project_id" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">-- Pilih Proyek --</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}" @selected(old('project_id', $selectedProjectId) == $project->id)>
                            {{ $project->project_code }} - {{ $project->name }}
                        </option>
                    @endforeach
                </select>
                @error('project_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Tanggal Pengeluaran</label>
                <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                @error('date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Kategori Biaya</label>
                <select name="category" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">-- Pilih Kategori --</option>
                    <option value="Material" @selected(old('category') == 'Material')>Material</option>
                    <option value="Upah Tenaga Kerja" @selected(old('category') == 'Upah Tenaga Kerja')>Upah Tenaga Kerja</option>
                    <option value="Sewa Alat" @selected(old('category') == 'Sewa Alat')>Sewa Alat</option>
                    <option value="Operasional" @selected(old('category') == 'Operasional')>Operasional</option>
                </select>
                @error('category')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Nominal (Rp)</label>
                <input type="number" name="amount" value="{{ old('amount') }}" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Contoh: 1500000">
                @error('amount')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Keterangan</label>
                <input type="text" name="description" value="{{ old('description') }}" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Contoh: Pembelian 20 sak semen">
                @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="sm:col-span-2 flex justify-end mt-2">
                <button type="submit" class="rounded-md bg-blue-600 px-6 py-2 text-sm font-medium text-white hover:bg-blue-700">Simpan Pengeluaran</button>
            </div>
        </form>
    </div>
@endsection
@extends('layouts.app')

@section('title', 'Edit Biaya')

@section('content')
    <div class="mb-6">
        <a href="{{ route('budgets.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Kembali</a>
        <h2 class="mt-2 text-xl font-bold text-slate-800">Edit Pengeluaran Biaya</h2>
    </div>

    <div class="rounded-lg bg-white p-6 shadow-sm border border-slate-200 max-w-2xl">
        <form method="POST" action="{{ route('budgets.update', $budget) }}" class="grid gap-5 sm:grid-cols-2">
            @csrf
            @method('PUT')
            
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-slate-700">Proyek</label>
                <select name="project_id" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}" @selected(old('project_id', $budget->project_id) == $project->id)>
                            {{ $project->project_code }} - {{ $project->name }}
                        </option>
                    @endforeach
                </select>
                @error('project_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Tanggal Pengeluaran</label>
                <input type="date" name="date" value="{{ old('date', $budget->date->toDateString()) }}" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                @error('date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Kategori Biaya</label>
                <select name="category" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                    @foreach(['Material', 'Upah Tenaga Kerja', 'Sewa Alat', 'Operasional'] as $cat)
                        <option value="{{ $cat }}" @selected(old('category', $budget->category) == $cat)>{{ $cat }}</option>
                    @endforeach
                </select>
                @error('category')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Nominal (Rp)</label>
                <input type="number" name="amount" value="{{ old('amount', $budget->amount) }}" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                @error('amount')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Keterangan</label>
                <input type="text" name="description" value="{{ old('description', $budget->description) }}" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="sm:col-span-2 flex justify-end mt-2">
                <button type="submit" class="rounded-md bg-blue-600 px-6 py-2 text-sm font-medium text-white hover:bg-blue-700">Perbarui Pengeluaran</button>
            </div>
        </form>
    </div>
@endsection
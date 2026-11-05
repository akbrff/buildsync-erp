@extends('layouts.app')

@section('title', 'Detail Biaya')

@section('content')
    <div class="mb-6">
        <a href="{{ route('budgets.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Kembali</a>
        <h2 class="mt-2 text-xl font-bold text-slate-800">Detail Pengeluaran Biaya</h2>
    </div>

    <div class="rounded-lg bg-white p-6 shadow-sm border border-slate-200 max-w-2xl">
        <dl class="grid gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <dt class="text-xs uppercase text-slate-500">Proyek</dt>
                <dd class="mt-1 text-lg font-bold text-blue-600">
                    <a href="{{ route('projects.show', $budget->project) }}" class="hover:underline">{{ $budget->project->project_code }} - {{ $budget->project->name }}</a>
                </dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-slate-500">Tanggal</dt>
                <dd class="mt-1 text-sm text-slate-800">{{ $budget->date->format('d M Y') }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-slate-500">Kategori</dt>
                <dd class="mt-1 text-sm text-slate-800">{{ $budget->category }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-xs uppercase text-slate-500">Keterangan</dt>
                <dd class="mt-1 text-sm text-slate-800">{{ $budget->description }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-xs uppercase text-slate-500">Nominal</dt>
                <dd class="mt-1 text-2xl font-bold text-red-600">Rp {{ number_format($budget->amount, 0, ',', '.') }}</dd>
            </div>
        </dl>
    </div>
@endsection
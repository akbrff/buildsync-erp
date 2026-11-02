@extends('layouts.app')

@section('title', 'Tambah Tenaga Kerja')

@section('content')
    <div class="mb-6">
        <a href="{{ route('workers.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Kembali ke daftar tenaga kerja</a>
        <h2 class="mt-2 text-xl font-bold text-slate-800">Tambah Tenaga Kerja</h2>
        <p class="mt-1 text-sm text-slate-500">Masukkan informasi tenaga kerja baru.</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-md bg-red-100 px-4 py-3 text-sm text-red-700">
            <p class="font-medium">Data belum lengkap atau tidak valid.</p>
        </div>
    @endif

    <form action="{{ route('workers.store') }}" method="POST" class="rounded-lg bg-white p-6 shadow-sm">
        @csrf
        @include('workers.partials.form', ['worker' => null])
        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('workers.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium hover:bg-slate-50">Batal</a>
            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Simpan Tenaga Kerja</button>
        </div>
    </form>
@endsection
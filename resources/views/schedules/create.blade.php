@extends('layouts.app')

@section('title', 'Tambah Jadwal')

@section('content')
    <div class="mb-6">
        <a href="{{ route('schedules.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Kembali ke daftar jadwal</a>
        <h2 class="mt-2 text-xl font-bold text-slate-800">Tambah Jadwal Pekerjaan</h2>
        <p class="mt-1 text-sm text-slate-500">Masukkan tahapan pekerjaan baru untuk sebuah proyek.</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-md bg-red-100 px-4 py-3 text-sm text-red-700">
            <p class="font-medium">Data belum lengkap atau tidak valid.</p>
        </div>
    @endif

    <form action="{{ route('schedules.store') }}" method="POST" class="rounded-lg bg-white p-6 shadow-sm">
        @csrf
        @include('schedules.partials.form', ['schedule' => null])
        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('schedules.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium hover:bg-slate-50">Batal</a>
            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Simpan Jadwal</button>
        </div>
    </form>
@endsection
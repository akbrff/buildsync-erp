@extends('layouts.app')

@section('title', 'Tambah Proyek')

@section('breadcrumbs')
<x-breadcrumb :links="[
    ['label' => 'Dashboard', 'href' => route('dashboard')],
    ['label' => 'Proyek',    'href' => route('projects.index')],
    ['label' => 'Tambah Proyek', 'href' => '#'],
]"/>
@endsection

@section('content')
    <x-page-header
        title="Tambah Proyek Baru"
        subtitle="Daftarkan proyek konstruksi baru dan tentukan pagu kontrak awal.">
        <a href="{{ route('projects.index') }}"
           class="btn-secondary">
            &larr; Kembali
        </a>
    </x-page-header>

    <x-flash-alert/>

    <div class="max-w-4xl rounded-2xl p-6 border border-slate-100 bg-white shadow-[0_2px_12px_rgba(44,62,80,0.04)]">
        <form action="{{ route('projects.store') }}" method="POST">
            @csrf
            @include('projects.partials.form', ['project' => null])

            <div class="mt-6 flex items-center justify-end gap-2.5 pt-4 border-t border-[#EEF4F8]">
                <a href="{{ route('projects.index') }}"
                   class="btn-secondary">
                    Batal
                </a>
                <button type="submit"
                        class="btn-primary">
                    Simpan Proyek
                </button>
            </div>
        </form>
    </div>
@endsection

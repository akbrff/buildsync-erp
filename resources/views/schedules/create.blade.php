@extends('layouts.app')

@section('title', 'Tambah Jadwal Pekerjaan')

@section('breadcrumbs')
<x-breadcrumb :links="[
    ['label' => 'Dashboard',     'href' => route('dashboard')],
    ['label' => 'Jadwal Proyek', 'href' => route('schedules.index')],
    ['label' => 'Tambah Jadwal', 'href' => '#'],
]"/>
@endsection

@section('content')
    <x-page-header
        title="Tambah Jadwal Pekerjaan"
        subtitle="Alokasikan tahapan pekerjaan baru dan tentukan bobot kontribusi terhadap total proyek.">
        <a href="{{ route('schedules.index', $selectedProjectId ? ['project_id' => $selectedProjectId] : []) }}"
           class="btn-secondary">
            &larr; Batal & Kembali
        </a>
    </x-page-header>

    <x-flash-alert/>

    <div class="max-w-4xl rounded-2xl p-6 border border-slate-100 bg-white shadow-[0_2px_12px_rgba(44,62,80,0.04)]">
        <form action="{{ route('schedules.store') }}" method="POST">
            @csrf
            @include('schedules.partials.form', ['schedule' => null])

            <div class="mt-6 flex items-center justify-end gap-2.5 pt-4 border-t border-[#EEF4F8]">
                <a href="{{ route('schedules.index', $selectedProjectId ? ['project_id' => $selectedProjectId] : []) }}"
                   class="btn-secondary">
                    Batal
                </a>
                <button type="submit"
                        class="btn-primary">
                    Simpan Jadwal
                </button>
            </div>
        </form>
    </div>
@endsection

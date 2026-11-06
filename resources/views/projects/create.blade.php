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
           class="inline-flex items-center gap-1.5 rounded px-3 font-medium transition-colors"
           style="height:2.25rem; font-size:.8125rem; border:1px solid #e2e8f0; background:#fff; color:#475569;"
           onmouseover="this.style.background='#f8fafc';" onmouseout="this.style.background='#fff';">
            &larr; Kembali
        </a>
    </x-page-header>

    <x-flash-alert/>

    <div class="max-w-4xl rounded-md p-5" style="border:1px solid #e2e8f0; background:#fff;">
        <form action="{{ route('projects.store') }}" method="POST">
            @csrf
            @include('projects.partials.form', ['project' => null])

            <div class="mt-6 flex items-center justify-end gap-2.5 pt-4" style="border-top:1px solid #f1f5f9;">
                <a href="{{ route('projects.index') }}"
                   class="inline-flex items-center justify-center rounded px-3.5 font-medium transition-colors"
                   style="height:2.25rem; font-size:.8125rem; border:1px solid #cbd5e1; background:#fff; color:#475569;"
                   onmouseover="this.style.background='#f8fafc';" onmouseout="this.style.background='#fff';">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center justify-center rounded px-4 font-semibold text-white transition-colors"
                        style="height:2.25rem; font-size:.8125rem; background:#2563eb; border:1px solid #1d4ed8;"
                        onmouseover="this.style.background='#1d4ed8';" onmouseout="this.style.background='#2563eb';">
                    Simpan Proyek
                </button>
            </div>
        </form>
    </div>
@endsection

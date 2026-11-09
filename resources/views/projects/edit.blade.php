@extends('layouts.app')

@section('title', 'Edit Proyek — ' . $project->project_code)

@section('breadcrumbs')
<x-breadcrumb :links="[
    ['label' => 'Dashboard', 'href' => route('dashboard')],
    ['label' => 'Proyek',    'href' => route('projects.index')],
    ['label' => $project->name, 'href' => route('projects.show', $project)],
    ['label' => 'Edit',      'href' => '#'],
]"/>
@endsection

@section('content')
    <x-page-header
        title="Edit Proyek: {{ $project->name }}"
        subtitle="Perbarui data spesifikasi, lokasi, atau pagu anggaran proyek {{ $project->project_code }}.">
        <a href="{{ route('projects.show', $project) }}"
           class="btn-secondary">
            &larr; Kembali ke Detail
        </a>
    </x-page-header>

    <x-flash-alert/>

    <div class="max-w-4xl rounded-2xl p-6 border border-slate-100 bg-white shadow-[0_2px_12px_rgba(44,62,80,0.04)]">
        <form action="{{ route('projects.update', $project) }}" method="POST">
            @csrf
            @method('PUT')
            @include('projects.partials.form', ['project' => $project])

            <div class="mt-6 flex items-center justify-end gap-2.5 pt-4 border-t border-[#EEF4F8]">
                <a href="{{ route('projects.show', $project) }}"
                   class="btn-secondary">
                    Batal
                </a>
                <button type="submit"
                        class="btn-primary">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection

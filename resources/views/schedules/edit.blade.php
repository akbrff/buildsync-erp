@extends('layouts.app')

@section('title', 'Edit Jadwal: ' . $schedule->name)

@section('breadcrumbs')
<x-breadcrumb :links="[
    ['label' => 'Dashboard',     'href' => route('dashboard')],
    ['label' => 'Jadwal Proyek', 'href' => route('schedules.index', ['project_id' => $schedule->project_id])],
    ['label' => $schedule->name, 'href' => route('schedules.show', $schedule)],
    ['label' => 'Edit',          'href' => '#'],
]"/>
@endsection

@section('content')
    <x-page-header
        title="Edit Jadwal: {{ $schedule->name }}"
        subtitle="Perbarui target waktu, alokasi bobot, atau deskripsi pekerjaan untuk {{ $schedule->project->project_code }}.">
        <a href="{{ route('schedules.show', $schedule) }}"
           class="btn-secondary">
            &larr; Kembali ke Detail
        </a>
    </x-page-header>

    <x-flash-alert/>

    <div class="max-w-4xl rounded-2xl p-6 border border-slate-100 bg-white shadow-[0_2px_12px_rgba(44,62,80,0.04)]">
        <form action="{{ route('schedules.update', $schedule) }}" method="POST">
            @csrf
            @method('PUT')
            @include('schedules.partials.form', ['schedule' => $schedule])

            <div class="mt-6 flex items-center justify-end gap-2.5 pt-4 border-t border-[#EEF4F8]">
                <a href="{{ route('schedules.show', $schedule) }}"
                   class="btn-secondary">
                    Batal
                </a>
                <button type="submit"
                        class="btn-primary">
                    Perbarui Jadwal
                </button>
            </div>
        </form>
    </div>
@endsection

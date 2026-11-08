@extends('layouts.app')

@section('title', 'Edit Tenaga Kerja: ' . $worker->name)

@section('breadcrumbs')
    <x-breadcrumb :links="[
        ['label' => 'Dashboard', 'href' => route('dashboard')],
        ['label' => 'SDM & Tenaga Kerja', 'href' => route('workers.index')],
        ['label' => $worker->worker_code, 'href' => route('workers.show', $worker)],
        ['label' => 'Edit Biodata', 'href' => '#'],
    ]" />
@endsection

@section('content')
<div class="max-w-4xl">
    {{-- Page Header --}}
    <x-page-header
        title="Perbarui Data Tenaga Kerja"
        subtitle="Ubah informasi kualifikasi, penyesuaian tarif upah harian, kontak telepon, atau status penugasan.">
        <x-slot:badges>
            <span class="font-code rounded border border-slate-300 bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-700">
                {{ $worker->worker_code }}
            </span>
        </x-slot:badges>
        <x-slot:actions>
            <a href="{{ route('workers.show', $worker) }}"
               class="inline-flex items-center gap-1.5 rounded border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors shadow-2xs">
                &larr; Batal & Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    {{-- Flash Notifications --}}
    <x-flash-alert />

    {{-- Form Container --}}
    <div class="rounded-md border border-slate-200 bg-white shadow-xs overflow-hidden">
        <div class="border-b border-slate-200 px-5 py-3 bg-slate-50 flex items-center justify-between">
            <h3 class="text-xs font-semibold text-slate-600 uppercase tracking-wider">Perubahan Biodata & Tarif Upah</h3>
            <span class="text-xs text-slate-400">ID #{{ $worker->id }}</span>
        </div>

        <form action="{{ route('workers.update', $worker) }}" method="POST" class="p-5">
            @csrf
            @method('PUT')

            @include('workers.partials.form', ['worker' => $worker])

            {{-- Form Footer Actions --}}
            <div class="mt-6 border-t border-slate-200 pt-4 flex items-center justify-end gap-2.5">
                <a href="{{ route('workers.show', $worker) }}"
                   class="rounded border border-slate-200 bg-white px-3.5 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="rounded bg-blue-600 px-4 py-1.5 text-xs font-semibold text-white hover:bg-blue-700 transition-colors shadow-xs">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

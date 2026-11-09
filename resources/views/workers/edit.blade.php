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
            <x-badge variant="neutral">{{ $worker->worker_code }}</x-badge>
        </x-slot:badges>
        <x-slot:actions>
            <a href="{{ route('workers.show', $worker) }}"
               class="btn-secondary">
                &larr; Batal & Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    {{-- Flash Notifications --}}
    <x-flash-alert />

    {{-- Form Container --}}
    <div class="rounded-2xl border border-slate-100 bg-white shadow-[0_2px_12px_rgba(44,62,80,0.04)] overflow-hidden">
        <div class="border-b border-[#EEF4F8] px-6 py-4 bg-[#F8FBFD] flex items-center justify-between">
            <h3 class="text-[15px] font-bold text-[#2C3E50]">Perubahan Biodata & Tarif Upah</h3>
            <span class="text-xs text-[#7F7F7F]">ID #{{ $worker->id }}</span>
        </div>

        <form action="{{ route('workers.update', $worker) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            @include('workers.partials.form', ['worker' => $worker])

            {{-- Form Footer Actions --}}
            <div class="mt-6 border-t border-[#EEF4F8] pt-4 flex items-center justify-end gap-2.5">
                <a href="{{ route('workers.show', $worker) }}"
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
</div>
@endsection

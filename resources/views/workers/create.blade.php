@extends('layouts.app')

@section('title', 'Tambah Tenaga Kerja Baru')

@section('breadcrumbs')
    <x-breadcrumb :links="[
        ['label' => 'Dashboard', 'href' => route('dashboard')],
        ['label' => 'SDM & Tenaga Kerja', 'href' => route('workers.index')],
        ['label' => 'Tambah Tenaga Kerja', 'href' => '#'],
    ]" />
@endsection

@section('content')
<div class="max-w-4xl">
    {{-- Page Header --}}
    <x-page-header
        title="Registrasi Tenaga Kerja Baru"
        subtitle="Daftarkan profil pekerja, tentukan jabatan atau spesialisasi, tarif upah harian, serta status keaktifan.">
        <x-slot:actions>
            <a href="{{ route('workers.index') }}"
               class="btn-secondary">
                &larr; Batal & Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    {{-- Flash Notifications --}}
    <x-flash-alert />

    {{-- Form Container --}}
    <div class="rounded-2xl border border-slate-100 bg-white shadow-[0_2px_12px_rgba(44,62,80,0.04)] overflow-hidden">
        <div class="border-b border-[#EEF4F8] px-6 py-4 bg-[#F8FBFD]">
            <h3 class="text-[15px] font-bold text-[#2C3E50]">Formulir Data Tenaga Kerja Konstruksi</h3>
        </div>

        <form action="{{ route('workers.store') }}" method="POST" class="p-6">
            @csrf

            @include('workers.partials.form', ['worker' => null])

            {{-- Form Footer Actions --}}
            <div class="mt-6 border-t border-[#EEF4F8] pt-4 flex items-center justify-end gap-2.5">
                <a href="{{ route('workers.index') }}"
                   class="btn-secondary">
                    Batal
                </a>
                <button type="submit"
                        class="btn-primary">
                    Simpan Tenaga Kerja
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

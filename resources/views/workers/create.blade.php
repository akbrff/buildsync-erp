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
        title="Registrasi Tenaga Kerja Baru">
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
    <div class="rounded-lg border border-[#E5E7EB] dark:border-[#27272A] bg-white dark:bg-[#18181B] shadow-none overflow-hidden">
        <div class="border-b border-[#E5E7EB] dark:border-[#27272A] px-5 py-3.5 bg-[#F4F8FB] dark:bg-[#27272A]">
            <h3 class="text-base font-bold text-[#1E293B] dark:text-white">Formulir Data Tenaga Kerja Konstruksi</h3>
        </div>

        <form action="{{ route('workers.store') }}" method="POST" class="p-5 sm:p-6">
            @csrf

            @include('workers.partials.form', ['worker' => null])

            {{-- Form Footer Actions --}}
            <div class="mt-8 border-t border-[#E5E7EB] dark:border-[#27272A] pt-6 flex items-center justify-end gap-3">
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

@extends('layouts.app')

@section('title', 'Tambah Tenaga Kerja Baru')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'SDM & Manpower', 'url' => route('workers.index')],
        ['label' => 'Tambah Tenaga Kerja'],
    ]" />
@endsection

@section('content')
<div class="max-w-4xl space-y-4">
    {{-- Page Header --}}
    <x-page-header
        title="Registrasi Tenaga Kerja Baru"
        subtitle="Daftarkan profil pekerja, tentukan jabatan atau spesialisasi, tarif upah harian, serta status keaktifan.">
        <x-slot:actions>
            <a href="{{ route('workers.index') }}"
               class="rounded border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors shadow-2xs">
                &larr; Batal & Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    {{-- Flash Notifications --}}
    <x-flash-alert />

    {{-- Form Container --}}
    <div class="rounded-md border border-slate-200 bg-white shadow-xs overflow-hidden">
        <div class="border-b border-slate-200 px-5 py-3 bg-slate-50">
            <h3 class="text-xs font-semibold text-slate-800 uppercase tracking-wider">Formulir Data Tenaga Kerja Konstruksi</h3>
        </div>

        <form action="{{ route('workers.store') }}" method="POST" class="p-5">
            @csrf

            @include('workers.partials.form', ['worker' => null])

            {{-- Form Footer Actions --}}
            <div class="mt-6 border-t border-slate-200 pt-4 flex items-center justify-end gap-2.5">
                <a href="{{ route('workers.index') }}"
                   class="rounded border border-slate-200 bg-white px-3.5 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="rounded bg-blue-600 px-4 py-1.5 text-xs font-semibold text-white hover:bg-blue-700 transition-colors shadow-xs">
                    Simpan Tenaga Kerja
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

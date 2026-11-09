@extends('layouts.app')

@section('title', 'Manajemen Proyek')

@section('breadcrumbs')
<x-breadcrumb :links="[
    ['label' => 'Dashboard', 'href' => route('dashboard')],
    ['label' => 'Proyek',    'href' => route('projects.index')],
]"/>
@endsection

@section('content')

    <x-page-header
        title="Daftar Proyek"
        subtitle="Kelola seluruh data proyek konstruksi.">
        <a href="{{ route('projects.create') }}"
           class="btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Proyek
        </a>
    </x-page-header>

    <x-flash-alert/>

    {{-- ── Data Table ─────────────────────────────────────────── --}}
    <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-[0_2px_12px_rgba(44,62,80,0.04)]">
        <div class="overflow-x-auto">
            <table class="erp-table w-full text-left">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Proyek</th>
                        <th>Lokasi</th>
                        <th>Tanggal Mulai</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($projects as $project)
                        @php
                            $statusVariant = match ($project->status) {
                                'ongoing'   => 'info',
                                'completed' => 'success',
                                'planning'  => 'neutral',
                                'on_hold'   => 'warning',
                                default     => 'neutral',
                            };
                            $statusLabel = match ($project->status) {
                                'ongoing'   => 'Berjalan',
                                'completed' => 'Selesai',
                                'planning'  => 'Perencanaan',
                                'on_hold'   => 'Ditunda',
                                default     => ucfirst(str_replace('_', ' ', $project->status)),
                            };
                        @endphp
                        <tr>
                            <td>
                                <span class="font-code text-[11px] font-semibold text-[#7F7F7F]">
                                    {{ $project->project_code }}
                                </span>
                            </td>
                            <td class="text-[13px] font-semibold text-[#2C3E50]">
                                {{ $project->name }}
                            </td>
                            <td class="text-xs text-[#7F7F7F]">
                                @if($project->location)
                                    {{ $project->location }}
                                @else
                                    <span class="text-slate-300 font-mono">-</span>
                                @endif
                            </td>
                            <td class="tabular text-xs text-[#7F7F7F]">
                                {{ $project->start_date->format('d M Y') }}
                            </td>
                            <td>
                                <x-badge :variant="$statusVariant">{{ $statusLabel }}</x-badge>
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <a href="{{ route('projects.show', $project) }}"
                                       class="text-xs font-semibold text-[#136F9A] hover:underline">Detail</a>
                                    <a href="{{ route('projects.edit', $project) }}"
                                       class="text-xs font-semibold text-[#ED7132] hover:underline">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-sm text-[#7F7F7F]">
                                Belum ada data proyek.
                                <br>
                                <a href="{{ route('projects.create') }}"
                                   class="mt-1.5 inline-block text-xs font-semibold text-[#136F9A] hover:underline">+ Buat proyek pertama</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($projects->hasPages())
            <div class="px-6 py-4 border-t border-[#EEF4F8]">
                {{ $projects->links() }}
            </div>
        @endif
    </div>
@endsection

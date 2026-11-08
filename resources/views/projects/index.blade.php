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
           class="inline-flex items-center gap-1.5 rounded px-3 font-semibold text-white transition-colors"
           style="height:2.25rem; font-size:.8125rem; background:#2563eb; border:1px solid #1d4ed8;"
           onmouseover="this.style.background='#1d4ed8';" onmouseout="this.style.background='#2563eb';">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Proyek
        </a>
    </x-page-header>

    <x-flash-alert/>

    {{-- ── Data Table ─────────────────────────────────────────── --}}
    <div class="overflow-hidden rounded-md" style="border:1px solid #e2e8f0; background:#fff;">
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
                                <span class="font-code text-[11px] font-semibold" style="color:#64748b;">
                                    {{ $project->project_code }}
                                </span>
                            </td>
                            <td class="text-[13px] font-medium text-slate-900">
                                {{ $project->name }}
                            </td>
                            <td class="text-xs text-slate-600">
                                @if($project->location)
                                    {{ $project->location }}
                                @else
                                    <span class="text-slate-300 font-mono">-</span>
                                @endif
                            </td>
                            <td class="tabular text-xs" style="color:#475569;">
                                {{ $project->start_date->format('d M Y') }}
                            </td>
                            <td>
                                <x-badge :variant="$statusVariant">{{ $statusLabel }}</x-badge>
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <a href="{{ route('projects.show', $project) }}"
                                       class="text-xs font-medium transition-colors"
                                       style="color:#2563eb;"
                                       onmouseover="this.style.textDecoration='underline';"
                                       onmouseout="this.style.textDecoration='none';">Detail</a>
                                    <a href="{{ route('projects.edit', $project) }}"
                                       class="text-xs font-medium transition-colors"
                                       style="color:#b45309;"
                                       onmouseover="this.style.textDecoration='underline';"
                                       onmouseout="this.style.textDecoration='none';">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-sm" style="color:#94a3b8;">
                                Belum ada data proyek.
                                <br>
                                <a href="{{ route('projects.create') }}"
                                   class="mt-1 inline-block text-xs font-medium"
                                   style="color:#2563eb;">+ Buat proyek pertama</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($projects->hasPages())
            <div class="px-4 py-3" style="border-top:1px solid #f1f5f9;">
                {{ $projects->links() }}
            </div>
        @endif
    </div>
@endsection

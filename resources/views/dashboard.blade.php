@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumbs')
<x-breadcrumb :links="[
    ['label' => 'Dashboard', 'href' => route('dashboard')],
]"/>
@endsection

@section('content')
    {{-- ── 4 KPI Stat Cards ────────────────────────────────── --}}
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4 mb-5">
        <x-stat-card
            label="Total Proyek"
            :value="$totalProjects"
            suffix="proyek"/>

        <x-stat-card
            label="Sedang Berjalan"
            :value="$ongoingProjects"
            suffix="proyek"
            variant="info"/>

        <x-stat-card
            label="Selesai"
            :value="$completedProjects"
            suffix="proyek"
            variant="success"/>

        <x-stat-card
            label="Ditunda"
            :value="$onHoldProjects"
            suffix="proyek"
            :variant="$onHoldProjects > 0 ? 'warning' : 'neutral'"/>
    </div>

    <div class="grid gap-4 lg:grid-cols-3">
        {{-- ── Tabel Progres Proyek (2/3 width) ──────────── --}}
        <div class="lg:col-span-2 overflow-hidden rounded-md" style="border:1px solid #e2e8f0; background:#fff;">
            {{-- Card header --}}
            <div class="flex items-center justify-between px-4 py-3"
                 style="border-bottom:1px solid #f1f5f9;">
                <p class="text-xs font-semibold uppercase tracking-wider" style="color:#0f172a; letter-spacing:.05em;">
                    Progres Proyek Terbaru
                </p>
                <a href="{{ route('projects.index') }}"
                   class="text-[11px] font-medium transition-colors"
                   style="color:#2563eb;"
                   onmouseover="this.style.textDecoration='underline';"
                   onmouseout="this.style.textDecoration='none';">
                    Lihat semua →
                </a>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="erp-table w-full text-left">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Proyek</th>
                            <th>Status</th>
                            <th style="min-width:140px;">Progres</th>
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
                                    default     => ucfirst($project->status),
                                };
                            @endphp
                            <tr>
                                <td>
                                    <span class="font-code text-[11px] font-semibold" style="color:#64748b;">
                                        {{ $project->project_code }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('projects.show', $project) }}"
                                       class="text-xs font-medium transition-colors"
                                       style="color:#2563eb;"
                                       onmouseover="this.style.textDecoration='underline';"
                                       onmouseout="this.style.textDecoration='none';">
                                        {{ $project->name }}
                                    </a>
                                </td>
                                <td>
                                    <x-badge :variant="$statusVariant">{{ $statusLabel }}</x-badge>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="relative h-1.5 flex-1 overflow-hidden rounded-full"
                                             style="background:#f1f5f9; min-width:80px;">
                                            <div class="absolute inset-y-0 left-0 rounded-full"
                                                 style="width:{{ $project->overall_progress }}%;
                                                        background:#2563eb;"></div>
                                        </div>
                                        <span class="shrink-0 text-[11px] font-semibold tabular w-10 text-right"
                                              style="color:#0f172a;">
                                            {{ number_format($project->overall_progress, 0) }}%
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-xs" style="color:#94a3b8;">
                                    Belum ada data proyek.
                                    <br>
                                    <a href="{{ route('projects.create') }}"
                                       class="mt-1 inline-block font-medium"
                                       style="color:#2563eb;">+ Buat proyek pertama</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── Panel Status Stok Material (1/3 width) ──── --}}
        <div class="overflow-hidden rounded-md" style="border:1px solid #e2e8f0; background:#fff;">
            <div class="flex items-center justify-between px-4 py-3"
                 style="border-bottom:1px solid #f1f5f9;">
                <p class="text-xs font-semibold uppercase tracking-wider" style="color:#0f172a; letter-spacing:.05em;">
                    Status Stok Material
                </p>
                <a href="{{ route('materials.index') }}"
                   class="text-[11px] font-medium transition-colors"
                   style="color:#2563eb;"
                   onmouseover="this.style.textDecoration='underline';"
                   onmouseout="this.style.textDecoration='none';">
                    Kelola →
                </a>
            </div>

            <ul>
                @forelse ($materials as $material)
                    @php $isLow = $material->stock <= $material->min_stock; @endphp
                    <li class="flex items-center justify-between gap-3 px-4 py-2.5"
                        style="border-bottom:1px solid #f8fafc;">
                        <div class="min-w-0">
                            <p class="truncate text-xs font-medium" style="color:#0f172a;">
                                {{ $material->name }}
                            </p>
                            <p class="text-[11px] tabular" style="color:#94a3b8;">
                                {{ $material->stock }} {{ $material->unit }}
                            </p>
                        </div>
                        @if ($isLow)
                            <x-badge variant="danger" size="xs">Menipis</x-badge>
                        @else
                            <x-badge variant="success" size="xs">Aman</x-badge>
                        @endif
                    </li>
                @empty
                    <li class="px-4 py-8 text-center text-xs" style="color:#94a3b8;">
                        Belum ada data material.
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
@endsection
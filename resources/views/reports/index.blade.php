@extends('layouts.app')

@section('title', 'Laporan Finansial & Proyek')

@section('breadcrumbs')
<x-breadcrumb :links="[
    ['label' => 'Dashboard', 'href' => route('dashboard')],
    ['label' => 'Laporan',   'href' => '#'],
]"/>
@endsection

@section('content')
    {{-- ── Page Header ────────────────────────────────────────── --}}
    <x-page-header
        title="Laporan Eksekutif Finansial & Proyek">
    </x-page-header>

    {{-- ── Filter Bar ─────────────────────────────────────────── --}}
    <div class="mb-4">
        <x-filter-bar action="{{ route('reports.index') }}">
            <select name="project_id" class="erp-input" style="width:280px;" onchange="this.form.submit()">
                <option value="">— Laporan Global (Semua Proyek) —</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}" @selected($projectId == $project->id)>
                        {{ $project->project_code }} – {{ $project->name }}
                    </option>
                @endforeach
            </select>
        </x-filter-bar>
    </div>

    @if (!$selectedProject)
        {{-- ═══════════════════════════════════════════════════════
             TAMPILAN GLOBAL (Seluruh Proyek)
             ═══════════════════════════════════════════════════════ --}}
        @php
            $globalActualCost = (float) \App\Models\Budget::where('status', 'approved')->sum('amount') + (float) \App\Models\CashFlow::where('type', 'out')->sum('amount');
            $globalMargin = $totalBudget - $globalActualCost;
            $globalSerapan = $totalBudget > 0 ? round(($globalActualCost / $totalBudget) * 100, 1) : 0;
            $criticalMaterials = $materials->filter(fn($m) => $m->stock <= $m->min_stock);
        @endphp

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
            <x-stat-card
                label="Total Proyek Terdaftar"
                :value="$totalProjects"
                suffix="proyek"/>

            <x-stat-card
                label="Total Pagu Kontrak (Global)"
                :value="'Rp ' . number_format($totalBudget, 0, ',', '.')"
                variant="default"/>

            <x-stat-card
                label="Realisasi Kas Dikeluarkan"
                :value="'Rp ' . number_format($globalActualCost, 0, ',', '.')"
                :variant="$globalActualCost > $totalBudget ? 'danger' : 'info'"
                :note="'Tingkat Serapan: ' . $globalSerapan . '%'">
                <div class="h-1.5 overflow-hidden rounded-full" style="background:#e2e8f0;">
                    <div class="h-full rounded-full"
                         style="width:{{ min(100, $globalSerapan) }}%; background:{{ $globalActualCost > $totalBudget ? '#ef4444' : '#2563eb' }};"></div>
                </div>
            </x-stat-card>

            <x-stat-card
                label="Sisa Cadangan Kas Pagu"
                :value="'Rp ' . number_format($globalMargin, 0, ',', '.')"
                :variant="$globalMargin < 0 ? 'danger' : 'success'"/>
        </div>

        {{-- Tabel Peringatan Stok Material Menipis --}}
        <div class="overflow-hidden rounded-lg border border-[#E5E7EB] dark:border-[#27272A] bg-white dark:bg-[#18181B] shadow-none">
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100 dark:border-[#27272A]">
                <div>
                    <h3 class="text-base font-semibold text-[#0B2545] dark:text-[#F4F4F5]">
                        Peringatan Stok Material Menipis (Restock Alert)
                    </h3>
                </div>
                @if (auth()->user()->hasRole('logistics'))
                    <a href="{{ route('materials.index') }}" class="text-xs font-semibold text-[#136F9A] dark:text-[#A1A1AA] hover:underline">
                        Kelola Katalog &rarr;
                    </a>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="erp-table w-full text-left">
                    <thead class="bg-[#F8FAFC] dark:bg-[#27272A]">
                        <tr class="border-b border-[#E5E7EB] dark:border-[#27272A]">
                            <th class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]" style="width:110px;">Kode</th>
                            <th class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">Nama Material</th>
                            <th class="text-right text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]" style="width:130px;">Harga Satuan</th>
                            <th class="text-right text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]" style="width:120px;">Stok Saat Ini</th>
                            <th class="text-right text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]" style="width:120px;">Batas Minimal</th>
                            <th class="text-center text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]" style="width:100px;">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E5E7EB] dark:divide-[#27272A]">
                        @forelse ($criticalMaterials as $material)
                            <tr class="hover:bg-[#F8FAFC] dark:hover:bg-white/5 transition-colors">
                                <td>
                                    <span class="font-mono text-xs font-medium px-2 py-0.5 rounded bg-[#F8FAFC] text-[#0B2545] border border-[#E5E7EB] dark:bg-white/5 dark:text-slate-300 dark:border-[#27272A]">
                                        {{ $material->code }}
                                    </span>
                                </td>
                                <td class="text-sm font-medium text-[#0B2545] dark:text-[#F4F4F5]">
                                    {{ $material->name }}
                                </td>
                                <td class="text-right tabular-nums text-sm font-medium text-[#0B2545] dark:text-[#F4F4F5]">
                                    Rp {{ number_format($material->price, 0, ',', '.') }} / {{ $material->unit }}
                                </td>
                                <td class="text-right tabular-nums text-sm font-semibold text-[#0B2545] dark:text-[#F4F4F5]">
                                    {{ $material->stock }} {{ $material->unit }}
                                </td>
                                <td class="text-right tabular-nums text-[13px] text-[#64748B] dark:text-slate-400">
                                    {{ $material->min_stock }} {{ $material->unit }}
                                </td>
                                <td class="text-center">
                                    <x-badge variant="danger" size="xs">Kritis</x-badge>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-xs text-[#64748B] dark:text-slate-400">
                                    ✓ Semua stok material proyek berada dalam kondisi aman di atas batas minimum.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @else
        {{-- ═══════════════════════════════════════════════════════
             TAMPILAN SPESIFIK PROYEK
             ═══════════════════════════════════════════════════════ --}}
        @php
            $projBudget = (float) $selectedProject->budget;
            $projMargin = $projBudget - $totalActualCost;
            $projSerapan = $projBudget > 0 ? round(($totalActualCost / $projBudget) * 100, 1) : 0;
            $isOver = $totalActualCost > $projBudget;
        @endphp

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 mb-4">
            <x-stat-card label="Upah Aktual Dibayar" :value="'Rp ' . number_format($laborActual, 0, ',', '.')"/>
            <x-stat-card label="Pembayaran Supplier" :value="'Rp ' . number_format($supplierPayments, 0, ',', '.')"/>
            <x-stat-card label="Total Biaya Aktual" :value="'Rp ' . number_format($totalActualCost, 0, ',', '.')"/>
        </div>

        {{-- Header Proyek Ringkas --}}
        <div class="mb-6 rounded-lg p-5 border border-[#E5E7EB] dark:border-[#27272A] bg-white dark:bg-[#18181B] shadow-none">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="font-code text-xs font-semibold px-2 py-0.5 rounded"
                              style="background:#f1f5f9; color:#475569; border:1px solid #e2e8f0;">
                            {{ $selectedProject->project_code }}
                        </span>
                        <x-badge :variant="$selectedProject->status === 'completed' ? 'success' : ($selectedProject->status === 'ongoing' ? 'info' : 'neutral')">
                            {{ ucfirst(str_replace('_', ' ', $selectedProject->status)) }}
                        </x-badge>
                        @if ($selectedProject->location)
                            <span class="text-xs text-slate-500">📍 {{ $selectedProject->location }}</span>
                        @endif
                    </div>
                    <h2 class="mt-1 text-lg font-semibold tracking-tight text-[#0B2545] dark:text-[#F4F4F5] truncate">
                        {{ $selectedProject->name }}
                    </h2>
                </div>
                <div class="flex items-center gap-2">
                    @if (auth()->user()->hasRole('project_manager'))
                        <a href="{{ route('projects.show', $selectedProject) }}" class="btn-primary">
                            Buka Detail Proyek &rarr;
                        </a>
                    @endif
                </div>
            </div>

            @if ($isOver)
                <div class="mt-3 flex items-center gap-2 rounded px-3 py-2 text-xs font-semibold text-red-700"
                     style="background:#fef2f2; border:1px solid #fecaca;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>Peringatan: Total pengeluaran aktual telah melampaui pagu anggaran proyek sebesar Rp {{ number_format(abs($projMargin), 0, ',', '.') }}!</span>
                </div>
            @endif
        </div>

        {{-- 3 KPI Metrik Proyek Terpilih --}}
        <div class="grid gap-4 sm:grid-cols-3 mb-6">
            <x-stat-card
                label="Pagu Anggaran Kontrak"
                :value="'Rp ' . number_format($projBudget, 0, ',', '.')"
                variant="default"/>

            <x-stat-card
                label="Total Pengeluaran Riil (Aktual)"
                :value="'Rp ' . number_format($totalActualCost, 0, ',', '.')"
                :variant="$isOver ? 'danger' : 'info'"
                :note="'Serapan Kas: ' . $projSerapan . '%'">
                <div class="h-1.5 overflow-hidden rounded-full" style="background:#e2e8f0;">
                    <div class="h-full rounded-full"
                         style="width:{{ min(100, $projSerapan) }}%; background:{{ $isOver ? '#ef4444' : '#2563eb' }};"></div>
                </div>
            </x-stat-card>

            <x-stat-card
                label="Sisa Margin Anggaran"
                :value="'Rp ' . number_format($projMargin, 0, ',', '.')"
                :variant="$projMargin < 0 ? 'danger' : 'success'"/>
        </div>

        {{-- Grid 2-Kolom: Rincian Biaya per Kategori & Progres Fisik --}}
        <div class="grid gap-5 md:grid-cols-2">
            {{-- Panel Kiri: Rincian Pengeluaran per Kategori --}}
            <div class="overflow-hidden rounded-lg border border-[#E5E7EB] dark:border-[#27272A] bg-white dark:bg-[#18181B] shadow-none">
                <div class="px-5 py-3.5 border-b border-[#E5E7EB] dark:border-[#27272A]">
                    <h3 class="text-base font-bold text-[#1E293B] dark:text-white">
                        Rincian Pengeluaran per Kategori
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="erp-table w-full text-left">
                        <thead class="bg-[#F8FAFC] dark:bg-[#27272A]">
                            <tr class="border-b border-[#E5E7EB] dark:border-[#27272A]">
                                <th class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">Kategori</th>
                                <th class="text-right text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">Nominal (Rp)</th>
                                <th class="text-right text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]" style="width:80px;">Porsi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E5E7EB] dark:divide-[#27272A]">
                            @forelse ($costByCategory as $category => $amount)
                                @php
                                    $porsi = $totalActualCost > 0 ? round(($amount / $totalActualCost) * 100, 1) : 0;
                                @endphp
                                <tr class="hover:bg-[#F8FAFC] dark:hover:bg-white/5 transition-colors">
                                    <td>
                                        <x-badge variant="neutral">{{ $category }}</x-badge>
                                    </td>
                                    <td class="text-right tabular-nums text-sm font-semibold text-[#0B2545] dark:text-[#F4F4F5]">
                                        Rp {{ number_format($amount, 0, ',', '.') }}
                                    </td>
                                    <td class="text-right tabular-nums text-[13px] text-[#64748B] dark:text-slate-400">
                                        {{ $porsi }}%
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-8 text-center text-xs text-[#64748B] dark:text-slate-400">
                                        Belum ada catatan pengeluaran kas untuk proyek ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($costByCategory->isNotEmpty())
                            <tfoot>
                                <tr class="font-medium text-xs text-[#0B2545] dark:text-slate-200 bg-[#F8FAFC] dark:bg-[#27272A] border-t border-[#E5E7EB] dark:border-[#27272A]">
                                    <td class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">Total:</td>
                                    <td class="text-right tabular-nums text-sm font-semibold">
                                        Rp {{ number_format($totalActualCost, 0, ',', '.') }}
                                    </td>
                                    <td class="text-right tabular-nums text-[13px]">100%</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            {{-- Panel Kanan: Progres Fisik Pekerjaan --}}
            <div class="overflow-hidden rounded-lg border border-[#E5E7EB] dark:border-[#27272A] bg-white dark:bg-[#18181B] shadow-none">
                <div class="flex items-center justify-between px-5 py-3.5 border-b border-[#E5E7EB] dark:border-[#27272A]">
                    <h3 class="text-base font-bold text-[#0B2545] dark:text-[#F4F4F5]">
                        Progres Fisik Tahapan Proyek
                    </h3>
                    <span class="text-xs font-bold tabular text-[#1D4E89] dark:text-[#93C5FD]">
                        {{ number_format($selectedProject->overall_progress, 2, ',', '.') }}%
                    </span>
                </div>
                <div class="p-5">
                    {{-- Mini Progress Bar Total --}}
                    <div class="mb-4">
                        <div class="flex justify-between text-xs text-slate-500 mb-1">
                            <span>Bobot Kumulatif Tertimbang</span>
                            <span class="tabular font-medium text-slate-700">{{ number_format($selectedProject->overall_progress, 1, ',', '.') }}% dari 100%</span>
                        </div>
                        <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-600 rounded-full transition-all"
                                 style="width: {{ min(100, $selectedProject->overall_progress) }}%"></div>
                        </div>
                    </div>

                    {{-- List Tahapan --}}
                    <ul class="divide-y divide-slate-100 text-xs">
                        @forelse ($selectedProject->schedules as $schedule)
                            <li class="py-2.5 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <a href="{{ route('schedules.show', $schedule) }}" class="font-medium text-slate-800 hover:text-blue-600 truncate block">
                                        {{ $schedule->name }}
                                    </a>
                                    <span class="text-[11px] text-slate-400 tabular">Bobot: {{ number_format($schedule->weight, 1, ',', '.') }}%</span>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <div class="h-1.5 w-16 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full {{ $schedule->current_progress >= 100 ? 'bg-green-600' : 'bg-blue-600' }} rounded-full"
                                             style="width: {{ min(100, $schedule->current_progress) }}%"></div>
                                    </div>
                                    <span class="font-bold tabular text-[#0B2545] dark:text-[#F4F4F5] w-9 text-right">
                                        {{ number_format($schedule->current_progress, 0) }}%
                                    </span>
                                </div>
                            </li>
                        @empty
                            <li class="py-6 text-center text-slate-400">
                                Belum ada jadwal pekerjaan terdaftar.
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    @endif
@endsection

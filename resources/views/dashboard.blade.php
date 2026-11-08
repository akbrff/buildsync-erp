@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumbs')
<x-breadcrumb :links="[
    ['label' => 'Dashboard', 'href' => route('dashboard')],
]"/>
@endsection

@section('content')
    {{-- ── 1. 4 KPI Stat Cards ──────────────────────────────── --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
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

    {{-- ── 2. Baris Grafik Utama (Tren Biaya & Distribusi Kategori) ── --}}
    <div class="grid gap-5 lg:grid-cols-3 mb-6 items-stretch">
        {{-- Grafik Kiri: Tren Realisasi Anggaran vs Pagu (2/3 width) --}}
        <div class="lg:col-span-2 flex flex-col overflow-hidden rounded-md border border-slate-200 bg-white">
            <div class="flex flex-wrap items-center justify-between gap-2 px-5 py-3 border-b border-slate-100">
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-600">
                        Tren Realisasi Anggaran vs Pagu
                    </h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">
                        Perbandingan alokasi rencana dan pengeluaran kas aktual (6 bulan terakhir)
                    </p>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <span class="inline-flex items-center gap-1.5 text-slate-600">
                        <span class="h-2 w-3 rounded-xs bg-slate-800"></span>
                        <span class="text-[11px]">Pagu Rencana</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-slate-600">
                        <span class="h-2 w-3 rounded-xs bg-blue-600"></span>
                        <span class="text-[11px]">Realisasi Kas</span>
                    </span>
                </div>
            </div>
            <div class="p-5 flex-1 flex flex-col justify-center">
                <div class="relative w-full h-64">
                    <canvas id="budgetTrendChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Grafik Kanan: Distribusi Alokasi Biaya (1/3 width) --}}
        <div class="flex flex-col overflow-hidden rounded-md border border-slate-200 bg-white">
            <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100">
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-600">
                        Distribusi Alokasi Biaya
                    </h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">
                        Komposisi pengeluaran kas per kategori
                    </p>
                </div>
                <span class="text-[11px] font-semibold tabular text-slate-700">
                    Rp {{ number_format($totalExpense, 0, ',', '.') }}
                </span>
            </div>
            <div class="p-5 flex-1 flex flex-col items-center justify-center">
                @if ($totalExpense > 0)
                    <div class="relative w-full h-44 flex items-center justify-center">
                        <canvas id="costDistributionChart"></canvas>
                    </div>
                    {{-- Clean Enterprise Legend --}}
                    <div class="w-full mt-3 space-y-1.5 pt-2 border-t border-slate-100 text-xs">
                        @php
                            $catColors = [
                                'Material'          => 'bg-blue-600',
                                'Upah Tenaga Kerja' => 'bg-slate-900',
                                'Sewa Alat'         => 'bg-emerald-600',
                                'Operasional'       => 'bg-slate-400',
                            ];
                        @endphp
                        @foreach ($costDistribution as $cat => $val)
                            @php
                                $percent = $totalExpense > 0 ? round(($val / $totalExpense) * 100, 1) : 0;
                                $color = $catColors[$cat] ?? 'bg-amber-500';
                            @endphp
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="flex items-center gap-1.5 text-slate-600">
                                    <span class="h-2 w-2 rounded-full {{ $color }}"></span>
                                    <span class="truncate max-w-[120px]">{{ $cat }}</span>
                                </span>
                                <span class="tabular font-medium text-slate-900">
                                    Rp {{ number_format($val, 0, ',', '.') }}
                                    <span class="text-slate-400 font-normal text-[10px]">({{ $percent }}%)</span>
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-12 text-center text-xs text-slate-400">
                        <svg class="h-8 w-8 text-slate-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                        </svg>
                        <p class="font-medium text-slate-600">Belum ada transaksi pengeluaran kas.</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Catatan pengeluaran disinkronkan dari modul Keuangan.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── 3. Tabel Progres Proyek & Panel Stok Material ────── --}}
    <div class="grid gap-5 lg:grid-cols-3 items-stretch">
        {{-- ── Tabel Progres Proyek (2/3 width) ──────────── --}}
        <div class="lg:col-span-2 flex flex-col overflow-hidden rounded-md border border-slate-200 bg-white">
            {{-- Card header --}}
            <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-600">
                    Progres Proyek Terbaru
                </p>
                <a href="{{ route('projects.index') }}"
                   class="text-[11px] font-medium text-blue-600 hover:underline transition-colors">
                    Lihat semua →
                </a>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto flex-1">
                <table class="erp-table w-full text-left">
                    <thead>
                        <tr>
                            <th class="w-28">Kode</th>
                            <th>Nama Proyek</th>
                            <th class="w-28">Status</th>
                            <th class="w-44">Progres</th>
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
                                    <span class="font-code text-xs font-semibold text-slate-500">
                                        {{ $project->project_code }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('projects.show', $project) }}"
                                       class="text-[13px] font-medium text-slate-900 hover:text-blue-600 transition-colors">
                                        {{ $project->name }}
                                    </a>
                                </td>
                                <td>
                                    <x-badge :variant="$statusVariant">{{ $statusLabel }}</x-badge>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="relative h-1.5 flex-1 overflow-hidden rounded-full bg-slate-100 min-w-[70px]">
                                            <div class="absolute inset-y-0 left-0 rounded-full bg-slate-800"
                                                 style="width:{{ $project->overall_progress }}%;"></div>
                                        </div>
                                        <span class="shrink-0 text-xs font-semibold tabular w-10 text-right text-slate-900">
                                            {{ number_format($project->overall_progress, 0) }}%
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-xs text-slate-400">
                                    Belum ada data proyek.
                                    <br>
                                    <a href="{{ route('projects.create') }}"
                                       class="mt-1 inline-block font-medium text-blue-600 hover:underline">
                                        + Buat proyek pertama
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── Panel Status Stok Material (1/3 width) ──── --}}
        <div class="flex flex-col overflow-hidden rounded-md border border-slate-200 bg-white">
            <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-600">
                    Status Stok Material
                </p>
                <a href="{{ route('materials.index') }}"
                   class="text-[11px] font-medium text-blue-600 hover:underline transition-colors">
                    Kelola →
                </a>
            </div>

            <ul class="flex-1 divide-y divide-slate-100">
                @forelse ($materials as $material)
                    @php $isLow = $material->stock <= $material->min_stock; @endphp
                    <li class="flex items-center justify-between gap-3 px-4 py-2.5 hover:bg-slate-50 transition-colors">
                        <div class="min-w-0">
                            <p class="truncate text-[13px] font-medium text-slate-900">
                                {{ $material->name }}
                            </p>
                            <p class="text-[11px] text-slate-400 tabular">
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
                    <li class="px-4 py-8 text-center text-xs text-slate-400">
                        Belum ada data material.
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') return;

    // ── Global Chart.js Defaults (Anti-Slop Enterprise Styling) ──
    Chart.defaults.font.family = "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
    Chart.defaults.font.size   = 11;
    Chart.defaults.color       = '#64748b';

    // 1. Chart Tren Anggaran vs Realisasi (Combo Bar & Line)
    const trendCtx = document.getElementById('budgetTrendChart');
    if (trendCtx) {
        new Chart(trendCtx, {
            type: 'bar',
            data: {
                labels: @json($trendLabels),
                datasets: [
                    {
                        type: 'line',
                        label: 'Pagu Rencana',
                        data: @json($trendBudgets),
                        borderColor: '#0f172a',
                        backgroundColor: '#0f172a',
                        borderWidth: 2,
                        borderDash: [4, 4],
                        pointRadius: 3,
                        pointBackgroundColor: '#0f172a',
                        pointHoverRadius: 5,
                        tension: 0.2,
                        order: 1
                    },
                    {
                        type: 'bar',
                        label: 'Realisasi Kas',
                        data: @json($trendActuals),
                        backgroundColor: '#2563eb',
                        hoverBackgroundColor: '#1d4ed8',
                        borderRadius: 3,
                        maxBarThickness: 32,
                        order: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false // Handled cleanly via HTML header badges
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#ffffff',
                        bodyColor: '#f8fafc',
                        titleFont: { size: 12, weight: '600' },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 4,
                        callbacks: {
                            label: function (context) {
                                const val = context.parsed.y || 0;
                                return ' ' + context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(val);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            color: '#64748b'
                        }
                    },
                    y: {
                        border: {
                            dash: [3, 3]
                        },
                        grid: {
                            color: '#f1f5f9',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#64748b',
                            callback: function (value) {
                                if (value >= 1000000000) return 'Rp ' + (value / 1000000000).toFixed(1) + 'M';
                                if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(0) + 'Jt';
                                if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'Rb';
                                return 'Rp ' + value;
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. Chart Distribusi Biaya (Doughnut)
    const distCtx = document.getElementById('costDistributionChart');
    if (distCtx) {
        const costData = @json(array_values($costDistribution));
        const costLabels = @json(array_keys($costDistribution));
        const colorPalette = ['#2563eb', '#0f172a', '#10b981', '#94a3b8', '#f59e0b'];

        new Chart(distCtx, {
            type: 'doughnut',
            data: {
                labels: costLabels,
                datasets: [{
                    data: costData,
                    backgroundColor: colorPalette.slice(0, costLabels.length),
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: {
                        display: false // Handled cleanly via HTML below
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#ffffff',
                        bodyColor: '#f8fafc',
                        padding: 10,
                        cornerRadius: 4,
                        callbacks: {
                            label: function (context) {
                                const val = context.raw || 0;
                                return ' ' + context.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(val);
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush

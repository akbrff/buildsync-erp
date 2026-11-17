@extends('layouts.app')

@section('title', __('dashboard.title'))

@section('breadcrumbs')
<x-breadcrumb :links="[
    ['label' => __('dashboard.title'), 'href' => route('dashboard')],
]"/>
@endsection

@section('content')
    {{-- ── 1. 4 KPI Stat Cards (Open Unified KPI Strip) ────── --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6 p-4 sm:p-5 rounded-lg bg-white dark:bg-[#18181B] border border-[#E5E7EB] dark:border-[#27272A] shadow-none divide-y sm:divide-y-0 sm:divide-x divide-[#E5E7EB] dark:divide-[#27272A]">
        <x-stat-card
            :plain="true"
            :label="__('dashboard.total_projects')"
            :value="$totalProjects"
            variant="neutral"/>

        <x-stat-card
            :plain="true"
            :label="__('dashboard.ongoing_projects')"
            :value="$ongoingProjects"
            variant="info"/>

        <x-stat-card
            :plain="true"
            :label="__('dashboard.completed_projects')"
            :value="$completedProjects"
            variant="success"/>

        <x-stat-card
            :plain="true"
            :label="__('dashboard.on_hold_projects')"
            :value="$onHoldProjects"
            :variant="$onHoldProjects > 0 ? 'warning' : 'neutral'"/>
    </div>

    {{-- ── 2. Baris Grafik Utama & Executive Accent Summary Card ───── --}}
    <div class="grid gap-6 lg:grid-cols-3 mb-6 items-stretch">
        {{-- Grafik Kiri: Tren Realisasi Anggaran vs Pagu (2/3 width) --}}
        <div class="lg:col-span-2 erp-card flex flex-col">
            <div class="erp-card-header px-5 py-3.5">
                <div>
                    <h3 class="text-base font-semibold text-[#0B2545] dark:text-[#F4F4F5]">
                        {{ __('dashboard.budget_trend_title') }}
                    </h3>
                </div>
                <div class="flex items-center gap-4 text-xs">
                    <span class="inline-flex items-center gap-1.5 text-[#64748B] dark:text-[#A1A1AA]">
                        <span class="h-2.5 w-3 rounded-xs bg-[#0B2545] dark:bg-[#71717A]"></span>
                        <span class="text-xs font-medium text-[#0B2545] dark:text-[#F4F4F5]">{{ __('dashboard.budget_planned') }}</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-[#64748B] dark:text-[#A1A1AA]">
                        <span class="h-2.5 w-3 rounded-xs bg-[#1D4E89] dark:bg-[#1D4E89]"></span>
                        <span class="text-xs font-medium text-[#1D4E89] dark:text-[#93C5FD]">{{ __('dashboard.actual_realization') }}</span>
                    </span>
                </div>
            </div>
            <div class="p-5 flex-1 flex flex-col justify-center">
                <div class="relative w-full h-64">
                    <canvas id="budgetTrendChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Executive Accent Summary Card (1/3 width, kanan grafik utama) --}}
        <div class="erp-card flex flex-col justify-between p-5 text-[#0B2545] dark:text-[#F4F4F5]">
            <div>
                <div>
                    <h3 class="text-base font-semibold text-[#0B2545] dark:text-[#F4F4F5]">
                        {{ __('dashboard.total_cash_realization') }}
                    </h3>
                </div>

                {{-- Prominent metric in Deep Navy / Inter --}}
                <div class="mt-4 mb-3">
                    <div class="text-[#0B2545] dark:text-[#F4F4F5] text-2xl lg:text-3xl font-bold tabular-nums tracking-tight">
                        Rp {{ number_format($totalExpense, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            {{-- Visual Distribusi / Donut Chart --}}
            <div class="my-2 flex-1 flex flex-col items-center justify-center">
                @if ($totalExpense > 0)
                    <div class="relative w-full h-36 flex items-center justify-center">
                        <canvas id="costDistributionChart"></canvas>
                    </div>
                @else
                    <div class="py-8 text-center text-xs text-[#64748B] dark:text-[#71717A]">
                        {{ __('dashboard.no_expense_transactions') }}
                    </div>
                @endif
            </div>

            {{-- Legend Breakdown --}}
            @if ($totalExpense > 0)
                <div class="w-full pt-3 border-t border-[#E5E7EB] dark:border-[#27272A] space-y-1.5 text-xs">
                    @php
                        $catColors = [
                            'Material'          => 'bg-[#1D4E89]',
                            'Upah Tenaga Kerja' => 'bg-[#64748B]',
                            'Sewa Alat'         => 'bg-[#0EA5E9]',
                            'Operasional'       => 'bg-[#16A34A]',
                        ];
                        $catLabels = [
                            'Material'          => __('dashboard.cost_material'),
                            'Upah Tenaga Kerja' => __('dashboard.cost_labor'),
                            'Sewa Alat'         => __('dashboard.cost_equipment'),
                            'Operasional'       => __('dashboard.cost_operational'),
                        ];
                    @endphp
                    @foreach ($costDistribution as $cat => $val)
                        @php
                            $percent = $totalExpense > 0 ? round(($val / $totalExpense) * 100, 1) : 0;
                            $color = $catColors[$cat] ?? 'bg-amber-400';
                        @endphp
                        <div class="flex items-center justify-between text-xs">
                            <span class="flex items-center gap-1.5 text-[#0B2545] dark:text-zinc-200">
                                <span class="h-2 w-2 rounded-full {{ $color }}"></span>
                                <span class="truncate max-w-[120px]">{{ $catLabels[$cat] ?? $cat }}</span>
                            </span>
                            <span class="tabular font-medium text-[#0B2545] dark:text-[#F4F4F5]">
                                Rp {{ number_format($val, 0, ',', '.') }}
                                <span class="text-[#64748B] dark:text-[#A1A1AA] font-normal text-[11px]">({{ $percent }}%)</span>
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ── 3. Tabel Progres Proyek & Panel Stok Material ────── --}}
    <div class="grid gap-6 lg:grid-cols-3 items-stretch">
        {{-- ── Tabel Progres Proyek (2/3 width) ──────────── --}}
        <div class="lg:col-span-2 erp-card flex flex-col">
            {{-- Card header --}}
            <div class="erp-card-header px-5 py-3.5">
                <div>
                    <h3 class="text-base font-semibold text-[#0B2545] dark:text-[#F4F4F5]">
                        {{ __('dashboard.recent_project_progress') }}
                    </h3>
                </div>
                <a href="{{ route('projects.index') }}"
                   class="text-[13px] font-medium text-[#1D4E89] dark:text-[#93C5FD] hover:underline transition-colors">
                    {{ __('common.view_all') }} &rarr;
                </a>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto flex-1">
                <table class="erp-table w-full text-left">
                    <thead>
                        <tr>
                            <th class="w-28 text-[13px] font-medium text-[#64748B]">{{ __('common.code') }}</th>
                            <th class="text-[13px] font-medium text-[#64748B]">{{ __('dashboard.project_name') }}</th>
                            <th class="w-28 text-[13px] font-medium text-[#64748B]">{{ __('common.status') }}</th>
                            <th class="w-44 text-[13px] font-medium text-[#64748B]">{{ __('dashboard.progress') }}</th>
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
                                    'ongoing'   => __('projects.status_ongoing'),
                                    'completed' => __('projects.status_completed'),
                                    'planning'  => __('projects.status_planning'),
                                    'on_hold'   => __('projects.status_on_hold'),
                                    default     => ucfirst($project->status),
                                };
                            @endphp
                            <tr class="hover:bg-[#F8FAFC] dark:hover:bg-white/5 transition-colors">
                                <td>
                                    <span class="font-mono text-xs font-medium text-[#64748B] dark:text-[#A1A1AA]">
                                        {{ $project->project_code }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('projects.show', $project) }}"
                                       class="text-sm font-medium text-[#0B2545] dark:text-[#F4F4F5] hover:text-[#1D4E89] dark:hover:text-[#93C5FD] transition-colors">
                                        {{ $project->name }}
                                    </a>
                                </td>
                                <td>
                                    <x-badge :variant="$statusVariant">{{ $statusLabel }}</x-badge>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2.5">
                                        <div class="relative h-2 flex-1 overflow-hidden rounded-full bg-slate-100 dark:bg-white/10 min-w-[70px]">
                                             <div class="absolute inset-y-0 left-0 rounded-full bg-[#1D4E89] dark:bg-[#1D4E89]"
                                                 style="width:{{ $project->overall_progress }}%;"></div>
                                        </div>
                                        <span class="shrink-0 text-xs font-semibold tabular w-10 text-right text-[#0B2545] dark:text-[#F4F4F5]">
                                            {{ number_format($project->overall_progress, 0) }}%
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="hover:bg-[#F8FAFC] dark:hover:bg-white/5 transition-colors">
                                <td colspan="4" class="py-8 text-center text-xs text-[#64748B] dark:text-[#71717A]">
                                    {{ __('dashboard.no_projects') }}
                                    <br>
                                    <a href="{{ route('projects.create') }}"
                                       class="mt-1.5 inline-block font-medium text-[#1D4E89] dark:text-[#93C5FD] hover:underline">
                                        {{ __('dashboard.create_first_project') }}
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── Panel Status Stok Material (1/3 width) ──── --}}
        <div class="erp-card flex flex-col">
            <div class="erp-card-header px-5 py-3.5">
                <div>
                    <h3 class="text-base font-semibold text-[#0B2545] dark:text-[#F4F4F5]">
                        {{ __('dashboard.material_stock_status') }}
                    </h3>
                </div>
                <a href="{{ route('materials.index') }}"
                   class="text-[13px] font-medium text-[#1D4E89] dark:text-[#93C5FD] hover:underline transition-colors">
                    {{ __('common.manage') }} &rarr;
                </a>
            </div>

            <ul class="flex-1 divide-y divide-[#E5E7EB] dark:divide-[#27272A]">
                @forelse ($materials as $material)
                    @php $isLow = $material->stock <= $material->min_stock; @endphp
                    <li class="flex items-center justify-between gap-3 px-5 py-3 hover:bg-[#F8FAFC] dark:hover:bg-white/5 transition-colors">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-[#0B2545] dark:text-white">
                                {{ $material->name }}
                            </p>
                            <p class="text-xs text-[#64748B] dark:text-[#A1A1AA] tabular mt-0.5">
                                {{ $material->stock }} {{ $material->unit }}
                            </p>
                        </div>
                        @if ($isLow)
                            <x-badge variant="danger" size="xs">{{ __('dashboard.stock_low') }}</x-badge>
                        @else
                            <x-badge variant="success" size="xs">{{ __('dashboard.stock_safe') }}</x-badge>
                        @endif
                    </li>
                @empty
                    <li class="px-6 py-8 text-center text-xs text-[#64748B] dark:text-[#71717A]">
                        {{ __('dashboard.no_materials') }}
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

    // Helper untuk membaca konfigurasi tema saat ini (Light / Dark)
    function getThemeColors() {
        const isDark = document.documentElement.classList.contains('dark');
        return {
            isDark: isDark,
            paguColor: isDark ? '#A1A1AA' : '#0B2545',
            tickColor: isDark ? '#71717A' : '#5A738E',
            gridColor: isDark ? 'rgba(255, 255, 255, 0.08)' : '#E2E8F0',
            tooltipBg: isDark ? '#18181B' : '#0B2545',
            doughnutBorder: isDark ? '#18181B' : '#0B2545'
        };
    }

    let themeColors = getThemeColors();

    // ── Global Chart.js Defaults (Inter Typography & Colors) ──
    Chart.defaults.font.family = "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
    Chart.defaults.font.size   = 11;
    Chart.defaults.color       = themeColors.tickColor;

    let trendChartInstance = null;
    let distChartInstance = null;

    // 1. Chart Tren Anggaran vs Realisasi (Combo Bar & Line)
    const trendCtx = document.getElementById('budgetTrendChart');
    if (trendCtx) {
        const ctx = trendCtx.getContext('2d');
        // Cobalt Gradient for Realisasi Kas
        const cobaltGrad = ctx.createLinearGradient(0, 0, 0, 260);
        cobaltGrad.addColorStop(0, '#1D4E89');
        cobaltGrad.addColorStop(1, 'rgba(29, 78, 137, 0.4)');

        trendChartInstance = new Chart(trendCtx, {
            type: 'bar',
            data: {
                labels: @json($trendLabels),
                datasets: [
                    {
                        type: 'line',
                        label: '{{ __('dashboard.budget_planned') }}',
                        data: @json($trendBudgets),
                        borderColor: themeColors.paguColor,
                        backgroundColor: themeColors.paguColor,
                        borderWidth: 2.5,
                        borderDash: [5, 4],
                        pointRadius: 3.5,
                        pointBackgroundColor: themeColors.paguColor,
                        pointHoverRadius: 5,
                        tension: 0.25,
                        order: 1
                    },
                    {
                        type: 'bar',
                        label: '{{ __('dashboard.actual_realization') }}',
                        data: @json($trendActuals),
                        backgroundColor: cobaltGrad,
                        hoverBackgroundColor: '#1D4E89',
                        borderRadius: 6,
                        maxBarThickness: 34,
                        order: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: themeColors.tooltipBg,
                        titleColor: '#FFFFFF',
                        bodyColor: '#CFE3F3',
                        titleFont: { size: 12, weight: '700' },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 6,
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
                            color: themeColors.tickColor
                        }
                    },
                    y: {
                        border: {
                            dash: [4, 4]
                        },
                        grid: {
                            color: themeColors.gridColor,
                            drawBorder: false
                        },
                        ticks: {
                            color: themeColors.tickColor,
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

    // 2. Chart Distribusi Biaya (Doughnut in Dark Accent Card)
    const distCtx = document.getElementById('costDistributionChart');
    if (distCtx) {
        const costData = @json(array_values($costDistribution));
        const costLabels = @json(array_keys($costDistribution));
        // Cool Blue System Palette: Cobalt, Deep Navy, Amber, Emerald, Dusty Blue
        const colorPalette = ['#1D4E89', '#0B2545', '#D97706', '#16A34A', '#8DA9C4'];

        distChartInstance = new Chart(distCtx, {
            type: 'doughnut',
            data: {
                labels: costLabels,
                datasets: [{
                    data: costData,
                    backgroundColor: colorPalette.slice(0, costLabels.length),
                    borderWidth: 2,
                    borderColor: themeColors.doughnutBorder,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0B2545',
                        titleColor: '#FFFFFF',
                        bodyColor: '#CFE3F3',
                        padding: 10,
                        cornerRadius: 6,
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

    // Listener custom event theme-changed untuk reaktifitas Chart.js
    window.addEventListener('theme-changed', function () {
        const updatedColors = getThemeColors();
        Chart.defaults.color = updatedColors.tickColor;

        if (trendChartInstance) {
            trendChartInstance.data.datasets[0].borderColor = updatedColors.paguColor;
            trendChartInstance.data.datasets[0].backgroundColor = updatedColors.paguColor;
            trendChartInstance.data.datasets[0].pointBackgroundColor = updatedColors.paguColor;
            trendChartInstance.options.scales.x.ticks.color = updatedColors.tickColor;
            trendChartInstance.options.scales.y.ticks.color = updatedColors.tickColor;
            trendChartInstance.options.scales.y.grid.color = updatedColors.gridColor;
            trendChartInstance.options.plugins.tooltip.backgroundColor = updatedColors.tooltipBg;
            trendChartInstance.update();
        }

        if (distChartInstance) {
            distChartInstance.data.datasets[0].borderColor = updatedColors.doughnutBorder;
            distChartInstance.update();
        }
    });
});
</script>
@endpush

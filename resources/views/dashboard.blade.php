@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumbs')
<x-breadcrumb :links="[
    ['label' => 'Dashboard', 'href' => route('dashboard')],
]"/>
@endsection

@section('content')
    {{-- ── 1. 4 KPI Stat Cards (Open KPI Strip) ─────────────── --}}
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <x-stat-card
            label="Total Proyek"
            :value="$totalProjects"
            suffix="proyek"
            variant="neutral"
            note="Keseluruhan proyek"/>

        <x-stat-card
            label="Sedang Berjalan"
            :value="$ongoingProjects"
            suffix="proyek"
            variant="info"
            note="Aktif di lapangan"/>

        <x-stat-card
            label="Selesai"
            :value="$completedProjects"
            suffix="proyek"
            variant="success"
            note="Serah terima final"/>

        <x-stat-card
            label="Ditunda"
            :value="$onHoldProjects"
            suffix="proyek"
            :variant="$onHoldProjects > 0 ? 'warning' : 'neutral'"
            :note="$onHoldProjects > 0 ? 'Perlu penyesuaian' : 'Tidak ada kendala'"/>
    </div>

    {{-- ── 2. Baris Grafik Utama & Dark Accent Summary Card ───── --}}
    <div class="grid gap-6 lg:grid-cols-3 mb-6 items-stretch">
        {{-- Grafik Kiri: Tren Realisasi Anggaran vs Pagu (2/3 width) --}}
        <div class="lg:col-span-2 flex flex-col overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-[0_2px_12px_rgba(44,62,80,0.04)]">
            <div class="flex flex-wrap items-center justify-between gap-2 px-6 py-4 border-b border-slate-100">
                <div>
                    <h3 class="text-[15px] font-bold text-[#2C3E50]">
                        Tren Realisasi Anggaran vs Pagu
                    </h3>
                    <p class="text-xs text-[#7F7F7F] mt-0.5">
                        Perbandingan alokasi rencana dan pengeluaran kas aktual (6 bulan terakhir)
                    </p>
                </div>
                <div class="flex items-center gap-4 text-xs">
                    <span class="inline-flex items-center gap-1.5 text-[#7F7F7F]">
                        <span class="h-2.5 w-3 rounded-xs bg-[#2C3E50] dark:bg-[#38BDF8]"></span>
                        <span class="text-[11px] font-semibold text-[#2C3E50] dark:text-[#F1F5F9]">Pagu Rencana</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-[#7F7F7F]">
                        <span class="h-2.5 w-3 rounded-xs bg-[#ED7132]"></span>
                        <span class="text-[11px] font-semibold text-[#ED7132]">Realisasi Kas</span>
                    </span>
                </div>
            </div>
            <div class="p-6 flex-1 flex flex-col justify-center">
                <div class="relative w-full h-64">
                    <canvas id="budgetTrendChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Dark Accent Summary Card (1/3 width, kanan grafik utama) --}}
        <div class="flex flex-col justify-between overflow-hidden rounded-2xl bg-[#2C3E50] dark:bg-gradient-to-br dark:from-[#16293D] dark:to-[#0F1E2E] dark:border dark:border-[#27B1AE]/25 text-white p-6 shadow-[0_2px_12px_rgba(44,62,80,0.04)]">
            <div>
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-300">
                            Total Realisasi Kas
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Pengeluaran kas aktual proyek</p>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-[#27B1AE]/20 text-[#27B1AE]">
                        Terkini
                    </span>
                </div>

                {{-- Prominent metric in Turquoise --}}
                <div class="mt-4 mb-3">
                    <div class="text-[#27B1AE] text-3xl font-extrabold tabular-nums tracking-tight">
                        Rp {{ number_format($totalExpense, 0, ',', '.') }}
                    </div>
                    <p class="text-[11px] text-slate-300 mt-1">Akumulasi pengeluaran seluruh kegiatan</p>
                </div>
            </div>

            {{-- Visual Distribusi / Donut Chart --}}
            <div class="my-2 flex-1 flex flex-col items-center justify-center">
                @if ($totalExpense > 0)
                    <div class="relative w-full h-36 flex items-center justify-center">
                        <canvas id="costDistributionChart"></canvas>
                    </div>
                @else
                    <div class="py-8 text-center text-xs text-slate-400">
                        <p class="font-medium text-slate-300">Belum ada transaksi pengeluaran kas.</p>
                        <p class="text-[11px] text-slate-400 mt-1">Data sinkron otomatis dari modul Keuangan.</p>
                    </div>
                @endif
            </div>

            {{-- Legend Breakdown --}}
            @if ($totalExpense > 0)
                <div class="w-full pt-3 border-t border-slate-700/60 space-y-1.5 text-xs">
                    @php
                        $catColors = [
                            'Material'          => 'bg-[#ED7132]',
                            'Upah Tenaga Kerja' => 'bg-[#27B1AE]',
                            'Sewa Alat'         => 'bg-[#136F9A]',
                            'Operasional'       => 'bg-slate-300',
                        ];
                    @endphp
                    @foreach ($costDistribution as $cat => $val)
                        @php
                            $percent = $totalExpense > 0 ? round(($val / $totalExpense) * 100, 1) : 0;
                            $color = $catColors[$cat] ?? 'bg-amber-400';
                        @endphp
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="flex items-center gap-1.5 text-slate-300">
                                <span class="h-2 w-2 rounded-full {{ $color }}"></span>
                                <span class="truncate max-w-[120px]">{{ $cat }}</span>
                            </span>
                            <span class="tabular font-semibold text-white">
                                Rp {{ number_format($val, 0, ',', '.') }}
                                <span class="text-slate-400 font-normal text-[10px]">({{ $percent }}%)</span>
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
        <div class="lg:col-span-2 flex flex-col overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-[0_2px_12px_rgba(44,62,80,0.04)]">
            {{-- Card header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div>
                    <h3 class="text-[15px] font-bold text-[#2C3E50]">
                        Progres Proyek Terbaru
                    </h3>
                    <p class="text-xs text-[#7F7F7F] mt-0.5">
                        Monitoring capaian fisik proyek konstruksi berjalan
                    </p>
                </div>
                <a href="{{ route('projects.index') }}"
                   class="text-xs font-semibold text-[#136F9A] hover:underline transition-colors">
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
                                    <span class="font-code text-xs font-semibold text-[#7F7F7F]">
                                        {{ $project->project_code }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('projects.show', $project) }}"
                                       class="text-[13px] font-semibold text-[#2C3E50] hover:text-[#136F9A] transition-colors">
                                        {{ $project->name }}
                                    </a>
                                </td>
                                <td>
                                    <x-badge :variant="$statusVariant">{{ $statusLabel }}</x-badge>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2.5">
                                        <div class="relative h-2 flex-1 overflow-hidden rounded-full bg-slate-100 min-w-[70px]">
                                            <div class="absolute inset-y-0 left-0 rounded-full bg-[#136F9A]"
                                                 style="width:{{ $project->overall_progress }}%;"></div>
                                        </div>
                                        <span class="shrink-0 text-xs font-bold tabular w-10 text-right text-[#2C3E50]">
                                            {{ number_format($project->overall_progress, 0) }}%
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-xs text-[#7F7F7F]">
                                    Belum ada data proyek.
                                    <br>
                                    <a href="{{ route('projects.create') }}"
                                       class="mt-1.5 inline-block font-semibold text-[#136F9A] hover:underline">
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
        <div class="flex flex-col overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-[0_2px_12px_rgba(44,62,80,0.04)]">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div>
                    <h3 class="text-[15px] font-bold text-[#2C3E50]">
                        Status Stok Material
                    </h3>
                    <p class="text-xs text-[#7F7F7F] mt-0.5">
                        Ketersediaan material di gudang
                    </p>
                </div>
                <a href="{{ route('materials.index') }}"
                   class="text-xs font-semibold text-[#136F9A] hover:underline transition-colors">
                    Kelola →
                </a>
            </div>

            <ul class="flex-1 divide-y divide-[#EEF4F8]">
                @forelse ($materials as $material)
                    @php $isLow = $material->stock <= $material->min_stock; @endphp
                    <li class="flex items-center justify-between gap-3 px-6 py-3 hover:bg-[#F8FBFD] transition-colors">
                        <div class="min-w-0">
                            <p class="truncate text-[13px] font-semibold text-[#2C3E50]">
                                {{ $material->name }}
                            </p>
                            <p class="text-[11px] text-[#7F7F7F] tabular mt-0.5">
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
                    <li class="px-6 py-8 text-center text-xs text-[#7F7F7F]">
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

    // Helper untuk membaca konfigurasi tema saat ini (Light / Dark)
    function getThemeColors() {
        const isDark = document.documentElement.classList.contains('dark');
        return {
            isDark: isDark,
            paguColor: isDark ? '#38BDF8' : '#2C3E50',
            tickColor: isDark ? '#94A3B8' : '#7F7F7F',
            gridColor: isDark ? 'rgba(255, 255, 255, 0.06)' : '#EEF4F8',
            tooltipBg: isDark ? '#0F172A' : '#2C3E50',
            doughnutBorder: isDark ? '#101E2E' : '#2C3E50'
        };
    }

    let themeColors = getThemeColors();

    // ── Global Chart.js Defaults (StarAdmin Soft-UI Typography & Colors) ──
    Chart.defaults.font.family = "'Plus Jakarta Sans', 'Manrope', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
    Chart.defaults.font.size   = 11;
    Chart.defaults.color       = themeColors.tickColor;

    let trendChartInstance = null;
    let distChartInstance = null;

    // 1. Chart Tren Anggaran vs Realisasi (Combo Bar & Line)
    const trendCtx = document.getElementById('budgetTrendChart');
    if (trendCtx) {
        const ctx = trendCtx.getContext('2d');
        // Orange Gradient for Realisasi Kas
        const orangeGrad = ctx.createLinearGradient(0, 0, 0, 260);
        orangeGrad.addColorStop(0, '#ED7132');
        orangeGrad.addColorStop(1, 'rgba(237, 113, 50, 0.4)');

        trendChartInstance = new Chart(trendCtx, {
            type: 'bar',
            data: {
                labels: @json($trendLabels),
                datasets: [
                    {
                        type: 'line',
                        label: 'Pagu Rencana',
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
                        label: 'Realisasi Kas',
                        data: @json($trendActuals),
                        backgroundColor: orangeGrad,
                        hoverBackgroundColor: '#ED7132',
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
                        bodyColor: '#F8FBFD',
                        titleFont: { size: 12, weight: '700' },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 8,
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
        // StarAdmin Palette: Orange, Turquoise, Primary Blue, Muted Light Blue
        const colorPalette = ['#ED7132', '#27B1AE', '#136F9A', '#9EACB9', '#F8FBFD'];

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
                        backgroundColor: '#1a252f',
                        titleColor: '#FFFFFF',
                        bodyColor: '#27B1AE',
                        padding: 10,
                        cornerRadius: 8,
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

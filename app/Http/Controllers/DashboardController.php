<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Material;
use App\Models\Budget;
use App\Models\ProjectSchedule;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Statistik Proyek (4 KPI Atas)
        $totalProjects     = Project::count();
        $ongoingProjects   = Project::where('status', 'ongoing')->count();
        $completedProjects = Project::where('status', 'completed')->count();
        $onHoldProjects    = Project::where('status', 'on_hold')->count();

        // 2. Data Tren Biaya Bulanan (6 bulan terakhir)
        $monthlyTrend = [];
        $now = now();
        for ($i = 5; $i >= 0; $i--) {
            $monthDate    = (clone $now)->subMonths($i);
            $startOfMonth = (clone $monthDate)->startOfMonth()->toDateString();
            $endOfMonth   = (clone $monthDate)->endOfMonth()->toDateString();
            $monthLabel   = $monthDate->translatedFormat('M Y');

            // Realisasi pengeluaran riil bulan ini
            $actualExpense = (float) Budget::where('status', '!=', 'rejected')
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->sum('amount');

            // Alokasi pagu proyek aktif bulan ini
            $activeProjectsBudget = (float) Project::where('status', '!=', 'on_hold')
                ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                    $query->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                          ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth])
                          ->orWhere(function ($q) use ($startOfMonth, $endOfMonth) {
                              $q->where('start_date', '<=', $startOfMonth)
                                ->where(function ($sq) use ($endOfMonth) {
                                    $sq->whereNull('end_date')->orWhere('end_date', '>=', $endOfMonth);
                                });
                          });
                })
                ->sum('budget');

            $plannedBudget = $activeProjectsBudget > 0 ? round($activeProjectsBudget / 6, 2) : 0;

            $monthlyTrend[] = [
                'label'  => $monthLabel,
                'budget' => $plannedBudget,
                'actual' => $actualExpense,
            ];
        }

        $trendLabels  = array_column($monthlyTrend, 'label');
        $trendBudgets = array_column($monthlyTrend, 'budget');
        $trendActuals = array_column($monthlyTrend, 'actual');

        // 3. Data Distribusi Pengeluaran Kas (Kategori)
        $standardCategories  = ['Material', 'Upah Tenaga Kerja', 'Sewa Alat', 'Operasional'];
        $rawCategoryExpenses = Budget::where('status', '!=', 'rejected')
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        $costDistribution = [];
        foreach ($standardCategories as $cat) {
            $costDistribution[$cat] = (float) ($rawCategoryExpenses[$cat] ?? 0);
        }
        foreach ($rawCategoryExpenses as $cat => $val) {
            if (!isset($costDistribution[$cat])) {
                $costDistribution[$cat] = (float) $val;
            }
        }

        $totalExpense = array_sum($costDistribution);

        // 4. Data Progres Proyek Aktif
        $activeProjectsChart = Project::whereIn('status', ['ongoing', 'planning'])
            ->orderBy('overall_progress', 'desc')
            ->take(5)
            ->get(['id', 'project_code', 'name', 'overall_progress', 'status']);

        // 5. Data Proyek untuk Tabel
        $projects = Project::latest()->take(5)->get();

        // 6. Data Material untuk Panel Kanan
        $materials = Material::orderBy('stock', 'asc')->take(5)->get();

        return view('dashboard', compact(
            'totalProjects', 'ongoingProjects', 'completedProjects', 'onHoldProjects',
            'projects', 'materials',
            'trendLabels', 'trendBudgets', 'trendActuals',
            'costDistribution', 'totalExpense',
            'activeProjectsChart'
        ));
    }
}

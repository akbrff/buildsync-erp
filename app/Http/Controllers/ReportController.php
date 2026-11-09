<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Material;
use App\Models\Budget;
use App\Models\CashFlow;
use App\Models\WorkerAttendance;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $projectId = $request->integer('project_id');
        $projects = Project::orderBy('name')->get(['id', 'project_code', 'name']);
        $selectedProject = $projectId
            ? Project::with(['schedules.latestProgress', 'budgets'])->find($projectId)
            : null;

        $totalProjects = Project::count();
        $totalBudget = Project::sum('budget');

        $approvedBudgets = Budget::where('status', 'approved')
            ->when($projectId, fn ($query) => $query->where('project_id', $projectId))
            ->get();
        $cashOutflows = CashFlow::where('type', 'out')
            ->when($projectId, fn ($query) => $query->where('project_id', $projectId))
            ->get();

        $totalActualCost = (float) $approvedBudgets->sum('amount') + (float) $cashOutflows->sum('amount');
        $costByCategory = $approvedBudgets->groupBy('category')->map->sum('amount');

        foreach ($cashOutflows->groupBy('category') as $category => $rows) {
            $costByCategory->put($category, (float) ($costByCategory->get($category, 0)) + (float) $rows->sum('amount'));
        }

        $materials = Material::orderBy('stock', 'asc')->get();
        $laborActual = (float) WorkerAttendance::where('status', 'paid')
            ->when($projectId, fn ($query) => $query->where('project_id', $projectId))
            ->sum('amount');
        $supplierPayments = (float) CashFlow::where('category', 'Pembayaran Supplier')
            ->when($projectId, fn ($query) => $query->where('project_id', $projectId))
            ->sum('amount');

        return view('reports.index', compact(
            'projects', 'projectId', 'selectedProject', 'totalProjects', 'totalBudget',
            'totalActualCost', 'costByCategory', 'materials', 'laborActual', 'supplierPayments'
        ));
    }
}
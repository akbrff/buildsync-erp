<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Material;
use App\Models\Budget;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $projectId = $request->integer('project_id');
        
        $projects = Project::orderBy('name')->get(['id', 'project_code', 'name']);
        
        // Ambil data proyek yang dipilih (atau null jika belum ada)
        $selectedProject = $projectId ? Project::with(['schedules.latestProgress', 'budgets'])->find($projectId) : null;

        // Statistik Global (Jika tidak memilih proyek)
        $totalProjects = Project::count();
        $totalBudget = Project::sum('budget');
        
        // Statistik Khusus Proyek (Jika memilih proyek)
        $totalActualCost = 0;
        $costByCategory = [];
        if ($selectedProject) {
            $totalActualCost = $selectedProject->budgets->sum('amount');
            $costByCategory = $selectedProject->budgets->groupBy('category')->map->sum('amount');
        }

        // Data Material (Global)
        $materials = Material::orderBy('stock', 'asc')->get();

        return view('reports.index', compact(
            'projects', 'projectId', 'selectedProject', 
            'totalProjects', 'totalBudget', 
            'totalActualCost', 'costByCategory', 'materials'
        ));
    }
}
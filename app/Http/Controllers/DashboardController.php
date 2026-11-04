<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Material;
use App\Models\ProjectSchedule;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Statistik Proyek
        $totalProjects = Project::count();
        $ongoingProjects = Project::where('status', 'ongoing')->count();
        $completedProjects = Project::where('status', 'completed')->count();
        $onHoldProjects = Project::where('status', 'on_hold')->count();

        // 2. Data Proyek untuk Tabel (Ambil 5 terbaru)
        $projects = Project::latest()->take(5)->get();

        // 3. Data Material untuk Panel Kanan
        $materials = Material::orderBy('stock', 'asc')->take(5)->get();

        return view('dashboard', compact(
            'totalProjects', 'ongoingProjects', 'completedProjects', 'onHoldProjects',
            'projects', 'materials'
        ));
    }
}
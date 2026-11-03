<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectSchedule;
use Illuminate\Http\Request;

class ScheduleDashboardController extends Controller
{
    public function index()
    {
        // 1. Statistik Ringkasan
        $totalProjects = Project::has('schedules')->count();
        $totalSchedules = ProjectSchedule::count();
        
        $schedules = ProjectSchedule::with('latestProgress')->get();
        
        $completed = $schedules->filter(fn($s) => $s->status === 'Selesai')->count();
        $ongoing = $schedules->filter(fn($s) => $s->status === 'Berlangsung')->count();
        $notStarted = $schedules->filter(fn($s) => $s->status === 'Belum Dimulai')->count();
        $late = $schedules->filter(fn($s) => $s->status === 'Terlambat')->count();

        // 2. Progres Keseluruhan (Global)
        // Menghitung rata-rata progres dari semua proyek yang ada
        $projects = Project::has('schedules')->get();
        $overallProgress = $projects->avg('overall_progress') ?? 0;

        return view('schedules.dashboard', compact(
            'totalProjects', 'totalSchedules', 'completed', 'ongoing', 
            'notStarted', 'late', 'overallProgress'
        ));
    }

    public function timeline(Request $request)
    {
        $projectId = $request->integer('project_id');
        $projects = Project::orderBy('name')->get(['id', 'project_code', 'name']);

        $schedules = ProjectSchedule::with(['project', 'latestProgress'])
            ->when($projectId, fn($q) => $q->where('project_id', $projectId))
            ->orderBy('start_date')
            ->get();

        return view('schedules.timeline', compact('projects', 'projectId', 'schedules'));
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectSchedule;
use Illuminate\Http\Request;

class ScheduleMonitoringController extends Controller
{
    public function index(Request $request)
    {
        $projectId = $request->integer('project_id');
        $statusFilter = $request->input('status');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $projects = Project::orderBy('name')->get(['id', 'project_code', 'name']);
        
        $schedulesQuery = ProjectSchedule::with(['project', 'latestProgress'])
            ->when($projectId, fn($q) => $q->where('project_id', $projectId))
            ->when($startDate, fn($q) => $q->where('start_date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->where('end_date', '<=', $endDate))
            ->orderBy('end_date');

        if ($statusFilter) {
            $schedulesQuery->byStatus($statusFilter);
        }

        $schedules = $schedulesQuery->get();

        // Kelompokkan berdasarkan status
        $grouped = [
            'Terlambat' => $schedules->filter(fn($s) => $s->status === 'Terlambat'),
            'Berlangsung' => $schedules->filter(fn($s) => $s->status === 'Berlangsung'),
            'Belum Dimulai' => $schedules->filter(fn($s) => $s->status === 'Belum Dimulai'),
            'Selesai' => $schedules->filter(fn($s) => $s->status === 'Selesai'),
        ];

        $selectedProject = $projectId ? Project::find($projectId) : null;

        return view('schedules.monitoring', compact('projects', 'projectId', 'grouped', 'selectedProject', 'schedules', 'statusFilter', 'startDate', 'endDate'));
    }
}
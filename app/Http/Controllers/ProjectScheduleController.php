<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectScheduleRequest;
use App\Http\Requests\UpdateProjectScheduleRequest;
use App\Models\Project;
use App\Models\ProjectSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProjectScheduleController extends Controller
{
    public function index(Request $request)
    {
        $projectId = $request->integer('project_id');
        $statusFilter = $request->input('status');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $schedules = ProjectSchedule::with('project', 'latestProgress')
            ->when($projectId, fn ($query) => $query->where('project_id', $projectId))
            ->when($startDate, fn ($query) => $query->where('start_date', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->where('end_date', '<=', $endDate))
            ->when($statusFilter, fn ($query) => $query->byStatus($statusFilter))
            ->orderBy('project_id')
            ->orderBy('start_date')
            ->paginate(10)
            ->withQueryString();

        $projects = Project::orderBy('name')->get(['id', 'project_code', 'name']);

        return view('schedules.index', compact('schedules', 'projects', 'projectId', 'statusFilter', 'startDate', 'endDate'));
    }

    public function create(Request $request)
    {
        $projects = Project::orderBy('name')->get(['id', 'project_code', 'name']);
        $selectedProjectId = $request->integer('project_id') ?: null;

        return view('schedules.create', compact('projects', 'selectedProjectId'));
    }

    public function store(StoreProjectScheduleRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            Project::whereKey($data['project_id'])->lockForUpdate()->firstOrFail();
            $this->ensureWeightFits((int) $data['project_id'], (float) $data['weight']);
            ProjectSchedule::create($data);
        });

        return redirect()
            ->route('schedules.index', ['project_id' => $data['project_id']])
            ->with('success', 'Jadwal pekerjaan berhasil ditambahkan.');
    }

        public function show(ProjectSchedule $schedule)
    {
        $schedule->load('project');
        $usedWeight = (float) ProjectSchedule::where('project_id', $schedule->project_id)->sum('weight');

        $latestProgress = $schedule->latestProgress;
        $history = $schedule->progressUpdates()
            ->orderByDesc('recorded_at')
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'riwayat');

        return view('schedules.show', compact('schedule', 'usedWeight', 'latestProgress', 'history'));
    }

    public function edit(ProjectSchedule $schedule)
    {
        $schedule->load('project');

        return view('schedules.edit', compact('schedule'));
    }

    public function update(UpdateProjectScheduleRequest $request, ProjectSchedule $schedule)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $schedule) {
            Project::whereKey($schedule->project_id)->lockForUpdate()->firstOrFail();
            $this->ensureWeightFits($schedule->project_id, (float) $data['weight'], $schedule->id);
            $schedule->update($data);
        });

        return redirect()
            ->route('schedules.index', ['project_id' => $schedule->project_id])
            ->with('success', 'Jadwal pekerjaan berhasil diperbarui.');
    }

    public function destroy(ProjectSchedule $schedule)
    {
        $projectId = $schedule->project_id;

        if ($schedule->progressUpdates()->exists()) {
            return redirect()
                ->route('schedules.index', ['project_id' => $projectId])
                ->with('error', 'Jadwal tidak dapat dihapus karena memiliki riwayat progres. Riwayat tersebut harus dipertahankan sebagai catatan proyek.');
        }

        $schedule->delete();

        return redirect()
            ->route('schedules.index', ['project_id' => $projectId])
            ->with('success', 'Jadwal pekerjaan berhasil dihapus.');
    }

    /**
     * Pastikan total bobot pekerjaan dalam satu proyek tidak melebihi 100%.
     * Perbandingan memakai bilangan bulat per seperseratus agar bebas galat pecahan.
     */
    private function ensureWeightFits(int $projectId, float $weight, ?int $ignoreId = null): void
    {
        $otherUnits = (int) round(
            ProjectSchedule::where('project_id', $projectId)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->sum('weight') * 100
        );
        $newUnits = (int) round($weight * 100);

        if ($otherUnits + $newUnits > 10000) {
            $remaining = max(0, 10000 - $otherUnits) / 100;

            throw ValidationException::withMessages([
                'weight' => 'Total bobot proyek ini akan melebihi 100%. Sisa bobot yang tersedia: '
                    .number_format($remaining, 2, ',', '.').'%.',
            ]);
        }
    }
}
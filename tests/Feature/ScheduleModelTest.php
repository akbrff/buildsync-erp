<?php

namespace Tests\Feature;

use App\Models\ProgressUpdate;
use App\Models\Project;
use App\Models\ProjectSchedule;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleModelTest extends TestCase
{
    use RefreshDatabase;

    private function makeProject(string $code = 'PRJ-001'): Project
    {
        return Project::create([
            'project_code' => $code,
            'name' => 'Proyek '.$code,
            'start_date' => '2026-11-01',
            'budget' => 100000000,
            'status' => 'ongoing',
        ]);
    }

    private function makeSchedule(Project $project, string $name = 'Pekerjaan Pondasi'): ProjectSchedule
    {
        return $project->schedules()->create([
            'name' => $name,
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
            'weight' => 40,
        ]);
    }

    private function makeProgress(ProjectSchedule $schedule, float $percentage, string $date): ProgressUpdate
    {
        return $schedule->progressUpdates()->create([
            'progress_percentage' => $percentage,
            'recorded_at' => $date,
        ]);
    }

    public function test_project_has_many_schedules(): void
    {
        $project = $this->makeProject();
        $this->makeSchedule($project, 'Pondasi');
        $this->makeSchedule($project, 'Struktur');

        $this->assertCount(2, $project->schedules);
    }

    public function test_schedule_belongs_to_project(): void
    {
        $project = $this->makeProject();
        $schedule = $this->makeSchedule($project);

        $this->assertTrue($schedule->project->is($project));
    }

    public function test_schedule_has_many_progress_updates(): void
    {
        $schedule = $this->makeSchedule($this->makeProject());
        $this->makeProgress($schedule, 20, '2026-11-01');
        $this->makeProgress($schedule, 50, '2026-11-10');

        $this->assertCount(2, $schedule->progressUpdates);
    }

    public function test_latest_progress_returns_most_recent_by_date(): void
    {
        $schedule = $this->makeSchedule($this->makeProject());
        $this->makeProgress($schedule, 20, '2026-11-01');
        $this->makeProgress($schedule, 70, '2026-11-05');
        $this->makeProgress($schedule, 60, '2026-11-03');

        $latest = ProjectSchedule::with('latestProgress')->find($schedule->id)->latestProgress;

        $this->assertSame('70.00', $latest->progress_percentage);
    }

    public function test_latest_progress_breaks_ties_by_id(): void
    {
        $schedule = $this->makeSchedule($this->makeProject());
        $this->makeProgress($schedule, 40, '2026-11-05');
        $this->makeProgress($schedule, 55, '2026-11-05');

        $latest = ProjectSchedule::with('latestProgress')->find($schedule->id)->latestProgress;

        $this->assertSame('55.00', $latest->progress_percentage);
    }

    public function test_deleting_schedule_cascades_to_progress_updates(): void
    {
        $schedule = $this->makeSchedule($this->makeProject());
        $this->makeProgress($schedule, 20, '2026-11-01');
        $this->makeProgress($schedule, 50, '2026-11-10');
        $this->assertDatabaseCount('progress_updates', 2);

        $schedule->delete();

        $this->assertDatabaseCount('progress_updates', 0);
    }

    public function test_project_with_schedules_cannot_be_deleted(): void
    {
        $project = $this->makeProject();
        $this->makeSchedule($project);

        $this->expectException(QueryException::class);

        $project->delete();
    }

    public function test_progress_update_requires_existing_schedule(): void
    {
        $this->expectException(QueryException::class);

        ProgressUpdate::create([
            'project_schedule_id' => 9999,
            'progress_percentage' => 10,
            'recorded_at' => '2026-11-01',
        ]);
    }
}
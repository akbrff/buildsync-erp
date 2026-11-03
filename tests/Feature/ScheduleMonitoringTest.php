<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleMonitoringTest extends TestCase
{
    use RefreshDatabase;

    private function makeProject(): Project
    {
        return Project::create([
            'project_code' => 'PRJ-MON',
            'name' => 'Proyek Monitoring',
            'start_date' => '2026-11-01',
            'budget' => 100000000,
            'status' => 'ongoing',
        ]);
    }

    private function makeSchedule(Project $project, array $attributes = []): ProjectSchedule
    {
        return $project->schedules()->create(array_merge([
            'name' => 'Pekerjaan Uji',
            'start_date' => now()->subDays(10)->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'weight' => 50,
        ], $attributes));
    }

    private function seedProgress(ProjectSchedule $schedule, float $percentage, string $date): void
    {
        $schedule->progressUpdates()->create([
            'progress_percentage' => $percentage,
            'recorded_at' => $date,
        ]);
    }

    // --- PENGUJIAN STATUS ---

    public function test_status_belum_dimulai(): void
    {
        $project = $this->makeProject();
        $schedule = $this->makeSchedule($project);

        $this->assertEquals('Belum Dimulai', $schedule->status);
    }

    public function test_status_berlangsung(): void
    {
        $project = $this->makeProject();
        $schedule = $this->makeSchedule($project);
        $this->seedProgress($schedule, 50, now()->toDateString());

        $this->assertEquals('Berlangsung', $schedule->fresh()->status);
    }

    public function test_status_selesai(): void
    {
        $project = $this->makeProject();
        $schedule = $this->makeSchedule($project, ['end_date' => now()->subDay()->toDateString()]);
        $this->seedProgress($schedule, 100, now()->toDateString());

        // Meskipun tanggal sudah lewat, jika progres 100% statusnya Selesai
        $this->assertEquals('Selesai', $schedule->fresh()->status);
    }

    public function test_status_terlambat(): void
    {
        $project = $this->makeProject();
        // Target selesai kemarin, belum ada progres atau progres belum 100%
        $schedule = $this->makeSchedule($project, ['end_date' => now()->subDay()->toDateString()]);
        
        $this->assertEquals('Terlambat', $schedule->status);
    }

    // --- PENGUJIAN PERHITUNGAN PROGRES KESELURUHAN (WEIGHTED) ---

    public function test_overall_progress_dihitung_berdasarkan_bobot(): void
    {
        $project = $this->makeProject();
        
        // Pekerjaan A: bobot 40%, progres 100%
        $scheduleA = $this->makeSchedule($project, ['weight' => 40, 'end_date' => now()->addDay()->toDateString()]);
        $this->seedProgress($scheduleA, 100, now()->toDateString());

        // Pekerjaan B: bobot 60%, progres 50%
        $scheduleB = $this->makeSchedule($project, ['weight' => 60, 'end_date' => now()->addDay()->toDateString()]);
        $this->seedProgress($scheduleB, 50, now()->toDateString());

        // Total bobot = 100
        // Rumus = ( (40 * 100) + (60 * 50) ) / 100 = 70
        $project->refresh();
        $this->assertEquals(70.0, $project->overall_progress);
    }

    public function test_overall_progress_dengan_bobot_tidak_penuh(): void
    {
        $project = $this->makeProject();
        
        // Pekerjaan A: bobot 20%, progres 100%
        $scheduleA = $this->makeSchedule($project, ['weight' => 20, 'end_date' => now()->addDay()->toDateString()]);
        $this->seedProgress($scheduleA, 100, now()->toDateString());

        // Pekerjaan B: bobot 30%, progres 50%
        $scheduleB = $this->makeSchedule($project, ['weight' => 30, 'end_date' => now()->addDay()->toDateString()]);
        $this->seedProgress($scheduleB, 50, now()->toDateString());

        // Total bobot = 50 (belum mencapai 100)
        // ( (20 * 100) + (30 * 50) ) / 50 = (2000 + 1500) / 50 = 3500 / 50 = 70
        $project->refresh();
        $this->assertEquals(70.0, $project->overall_progress);
    }
}
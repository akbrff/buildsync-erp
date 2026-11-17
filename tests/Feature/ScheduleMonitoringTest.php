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

    // --- PENGUJIAN PERHITUNGAN PROGRES KESELURUHAN ---

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
        $this->assertEquals(100.0, $project->total_weight);
        $this->assertTrue($project->is_weight_final);
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
        // Rumus baru: ( (20 * 100) + (30 * 50) ) / 100 = 35
        $project->refresh();
        $this->assertEquals(35.0, $project->overall_progress);
        $this->assertEquals(50.0, $project->total_weight);
        $this->assertFalse($project->is_weight_final);
    }

    public function test_overall_progress_final_jika_bobot_100(): void
    {
        $project = $this->makeProject();
        
        $scheduleA = $this->makeSchedule($project, ['weight' => 40, 'end_date' => now()->addDay()->toDateString()]);
        $this->seedProgress($scheduleA, 100, now()->toDateString());
        
        $scheduleB = $this->makeSchedule($project, ['weight' => 60, 'end_date' => now()->addDay()->toDateString()]);
        $this->seedProgress($scheduleB, 50, now()->toDateString());

        $project->refresh();
        $this->assertEquals(70.0, $project->overall_progress);
        $this->assertEquals(100.0, $project->total_weight);
        $this->assertTrue($project->is_weight_final);
    }

    public function test_halaman_monitoring_menampilkan_peringatan_jika_bobot_belum_100(): void
    {
        $project = $this->makeProject();
        $schedule = $this->makeSchedule($project, ['weight' => 50]); // Bobot 50% belum final

        $response = $this->get(route('schedules.monitoring', ['project_id' => $project->id]));

        $response->assertStatus(200);
        // Memastikan teks peringatan muncul (English default)
        $response->assertSee('Not Final');
        $response->assertSee('50,00% of 100%');

        // Memastikan teks peringatan muncul (Indonesian)
        $user = auth()->user();
        $user->update(['locale' => 'id']);
        $idResponse = $this->get(route('schedules.monitoring', ['project_id' => $project->id]));
        $idResponse->assertStatus(200);
        $idResponse->assertSee('Belum Final');
        $idResponse->assertSee('50,00% dari 100%');
        $user->update(['locale' => 'en']);
    }
}
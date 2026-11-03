<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function makeProject(): Project
    {
        return Project::create([
            'project_code' => 'PRJ-DASH',
            'name' => 'Proyek Dashboard',
            'start_date' => now()->subDays(10)->toDateString(),
            'budget' => 100000000,
            'status' => 'ongoing',
        ]);
    }

    public function test_dashboard_menampilkan_statistik_yang_benar(): void
    {
        $project = $this->makeProject();
        
        // 1. Pekerjaan Selesai
        $s1 = $project->schedules()->create([
            'name' => 'Pekerjaan A', 'start_date' => now()->subDays(5)->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(), 'weight' => 50
        ]);
        $s1->progressUpdates()->create(['progress_percentage' => 100, 'recorded_at' => now()->toDateString()]);

        // 2. Pekerjaan Terlambat
        $s2 = $project->schedules()->create([
            'name' => 'Pekerjaan B', 'start_date' => now()->subDays(20)->toDateString(),
            'end_date' => now()->subDays(2)->toDateString(), 'weight' => 50
        ]);

        $response = $this->get(route('schedules.dashboard'));
        
        $response->assertStatus(200);
        $response->assertSee('Dashboard Jadwal & Progres', false);
        
        $response->assertViewHas('totalProjects', 1);
        $response->assertViewHas('totalSchedules', 2);
        $response->assertViewHas('completed', 1);
        $response->assertViewHas('late', 1);
    }

    public function test_timeline_menampilkan_data_jadwal(): void
    {
        $project = $this->makeProject();
        
        $project->schedules()->create([
            'name' => 'Pekerjaan Timeline',
            'start_date' => now()->subDays(5)->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'weight' => 100
        ]);

        $response = $this->get(route('schedules.timeline'));
        
        $response->assertStatus(200);
        $response->assertSee('Timeline & Gantt Chart', false);
        $response->assertSee('Pekerjaan Timeline');
        $response->assertSee('Garis Waktu (Gantt)');
    }

    public function test_dashboard_hanya_menghitung_rata_rata_dari_proyek_final(): void
    {
        // Proyek 1: Bobot 100%, progres 100% (Final)
        $p1 = $this->makeProject();
        $s1 = $p1->schedules()->create([
            'name' => 'P1', 'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(), 'weight' => 100
        ]);
        $s1->progressUpdates()->create(['progress_percentage' => 100, 'recorded_at' => now()->toDateString()]);

        // Proyek 2: Bobot 50%, progres 50% (Tidak Final)
        $p2 = Project::create([
            'project_code' => 'PRJ-INCOMPLETE', 'name' => 'Proyek Belum Final',
            'start_date' => now()->toDateString(), 'budget' => 1000, 'status' => 'ongoing'
        ]);
        $s2 = $p2->schedules()->create([
            'name' => 'P2', 'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(), 'weight' => 50
        ]);
        $s2->progressUpdates()->create(['progress_percentage' => 50, 'recorded_at' => now()->toDateString()]);

        $response = $this->get(route('schedules.dashboard'));

        // Rata-rata progres harus 100 (dari P1), bukan 75
        $response->assertViewHas('overallProgress', 100.0);
        $response->assertViewHas('incompleteProjectsCount', 1);
    }
}
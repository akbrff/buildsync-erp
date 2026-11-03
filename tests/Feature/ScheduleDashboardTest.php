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
        
        // Memastikan angka statistik sesuai (Total Proyek: 1, Total Pekerjaan: 2, Selesai: 1, Terlambat: 1)
        // Kita menggunakan assertViewHas untuk mengecek data yang dikirim controller ke view
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
}
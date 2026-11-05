<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_can_be_created(): void
    {
        $project = Project::create([
            'project_code' => 'PRJ-001',
            'name' => 'Pembangunan Gedung Kantor',
            'location' => 'Jakarta',
            'start_date' => '2026-11-01',
            'end_date' => '2027-05-01',
            'budget' => 500000000,
            'status' => 'planning',
            'description' => 'Proyek pembangunan gedung kantor.',
        ]);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'project_code' => 'PRJ-001',
            'name' => 'Pembangunan Gedung Kantor',
        ]);
    }

    public function test_project_code_must_be_unique(): void
    {
        Project::create([
            'project_code' => 'PRJ-001',
            'name' => 'Proyek Pertama',
            'start_date' => '2026-11-01',
            'budget' => 100000000,
            'status' => 'planning',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Project::create([
            'project_code' => 'PRJ-001',
            'name' => 'Proyek Kedua',
            'start_date' => '2026-11-02',
            'budget' => 200000000,
            'status' => 'ongoing',
        ]);
    }

    public function test_project_tidak_bisa_dihapus_jika_memiliki_jadwal(): void
    {
        $project = Project::create([
            'project_code' => 'PRJ-DEL',
            'name' => 'Proyek Test Hapus',
            'start_date' => now()->toDateString(),
            'budget' => 1000000,
            'status' => 'ongoing'
        ]);

        $project->schedules()->create([
            'name' => 'Pekerjaan 1',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'weight' => 100
        ]);

        $response = $this->delete(route('projects.destroy', $project));

        // Harus redirect dengan pesan error, dan proyek TIDAK terhapus
        $response->assertRedirect(route('projects.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('projects', ['id' => $project->id]);
    }
}
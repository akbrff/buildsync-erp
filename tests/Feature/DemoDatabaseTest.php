<?php

namespace Tests\Feature;

use App\Models\Budget;
use App\Models\CashFlow;
use App\Models\Material;
use App\Models\ProgressUpdate;
use App\Models\Project;
use App\Models\ProjectSchedule;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\StockTransaction;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Worker;
use App\Models\WorkerAssignment;
use App\Models\WorkerAttendance;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class DemoDatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_data_seeder_populates_all_modules_with_required_records_and_users(): void
    {
        $this->seed(DemoDataSeeder::class);

        // 1. Verify Users & Required Exact Names
        $this->assertDatabaseHas('users', [
            'name' => 'AHMAD SAUQI ZAMZAMI',
            'role' => 'superadmin',
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'MUHAMAD AKBAR FEBIANSYAH',
            'role' => 'project_manager',
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'MUHAMMAD FADHILLAH AKBAR',
            'role' => 'logistics',
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'SITI NURHALIZA',
            'role' => 'finance',
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'JOKO PRASETYO',
            'role' => 'field_staff',
            'status' => 'active',
        ]);

        // Verify exactly 5 distinct demo roles exist
        $distinctRoles = User::whereIn('email', [
            'admin@buildsync.com',
            'akbar.febiansyah@buildsync.com',
            'fadhillah.akbar@buildsync.com',
            'siti.nurhaliza@buildsync.com',
            'joko.prasetyo@buildsync.com',
        ])->pluck('role')->unique();

        $this->assertCount(5, $distinctRoles);

        // Verify Field Staff is linked to worker
        $fieldUser = User::where('role', 'field_staff')->first();
        $this->assertNotNull($fieldUser->worker_id);

        // 2. Verify Authentication for All 5 Demo Users
        $credentials = [
            ['email' => 'admin@buildsync.com', 'password' => 'password'],
            ['email' => 'akbar.febiansyah@buildsync.com', 'password' => 'password'],
            ['email' => 'fadhillah.akbar@buildsync.com', 'password' => 'password'],
            ['email' => 'siti.nurhaliza@buildsync.com', 'password' => 'password'],
            ['email' => 'joko.prasetyo@buildsync.com', 'password' => 'password'],
        ];

        foreach ($credentials as $cred) {
            $this->assertTrue(Auth::attempt($cred), "Failed to authenticate {$cred['email']}");
            Auth::logout();
        }

        // 3. Verify 30 Records per Relevant Operational Module
        $this->assertGreaterThanOrEqual(30, Project::count());
        $this->assertGreaterThanOrEqual(30, ProjectSchedule::count());
        $this->assertGreaterThanOrEqual(30, ProgressUpdate::count());
        $this->assertGreaterThanOrEqual(30, Material::count());
        $this->assertGreaterThanOrEqual(30, Supplier::count());
        $this->assertGreaterThanOrEqual(30, PurchaseOrder::count());
        $this->assertGreaterThanOrEqual(30, PurchaseOrderItem::count());
        $this->assertGreaterThanOrEqual(30, StockTransaction::count());
        $this->assertGreaterThanOrEqual(30, Worker::count());
        $this->assertGreaterThanOrEqual(30, WorkerAssignment::count());
        $this->assertGreaterThanOrEqual(30, WorkerAttendance::count());
        $this->assertGreaterThanOrEqual(30, Budget::count());
        $this->assertGreaterThanOrEqual(30, CashFlow::count());

        // 4. Verify Project Schedule Cumulative Weights Do Not Exceed 100%
        $projects = Project::with('schedules')->get();
        foreach ($projects as $project) {
            $totalWeight = $project->schedules->sum('weight');
            $this->assertLessThanOrEqual(100.001, $totalWeight, "Project {$project->project_code} weight exceeds 100%");
        }
    }

    public function test_standalone_demo_sql_file_exists_and_contains_valid_export(): void
    {
        $sqlPath = base_path('database/demo/buildsync_demo.sql');
        $this->assertFileExists($sqlPath);

        $content = file_get_contents($sqlPath);
        $this->assertGreaterThan(50000, strlen($content));
        $this->assertStringContainsString('PRAGMA foreign_keys', $content);
        $this->assertStringContainsString('CREATE TABLE', $content);
        $this->assertStringContainsString('AHMAD SAUQI ZAMZAMI', $content);
        $this->assertStringContainsString('MUHAMAD AKBAR FEBIANSYAH', $content);
        $this->assertStringContainsString('MUHAMMAD FADHILLAH AKBAR', $content);
        $this->assertStringContainsString('PRJ-2026-001', $content);
        $this->assertStringContainsString('PRJ-2026-030', $content);
    }
}

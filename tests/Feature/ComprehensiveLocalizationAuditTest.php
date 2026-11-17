<?php

namespace Tests\Feature;

use App\Models\Budget;
use App\Models\Material;
use App\Models\Project;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComprehensiveLocalizationAuditTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'superadmin',
            'locale' => 'en',
        ]);
        $this->actingAs($this->admin);

        $this->project = Project::create([
            'project_code' => 'PRJ-TEST-001',
            'name' => 'Menara BuildSync',
            'budget' => 1000000000,
            'start_date' => now()->toDateString(),
            'status' => 'ongoing',
        ]);

        Material::create([
            'code' => 'MAT-001',
            'name' => 'Semen Portland',
            'unit' => 'Sak',
            'price' => 65000,
            'stock' => 100,
            'min_stock' => 20,
        ]);

        Supplier::create([
            'supplier_code' => 'SUP-001',
            'name' => 'PT Semen Perkasa',
            'status' => 'active',
        ]);

        Worker::create([
            'worker_code' => 'WRK-001',
            'name' => 'Tukang Handal',
            'position' => 'Tukang Batu',
            'daily_rate' => 175000,
            'status' => 'active',
        ]);

        Budget::create([
            'project_id' => $this->project->id,
            'category' => 'Material',
            'description' => 'Pembelian semen',
            'amount' => 5000000,
            'date' => now()->toDateString(),
            'status' => 'approved',
        ]);
    }

    public function test_all_major_modules_render_in_english_by_default(): void
    {
        $this->admin->update(['locale' => 'en']);

        // 1. Dashboard
        $res = $this->get(route('dashboard'));
        $res->assertOk()
            ->assertSee('<html lang="en">', false)
            ->assertSee('Total Projects')
            ->assertSee('Project Progress');

        // 2. Settings
        $res = $this->get(route('settings.index'));
        $res->assertOk()
            ->assertSee('<html lang="en">', false)
            ->assertSee('Language &amp; Regional Settings', false)
            ->assertSee('Save Settings');

        // 3. Projects
        $res = $this->get(route('projects.index'));
        $res->assertOk()
            ->assertSee('<html lang="en">', false)
            ->assertSee('Construction Project List')
            ->assertSee('Add Project');

        // 4. Schedules
        $res = $this->get(route('schedules.index'));
        $res->assertOk()
            ->assertSee('<html lang="en">', false)
            ->assertSee('Work Schedule List')
            ->assertSee('Add Schedule');

        // 5. Materials & Inventory
        $res = $this->get(route('materials.index'));
        $res->assertOk()
            ->assertSee('<html lang="en">', false)
            ->assertSee('Materials &amp; Inventory', false)
            ->assertSee('Add Material');

        // 6. Procurement
        $res = $this->get(route('procurement.index'));
        $res->assertOk()
            ->assertSee('<html lang="en">', false)
            ->assertSee('Suppliers &amp; Procurement', false)
            ->assertSee('Add Supplier')
            ->assertSee('Create Purchase Order');

        // 7. Workforce & Workers
        $res = $this->get(route('workers.index'));
        $res->assertOk()
            ->assertSee('<html lang="en">', false)
            ->assertSee('Workforce &amp; Labor', false)
            ->assertSee('Add Worker');

        // 8. Workforce Operations
        $res = $this->get(route('workforce.index'));
        $res->assertOk()
            ->assertSee('<html lang="en">', false)
            ->assertSee('Workforce Assignments &amp; Attendance', false);

        // 9. Budgets
        $res = $this->get(route('budgets.index', ['project_id' => $this->project->id]));
        $res->assertOk()
            ->assertSee('<html lang="en">', false)
            ->assertSee('Project Budget &amp; Cash Realization', false)
            ->assertSee('Record Cash Expense');

        // 10. Finance
        $res = $this->get(route('finance.index'));
        $res->assertOk()
            ->assertSee('<html lang="en">', false)
            ->assertSee('Cash Flow &amp; Financial Integration', false)
            ->assertSee('Record Manual Cash Flow');

        // 11. Reports
        $res = $this->get(route('reports.index'));
        $res->assertOk()
            ->assertSee('<html lang="en">', false)
            ->assertSee('Executive Financial &amp; Project Reports', false);

        // 12. User Management
        $res = $this->get(route('users.roles.index'));
        $res->assertOk()
            ->assertSee('<html lang="en">', false)
            ->assertSee('User Permissions')
            ->assertSee('Add User');
    }

    public function test_all_major_modules_render_in_indonesian_when_preferred(): void
    {
        $this->admin->update(['locale' => 'id']);

        // 1. Dashboard
        $res = $this->get(route('dashboard'));
        $res->assertOk()
            ->assertSee('<html lang="id">', false)
            ->assertSee('Total Proyek')
            ->assertSee('Progres Proyek');

        // 2. Settings
        $res = $this->get(route('settings.index'));
        $res->assertOk()
            ->assertSee('<html lang="id">', false)
            ->assertSee('Pengaturan Bahasa &amp; Regional', false)
            ->assertSee('Simpan Pengaturan');

        // 3. Projects
        $res = $this->get(route('projects.index'));
        $res->assertOk()
            ->assertSee('<html lang="id">', false)
            ->assertSee('Daftar Proyek Konstruksi')
            ->assertSee('Tambah Proyek');

        // 4. Schedules
        $res = $this->get(route('schedules.index'));
        $res->assertOk()
            ->assertSee('<html lang="id">', false)
            ->assertSee('Daftar Jadwal Pekerjaan')
            ->assertSee('Tambah Jadwal');

        // 5. Materials & Inventory
        $res = $this->get(route('materials.index'));
        $res->assertOk()
            ->assertSee('<html lang="id">', false)
            ->assertSee('Logistik &amp; Inventaris Material', false)
            ->assertSee('Tambah Material');

        // 6. Procurement
        $res = $this->get(route('procurement.index'));
        $res->assertOk()
            ->assertSee('<html lang="id">', false)
            ->assertSee('Supplier &amp; Pengadaan', false)
            ->assertSee('Tambah Supplier')
            ->assertSee('Buat Purchase Order');

        // 7. Workforce & Workers
        $res = $this->get(route('workers.index'));
        $res->assertOk()
            ->assertSee('<html lang="id">', false)
            ->assertSee('Tenaga Kerja')
            ->assertSee('Tambah Pekerja');

        // 8. Workforce Operations
        $res = $this->get(route('workforce.index'));
        $res->assertOk()
            ->assertSee('<html lang="id">', false)
            ->assertSee('Penugasan &amp; Kehadiran Tenaga Kerja', false);

        // 9. Budgets
        $res = $this->get(route('budgets.index', ['project_id' => $this->project->id]));
        $res->assertOk()
            ->assertSee('<html lang="id">', false)
            ->assertSee('Realisasi Anggaran &amp; Kas Proyek', false)
            ->assertSee('Catat Pengeluaran Kas');

        // 10. Finance
        $res = $this->get(route('finance.index'));
        $res->assertOk()
            ->assertSee('<html lang="id">', false)
            ->assertSee('Arus Kas &amp; Integrasi Keuangan', false)
            ->assertSee('Catat Arus Kas Manual');

        // 11. Reports
        $res = $this->get(route('reports.index'));
        $res->assertOk()
            ->assertSee('<html lang="id">', false)
            ->assertSee('Laporan Eksekutif Finansial &amp; Proyek', false);

        // 12. User Management
        $res = $this->get(route('users.roles.index'));
        $res->assertOk()
            ->assertSee('<html lang="id">', false)
            ->assertSee('Hak Akses Pengguna')
            ->assertSee('Tambah Pengguna');
    }
}

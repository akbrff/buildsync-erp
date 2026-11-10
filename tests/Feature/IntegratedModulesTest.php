<?php

namespace Tests\Feature;

use App\Models\{Material,Project,PurchaseOrder,StockTransaction,Supplier,User,Worker,WorkerAssignment,WorkerAttendance,CashFlow};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IntegratedModulesTest extends TestCase
{
    use RefreshDatabase;

    private function project(): Project
    {
        return Project::create(['project_code'=>'PRJ-INT-01','name'=>'Proyek Integrasi','start_date'=>now()->toDateString(),'budget'=>100000000,'status'=>'planning']);
    }

    public function test_purchase_order_receipt_updates_stock_and_creates_audit_history(): void
    {
        $user=User::factory()->create(['role'=>'logistics']);
        $project=$this->project();
        $supplier=Supplier::create(['supplier_code'=>'SUP-001','name'=>'Supplier Beton','status'=>'active']);
        $material=Material::create(['code'=>'MAT-001','name'=>'Semen','unit'=>'sak','price'=>70000,'stock'=>10,'min_stock'=>5]);

        $this->actingAs($user)->post(route('purchase-orders.store'),[
            'supplier_id'=>$supplier->id,'project_id'=>$project->id,'order_date'=>now()->toDateString(),
            'items'=>[['material_id'=>$material->id,'quantity'=>20,'unit_price'=>68000]],
        ])->assertRedirect();

        $order=PurchaseOrder::firstOrFail();
        $this->actingAs($user)->post(route('purchase-orders.receive',$order),[
            'items'=>[$order->items()->first()->id=>12],
        ])->assertRedirect();

        $this->assertSame(22,$material->fresh()->stock);
        $this->assertDatabaseHas('stock_transactions',['material_id'=>$material->id,'purchase_order_id'=>$order->id,'quantity'=>12,'stock_before'=>10,'stock_after'=>22]);
        $this->assertSame('partial',$order->fresh()->status);
    }

    public function test_field_staff_can_only_record_attendance_for_their_linked_worker(): void
    {
        $worker=Worker::create(['worker_code'=>'WRK-INT-01','name'=>'Pekerja A','position'=>'Tukang','daily_rate'=>150000,'status'=>'active']);
        $other=Worker::create(['worker_code'=>'WRK-INT-02','name'=>'Pekerja B','position'=>'Helper','daily_rate'=>120000,'status'=>'active']);
        $project=$this->project();
        $assignment=WorkerAssignment::create(['worker_id'=>$worker->id,'project_id'=>$project->id,'task_name'=>'Pekerjaan pondasi','start_date'=>now()->toDateString(),'status'=>'active']);
        $otherAssignment=WorkerAssignment::create(['worker_id'=>$other->id,'project_id'=>$project->id,'task_name'=>'Pekerjaan dinding','start_date'=>now()->toDateString(),'status'=>'active']);
        $user=User::factory()->create(['role'=>'field_staff','worker_id'=>$worker->id]);

        $this->actingAs($user)->post(route('workforce.attendance'),[
            'worker_assignment_id'=>$assignment->id,'work_date'=>now()->toDateString(),'hours'=>8,'days_worked'=>1,
        ])->assertRedirect();
        $this->assertDatabaseHas('worker_attendances',['worker_id'=>$worker->id,'amount'=>150000]);

        $this->actingAs($user)->post(route('workforce.attendance'),[
            'worker_assignment_id'=>$otherAssignment->id,'work_date'=>now()->toDateString(),'hours'=>8,'days_worked'=>1,
        ])->assertForbidden();
    }

    public function test_procurement_index_page_loads_with_star_admin_ui(): void
    {
        $user = User::factory()->create(['role' => 'logistics']);
        $supplier = Supplier::create(['supplier_code' => 'SUP-001', 'name' => 'Supplier Baja', 'status' => 'active']);
        $project = $this->project();
        $material = Material::create(['code' => 'MAT-001', 'name' => 'Besi Beton', 'unit' => 'batang', 'price' => 50000, 'stock' => 100, 'min_stock' => 20]);

        $response = $this->actingAs($user)->get(route('procurement.index'));

        $response->assertOk();
        $response->assertSee('Tambah Supplier');
        $response->assertSee('Buat Purchase Order');
        $response->assertSee('Riwayat Pembelian & Penerimaan', false);
        $response->assertSee('rounded-2xl');
        $response->assertSee('+ Tambah item material');
        $response->assertSee('Simpan Supplier');
        $response->assertSee('Buat PO');
        $response->assertSee('Filter riwayat');
    }

    public function test_inventory_transactions_page_loads_with_star_admin_ui(): void
    {
        $user = User::factory()->create(['role' => 'logistics']);
        $material = Material::create(['code' => 'MAT-002', 'name' => 'Pasir Pasang', 'unit' => 'm3', 'price' => 150000, 'stock' => 50, 'min_stock' => 10]);

        $response = $this->actingAs($user)->get(route('inventory.transactions'));

        $response->assertOk();
        $response->assertSee('Riwayat Transaksi Inventaris');
        $response->assertSee('Audit stok masuk dan keluar');
        $response->assertSee('rounded-2xl');
        $response->assertSee('bg-[#2C3E50]');
        $response->assertSee('Belum ada transaksi stok.');
    }

    public function test_workforce_page_loads_with_star_admin_ui(): void
    {
        $user = User::factory()->create(['role' => 'project_manager']);
        $worker = Worker::create(['worker_code' => 'WRK-001', 'name' => 'Budi Santoso', 'position' => 'Mandor', 'daily_rate' => 200000, 'status' => 'active']);
        $project = $this->project();

        $response = $this->actingAs($user)->get(route('workforce.index'));

        $response->assertOk();
        $response->assertSee('Penugasan & Kehadiran Tenaga Kerja', false);
        $response->assertSee('Buat Penugasan');
        $response->assertSee('Catat Kehadiran / Jam Kerja');
        $response->assertSee('Penugasan Aktif');
        $response->assertSee('Kehadiran & Pembayaran Upah', false);
        $response->assertSee('rounded-2xl');
        $response->assertSee('Simpan Penugasan');
        $response->assertSee('Simpan Kehadiran');
    }

    public function test_user_roles_page_loads_with_star_admin_ui(): void
    {
        $superadmin = User::factory()->create(['name' => 'Administrator', 'role' => 'superadmin']);

        $response = $this->actingAs($superadmin)->get(route('users.roles.index'));

        $response->assertOk();
        $response->assertSee('Manajemen Pengguna & Hak Akses', false);
        $response->assertSee('rounded-2xl');
        $response->assertSee('bg-[#F8FBFD]');
        $response->assertSee('bg-[#136F9A]');
        $response->assertSee('Administrator');
        $response->assertSee('Superadmin');
    }

    public function test_finance_page_loads_with_star_admin_ui(): void
    {
        $financeUser = User::factory()->create(['name' => 'Finance Staff', 'role' => 'finance']);

        $response = $this->actingAs($financeUser)->get(route('finance.index'));

        $response->assertOk();
        $response->assertSee('Arus Kas & Integrasi Keuangan', false);
        $response->assertSee('Catat Arus Kas Manual');
        $response->assertSee('Tagihan Supplier yang Belum Lunas');
        $response->assertSee('Riwayat Transaksi Arus Kas');
        $response->assertSee('rounded-2xl');
        $response->assertSee('bg-[#136F9A]');
        $response->assertSee('bg-[#2C3E50]');
        $response->assertSee('Tidak ada tagihan supplier yang terbuka.');
        $response->assertSee('Belum ada transaksi arus kas.');
    }
}
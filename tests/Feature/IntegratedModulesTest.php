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
}
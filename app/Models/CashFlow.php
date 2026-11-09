<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class CashFlow extends Model {
 protected $fillable=['project_id','user_id','supplier_id','purchase_order_id','worker_attendance_id','type','category','description','amount','transaction_date','reference'];
 protected function casts(): array { return ['amount'=>'decimal:2','transaction_date'=>'date']; }
 public function project(): BelongsTo { return $this->belongsTo(Project::class); }
 public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
}
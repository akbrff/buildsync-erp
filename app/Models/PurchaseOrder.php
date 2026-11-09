<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo,HasMany};
class PurchaseOrder extends Model {
 protected $fillable=['po_number','supplier_id','project_id','user_id','order_date','expected_date','status','total_amount','paid_amount','notes','received_at'];
 protected function casts(): array { return ['order_date'=>'date','expected_date'=>'date','received_at'=>'datetime','total_amount'=>'decimal:2','paid_amount'=>'decimal:2']; }
 public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
 public function project(): BelongsTo { return $this->belongsTo(Project::class); }
 public function items(): HasMany { return $this->hasMany(PurchaseOrderItem::class); }
}
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class PurchaseOrderItem extends Model {
 protected $fillable=['purchase_order_id','material_id','quantity','received_quantity','unit_price','line_total'];
 public function material(): BelongsTo { return $this->belongsTo(Material::class); }
 public function purchaseOrder(): BelongsTo { return $this->belongsTo(PurchaseOrder::class); }
}
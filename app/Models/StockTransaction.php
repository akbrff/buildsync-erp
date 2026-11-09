<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class StockTransaction extends Model {
 protected $fillable=['material_id','user_id','project_id','purchase_order_id','type','quantity','stock_before','stock_after','reference','source','notes','occurred_at'];
 protected function casts(): array { return ['occurred_at'=>'datetime']; }
 public function material(): BelongsTo { return $this->belongsTo(Material::class); }
 public function project(): BelongsTo { return $this->belongsTo(Project::class); }
}
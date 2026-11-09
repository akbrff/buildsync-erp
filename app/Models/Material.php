<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Material extends Model
{
    protected $fillable = [
        'code',
        'name',
        'unit',
        'price',
        'stock',
        'min_stock',
    ];

    public function stockTransactions(): HasMany { return $this->hasMany(StockTransaction::class); }

    public function purchaseOrderItems(): HasMany { return $this->hasMany(PurchaseOrderItem::class); }

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'min_stock' => 'integer',
        ];
    }
}
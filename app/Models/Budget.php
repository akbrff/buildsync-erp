<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    protected $fillable = [
        'project_id',
        'voucher_code',
        'category',
        'description',
        'amount',
        'status',
        'receipt',
        'date',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (Budget $budget) {
            if (blank($budget->voucher_code)) {
                $budget->updateQuietly([
                    'voucher_code' => 'EXP-' . str_pad($budget->id, 5, '0', STR_PAD_LEFT),
                ]);
            }
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Voucher Code fallback jika belum terisi.
     */
    public function getFormattedVoucherAttribute(): string
    {
        return $this->voucher_code ?: ('EXP-' . str_pad($this->id, 5, '0', STR_PAD_LEFT));
    }
}

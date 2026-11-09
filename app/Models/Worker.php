<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    protected $fillable = [
        'worker_code',
        'name',
        'position',
        'phone',
        'daily_rate',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'daily_rate' => 'decimal:2',
        ];
    }

    /**
     * Format nomor telepon standar: XXXX-XXXX-XXXX (10-13 digit).
     */
    public function getFormattedPhoneAttribute(): ?string
    {
        if (!$this->phone) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $this->phone);
        if (empty($digits)) {
            return $this->phone;
        }

        if (strlen($digits) > 8) {
            return substr($digits, 0, 4) . '-' . substr($digits, 4, 4) . '-' . substr($digits, 8, 5);
        }

        if (strlen($digits) > 4) {
            return substr($digits, 0, 4) . '-' . substr($digits, 4);
        }

        return $digits;
    }
}
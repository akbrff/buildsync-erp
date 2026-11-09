<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'project_code',
        'name',
        'location',
        'start_date',
        'end_date',
        'budget',
        'status',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'budget' => 'decimal:2',
        ];
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(ProjectSchedule::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    public function purchaseOrders(): HasMany { return $this->hasMany(PurchaseOrder::class); }

    public function workerAssignments(): HasMany { return $this->hasMany(WorkerAssignment::class); }

    public function cashFlows(): HasMany { return $this->hasMany(CashFlow::class); }

    /**
     * Menghitung progres keseluruhan proyek (skala 0-100).
     * Jika total bobot belum 100, progres dihitung proporsional terhadap 100.
     */
    public function getOverallProgressAttribute(): float
    {
        $this->loadMissing('schedules.latestProgress');

        if ($this->schedules->isEmpty()) {
            return 0;
        }

        // Total (Bobot x Progres)
        $weightedProgress = $this->schedules->sum(function ($schedule) {
            return $schedule->weight * $schedule->current_progress;
        });

        // Dibagi 100, BUKAN dibagi total bobot yang ada.
        // Contoh: Bobot 50%, Progres 100% -> (50*100)/100 = 50%.
        return round($weightedProgress / 100, 2);
    }

    /**
     * Mendapatkan total bobot pekerjaan saat ini.
     */
    public function getTotalWeightAttribute(): float
    {
        return (float) $this->schedules->sum('weight');
    }

    /**
     * Cek apakah bobot pekerjaan sudah mencapai 100% (final).
     */
    public function getIsWeightFinalAttribute(): bool
    {
        return $this->total_weight >= 100;
    }
}
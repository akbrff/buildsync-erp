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

    /**
     * Menghitung progres keseluruhan proyek berdasarkan bobot pekerjaan.
     */
    public function getOverallProgressAttribute(): float
    {
        // Eager load agar tidak N+1
        $this->loadMissing('schedules.latestProgress');

        if ($this->schedules->isEmpty()) {
            return 0;
        }

        $totalWeight = $this->schedules->sum('weight');
        
        // Jika total bobot 0, hindari division by zero
        if ($totalWeight <= 0) {
            return 0;
        }

        $weightedProgress = $this->schedules->sum(function ($schedule) {
            return $schedule->weight * $schedule->current_progress;
        });

        return round($weightedProgress / $totalWeight, 2);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressUpdate extends Model
{
    protected $fillable = [
        'project_schedule_id',
        'progress_percentage',
        'notes',
        'is_correction',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'progress_percentage' => 'decimal:2',
            'is_correction' => 'boolean',
            'recorded_at' => 'date',
        ];
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ProjectSchedule::class, 'project_schedule_id');
    }
}
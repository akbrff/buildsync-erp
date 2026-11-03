<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProjectSchedule extends Model
{
    protected $fillable = [
        'project_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'weight',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'weight' => 'decimal:2',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function progressUpdates(): HasMany
    {
        return $this->hasMany(ProgressUpdate::class);
    }

    public function latestProgress(): HasOne
    {
        return $this->hasOne(ProgressUpdate::class)->latestOfMany(['recorded_at', 'id']);
    }
}
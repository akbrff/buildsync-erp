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
    /**
     * Progres aktual terakhir (0 jika belum ada catatan).
     */
    public function getCurrentProgressAttribute(): float
    {
        return (float) ($this->latestProgress?->progress_percentage ?? 0);
    }

    /**
     * Mendapatkan status pekerjaan berdasarkan tanggal dan progres aktual.
     */
    public function getStatusAttribute(): string
    {
        $progress = $this->current_progress;
        $today = now()->startOfDay();
        $endDate = \Illuminate\Support\Carbon::parse($this->end_date)->startOfDay();

        if ($progress >= 100) {
            return 'Selesai';
        }

        if ($endDate->lt($today)) {
            return 'Terlambat';
        }

        if ($progress > 0) {
            return 'Berlangsung';
        }

        return 'Belum Dimulai';
    }

    /**
     * Scope untuk filter query berdasarkan status.
     */
    public function scopeByStatus($query, string $status)
    {
        $today = now()->toDateString();

        return match ($status) {
            'Selesai' => $query->whereHas('latestProgress', fn($q) => $q->where('progress_percentage', '>=', 100)),
            'Terlambat' => $query->where('end_date', '<', $today)
                ->where(fn($q) => $q->whereDoesntHave('latestProgress')->orWhereHas('latestProgress', fn($q2) => $q2->where('progress_percentage', '<', 100))),
            'Berlangsung' => $query->where('end_date', '>=', $today)
                ->whereHas('latestProgress', fn($q) => $q->where('progress_percentage', '>', 0)->where('progress_percentage', '<', 100)),
            'Belum Dimulai' => $query->where('end_date', '>=', $today)
                ->where(fn($q) => $q->whereDoesntHave('latestProgress')->orWhereHas('latestProgress', fn($q2) => $q2->where('progress_percentage', 0))),
            default => $query,
        };
    }
}
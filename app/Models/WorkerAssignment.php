<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo,HasMany};
class WorkerAssignment extends Model {
 protected $fillable=['worker_id','project_id','project_schedule_id','task_name','start_date','end_date','status','notes'];
 protected function casts(): array { return ['start_date'=>'date','end_date'=>'date']; }
 public function worker(): BelongsTo { return $this->belongsTo(Worker::class); }
 public function project(): BelongsTo { return $this->belongsTo(Project::class); }
 public function schedule(): BelongsTo { return $this->belongsTo(ProjectSchedule::class,'project_schedule_id'); }
 public function attendances(): HasMany { return $this->hasMany(WorkerAttendance::class); }
}
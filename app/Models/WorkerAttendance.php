<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class WorkerAttendance extends Model {
 protected $fillable=['worker_assignment_id','worker_id','project_id','user_id','work_date','hours','days_worked','daily_rate','amount','status','paid_at','notes'];
 protected function casts(): array { return ['work_date'=>'date','paid_at'=>'datetime','hours'=>'decimal:2','days_worked'=>'decimal:2','daily_rate'=>'decimal:2','amount'=>'decimal:2']; }
 public function worker(): BelongsTo { return $this->belongsTo(Worker::class); }
 public function project(): BelongsTo { return $this->belongsTo(Project::class); }
 public function assignment(): BelongsTo { return $this->belongsTo(WorkerAssignment::class,'worker_assignment_id'); }
}
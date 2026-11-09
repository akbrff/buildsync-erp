<?php
namespace App\Http\Controllers;
use App\Models\{Worker,Project,ProjectSchedule,WorkerAssignment,WorkerAttendance,CashFlow};
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class WorkforceController extends Controller {
 public function index() {
  $assignmentQuery=WorkerAssignment::with(['worker','project','schedule'])->withCount('attendances');
  $attendanceQuery=WorkerAttendance::with(['worker','project']);
  $projectsQuery=Project::with('schedules')->orderBy('name');
  if(auth()->user()->role==='field_staff'){
   abort_unless(auth()->user()->worker_id,403,'Akun Field Staff belum ditautkan ke data tenaga kerja.');
   $assignmentQuery->where('worker_id',auth()->user()->worker_id);
   $attendanceQuery->where('worker_id',auth()->user()->worker_id);
   $projectsQuery->whereHas('workerAssignments',fn($q)=>$q->where('worker_id',auth()->user()->worker_id));
  }
  $assignments=$assignmentQuery->latest()->paginate(15);
  $workers=Worker::where('status','active')->orderBy('name')->get();
  $projects=$projectsQuery->get();
  $attendances=$attendanceQuery->latest('work_date')->paginate(15,['*'],'attendance_page');$pendingQuery=WorkerAttendance::where('status','pending');if(auth()->user()->role==='field_staff')$pendingQuery->where('worker_id',auth()->user()->worker_id);$pendingPayroll=$pendingQuery->sum('amount');
  return view('operations.workforce',compact('assignments','workers','projects','attendances','pendingPayroll'));
 }
 public function assign(Request $r) {
  $d=$r->validate(['worker_id'=>['required','exists:workers,id'],'project_id'=>['required','exists:projects,id'],'project_schedule_id'=>['nullable','exists:project_schedules,id'],'task_name'=>['required','max:255'],'start_date'=>['required','date'],'end_date'=>['nullable','date','after_or_equal:start_date']]);if(!empty($d['project_schedule_id']))abort_unless(ProjectSchedule::whereKey($d['project_schedule_id'])->where('project_id',$d['project_id'])->exists(),422,'Tahapan jadwal harus berasal dari proyek yang dipilih.');WorkerAssignment::create($d+['status'=>'active']);return back()->with('success','Penugasan pekerja tersimpan.');
 }
 public function attendance(Request $r) {
  $d=$r->validate(['worker_assignment_id'=>['required','exists:worker_assignments,id'],'work_date'=>['required','date',Rule::unique('worker_attendances','work_date')->where(fn($q)=>$q->where('worker_assignment_id',$r->input('worker_assignment_id')))],'hours'=>['required','numeric','min:0.5','max:24'],'days_worked'=>['required','numeric','min:0.1','max:1']]);$a=WorkerAssignment::with('worker')->findOrFail($d['worker_assignment_id']);abort_unless($a->status==='active',422);abort_unless(auth()->user()->role!=='field_staff'||auth()->user()->worker_id===$a->worker_id,403,'Penugasan ini bukan milik akun Anda.');abort_unless($d['work_date'] >= $a->start_date->toDateString() && (!$a->end_date || $d['work_date'] <= $a->end_date->toDateString()),422,'Tanggal kehadiran di luar periode penugasan.');WorkerAttendance::create(['worker_assignment_id'=>$a->id,'worker_id'=>$a->worker_id,'project_id'=>$a->project_id,'user_id'=>$r->user()->id,'work_date'=>$d['work_date'],'hours'=>$d['hours'],'days_worked'=>$d['days_worked'],'daily_rate'=>$a->worker->daily_rate,'amount'=>$a->worker->daily_rate*$d['days_worked']]);return back()->with('success','Kehadiran dan biaya aktual tenaga kerja dicatat.');
 }
 public function pay(Request $r, WorkerAttendance $attendance) {
  DB::transaction(function()use($attendance,$r){$a=WorkerAttendance::lockForUpdate()->findOrFail($attendance->id);abort_if($a->status==='paid',422);CashFlow::create(['project_id'=>$a->project_id,'user_id'=>$r->user()->id,'worker_attendance_id'=>$a->id,'type'=>'out','category'=>'Upah Tenaga Kerja','description'=>'Pembayaran upah '.$a->worker->name,'amount'=>$a->amount,'transaction_date'=>now()->toDateString()]);$a->update(['status'=>'paid','paid_at'=>now()]);});return back()->with('success','Pembayaran upah masuk ke arus kas.');
 }
}
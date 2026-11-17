<?php
namespace App\Http\Controllers;
use App\Models\{CashFlow,Project,PurchaseOrder,WorkerAttendance,Budget};
use Illuminate\Http\Request;
class FinanceController extends Controller {
 public function index(Request $r) {
  $projectId=$r->integer('project_id');$flows=CashFlow::with(['project','supplier'])->when($projectId,fn($q)=>$q->where('project_id',$projectId))->latest('transaction_date')->paginate(20)->withQueryString();$projects=Project::orderBy('name')->get();
  $income=(float)CashFlow::where('type','in')->when($projectId,fn($q)=>$q->where('project_id',$projectId))->sum('amount');$outflow=(float)CashFlow::where('type','out')->when($projectId,fn($q)=>$q->where('project_id',$projectId))->sum('amount');
  $approvedExpenses=(float)Budget::where('status','approved')->when($projectId,fn($q)=>$q->where('project_id',$projectId))->sum('amount');$pendingPayroll=(float)WorkerAttendance::where('status','pending')->when($projectId,fn($q)=>$q->where('project_id',$projectId))->sum('amount');$unpaidOrders=PurchaseOrder::with(['supplier','project'])->whereColumn('paid_amount','<','total_amount')->when($projectId,fn($q)=>$q->where('project_id',$projectId))->latest('order_date')->get();
  $poOutstanding=(float)$unpaidOrders->sum(fn($p)=>$p->total_amount-$p->paid_amount);
  return view('operations.finance',compact('flows','projects','projectId','income','outflow','approvedExpenses','pendingPayroll','poOutstanding','unpaidOrders'));
 }
 public function store(Request $r) {
  $d=$r->validate(['project_id'=>['nullable','exists:projects,id'],'type'=>['required','in:in,out'],'category'=>['required','max:80'],'description'=>['required','max:255'],'amount'=>['required','numeric','min:1'],'transaction_date'=>['required','date'],'reference'=>['nullable','max:100']]);
  CashFlow::create($d+['user_id'=>$r->user()->id]);return back()->with('success',__('messages.cash_flow_created'));
 }
}
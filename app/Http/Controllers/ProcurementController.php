<?php
namespace App\Http\Controllers;
use App\Models\{Material,Project,PurchaseOrder,PurchaseOrderItem,Supplier,StockTransaction,CashFlow};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
class ProcurementController extends Controller {
 public function index(Request $r) {
  $suppliers=Supplier::withCount('purchaseOrders')->orderBy('name')->get();
  $supplierId=$r->integer('supplier_id');
  $orders=PurchaseOrder::with(['supplier','project','items.material'])->when($supplierId,fn($q)=>$q->where('supplier_id',$supplierId))->latest('order_date')->paginate(15)->withQueryString();
  $projects=Project::orderBy('name')->get();$materials=Material::orderBy('name')->get();
  return view('operations.procurement',compact('suppliers','orders','projects','materials','supplierId'));
 }
 public function supplier(Request $r) {
  $d=$r->validate(['supplier_code'=>['required','max:40','unique:suppliers,supplier_code'],'name'=>['required','max:255'],'contact_person'=>['nullable','max:255'],'phone'=>['nullable','max:40'],'email'=>['nullable','email'],'address'=>['nullable','string'],'status'=>['required','in:active,inactive']]);
  Supplier::create($d);return back()->with('success','Supplier berhasil ditambahkan.');
 }
 public function updateSupplier(Request $r,Supplier $supplier) {
  $d=$r->validate(['supplier_code'=>['required','max:40',Rule::unique('suppliers','supplier_code')->ignore($supplier->id)],'name'=>['required','max:255'],'contact_person'=>['nullable','max:255'],'phone'=>['nullable','max:40'],'email'=>['nullable','email'],'address'=>['nullable','string'],'status'=>['required','in:active,inactive']]);
  $supplier->update($d);return back()->with('success','Data supplier berhasil diperbarui.');
 }
 public function order(Request $r) {
  $d=$r->validate(['supplier_id'=>['required',Rule::exists('suppliers','id')->where(fn($q)=>$q->where('status','active'))],'project_id'=>['required','exists:projects,id'],'order_date'=>['required','date'],'expected_date'=>['nullable','date','after_or_equal:order_date'],'items'=>['required','array','min:1'],'items.*.material_id'=>['required','distinct','exists:materials,id'],'items.*.quantity'=>['required','integer','min:1'],'items.*.unit_price'=>['required','numeric','min:0'],'notes'=>['nullable','string']]);
  DB::transaction(function()use($d,$r){$po=PurchaseOrder::create(['po_number'=>'PO-'.now()->format('ymd').'-'.strtoupper(str()->random(5)),'supplier_id'=>$d['supplier_id'],'project_id'=>$d['project_id'],'user_id'=>$r->user()->id,'order_date'=>$d['order_date'],'expected_date'=>$d['expected_date']??null,'notes'=>$d['notes']??null]);$total=0;foreach($d['items'] as $i){$line=$i['quantity']*$i['unit_price'];$total+=$line;$po->items()->create(['material_id'=>$i['material_id'],'quantity'=>$i['quantity'],'unit_price'=>$i['unit_price'],'line_total'=>$line]);}$po->update(['total_amount'=>$total]);});
  return back()->with('success','PO dibuat. Stok baru bertambah setelah penerimaan barang.');
 }
 public function receive(Request $r, PurchaseOrder $purchaseOrder) {
  $d=$r->validate(['items'=>['required','array'],'items.*'=>['nullable','integer','min:0']]);
  DB::transaction(function()use($d,$purchaseOrder,$r){$po=PurchaseOrder::with('items')->lockForUpdate()->findOrFail($purchaseOrder->id);abort_if(in_array($po->status,['received','cancelled'],true),422);foreach($po->items as $i){$qty=(int)($d['items'][$i->id]??0);abort_if($i->received_quantity+$qty>$i->quantity,422,'Penerimaan melebihi jumlah PO.');if(!$qty)continue;$m=Material::lockForUpdate()->findOrFail($i->material_id);$before=$m->stock;$m->increment('stock',$qty);$i->increment('received_quantity',$qty);StockTransaction::create(['material_id'=>$m->id,'user_id'=>$r->user()->id,'project_id'=>$po->project_id,'purchase_order_id'=>$po->id,'type'=>'in','quantity'=>$qty,'stock_before'=>$before,'stock_after'=>$before+$qty,'reference'=>$po->po_number,'source'=>'purchase_order','notes'=>'Penerimaan PO '.$po->po_number,'occurred_at'=>now()]);}$po->refresh()->load('items');$done=$po->items->every(fn($i)=>$i->received_quantity >= $i->quantity);$partial=$po->items->contains(fn($i)=>$i->received_quantity>0);$po->update(['status'=>$done?'received':($partial?'partial':'ordered'),'received_at'=>$done?now():null]);});
  return back()->with('success','Penerimaan barang dan mutasi stok tercatat.');
 }
 public function pay(Request $r, PurchaseOrder $purchaseOrder) {
  $d=$r->validate(['amount'=>['required','numeric','min:1'],'transaction_date'=>['required','date'],'reference'=>['nullable','max:100']]);
  DB::transaction(function()use($d,$purchaseOrder,$r){$po=PurchaseOrder::lockForUpdate()->findOrFail($purchaseOrder->id);abort_if($d['amount']>$po->total_amount-$po->paid_amount,422,'Pembayaran melebihi sisa tagihan.');CashFlow::create(['project_id'=>$po->project_id,'user_id'=>$r->user()->id,'supplier_id'=>$po->supplier_id,'purchase_order_id'=>$po->id,'type'=>'out','category'=>'Pembayaran Supplier','description'=>'Pembayaran '.$po->po_number,'amount'=>$d['amount'],'transaction_date'=>$d['transaction_date'],'reference'=>$d['reference']??$po->po_number]);$po->increment('paid_amount',$d['amount']);});
  return back()->with('success','Pembayaran supplier tercatat di arus kas.');
 }
}
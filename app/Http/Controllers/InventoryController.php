<?php
namespace App\Http\Controllers;
use App\Models\{Material,StockTransaction};
use Illuminate\Http\Request;
class InventoryController extends Controller {
 public function index(Request $r) {
  $transactions=StockTransaction::with(['material','project'])->when($r->filled('material_id'),fn($q)=>$q->where('material_id',$r->integer('material_id')))->when($r->filled('type'),fn($q)=>$q->where('type',$r->input('type')))->latest('occurred_at')->paginate(20)->withQueryString();
  $materials=Material::orderBy('name')->get(['id','code','name']);return view('operations.inventory',compact('transactions','materials'));
 }
}
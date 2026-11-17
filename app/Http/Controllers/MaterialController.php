<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        // KPI metrik inventaris menyeluruh
        $totalMaterials     = Material::count();
        $safeCount          = Material::whereRaw('stock > (min_stock * 1.2)')->count();
        $lowCount           = Material::whereRaw('stock > min_stock AND stock <= (min_stock * 1.2)')->count();
        $criticalCount      = Material::whereRaw('stock <= min_stock')->count();
        $totalValuation     = Material::sum(DB::raw('stock * price')) ?? 0;

        // Daftar satuan unik untuk opsi filter dropdown
        $units = Material::distinct()->whereNotNull('unit')->orderBy('unit')->pluck('unit');

        // Query dengan filter presisi
        $query = Material::query();

        if ($request->filled('q')) {
            $term = '%' . $request->input('q') . '%';
            $query->where(function ($q) use ($term) {
                $q->where('code', 'like', $term)
                  ->orWhere('name', 'like', $term);
            });
        }

        if ($request->filled('unit')) {
            $query->where('unit', $request->input('unit'));
        }

        if ($request->filled('status')) {
            if ($request->status === 'kritis') {
                $query->whereRaw('stock <= min_stock');
            } elseif ($request->status === 'menipis') {
                $query->whereRaw('stock > min_stock AND stock <= (min_stock * 1.2)');
            } elseif ($request->status === 'aman') {
                $query->whereRaw('stock > (min_stock * 1.2)');
            }
        }

        $materials = $query->orderBy('code')->paginate(10)->appends($request->query());

        return view('materials.index', compact(
            'materials',
            'totalMaterials',
            'safeCount',
            'lowCount',
            'criticalCount',
            'totalValuation',
            'units'
        ));
    }

    public function create()
    {
        return view('materials.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateMaterial($request);
        $material = Material::create($validated);

        if ($material->stock > 0) {
            StockTransaction::create([
                'material_id' => $material->id, 'user_id' => $request->user()->id,
                'type' => 'in', 'quantity' => $material->stock, 'stock_before' => 0,
                'stock_after' => $material->stock, 'source' => 'opening_balance',
                'notes' => 'Saldo awal saat material didaftarkan', 'occurred_at' => now(),
            ]);
        }

        return redirect()->route('materials.index')->with('success', __('messages.material_created'));
    }

    public function show(Material $material)
    {
        // Status level stok material
        $statusLevel = 'safe';
        if ($material->stock <= $material->min_stock) {
            $statusLevel = 'critical';
        } elseif ($material->stock <= ($material->min_stock * 1.2)) {
            $statusLevel = 'warning';
        }

        $materialValuation = $material->stock * $material->price;

        return view('materials.show', compact('material', 'statusLevel', 'materialValuation'));
    }

    public function edit(Material $material)
    {
        return view('materials.edit', compact('material'));
    }

    public function update(Request $request, Material $material)
    {
        $validated = $this->validateMaterial($request, $material);
        // Perubahan stok wajib melalui mutasi agar histori inventaris tetap akurat.
        $validated['stock'] = $material->stock;
        $material->update($validated);

        return redirect()->route('materials.index')->with('success', __('messages.material_updated', ['code' => $material->code]));
    }

    public function destroy(Material $material)
    {
        if ($material->stockTransactions()->exists() || $material->purchaseOrderItems()->exists()) {
            return back()->with('error', __('messages.material_has_transactions'));
        }

        $code = $material->code;
        $material->delete();

        return redirect()->route('materials.index')->with('success', __('messages.material_deleted', ['code' => $code]));
    }

    public function adjustStock(Request $request, Material $material)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:in,out'],
            'amount' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $quantity = (int) $validated['amount'];
        $success = DB::transaction(function () use ($request, $material, $validated, $quantity) {
            $locked = Material::query()->lockForUpdate()->findOrFail($material->id);
            $before = (int) $locked->stock;

            if ($validated['type'] === 'out' && $before < $quantity) {
                return false;
            }

            $after = $before + ($validated['type'] === 'in' ? $quantity : -$quantity);
            $locked->update(['stock' => $after]);

            StockTransaction::create([
                'material_id' => $locked->id,
                'user_id' => $request->user()->id,
                'type' => $validated['type'],
                'quantity' => $quantity,
                'stock_before' => $before,
                'stock_after' => $after,
                'source' => 'manual',
                'notes' => $validated['notes'] ?? null,
                'occurred_at' => now(),
            ]);

            return true;
        });

        if (! $success) {
            return back()->with('error', __('messages.insufficient_stock', ['stock' => $material->stock, 'unit' => $material->unit, 'requested' => $quantity]));
        }

        return back()->with('success', __('messages.stock_adjusted'));
    }

    private function validateMaterial(Request $request, ?Material $material = null): array
    {
        return $request->validate([
            'code'      => ['required', 'string', 'max:50', Rule::unique('materials', 'code')->ignore($material?->id)],
            'name'      => ['required', 'string', 'max:255'],
            'unit'      => ['required', 'string', 'max:20'],
            'price'     => ['required', 'numeric', 'min:0'],
            'stock'     => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
        ]);
    }
}

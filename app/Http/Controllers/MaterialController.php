<?php

namespace App\Http\Controllers;

use App\Models\Material;
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
        Material::create($validated);

        return redirect()->route('materials.index')->with('success', 'Material baru berhasil ditambahkan ke inventaris.');
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
        $material->update($validated);

        return redirect()->route('materials.index')->with('success', 'Data material ' . $material->code . ' berhasil diperbarui.');
    }

    public function destroy(Material $material)
    {
        $code = $material->code;
        $material->delete();

        return redirect()->route('materials.index')->with('success', "Material [{$code}] berhasil dihapus dari inventaris.");
    }

    public function adjustStock(Request $request, Material $material)
    {
        $validated = $request->validate([
            'type'   => ['required', 'in:in,out'],
            'amount' => ['required', 'integer', 'min:1'],
            'notes'  => ['nullable', 'string', 'max:255'],
        ]);

        $amount = (int) $validated['amount'];

        if ($validated['type'] === 'in') {
            $material->increment('stock', $amount);
            $msg = "Penerimaan material {$material->name} (+{$amount} {$material->unit}) berhasil dicatat. Stok saat ini: {$material->stock} {$material->unit}.";
        } else {
            if ($material->stock < $amount) {
                return back()->with('error', "Gagal mengeluarkan material! Stok fisik hanya tersisa {$material->stock} {$material->unit}, permintaan: {$amount} {$material->unit}.");
            }
            $material->decrement('stock', $amount);
            $msg = "Pengeluaran material {$material->name} (-{$amount} {$material->unit}) untuk proyek berhasil dicatat. Stok tersisa: {$material->stock} {$material->unit}.";
        }

        return back()->with('success', $msg);
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

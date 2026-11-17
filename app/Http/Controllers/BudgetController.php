<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $projectId      = $request->integer('project_id');
        $categoryFilter = $request->input('category');
        $statusFilter   = $request->input('status');
        $startDate      = $request->input('start_date');
        $endDate        = $request->input('end_date');
        $searchQuery    = $request->input('q');

        $projects = Project::orderBy('name')->get(['id', 'project_code', 'name', 'budget']);
        $selectedProject = $projectId ? $projects->firstWhere('id', $projectId) : null;

        // ── Query Dasar Finansial ────────────────────────────────
        $budgetsQuery = Budget::with('project')
            ->when($projectId, fn($q) => $q->where('project_id', $projectId))
            ->when($categoryFilter, fn($q) => $q->where('category', $categoryFilter))
            ->when($statusFilter, fn($q) => $q->where('status', $statusFilter))
            ->when($startDate, fn($q) => $q->where('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->where('date', '<=', $endDate))
            ->when($searchQuery, function ($q) use ($searchQuery) {
                $q->where(function ($sub) use ($searchQuery) {
                    $sub->where('description', 'like', "%{$searchQuery}%")
                        ->orWhere('voucher_code', 'like', "%{$searchQuery}%");
                });
            })
            ->latest('date')
            ->latest('id');

        $budgets = $budgetsQuery->paginate(10)->withQueryString();

        // ── 4 KPI Finansial Atas ─────────────────────────────────
        // 1. Total Pagu
        $totalPagu = $selectedProject
            ? (float) $selectedProject->budget
            : (float) Project::sum('budget');

        // 2. Realisasi Pengeluaran Kas (Approved)
        $approvedQuery = Budget::when($projectId, fn($q) => $q->where('project_id', $projectId))
            ->where('status', 'approved');
        $totalActualCost = (float) $approvedQuery->sum('amount');
        $serapanPercent  = $totalPagu > 0 ? round(($totalActualCost / $totalPagu) * 100, 1) : 0;

        // 3. Sisa Saldo / Margin Kas
        $sisaSaldo = $totalPagu - $totalActualCost;

        // 4. Menunggu Approval / Pending
        $pendingQuery = Budget::when($projectId, fn($q) => $q->where('project_id', $projectId))
            ->where('status', 'pending');
        $pendingAmount = (float) $pendingQuery->sum('amount');
        $pendingCount  = $pendingQuery->count();

        return view('budgets.index', compact(
            'budgets',
            'projects',
            'projectId',
            'selectedProject',
            'categoryFilter',
            'statusFilter',
            'startDate',
            'endDate',
            'searchQuery',
            'totalPagu',
            'totalActualCost',
            'serapanPercent',
            'sisaSaldo',
            'pendingAmount',
            'pendingCount'
        ));
    }

    public function create(Request $request)
    {
        $projects = Project::orderBy('name')->get(['id', 'project_code', 'name', 'budget']);
        $selectedProjectId = $request->integer('project_id') ?: null;

        return view('budgets.create', compact('projects', 'selectedProjectId'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateBudget($request);

        if ($request->hasFile('receipt')) {
            $path = $request->file('receipt')->store('receipts', 'public');
            $validated['receipt'] = $path;
        }

        if (empty($validated['status'])) {
            $validated['status'] = 'approved';
        }

        Budget::create($validated);

        return redirect()->route('budgets.index', ['project_id' => $validated['project_id']])
            ->with('success', __('messages.expense_created'));
    }

    public function show(Budget $budget)
    {
        $budget->load('project');
        return view('budgets.show', compact('budget'));
    }

    public function edit(Budget $budget)
    {
        $projects = Project::orderBy('name')->get(['id', 'project_code', 'name', 'budget']);
        return view('budgets.edit', compact('budget', 'projects'));
    }

    public function update(Request $request, Budget $budget)
    {
        $validated = $this->validateBudget($request, $budget);

        if ($request->hasFile('receipt')) {
            // Hapus bukti lama jika ada
            if ($budget->receipt && Storage::disk('public')->exists($budget->receipt)) {
                Storage::disk('public')->delete($budget->receipt);
            }
            $path = $request->file('receipt')->store('receipts', 'public');
            $validated['receipt'] = $path;
        }

        $budget->update($validated);

        return redirect()->route('budgets.index', ['project_id' => $validated['project_id']])
            ->with('success', __('messages.expense_updated'));
    }

    public function destroy(Budget $budget)
    {
        $projectId = $budget->project_id;

        if ($budget->receipt && Storage::disk('public')->exists($budget->receipt)) {
            Storage::disk('public')->delete($budget->receipt);
        }

        $budget->delete();

        return redirect()->route('budgets.index', ['project_id' => $projectId])
            ->with('success', __('messages.expense_deleted'));
    }

    private function validateBudget(Request $request, ?Budget $budget = null): array
    {
        return $request->validate([
            'project_id'  => ['required', 'exists:projects,id'],
            'category'    => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:255'],
            'amount'      => ['required', 'numeric', 'min:1'],
            'date'        => ['required', 'date'],
            'status'      => ['nullable', 'string', 'in:approved,pending,rejected'],
            'receipt'     => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);
    }
}

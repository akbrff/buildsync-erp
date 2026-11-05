<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Project;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $projectId = $request->integer('project_id');
        
        $budgets = Budget::with('project')
            ->when($projectId, fn($q) => $q->where('project_id', $projectId))
            ->latest('date')
            ->paginate(10)
            ->withQueryString();

        $projects = Project::orderBy('name')->get(['id', 'project_code', 'name']);

        return view('budgets.index', compact('budgets', 'projects', 'projectId'));
    }

    public function create(Request $request)
    {
        $projects = Project::orderBy('name')->get(['id', 'project_code', 'name']);
        $selectedProjectId = $request->integer('project_id') ?: null;

        return view('budgets.create', compact('projects', 'selectedProjectId'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateBudget($request);
        Budget::create($validated);

        return redirect()->route('budgets.index', ['project_id' => $validated['project_id']])
            ->with('success', 'Pengeluaran biaya berhasil dicatat.');
    }

    public function show(Budget $budget)
    {
        $budget->load('project');
        return view('budgets.show', compact('budget'));
    }

    public function edit(Budget $budget)
    {
        $projects = Project::orderBy('name')->get(['id', 'project_code', 'name']);
        return view('budgets.edit', compact('budget', 'projects'));
    }

    public function update(Request $request, Budget $budget)
    {
        $validated = $this->validateBudget($request);
        $budget->update($validated);

        return redirect()->route('budgets.index', ['project_id' => $validated['project_id']])
            ->with('success', 'Data pengeluaran berhasil diperbarui.');
    }

    public function destroy(Budget $budget)
    {
        $projectId = $budget->project_id;
        $budget->delete();

        return redirect()->route('budgets.index', ['project_id' => $projectId])
            ->with('success', 'Pengeluaran berhasil dihapus.');
    }

    private function validateBudget(Request $request): array
    {
        return $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:1'],
            'date' => ['required', 'date'],
        ]);
    }
}
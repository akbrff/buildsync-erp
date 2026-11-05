<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    /**
     * Menampilkan daftar proyek.
     */
    public function index()
    {
        $projects = Project::latest()->paginate(10);

        return view('projects.index', compact('projects'));
    }

    /**
     * Menampilkan formulir tambah proyek.
     */
    public function create()
    {
        return view('projects.create');
    }

    /**
     * Menyimpan proyek baru.
     */
    public function store(Request $request)
    {
        $validated = $this->validateProject($request);

        Project::create($validated);

        return redirect()
            ->route('projects.index')
            ->with('success', 'Data proyek berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail proyek.
     */
    public function show(Project $project)
    {
        return view('projects.show', compact('project'));
    }

    /**
     * Menampilkan formulir edit proyek.
     */
    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    /**
     * Memperbarui data proyek.
     */
    public function update(Request $request, Project $project)
    {
        $validated = $this->validateProject($request, $project);

        $project->update($validated);

        return redirect()
            ->route('projects.index')
            ->with('success', 'Data proyek berhasil diperbarui.');
    }

    /**
     * Menghapus proyek hanya jika belum memiliki data turunan.
     */
    public function destroy(Project $project)
    {
        if ($project->schedules()->exists()) {
            return redirect()
                ->route('projects.index')
                ->with('error', 'Proyek tidak dapat dihapus karena masih memiliki Jadwal Pekerjaan. Hapus atau selesaikan data jadwal terlebih dahulu.');
        }

        if ($project->budgets()->exists()) {
            return redirect()
                ->route('projects.index')
                ->with('error', 'Proyek tidak dapat dihapus karena masih memiliki catatan anggaran. Pertahankan riwayat biaya proyek.');
        }

        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('success', 'Data proyek berhasil dihapus.');
    }

    /**
     * Validasi data proyek.
     */
    private function validateProject(Request $request, ?Project $project = null): array
    {
        return $request->validate([
            'project_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('projects', 'project_code')->ignore($project?->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'budget' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['planning', 'ongoing', 'completed', 'on_hold'])],
            'description' => ['nullable', 'string'],
        ]);
    }
}

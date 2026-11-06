<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkerRequest;
use App\Http\Requests\UpdateWorkerRequest;
use App\Models\Worker;
use Illuminate\Http\Request;

class WorkerController extends Controller
{
    public function index(Request $request)
    {
        // KPI metrik SDM / Tenaga Kerja
        $totalWorkers    = Worker::count();
        $activeWorkers   = Worker::where('status', 'active')->count();
        $inactiveWorkers = Worker::where('status', '!=', 'active')->count();
        $totalDailyWages = Worker::where('status', 'active')->sum('daily_rate') ?? 0;

        // Daftar jabatan/posisi unik untuk filter dropdown
        $positions = Worker::distinct()->whereNotNull('position')->orderBy('position')->pluck('position');

        // Query filter
        $query = Worker::query();

        if ($request->filled('q')) {
            $term = '%' . $request->input('q') . '%';
            $query->where(function ($q) use ($term) {
                $q->where('worker_code', 'like', $term)
                  ->orWhere('name', 'like', $term)
                  ->orWhere('phone', 'like', $term);
            });
        }

        if ($request->filled('position')) {
            $query->where('position', $request->input('position'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $workers = $query->orderBy('worker_code')->paginate(10)->appends($request->query());

        return view('workers.index', compact(
            'workers',
            'totalWorkers',
            'activeWorkers',
            'inactiveWorkers',
            'totalDailyWages',
            'positions'
        ));
    }

    public function create()
    {
        return view('workers.create');
    }

    public function store(StoreWorkerRequest $request)
    {
        $worker = Worker::create($request->validated());

        return redirect()->route('workers.index')
            ->with('success', "Tenaga kerja baru [{$worker->worker_code}] {$worker->name} berhasil ditambahkan.");
    }

    public function show(Worker $worker)
    {
        // Estimasi beban upah bulanan (asumsi 25 hari kerja efektif) & mingguan (6 hari)
        $weeklyWageEst  = $worker->daily_rate * 6;
        $monthlyWageEst = $worker->daily_rate * 25;

        return view('workers.show', compact('worker', 'weeklyWageEst', 'monthlyWageEst'));
    }

    public function edit(Worker $worker)
    {
        return view('workers.edit', compact('worker'));
    }

    public function update(UpdateWorkerRequest $request, Worker $worker)
    {
        $worker->update($request->validated());

        return redirect()->route('workers.index')
            ->with('success', "Data tenaga kerja [{$worker->worker_code}] {$worker->name} berhasil diperbarui.");
    }

    public function destroy(Worker $worker)
    {
        $name = $worker->name;
        $code = $worker->worker_code;
        $worker->delete();

        return redirect()->route('workers.index')
            ->with('success', "Tenaga kerja [{$code}] {$name} berhasil dihapus dari sistem.");
    }
}

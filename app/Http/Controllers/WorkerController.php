<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkerRequest;
use App\Http\Requests\UpdateWorkerRequest;
use App\Models\Worker;

class WorkerController extends Controller
{
    public function index()
    {
        $workers = Worker::orderBy('worker_code')->paginate(10);

        return view('workers.index', compact('workers'));
    }

    public function create()
    {
        return view('workers.create');
    }

    public function store(StoreWorkerRequest $request)
    {
        Worker::create($request->validated());

        return redirect()->route('workers.index')
            ->with('success', 'Data tenaga kerja berhasil ditambahkan.');
    }

    public function show(Worker $worker)
    {
        return view('workers.show', compact('worker'));
    }

    public function edit(Worker $worker)
    {
        return view('workers.edit', compact('worker'));
    }

    public function update(UpdateWorkerRequest $request, Worker $worker)
    {
        $worker->update($request->validated());

        return redirect()->route('workers.index')
            ->with('success', 'Data tenaga kerja berhasil diperbarui.');
    }

    public function destroy(Worker $worker)
    {
        $worker->delete();

        return redirect()->route('workers.index')
            ->with('success', 'Data tenaga kerja berhasil dihapus.');
    }
}
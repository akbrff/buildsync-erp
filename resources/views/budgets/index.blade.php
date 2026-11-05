@extends('layouts.app')

@section('title', 'Anggaran & Biaya')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Anggaran & Biaya</h2>
            <p class="mt-1 text-sm text-slate-500">Catat dan pantau pengeluaran aktual proyek.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <form method="GET" action="{{ route('budgets.index') }}" class="flex items-center gap-2">
                <select name="project_id" class="rounded-md border border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500" onchange="this.form.submit()">
                    <option value="">-- Semua Proyek --</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}" @selected($projectId == $project->id)>
                            {{ $project->project_code }} - {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('budgets.create', ['project_id' => $projectId]) }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Catat Biaya</a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-md bg-green-100 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <div class="rounded-lg bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Proyek</th>
                        <th class="px-6 py-3">Kategori</th>
                        <th class="px-6 py-3">Keterangan</th>
                        <th class="px-6 py-3 text-right">Nominal</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($budgets as $budget)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">{{ $budget->date->format('d M Y') }}</td>
                            <td class="px-6 py-4 font-medium text-blue-600">
                                <a href="{{ route('projects.show', $budget->project) }}">{{ $budget->project->project_code }}</a>
                            </td>
                            <td class="px-6 py-4">
                                <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700">{{ $budget->category }}</span>
                            </td>
                            <td class="px-6 py-4">{{ $budget->description }}</td>
                            <td class="px-6 py-4 text-right font-bold text-slate-800">Rp {{ number_format($budget->amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('budgets.edit', $budget) }}" class="text-blue-600 hover:underline">Edit</a>
                                <form action="{{ route('budgets.destroy', $budget) }}" method="POST" class="inline" onsubmit="return confirm('Hapus catatan biaya ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-slate-500">Belum ada data pengeluaran biaya.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 px-6 py-4">
            {{ $budgets->links() }}
        </div>
    </div>
@endsection
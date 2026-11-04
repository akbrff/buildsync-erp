@extends('layouts.app')

@section('title', 'Laporan Proyek')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Laporan Proyek</h2>
            <p class="mt-1 text-sm text-slate-500">Ringkasan menyeluruh proyek, anggaran, dan material.</p>
        </div>
        
        <form method="GET" action="{{ route('reports.index') }}" class="flex items-center gap-2">
            <select name="project_id" class="rounded-md border border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500" onchange="this.form.submit()">
                <option value="">-- Laporan Global (Semua) --</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}" @selected($projectId == $project->id)>
                        {{ $project->project_code }} - {{ $project->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    @if (!$selectedProject)
        <!-- TAMPILAN GLOBAL -->
        <div class="grid gap-4 sm:grid-cols-2 mb-6">
            <div class="rounded-lg bg-white p-6 shadow-sm border border-slate-200">
                <p class="text-sm font-medium text-slate-500">Total Proyek Terdaftar</p>
                <p class="mt-2 text-3xl font-bold text-slate-800">{{ $totalProjects }}</p>
            </div>
            <div class="rounded-lg bg-white p-6 shadow-sm border border-slate-200">
                <p class="text-sm font-medium text-slate-500">Total Pagu Anggaran (Semua Proyek)</p>
                <p class="mt-2 text-3xl font-bold text-blue-600">Rp {{ number_format($totalBudget, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="rounded-lg bg-white p-6 shadow-sm border border-slate-200">
            <h3 class="font-semibold text-slate-800 mb-4">Peringatan Stok Material Menipis</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">Kode</th>
                            <th class="px-4 py-3">Material</th>
                            <th class="px-4 py-3">Stok</th>
                            <th class="px-4 py-3">Minimal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($materials->filter(fn($m) => $m->stock <= $m->min_stock) as $material)
                            <tr>
                                <td class="px-4 py-3">{{ $material->code }}</td>
                                <td class="px-4 py-3 font-medium">{{ $material->name }}</td>
                                <td class="px-4 py-3 text-red-600 font-bold">{{ $material->stock }} {{ $material->unit }}</td>
                                <td class="px-4 py-3">{{ $material->min_stock }} {{ $material->unit }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-slate-500">Semua stok material dalam kondisi aman.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @else
        <!-- TAMPILAN SPESIFIK PROYEK -->
        <div class="mb-6 rounded-lg bg-white p-6 shadow-sm border border-slate-200">
            <h3 class="text-lg font-bold text-slate-800">{{ $selectedProject->project_code }} - {{ $selectedProject->name }}</h3>
            <p class="text-sm text-slate-500 mt-1">Lokasi: {{ $selectedProject->location ?? '-' }} | Status: {{ ucfirst($selectedProject->status) }}</p>
            
            <div class="mt-4 grid gap-4 sm:grid-cols-3 border-t border-slate-100 pt-4">
                <div>
                    <p class="text-xs text-slate-500 uppercase">Pagu Anggaran Proyek</p>
                    <p class="text-lg font-bold text-slate-800">Rp {{ number_format($selectedProject->budget, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 uppercase">Total Pengeluaran (Aktual)</p>
                    <p class="text-lg font-bold text-red-600">Rp {{ number_format($totalActualCost, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 uppercase">Sisa Anggaran</p>
                    <p class="text-lg font-bold {{ ($selectedProject->budget - $totalActualCost) < 0 ? 'text-red-600' : 'text-green-600' }}">
                        Rp {{ number_format($selectedProject->budget - $totalActualCost, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            @if($totalActualCost > $selectedProject->budget)
                <div class="mt-4 rounded-md bg-red-100 px-4 py-3 text-sm font-medium text-red-700">
                    ⚠ Peringatan: Pengeluaran aktual telah melebihi pagu anggaran proyek!
                </div>
            @endif
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <!-- Rincian Biaya -->
            <div class="rounded-lg bg-white p-6 shadow-sm border border-slate-200">
                <h3 class="font-semibold text-slate-800 mb-4">Rincian Pengeluaran per Kategori</h3>
                <ul class="space-y-3">
                    @forelse ($costByCategory as $category => $amount)
                        <li class="flex justify-between items-center border-b border-slate-100 pb-2">
                            <span class="text-sm text-slate-600">{{ $category }}</span>
                            <span class="font-medium text-slate-800">Rp {{ number_format($amount, 0, ',', '.') }}</span>
                        </li>
                    @empty
                        <li class="text-sm text-slate-500 text-center py-4">Belum ada pengeluaran.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Progres Pekerjaan -->
            <div class="rounded-lg bg-white p-6 shadow-sm border border-slate-200">
                <h3 class="font-semibold text-slate-800 mb-4">Progres Pekerjaan</h3>
                <div class="mb-4">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-slate-700">Progres Keseluruhan</span>
                        <span class="font-bold text-blue-600">{{ number_format($selectedProject->overall_progress, 2, ',', '.') }}%</span>
                    </div>
                    <div class="h-2 w-full bg-slate-200 rounded-full">
                        <div class="h-2 bg-blue-600 rounded-full" style="width: {{ $selectedProject->overall_progress }}%"></div>
                    </div>
                </div>
                <ul class="space-y-2 text-sm">
                    @foreach ($selectedProject->schedules as $schedule)
                        <li class="flex justify-between">
                            <span class="text-slate-600">{{ $schedule->name }}</span>
                            <span class="font-medium">{{ number_format($schedule->current_progress, 0) }}%</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
@endsection
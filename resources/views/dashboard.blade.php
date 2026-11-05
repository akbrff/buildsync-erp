@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <!-- 4 Kotak Statistik Atas -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <div class="rounded-lg bg-white p-6 shadow-sm border border-slate-200">
            <p class="text-sm font-medium text-slate-500">Total Proyek</p>
            <p class="mt-2 text-3xl font-bold text-slate-800">{{ $totalProjects }}</p>
        </div>
        <div class="rounded-lg bg-white p-6 shadow-sm border border-slate-200">
            <p class="text-sm font-medium text-slate-500">Proyek Berjalan</p>
            <p class="mt-2 text-3xl font-bold text-blue-600">{{ $ongoingProjects }}</p>
        </div>
        <div class="rounded-lg bg-white p-6 shadow-sm border border-slate-200">
            <p class="text-sm font-medium text-slate-500">Proyek Selesai</p>
            <p class="mt-2 text-3xl font-bold text-green-600">{{ $completedProjects }}</p>
        </div>
        <div class="rounded-lg bg-white p-6 shadow-sm border border-slate-200">
            <p class="text-sm font-medium text-slate-500">Ditunda</p>
            <p class="mt-2 text-3xl font-bold text-amber-500">{{ $onHoldProjects }}</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Tabel Progres Proyek (Kiri) -->
        <div class="lg:col-span-2 rounded-lg bg-white shadow-sm border border-slate-200 overflow-hidden">
            <div class="border-b border-slate-100 px-6 py-4">
                <h3 class="font-semibold text-slate-800">Progres Proyek Terbaru</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3">Kode</th>
                            <th class="px-6 py-3">Nama</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 w-1/3">Progres</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($projects as $project)
                            @php
                                $statusColor = match($project->status) {
                                    'ongoing' => 'bg-blue-100 text-blue-700',
                                    'completed' => 'bg-green-100 text-green-700',
                                    'planning' => 'bg-slate-100 text-slate-700',
                                    'on_hold' => 'bg-amber-100 text-amber-700',
                                    default => 'bg-slate-100 text-slate-700',
                                };
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 font-medium">{{ $project->project_code }}</td>
                                <td class="px-6 py-4"><a href="{{ route('projects.show', $project) }}" class="text-blue-600 hover:underline">{{ $project->name }}</a></td>
                                <td class="px-6 py-4">
                                    <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusColor }}">
                                        {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-2 flex-1 rounded-full bg-slate-200">
                                            <div class="h-2 rounded-full bg-blue-600" style="width: {{ $project->overall_progress }}%"></div>
                                        </div>
                                        <span class="text-xs font-bold text-slate-700">{{ number_format($project->overall_progress, 0) }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-slate-500">Belum ada data proyek.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-6 py-3 text-right">
                <a href="{{ route('projects.index') }}" class="text-sm text-blue-600 hover:underline">Lihat Semua Proyek &rarr;</a>
            </div>
        </div>

        <!-- Panel Stok Material (Kanan) -->
        <div class="rounded-lg bg-white shadow-sm border border-slate-200">
            <div class="border-b border-slate-100 px-6 py-4">
                <h3 class="font-semibold text-slate-800">Status Stok Material</h3>
            </div>
            <ul class="divide-y divide-slate-100">
                @forelse ($materials as $material)
                    <li class="px-6 py-4 flex justify-between items-center">
                        <div>
                            <p class="font-medium text-slate-800">{{ $material->name }}</p>
                            <p class="text-xs text-slate-500">{{ $material->stock }} {{ $material->unit }}</p>
                        </div>
                        @if($material->stock <= $material->min_stock)
                            <span class="rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-bold text-red-600">Menipis</span>
                        @else
                            <span class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-bold text-green-600">Aman</span>
                        @endif
                    </li>
                @empty
                    <li class="px-6 py-10 text-center text-slate-500 text-sm">Belum ada data material.</li>
                @endforelse
            </ul>
            <div class="border-t border-slate-100 px-6 py-3 text-right">
                <a href="{{ route('materials.index') }}" class="text-sm text-blue-600 hover:underline">Kelola Material &rarr;</a>
            </div>
        </div>
    </div>
@endsection
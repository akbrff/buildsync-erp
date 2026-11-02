@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @php
        // DATA PLACEHOLDER (statis). Diganti data database saat modul terkait selesai.
        $statusMap = [
            'planning'  => ['Perencanaan', 'bg-slate-100 text-slate-700'],
            'ongoing'   => ['Berjalan', 'bg-blue-100 text-blue-700'],
            'completed' => ['Selesai', 'bg-green-100 text-green-700'],
            'on_hold'   => ['Ditunda', 'bg-amber-100 text-amber-700'],
        ];

        $projects = [
            ['code' => 'PRJ-001', 'name' => 'Proyek Contoh A', 'status' => 'ongoing',   'progress' => 65],
            ['code' => 'PRJ-002', 'name' => 'Proyek Contoh B', 'status' => 'ongoing',   'progress' => 40],
            ['code' => 'PRJ-003', 'name' => 'Proyek Contoh C', 'status' => 'planning',  'progress' => 5],
            ['code' => 'PRJ-004', 'name' => 'Proyek Contoh D', 'status' => 'completed', 'progress' => 100],
            ['code' => 'PRJ-005', 'name' => 'Proyek Contoh E', 'status' => 'on_hold',   'progress' => 30],
        ];

        $materials = [
            ['name' => 'Semen',  'unit' => 'sak',   'stock' => 120, 'min' => 100],
            ['name' => 'Pasir',  'unit' => 'm3',    'stock' => 35,  'min' => 20],
            ['name' => 'Besi 10 mm', 'unit' => 'batang', 'stock' => 80, 'min' => 150],
            ['name' => 'Bata merah', 'unit' => 'buah',   'stock' => 4000, 'min' => 2500],
        ];
    @endphp

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Total Proyek" value="12" />
        <x-stat-card label="Proyek Berjalan" value="5" />
        <x-stat-card label="Proyek Selesai" value="6" />
        <x-stat-card label="Ditunda" value="1" />
    </div>

    <div class="mt-6 grid gap-4 xl:grid-cols-3">
        <section class="rounded-lg bg-white p-5 shadow-sm xl:col-span-2">
            <h2 class="font-semibold">Progres Proyek</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="text-xs uppercase text-slate-500">
                        <tr>
                            <th class="py-2 pr-4">Kode</th>
                            <th class="py-2 pr-4">Nama</th>
                            <th class="py-2 pr-4">Status</th>
                            <th class="py-2">Progres</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($projects as $p)
                            <tr>
                                <td class="py-3 pr-4 text-slate-500">{{ $p['code'] }}</td>
                                <td class="py-3 pr-4 font-medium">{{ $p['name'] }}</td>
                                <td class="py-3 pr-4">
                                    <span class="rounded-full px-2 py-1 text-xs {{ $statusMap[$p['status']][1] }}">
                                        {{ $statusMap[$p['status']][0] }}
                                    </span>
                                </td>
                                <td class="min-w-32 py-3">
                                    <div class="h-2 rounded bg-slate-200">
                                        <div class="h-2 rounded bg-blue-600" style="width: {{ $p['progress'] }}%"></div>
                                    </div>
                                    <span class="text-xs text-slate-500">{{ $p['progress'] }}%</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-lg bg-white p-5 shadow-sm">
            <h2 class="font-semibold">Stok Material</h2>
            <ul class="mt-4 divide-y divide-slate-100 text-sm">
                @foreach ($materials as $m)
                    <li class="flex items-center justify-between py-3">
                        <div>
                            <p class="font-medium">{{ $m['name'] }}</p>
                            <p class="text-xs text-slate-500">{{ number_format($m['stock']) }} {{ $m['unit'] }}</p>
                        </div>
                        @if ($m['stock'] < $m['min'])
                            <span class="rounded-full bg-red-100 px-2 py-1 text-xs text-red-700">Menipis</span>
                        @else
                            <span class="rounded-full bg-green-100 px-2 py-1 text-xs text-green-700">Aman</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </section>
    </div>

    <p class="mt-4 text-xs text-slate-400">Seluruh data di halaman ini adalah placeholder statis.</p>
@endsection
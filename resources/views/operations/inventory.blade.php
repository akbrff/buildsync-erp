@extends('layouts.app')

@section('title', 'Riwayat Transaksi Inventaris')

@section('breadcrumbs')
    <x-breadcrumb :links="[
        ['label' => 'Dashboard', 'href' => route('dashboard')],
        ['label' => 'Riwayat Inventaris', 'href' => '#'],
    ]" />
@endsection

@section('content')
    <x-page-header
        title="Riwayat Transaksi Inventaris"
        subtitle="Audit stok masuk dan keluar, sumber transaksi, referensi, serta saldo sebelum dan sesudah mutasi."
    />

    <x-flash-alert />

    <!-- WADAH UTAMA KARTU (CARD CONTAINER) -->
    <div class="bg-white rounded-2xl shadow-[0_2px_12px_rgba(44,62,80,0.04)] p-6 mb-6">
        <h2 class="text-[15px] font-bold text-[#2C3E50] mb-1">Riwayat Transaksi Inventaris</h2>
        <p class="text-xs text-[#7F7F7F] mb-6">Audit stok masuk dan keluar, sumber transaksi, referensi, serta saldo sebelum dan sesudah mutasi.</p>

        <!-- FILTER BAR -->
        <form method="GET" action="{{ route('inventory.transactions') }}" class="flex flex-col sm:flex-row gap-4 mb-6 items-center">
            <select name="material_id" class="flex-1 w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all">
                <option value="">Semua material</option>
                @foreach($materials as $m)
                    <option value="{{ $m->id }}" @selected(request('material_id') == $m->id)>{{ $m->code }} - {{ $m->name }}</option>
                @endforeach
            </select>

            <select name="type" class="flex-1 w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all">
                <option value="">Semua tipe</option>
                <option value="in" @selected(request('type') === 'in')>Masuk</option>
                <option value="out" @selected(request('type') === 'out')>Keluar</option>
                <option value="adjustment" @selected(request('type') === 'adjustment')>Penyesuaian</option>
            </select>

            <button type="submit" class="bg-[#2C3E50] text-white rounded-lg px-6 py-2.5 text-xs font-bold hover:bg-[#2C3E50]/90 transition-colors w-full sm:w-auto shrink-0">
                Filter
            </button>

            @if(request('material_id') || request('type'))
                <a href="{{ route('inventory.transactions') }}" class="text-xs font-semibold text-[#136F9A] hover:text-[#2C3E50] transition-colors shrink-0 whitespace-nowrap">
                    Reset Filter
                </a>
            @endif
        </form>

        <!-- TABEL TRANSAKSI -->
        <div class="overflow-x-auto w-full rounded-xl border border-slate-100">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8FBFD]">
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap">WAKTU</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100">MATERIAL</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 text-center">TIPE</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100">JUMLAH</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100">SALDO</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100">SUMBER / REFERENSI</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100">PROYEK</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100">CATATAN</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $row)
                        <tr class="text-[13px] text-[#2C3E50] border-b border-slate-50 hover:bg-[#F8FBFD]/50 transition-colors">
                            <td class="px-4 py-3 text-slate-600 text-xs whitespace-nowrap">{{ $row->occurred_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3 font-medium text-[#2C3E50]">
                                <div>{{ $row->material->name }}</div>
                                <div class="text-[11px] font-mono text-slate-500 font-normal">{{ $row->material->code }}</div>
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <x-badge variant="{{ $row->type === 'in' ? 'success' : ($row->type === 'out' ? 'warning' : 'neutral') }}">
                                    {{ strtoupper($row->type) }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-3 tabular font-semibold text-[#2C3E50] whitespace-nowrap">
                                {{ $row->type === 'in' ? '+' : ($row->type === 'out' ? '-' : '') }}{{ number_format($row->quantity) }} {{ $row->material->unit }}
                            </td>
                            <td class="px-4 py-3 tabular text-xs text-slate-600 whitespace-nowrap">
                                <span class="text-slate-400">{{ $row->stock_before }}</span> <span class="text-slate-300 mx-1">→</span> <span class="font-medium text-[#2C3E50]">{{ $row->stock_after }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-xs font-medium text-[#2C3E50]">{{ ucfirst($row->source) }}</div>
                                <div class="text-[11px] font-mono text-slate-500">{{ $row->reference ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($row->project)
                                    <div class="font-medium text-[#2C3E50] text-xs">{{ $row->project->name }}</div>
                                    <div class="text-[11px] font-mono text-slate-500">{{ $row->project->project_code }}</div>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600 text-xs">
                                {{ $row->notes ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-[13px] text-[#7F7F7F] font-medium">Belum ada transaksi stok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="mt-5">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
@endsection

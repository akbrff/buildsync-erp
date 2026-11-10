@extends('layouts.app')

@section('title', 'Anggaran & Kas Proyek')

@section('breadcrumbs')
<x-breadcrumb :links="[
    ['label' => 'Dashboard',        'href' => route('dashboard')],
    ['label' => 'Anggaran & Biaya', 'href' => '#'],
]"/>
@endsection

@section('content')

    {{-- ── Page Header ────────────────────────────────────────── --}}
    <x-page-header
        title="Realisasi Anggaran & Kas Proyek"
        subtitle="Pencatatan pengeluaran riil, approval kas proyek, kontrol pagu biaya, dan dokumen bukti pembayaran.">
        <a href="{{ route('budgets.create', $projectId ? ['project_id' => $projectId] : []) }}"
           class="bg-[#136F9A] text-white rounded-lg px-4 py-2.5 text-xs font-bold hover:bg-[#136F9A]/90 transition-colors shadow-[0_4px_10px_rgba(19,111,154,0.2)] inline-flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Catat Pengeluaran Kas
        </a>
    </x-page-header>

    {{-- ── 4 KPI Finansial Atas ───────────────────────────────── --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        {{-- Total Pagu --}}
        <x-stat-card
            label="Total Pagu Anggaran"
            :value="'Rp ' . number_format($totalPagu, 0, ',', '.')"
            variant="default"
            :note="$selectedProject ? 'Pagu Proyek: ' . $selectedProject->project_code : 'Akumulasi Seluruh Proyek'"/>

        {{-- Realisasi Pengeluaran Kas --}}
        <x-stat-card
            label="Realisasi Kas Disetujui"
            :value="'Rp ' . number_format($totalActualCost, 0, ',', '.')"
            :variant="$totalActualCost > $totalPagu ? 'danger' : ($serapanPercent > 85 ? 'warning' : 'info')"
            :note="'Serapan Kas: ' . $serapanPercent . '% dari pagu'">
            <div class="h-1.5 overflow-hidden rounded-full bg-slate-100">
                <div class="h-full rounded-full transition-all"
                     style="width:{{ min(100, $serapanPercent) }}%; background:{{ $totalActualCost > $totalPagu ? '#ED7132' : '#136F9A' }};"></div>
            </div>
        </x-stat-card>

        {{-- Sisa Saldo / Margin --}}
        <x-stat-card
            label="Sisa Saldo Pagu Kas"
            :value="'Rp ' . number_format($sisaSaldo, 0, ',', '.')"
            :variant="$sisaSaldo < 0 ? 'danger' : 'success'"
            :note="$sisaSaldo < 0 ? '⚠ Pagu terlampaui (Defisit)' : '✓ Sisa pagu aman terkendali'"/>

        {{-- Menunggu Approval / Pending --}}
        <x-stat-card
            label="Menunggu Approval"
            :value="'Rp ' . number_format($pendingAmount, 0, ',', '.')"
            :variant="$pendingCount > 0 ? 'warning' : 'neutral'"
            :note="$pendingCount > 0 ? $pendingCount . ' transaksi pending verifikasi' : 'Tidak ada transaksi pending'"/>
    </div>

    <x-flash-alert/>

    {{-- ── Card Container: Filter & Data Table ─────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-[0_2px_12px_rgba(44,62,80,0.04)] p-6 mb-6">
        {{-- Filter Bar Presisi --}}
        <form method="GET" action="{{ route('budgets.index') }}" class="flex flex-wrap items-center gap-3 mb-6">
            {{-- 1. Search Input --}}
            <div class="relative w-full sm:w-64">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7 7 0 1 0 6.65 6.65a7 7 0 0 0 9.9 9.9z"/>
                </svg>
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       placeholder="Cari voucher / keterangan..."
                       class="w-full rounded-lg border border-slate-200 bg-slate-50/50 pl-10 pr-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all">
            </div>

            {{-- 2. Proyek --}}
            <div class="w-full sm:w-48">
                <select name="project_id"
                        class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all"
                        onchange="this.form.submit()">
                    <option value="">— Semua Proyek —</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}" @selected($projectId == $project->id)>
                            {{ $project->project_code }} – {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- 3. Kategori --}}
            <div class="w-full sm:w-44">
                <select name="category"
                        class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all"
                        onchange="this.form.submit()">
                    <option value="">— Semua Kategori —</option>
                    @foreach (['Material', 'Upah Tenaga Kerja', 'Sewa Alat', 'Operasional'] as $cat)
                        <option value="{{ $cat }}" @selected(($categoryFilter ?? '') === $cat)>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 4. Status Verifikasi --}}
            <div class="w-full sm:w-40">
                <select name="status"
                        class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all"
                        onchange="this.form.submit()">
                    <option value="">— Status Kas —</option>
                    <option value="approved" @selected(($statusFilter ?? '') === 'approved')>Disetujui (Approved)</option>
                    <option value="pending" @selected(($statusFilter ?? '') === 'pending')>Menunggu (Pending)</option>
                    <option value="rejected" @selected(($statusFilter ?? '') === 'rejected')>Ditolak (Rejected)</option>
                </select>
            </div>

            {{-- 5. Rentang Tanggal --}}
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <input type="date" name="start_date" value="{{ $startDate ?? request('start_date') }}"
                       class="w-full sm:w-36 rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all tabular" title="Tanggal Mulai">
                <span class="text-xs text-[#7F7F7F] shrink-0">s/d</span>
                <input type="date" name="end_date" value="{{ $endDate ?? request('end_date') }}"
                       class="w-full sm:w-36 rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all tabular" title="Tanggal Selesai">
            </div>

            {{-- 6. Tombol Terapkan --}}
            <button type="submit"
                    class="bg-white border border-slate-200/80 text-[#2C3E50] rounded-lg px-4 py-2.5 text-xs font-bold hover:bg-slate-50 transition-colors shrink-0 flex items-center gap-2">
                <svg class="h-3.5 w-3.5 text-[#7F7F7F]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                <span>Terapkan</span>
            </button>

            {{-- 7. Tombol Reset --}}
            @php
                $hasFilters = count(array_filter(request()->except(['page', '_token']))) > 0;
            @endphp
            @if ($hasFilters)
                <a href="{{ route('budgets.index') }}"
                   class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2.5 text-xs font-semibold bg-[#ED7132]/10 text-[#ED7132] border border-[#ED7132]/20 hover:bg-[#ED7132]/20 transition-colors shrink-0">
                    <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span>Reset</span>
                </a>
            @endif
        </form>

        {{-- Tabel Data --}}
        <div class="overflow-x-auto w-full mt-4 rounded-xl border border-slate-100">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8FBFD]">
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap" style="width:110px;">NO. VOUCHER</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap" style="width:100px;">TANGGAL</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap" style="min-width:140px;">PROYEK</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap" style="min-width:130px;">KATEGORI BIAYA</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap">KETERANGAN PENGELUARAN</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap text-center" style="width:70px;">BUKTI</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap" style="width:100px;">VERIFIKASI</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap text-right" style="min-width:140px;">NOMINAL (RP)</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap text-center" style="width:110px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($budgets as $budget)
                        @php
                            $isOver = $budget->project && $budget->project->budget > 0
                                && ($budget->project->budgets()->where('status', 'approved')->sum('amount') > $budget->project->budget);

                            $approvalVariant = match ($budget->status) {
                                'approved' => 'success',
                                'pending'  => 'warning',
                                'rejected' => 'danger',
                                default    => 'neutral',
                            };
                            $approvalLabel = match ($budget->status) {
                                'approved' => 'Disetujui',
                                'pending'  => 'Pending',
                                'rejected' => 'Ditolak',
                                default    => ucfirst($budget->status ?? 'Approved'),
                            };
                        @endphp
                        <tr class="text-[13px] text-[#2C3E50] border-b border-slate-50 hover:bg-[#F8FBFD]/50 transition-colors {{ $isOver ? 'bg-rose-50/30' : '' }}">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-mono text-xs font-semibold px-2 py-1 rounded bg-[#F8FBFD] text-slate-700 border border-slate-200">
                                    {{ $budget->formatted_voucher }}
                                </span>
                            </td>
                            <td class="px-4 py-3 tabular text-xs text-slate-600 whitespace-nowrap">
                                @if($budget->date)
                                    {{ $budget->date->format('d M Y') }}
                                @else
                                    <span class="text-slate-300 font-mono">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <a href="{{ route('projects.show', $budget->project) }}"
                                   class="font-mono text-xs font-bold text-[#136F9A] hover:underline">
                                    {{ $budget->project->project_code }}
                                </a>
                                @if ($isOver)
                                    <span class="ml-1 inline-block text-[10px] font-bold text-[#ED7132]" title="Total realisasi proyek melebihi pagu">
                                        [Over]
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <x-badge variant="neutral">{{ $budget->category }}</x-badge>
                            </td>
                            <td class="px-4 py-3 max-w-xs truncate">
                                <a href="{{ route('budgets.show', $budget) }}"
                                   class="text-[13px] font-medium text-[#2C3E50] hover:text-[#136F9A] hover:underline">
                                    {{ $budget->description }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                @if ($budget->receipt)
                                    <a href="{{ asset('storage/' . $budget->receipt) }}" target="_blank"
                                       class="inline-flex items-center justify-center rounded-lg p-1.5 text-[#136F9A] hover:bg-[#136F9A]/10 transition-colors"
                                       title="Buka Dokumen Bukti / Struk">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                        </svg>
                                    </a>
                                @else
                                    <span class="text-slate-300 font-mono">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <x-badge :variant="$approvalVariant">{{ $approvalLabel }}</x-badge>
                            </td>
                            <td class="px-4 py-3 text-right tabular font-bold text-[#2C3E50] whitespace-nowrap">
                                Rp {{ number_format($budget->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('budgets.show', $budget) }}"
                                       class="rounded-md px-2 py-1 text-xs font-semibold text-[#136F9A] hover:bg-[#136F9A]/10 transition">
                                        Detail
                                    </a>
                                    <a href="{{ route('budgets.edit', $budget) }}"
                                       class="rounded-md px-2 py-1 text-xs font-semibold text-[#ED7132] hover:bg-[#ED7132]/10 transition">
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="py-12 flex flex-col items-center justify-center text-center">
                                    <p class="text-[13px] text-[#7F7F7F] font-medium mb-4">
                                        Belum ada catatan pengeluaran kas yang sesuai filter.
                                    </p>
                                    <a href="{{ route('budgets.create') }}"
                                       class="bg-[#136F9A] text-white rounded-lg px-5 py-2.5 text-[13px] font-bold hover:bg-[#136F9A]/90 transition-colors shadow-[0_4px_10px_rgba(19,111,154,0.2)] inline-flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        <span>+ Catat Pengeluaran Pertama</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($budgets->isNotEmpty())
                    <tfoot>
                        <tr class="font-semibold text-xs text-[#2C3E50] border-t-2 border-slate-200 bg-[#F8FBFD]">
                            <td colspan="7" class="py-3 px-4 text-right uppercase tracking-wider text-[11px] text-[#7F7F7F]">
                                Total Pengeluaran (Halaman Ini):
                            </td>
                            <td class="py-3 px-4 text-right tabular font-bold text-[#2C3E50]">
                                Rp {{ number_format($budgets->sum('amount'), 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        @if ($budgets->hasPages())
            <div class="mt-5">
                {{ $budgets->links() }}
            </div>
        @endif
    </div>
@endsection

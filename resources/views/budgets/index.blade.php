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
           class="inline-flex items-center gap-1.5 rounded px-3 font-semibold text-white transition-colors"
           style="height:2.25rem; font-size:.8125rem; background:#2563eb; border:1px solid #1d4ed8;"
           onmouseover="this.style.background='#1d4ed8';" onmouseout="this.style.background='#2563eb';">
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
            <div class="h-1.5 overflow-hidden rounded-full" style="background:#e2e8f0;">
                <div class="h-full rounded-full transition-all"
                     style="width:{{ min(100, $serapanPercent) }}%; background:{{ $totalActualCost > $totalPagu ? '#ef4444' : '#2563eb' }};"></div>
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

    {{-- ── Filter Bar Presisi ─────────────────────────────────── --}}
    <div class="mb-4">
        <x-filter-bar action="{{ route('budgets.index') }}" :searchable="true" searchPlaceholder="Cari voucher / keterangan...">
            {{-- Proyek --}}
            <select name="project_id" class="erp-input" style="width:200px;" onchange="this.form.submit()">
                <option value="">— Semua Proyek —</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}" @selected($projectId == $project->id)>
                        {{ $project->project_code }} – {{ $project->name }}
                    </option>
                @endforeach
            </select>

            {{-- Kategori --}}
            <select name="category" class="erp-input" style="width:160px;" onchange="this.form.submit()">
                <option value="">— Semua Kategori —</option>
                @foreach (['Material', 'Upah Tenaga Kerja', 'Sewa Alat', 'Operasional'] as $cat)
                    <option value="{{ $cat }}" @selected(($categoryFilter ?? '') === $cat)>{{ $cat }}</option>
                @endforeach
            </select>

            {{-- Status Verifikasi --}}
            <select name="status" class="erp-input" style="width:145px;" onchange="this.form.submit()">
                <option value="">— Status Kas —</option>
                <option value="approved" @selected(($statusFilter ?? '') === 'approved')>Disetujui (Approved)</option>
                <option value="pending" @selected(($statusFilter ?? '') === 'pending')>Menunggu (Pending)</option>
                <option value="rejected" @selected(($statusFilter ?? '') === 'rejected')>Ditolak (Rejected)</option>
            </select>

            {{-- Rentang Tanggal --}}
            <div class="flex items-center gap-1.5">
                <input type="date" name="start_date" value="{{ $startDate ?? request('start_date') }}"
                       class="erp-input tabular" style="width:130px;" title="Tanggal Mulai">
                <span class="text-xs text-slate-400">s/d</span>
                <input type="date" name="end_date" value="{{ $endDate ?? request('end_date') }}"
                       class="erp-input tabular" style="width:130px;" title="Tanggal Selesai">
            </div>
        </x-filter-bar>
    </div>

    <x-flash-alert/>

    {{-- ── Data Table ─────────────────────────────────────────── --}}
    <div class="overflow-hidden rounded-md border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="erp-table w-full text-left">
                <thead>
                    <tr>
                        <th style="width:105px;">No. Voucher</th>
                        <th style="width:100px;">Tanggal</th>
                        <th style="min-width:140px;">Proyek</th>
                        <th style="min-width:130px;">Kategori Biaya</th>
                        <th>Keterangan Pengeluaran</th>
                        <th class="text-center" style="width:70px;">Bukti</th>
                        <th style="width:100px;">Verifikasi</th>
                        <th class="text-right" style="min-width:140px;">Nominal (Rp)</th>
                        <th class="text-center" style="width:110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($budgets as $budget)
                        @php
                            // Overbudget check per project
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

                            $rowStyle = $isOver ? 'background:#fffbfb;' : '';
                        @endphp
                        <tr style="{{ $rowStyle }}">
                            <td>
                                <span class="font-code text-xs font-semibold px-1.5 py-0.5 rounded"
                                      style="background:#f1f5f9; color:#475569; border:1px solid #e2e8f0;">
                                    {{ $budget->formatted_voucher }}
                                </span>
                            </td>
                            <td class="tabular text-xs text-slate-600">
                                @if($budget->date)
                                    {{ $budget->date->format('d M Y') }}
                                @else
                                    <span class="text-slate-300 font-mono">-</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('projects.show', $budget->project) }}"
                                   class="font-code text-xs font-semibold text-slate-800 hover:text-blue-600">
                                    {{ $budget->project->project_code }}
                                </a>
                                @if ($isOver)
                                    <span class="ml-1 inline-block text-[10px] font-bold text-red-600" title="Total realisasi proyek melebihi pagu">
                                        [Over]
                                    </span>
                                @endif
                            </td>
                            <td>
                                <x-badge variant="neutral">{{ $budget->category }}</x-badge>
                            </td>
                            <td>
                                <a href="{{ route('budgets.show', $budget) }}"
                                   class="text-[13px] font-medium text-slate-900 hover:text-blue-600 hover:underline">
                                    {{ $budget->description }}
                                </a>
                            </td>
                            <td class="text-center">
                                @if ($budget->receipt)
                                    <a href="{{ asset('storage/' . $budget->receipt) }}" target="_blank"
                                       class="inline-flex items-center justify-center rounded p-1 text-blue-600 hover:bg-blue-50"
                                       title="Buka Dokumen Bukti / Struk">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                        </svg>
                                    </a>
                                @else
                                    <span class="text-slate-300 font-mono">-</span>
                                @endif
                            </td>
                            <td>
                                <x-badge :variant="$approvalVariant">{{ $approvalLabel }}</x-badge>
                            </td>
                            <td class="text-right tabular font-bold text-slate-900">
                                Rp {{ number_format($budget->amount, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('budgets.show', $budget) }}"
                                       class="rounded px-1.5 py-0.5 text-xs font-medium text-blue-600 hover:bg-blue-50 transition">
                                        Detail
                                    </a>
                                    <a href="{{ route('budgets.edit', $budget) }}"
                                       class="rounded px-1.5 py-0.5 text-xs font-medium text-amber-600 hover:bg-amber-50 transition">
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-xs text-slate-400">
                                Belum ada catatan pengeluaran kas yang sesuai filter.
                                <br>
                                <a href="{{ route('budgets.create') }}"
                                   class="mt-2 inline-flex items-center gap-1 rounded px-3 py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 transition">
                                    + Catat Pengeluaran Pertama
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($budgets->isNotEmpty())
                    <tfoot>
                        <tr class="font-semibold text-xs text-slate-800 border-t-2 border-slate-300 bg-slate-50">
                            <td colspan="7" class="py-3 px-4 text-right uppercase tracking-wider text-[11px] text-slate-500">
                                Total Pengeluaran (Halaman Ini):
                            </td>
                            <td class="py-3 px-4 text-right tabular font-bold text-slate-900">
                                Rp {{ number_format($budgets->sum('amount'), 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        @if ($budgets->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">
                {{ $budgets->links() }}
            </div>
        @endif
    </div>
@endsection

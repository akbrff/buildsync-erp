@extends('layouts.app')

@section('title', 'Detail Biaya: ' . $budget->formatted_voucher)

@section('breadcrumbs')
<x-breadcrumb :links="[
    ['label' => 'Dashboard',        'href' => route('dashboard')],
    ['label' => 'Anggaran & Biaya', 'href' => route('budgets.index', ['project_id' => $budget->project_id])],
    ['label' => $budget->formatted_voucher, 'href' => '#'],
]"/>
@endsection

@section('content')
@php
    $approvalVariant = match ($budget->status) {
        'approved' => 'success',
        'pending'  => 'warning',
        'rejected' => 'danger',
        default    => 'neutral',
    };
    $approvalLabel = match ($budget->status) {
        'approved' => 'Disetujui (Approved)',
        'pending'  => 'Menunggu (Pending)',
        'rejected' => 'Ditolak (Rejected)',
        default    => ucfirst($budget->status ?? 'Approved'),
    };
@endphp

    {{-- ── Flash Message ──────────────────────────────────────── --}}
    <x-flash-alert/>

    {{-- ── Page Header ────────────────────────────────────────── --}}
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2">
                <span class="font-code text-xs font-semibold px-2 py-0.5 rounded"
                      style="background:#f1f5f9; color:#475569; border:1px solid #e2e8f0;">
                    {{ $budget->formatted_voucher }}
                </span>
                <x-badge :variant="$approvalVariant" :dot="true">
                    {{ $approvalLabel }}
                </x-badge>
                <a href="{{ route('projects.show', $budget->project) }}"
                   class="font-code text-xs font-semibold text-slate-600 hover:text-blue-600">
                    {{ $budget->project->project_code }} — {{ $budget->project->name }}
                </a>
            </div>

            <h1 class="mt-1 text-xl font-bold tracking-tight text-slate-900 truncate">
                {{ $budget->description }}
            </h1>

            <p class="mt-0.5 text-xs text-slate-500">
                Dicatat pada <span class="tabular font-medium text-slate-700">{{ $budget->date?->format('d F Y') ?? '-' }}</span>
                &bull; Kategori: <strong class="text-slate-700">{{ $budget->category }}</strong>
            </p>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('budgets.index', ['project_id' => $budget->project_id]) }}"
               class="inline-flex items-center gap-1.5 rounded px-3 font-medium transition-colors"
               style="height:2.125rem; font-size:.8125rem; border:1px solid #cbd5e1; background:#fff; color:#475569;"
               onmouseover="this.style.background='#f8fafc';" onmouseout="this.style.background='#fff';">
                &larr; Daftar Biaya
            </a>

            <a href="{{ route('budgets.edit', $budget) }}"
               class="inline-flex items-center gap-1.5 rounded px-3 font-medium transition-colors"
               style="height:2.125rem; font-size:.8125rem; border:1px solid #cbd5e1; background:#fff; color:#475569;"
               onmouseover="this.style.background='#f8fafc';" onmouseout="this.style.background='#fff';">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>

            <form action="{{ route('budgets.destroy', $budget) }}" method="POST"
                  onsubmit="return confirm('Hapus catatan pengeluaran kas ini?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                        title="Hapus Transaksi"
                        class="inline-flex items-center justify-center rounded px-2.5 font-medium transition-colors"
                        style="height:2.125rem; font-size:.8125rem; border:1px solid #fecaca; background:#fef2f2; color:#b91c1c;"
                        onmouseover="this.style.background='#fee2e2';" onmouseout="this.style.background='#fef2f2';">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    {{-- ── Grid Rincian Voucher Transaksi ─────────────────────── --}}
    <div class="grid gap-4 lg:grid-cols-3 max-w-5xl">
        {{-- Panel Kiri: Sheet Rincian --}}
        <div class="lg:col-span-2 overflow-hidden rounded-md" style="border:1px solid #e2e8f0; background:#fff;">
            <div class="px-4 py-3" style="border-bottom:1px solid #f1f5f9;">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-800">
                    Lembar Voucher Pengeluaran Kas
                </h3>
            </div>

            <div class="p-4">
                {{-- Nominal Highlight --}}
                <div class="mb-4 rounded-md p-4" style="background:#f8fafc; border:1px solid #e2e8f0;">
                    <p class="text-[11px] font-medium uppercase tracking-wider text-slate-500">Nominal Pengeluaran Kas</p>
                    <p class="mt-1 text-3xl font-bold tabular text-slate-900">
                        Rp {{ number_format($budget->amount, 0, ',', '.') }}
                    </p>
                </div>

                <dl class="grid gap-3 sm:grid-cols-2 text-xs">
                    <div class="rounded p-2.5" style="background:#f8fafc; border:1px solid #f1f5f9;">
                        <dt class="text-[11px] font-medium text-slate-500">Nomor Voucher</dt>
                        <dd class="mt-0.5 font-code font-bold text-slate-900">{{ $budget->formatted_voucher }}</dd>
                    </div>

                    <div class="rounded p-2.5" style="background:#f8fafc; border:1px solid #f1f5f9;">
                        <dt class="text-[11px] font-medium text-slate-500">Status Verifikasi</dt>
                        <dd class="mt-0.5 font-semibold">
                            <x-badge :variant="$approvalVariant">{{ $approvalLabel }}</x-badge>
                        </dd>
                    </div>

                    <div class="rounded p-2.5" style="background:#f8fafc; border:1px solid #f1f5f9;">
                        <dt class="text-[11px] font-medium text-slate-500">Proyek Konstruksi</dt>
                        <dd class="mt-0.5 font-semibold text-blue-700">
                            <a href="{{ route('projects.show', $budget->project) }}" class="hover:underline">
                                {{ $budget->project->project_code }} — {{ $budget->project->name }}
                            </a>
                        </dd>
                    </div>

                    <div class="rounded p-2.5" style="background:#f8fafc; border:1px solid #f1f5f9;">
                        <dt class="text-[11px] font-medium text-slate-500">Kategori Biaya</dt>
                        <dd class="mt-0.5 font-semibold text-slate-800">{{ $budget->category }}</dd>
                    </div>

                    <div class="rounded p-2.5" style="background:#f8fafc; border:1px solid #f1f5f9;">
                        <dt class="text-[11px] font-medium text-slate-500">Tanggal Transaksi</dt>
                        <dd class="mt-0.5 font-medium tabular text-slate-900">{{ $budget->date?->format('d F Y') ?? '-' }}</dd>
                    </div>

                    <div class="rounded p-2.5" style="background:#f8fafc; border:1px solid #f1f5f9;">
                        <dt class="text-[11px] font-medium text-slate-500">Pagu Proyek Terkait</dt>
                        <dd class="mt-0.5 font-medium tabular text-slate-700">Rp {{ number_format($budget->project->budget, 0, ',', '.') }}</dd>
                    </div>

                    <div class="sm:col-span-2 rounded p-2.5" style="background:#f8fafc; border:1px solid #f1f5f9;">
                        <dt class="text-[11px] font-medium text-slate-500">Keterangan / Uraian Pengeluaran</dt>
                        <dd class="mt-1 leading-relaxed text-slate-800">{{ $budget->description }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Panel Kanan: Bukti Transaksi & Audit Log --}}
        <div class="space-y-4">
            {{-- Dokumen Bukti / Struk --}}
            <div class="rounded-md p-4" style="border:1px solid #e2e8f0; background:#fff;">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-800 mb-2">
                    Dokumen Bukti / Struk
                </h3>

                @if ($budget->receipt)
                    <div class="rounded-md p-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
                        <div class="flex items-center gap-2 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="text-xs font-medium text-slate-700 truncate">Berkas Terlampir</span>
                        </div>
                        <a href="{{ asset('storage/' . $budget->receipt) }}" target="_blank"
                           class="inline-flex w-full items-center justify-center gap-1 rounded py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 transition">
                            Buka Bukti Fisik &rarr;
                        </a>
                    </div>
                @else
                    <div class="rounded-md p-4 text-center text-xs text-slate-400" style="background:#f8fafc; border:1px dashed #cbd5e1;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-6 w-6 text-slate-300 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                        Belum ada lampiran bukti struk/kwitansi.
                        <br>
                        <a href="{{ route('budgets.edit', $budget) }}" class="mt-1 inline-block text-blue-600 hover:underline">
                            + Unggah bukti
                        </a>
                    </div>
                @endif
            </div>

            {{-- Audit Log Sistem --}}
            <div class="rounded-md p-4" style="border:1px solid #e2e8f0; background:#fff;">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-800 mb-2">
                    Audit Log Sistem
                </h3>
                <dl class="space-y-2 text-xs">
                    <div class="flex justify-between py-1" style="border-bottom:1px solid #f8fafc;">
                        <dt class="text-slate-500">Tanggal Entri</dt>
                        <dd class="font-medium tabular text-slate-800">{{ $budget->created_at?->format('d M Y H:i') ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between py-1" style="border-bottom:1px solid #f8fafc;">
                        <dt class="text-slate-500">Update Terakhir</dt>
                        <dd class="font-medium tabular text-slate-800">{{ $budget->updated_at?->format('d M Y H:i') ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between py-1">
                        <dt class="text-slate-500">ID Database</dt>
                        <dd class="font-mono text-slate-600">#{{ $budget->id }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
@endsection

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
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2">
                <span class="font-mono text-xs font-semibold px-2.5 py-1 rounded-md border border-[#E5E7EB] dark:border-[#27272A] bg-[#F4F8FB] dark:bg-[#27272A] text-[#5A738E] dark:text-slate-300">
                    {{ $budget->formatted_voucher }}
                </span>
                <x-badge :variant="$approvalVariant" :dot="true">
                    {{ $approvalLabel }}
                </x-badge>
                <a href="{{ route('projects.show', $budget->project) }}"
                   class="font-mono text-xs font-semibold text-[#5A738E] dark:text-[#A1A1AA] hover:text-[#1D4E89] dark:hover:text-[#CFE3F3]">
                    {{ $budget->project->project_code }} — {{ $budget->project->name }}
                </a>
            </div>

            <h1 class="mt-2 text-2xl font-bold tracking-tight text-[#0B2545] dark:text-[#F4F4F5] truncate">
                {{ $budget->description }}
            </h1>

            <p class="mt-1 text-xs text-[#64748B] dark:text-slate-400">
                Dicatat pada <span class="tabular font-medium text-[#1E293B] dark:text-slate-300">{{ $budget->date ? $budget->date->format('d F Y') : '-' }}</span>
                &bull; Kategori: <strong class="text-[#1E293B] dark:text-white">{{ $budget->category }}</strong>
            </p>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('budgets.index', ['project_id' => $budget->project_id]) }}"
               class="btn-secondary">
                &larr; Daftar Biaya
            </a>

            <a href="{{ route('budgets.edit', $budget) }}"
               class="btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#64748B] dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>

            <form action="{{ route('budgets.destroy', $budget) }}" method="POST"
                  onsubmit="return confirm('Yakin ingin menghapus catatan pengeluaran kas ini?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                        title="Hapus Transaksi"
                        class="inline-flex items-center justify-center rounded-lg px-2.5 h-10 font-medium transition-colors bg-[#DC2626]/10 text-[#DC2626] border border-[#DC2626]/20 hover:bg-[#DC2626]/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    {{-- ── Grid Rincian Voucher Transaksi ─────────────────────── --}}
    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Panel Kiri: Sheet Rincian --}}
        <div class="lg:col-span-2 overflow-hidden rounded-lg border border-[#E5E7EB] dark:border-[#27272A] bg-white dark:bg-[#18181B] shadow-none">
            <div class="px-5 py-3.5 border-b border-[#E5E7EB] dark:border-[#27272A]">
                <h3 class="text-base font-semibold text-[#0B2545] dark:text-[#F4F4F5]">
                    Lembar Voucher Pengeluaran Kas
                </h3>
            </div>

            <div class="p-5">
                {{-- Nominal Highlight --}}
                <div class="mb-5 rounded-lg p-4 bg-[#F8FAFC] dark:bg-[#27272A] border border-[#E5E7EB] dark:border-[#27272A]">
                    <p class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">Nominal Pengeluaran Kas</p>
                    <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-[#0B2545] dark:text-[#F4F4F5]">
                        Rp {{ number_format($budget->amount, 0, ',', '.') }}
                    </p>
                </div>

                <dl class="grid gap-3 sm:grid-cols-2 text-sm">
                    <div class="rounded-lg p-3 bg-[#F8FAFC] dark:bg-[#27272A] border border-[#E5E7EB] dark:border-[#27272A]">
                        <dt class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">Nomor Voucher</dt>
                        <dd class="mt-0.5 font-mono text-sm font-semibold text-[#0B2545] dark:text-slate-100">{{ $budget->formatted_voucher }}</dd>
                    </div>

                    <div class="rounded-lg p-3 bg-[#F8FAFC] dark:bg-[#27272A] border border-[#E5E7EB] dark:border-[#27272A]">
                        <dt class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">Status Verifikasi</dt>
                        <dd class="mt-1">
                            <x-badge :variant="$approvalVariant">{{ $approvalLabel }}</x-badge>
                        </dd>
                    </div>

                    <div class="rounded-lg p-3 bg-[#F8FAFC] dark:bg-[#27272A] border border-[#E5E7EB] dark:border-[#27272A]">
                        <dt class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">Proyek Konstruksi</dt>
                        <dd class="mt-0.5 font-medium text-[#1D4E89] dark:text-[#93C5FD]">
                            <a href="{{ route('projects.show', $budget->project) }}" class="hover:underline">
                                {{ $budget->project->project_code }} — {{ $budget->project->name }}
                            </a>
                        </dd>
                    </div>

                    <div class="rounded-lg p-3 bg-[#F8FAFC] dark:bg-[#27272A] border border-[#E5E7EB] dark:border-[#27272A]">
                        <dt class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">Kategori Biaya</dt>
                        <dd class="mt-0.5 font-medium text-[#0B2545] dark:text-slate-200">{{ $budget->category }}</dd>
                    </div>

                    <div class="rounded-lg p-3 bg-[#F8FAFC] dark:bg-[#27272A] border border-[#E5E7EB] dark:border-[#27272A]">
                        <dt class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">Tanggal Transaksi</dt>
                        <dd class="mt-0.5 font-medium tabular-nums text-[#0B2545] dark:text-slate-200">
                            @if($budget->date)
                                {{ $budget->date->format('d F Y') }}
                            @else
                                <span class="text-slate-300 font-mono">-</span>
                            @endif
                        </dd>
                    </div>

                    <div class="rounded-lg p-3 bg-[#F8FAFC] dark:bg-[#27272A] border border-[#E5E7EB] dark:border-[#27272A]">
                        <dt class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">Pagu Proyek Terkait</dt>
                        <dd class="mt-0.5 font-medium tabular-nums text-[#0B2545] dark:text-slate-200">Rp {{ number_format($budget->project->budget, 0, ',', '.') }}</dd>
                    </div>

                    <div class="sm:col-span-2 rounded-lg p-3 bg-[#F8FAFC] dark:bg-[#27272A] border border-[#E5E7EB] dark:border-[#27272A]">
                        <dt class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">Keterangan / Uraian Pengeluaran</dt>
                        <dd class="mt-1 leading-relaxed text-sm text-[#0B2545] dark:text-slate-300">{{ $budget->description }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Panel Kanan: Bukti Transaksi & Audit Log --}}
        <div class="space-y-4">
            {{-- Dokumen Bukti / Struk --}}
            <div class="rounded-lg p-5 border border-[#E5E7EB] dark:border-[#27272A] bg-white dark:bg-[#18181B] shadow-none">
                <h3 class="text-[15px] font-bold text-[#0B2545] dark:text-[#F4F4F5] mb-3">
                    Dokumen Bukti / Struk
                </h3>

                @if ($budget->receipt)
                    <div class="rounded-md p-3.5 bg-slate-50 dark:bg-[#27272A] border border-slate-200 dark:border-[#27272A]">
                        <div class="flex items-center gap-2 mb-2.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300 truncate">Berkas Terlampir</span>
                        </div>
                        <a href="{{ asset('storage/' . $budget->receipt) }}" target="_blank"
                           class="inline-flex w-full items-center justify-center gap-1 rounded py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 transition">
                            Buka Bukti Fisik &rarr;
                        </a>
                    </div>
                @else
                    <div class="rounded-md p-5 text-center text-xs text-slate-400 bg-slate-50 dark:bg-[#27272A] border border-dashed border-slate-300 dark:border-[#27272A]">
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
            <div class="rounded-lg p-5 border border-[#E5E7EB] dark:border-[#27272A] bg-white dark:bg-[#18181B] shadow-none">
                <h3 class="text-[15px] font-bold text-[#0B2545] dark:text-[#F4F4F5] mb-3">
                    Audit Log Sistem
                </h3>
                <dl class="space-y-2 text-xs">
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <dt class="text-slate-500">Tanggal Entri</dt>
                        <dd class="font-medium tabular text-slate-800">
                            @if($budget->created_at)
                                {{ $budget->created_at->format('d M Y H:i') }}
                            @else
                                <span class="text-slate-300 font-mono">-</span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <dt class="text-slate-500">Update Terakhir</dt>
                        <dd class="font-medium tabular text-slate-800">
                            @if($budget->updated_at)
                                {{ $budget->updated_at->format('d M Y H:i') }}
                            @else
                                <span class="text-slate-300 font-mono">-</span>
                            @endif
                        </dd>
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

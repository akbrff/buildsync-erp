@extends('layouts.app')

@section('title', __('budgets.title'))

@section('breadcrumbs')
<x-breadcrumb :links="[
    ['label' => __('common.dashboard'), 'href' => route('dashboard')],
    ['label' => __('budgets.nav_breadcrumb'), 'href' => '#'],
]"/>
@endsection

@section('content')

    {{-- ── Page Header ────────────────────────────────────────── --}}
    <x-page-header
        :title="__('budgets.realization_title')">
        <a href="{{ route('budgets.create', $projectId ? ['project_id' => $projectId] : []) }}"
           class="bg-[#136F9A] text-white rounded-md px-4 py-2.5 text-xs font-bold hover:bg-[#136F9A]/90 transition-colors shadow-none inline-flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('budgets.record_expense_action') }}
        </a>
    </x-page-header>

    {{-- ── 4 KPI Finansial Atas ───────────────────────────────── --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        {{-- Total Pagu --}}
        <x-stat-card
            :label="__('budgets.total_pagu')"
            :value="'Rp ' . number_format($totalPagu, 0, ',', '.')"
            variant="default"/>

        {{-- Realisasi Pengeluaran Kas --}}
        <x-stat-card
            :label="__('budgets.realized_approved')"
            :value="'Rp ' . number_format($totalActualCost, 0, ',', '.')"
            :variant="$totalActualCost > $totalPagu ? 'danger' : ($serapanPercent > 85 ? 'warning' : 'info')">
            <div class="h-1.5 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-700">
                <div class="h-full rounded-full transition-all"
                     style="width:{{ min(100, $serapanPercent) }}%; background:{{ $totalActualCost > $totalPagu ? '#ED7132' : '#136F9A' }};"></div>
            </div>
        </x-stat-card>

        {{-- Sisa Saldo / Margin --}}
        <x-stat-card
            :label="__('budgets.remaining_balance')"
            :value="'Rp ' . number_format($sisaSaldo, 0, ',', '.')"
            :variant="$sisaSaldo < 0 ? 'danger' : 'success'"/>

        {{-- Menunggu Approval / Pending --}}
        <x-stat-card
            :label="__('budgets.awaiting_approval')"
            :value="'Rp ' . number_format($pendingAmount, 0, ',', '.')"
            :variant="$pendingCount > 0 ? 'warning' : 'neutral'"/>
    </div>

    <x-flash-alert/>

    {{-- ── Card Container: Filter & Data Table ─────────────────────────── --}}
    <div class="bg-white dark:bg-[#18181B] rounded-lg rounded-2xl border border-[#E5E7EB] dark:border-[#27272A] shadow-none p-5 mb-6">
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
                       placeholder="{{ __('budgets.search_voucher') }}"
                       class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] pl-10 pr-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all">
            </div>

            {{-- 2. Proyek --}}
            <div class="w-full sm:w-48">
                <select name="project_id"
                        class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all"
                        onchange="this.form.submit()">
                    <option value="">{{ __('budgets.all_projects') }}</option>
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
                        class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all"
                        onchange="this.form.submit()">
                    <option value="">{{ __('budgets.all_categories') }}</option>
                    @foreach (['Material', 'Upah Tenaga Kerja', 'Sewa Alat', 'Operasional'] as $cat)
                        <option value="{{ $cat }}" @selected(($categoryFilter ?? '') === $cat)>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 4. Status Verifikasi --}}
            <div class="w-full sm:w-40">
                <select name="status"
                        class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all"
                        onchange="this.form.submit()">
                    <option value="">{{ __('budgets.cash_status_placeholder') }}</option>
                    <option value="approved" @selected(($statusFilter ?? '') === 'approved')>{{ __('budgets.status_approved') }}</option>
                    <option value="pending" @selected(($statusFilter ?? '') === 'pending')>{{ __('budgets.status_pending') }}</option>
                    <option value="rejected" @selected(($statusFilter ?? '') === 'rejected')>{{ __('budgets.status_rejected') }}</option>
                </select>
            </div>

            {{-- 5. Rentang Tanggal --}}
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <input type="date" name="start_date" value="{{ $startDate ?? request('start_date') }}"
                       class="w-full sm:w-36 rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all tabular" title="{{ __('budgets.date') }}">
                <span class="text-xs text-[#7F7F7F] shrink-0">s/d</span>
                <input type="date" name="end_date" value="{{ $endDate ?? request('end_date') }}"
                       class="w-full sm:w-36 rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all tabular" title="{{ __('budgets.date') }}">
            </div>

            {{-- 6. Tombol Terapkan --}}
            <button type="submit"
                    class="bg-white dark:bg-[#27272A] border border-slate-200/80 dark:border-[#27272A] text-[#2C3E50] dark:text-slate-200 rounded-md px-4 py-2.5 text-xs font-bold hover:bg-slate-50 dark:hover:bg-white/5 transition-colors shrink-0 flex items-center gap-2">
                <svg class="h-3.5 w-3.5 text-[#7F7F7F]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                <span>{{ __('budgets.apply') }}</span>
            </button>

            {{-- 7. Tombol Reset --}}
            @php
                $hasFilters = count(array_filter(request()->except(['page', '_token']))) > 0;
            @endphp
            @if ($hasFilters)
                <a href="{{ route('budgets.index') }}"
                   class="inline-flex items-center gap-1.5 rounded-md px-3 py-2.5 text-xs font-semibold bg-[#ED7132]/10 text-[#ED7132] border border-[#ED7132]/20 hover:bg-[#ED7132]/20 transition-colors shrink-0">
                    <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span>{{ __('budgets.reset') }}</span>
                </a>
            @endif
        </form>

        {{-- Tabel Data --}}
        <div class="overflow-x-auto w-full mt-4 rounded-lg border border-[#E5E7EB] dark:border-[#27272A]">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8FBFD] dark:bg-[#27272A]">
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] whitespace-nowrap" style="width:110px;">{{ __('budgets.voucher_no') }}</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] whitespace-nowrap" style="width:100px;">{{ __('budgets.date') }}</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] whitespace-nowrap" style="min-width:140px;">{{ __('budgets.project') }}</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] whitespace-nowrap" style="min-width:130px;">{{ __('budgets.cost_category') }}</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] whitespace-nowrap">{{ __('budgets.expense_desc') }}</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] whitespace-nowrap text-center" style="width:70px;">{{ __('budgets.proof') }}</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] whitespace-nowrap" style="width:100px;">{{ __('budgets.verification') }}</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] whitespace-nowrap text-right" style="min-width:140px;">{{ __('budgets.amount_rp') }}</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] whitespace-nowrap text-center" style="width:110px;">{{ __('budgets.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E7EB] dark:divide-[#27272A]">
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
                                'approved' => __('budgets.approved_label'),
                                'pending'  => __('budgets.pending_label'),
                                'rejected' => __('budgets.rejected_label'),
                                default    => ucfirst($budget->status ?? 'Approved'),
                            };
                        @endphp
                        <tr class="bg-white dark:bg-transparent text-sm text-[#0B2545] dark:text-slate-300 border-b border-[#E5E7EB] dark:border-[#27272A] hover:bg-[#F8FAFC] dark:hover:bg-white/5 transition-colors {{ $isOver ? 'bg-rose-50/30 dark:bg-rose-950/20' : '' }}">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-mono text-xs font-semibold px-2 py-1 rounded bg-[#F8FAFC] dark:bg-[#27272A] text-slate-700 dark:text-slate-300 border border-[#E5E7EB] dark:border-[#27272A]">
                                    {{ $budget->formatted_voucher }}
                                </span>
                            </td>
                            <td class="px-4 py-3 tabular-nums text-[13px] text-[#64748B] dark:text-[#A1A1AA] whitespace-nowrap">
                                @if($budget->date)
                                    {{ $budget->date->format('d M Y') }}
                                @else
                                    <span class="text-slate-300 dark:text-slate-600 font-mono">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <a href="{{ route('projects.show', $budget->project) }}"
                                   class="font-mono text-xs font-medium text-[#1D4E89] dark:text-[#93C5FD] hover:underline">
                                    {{ $budget->project->project_code }}
                                </a>
                                @if ($isOver)
                                    <span class="ml-1 inline-block text-xs font-medium text-amber-600" title="{{ __('budgets.over_title') }}">
                                        {{ __('budgets.over_badge') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <x-badge variant="neutral">{{ $budget->category }}</x-badge>
                            </td>
                            <td class="px-4 py-3 max-w-xs truncate">
                                <a href="{{ route('budgets.show', $budget) }}"
                                   class="text-sm font-medium text-[#0B2545] dark:text-[#F4F4F5] hover:text-[#1D4E89] dark:hover:text-[#93C5FD] hover:underline">
                                    {{ $budget->description }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                @if ($budget->receipt)
                                    <a href="{{ asset('storage/' . $budget->receipt) }}" target="_blank"
                                       class="inline-flex items-center justify-center rounded-lg p-1.5 text-[#1D4E89] dark:text-[#93C5FD] hover:bg-[#1D4E89]/10 transition-colors"
                                       title="{{ __('budgets.open_receipt_title') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                        </svg>
                                    </a>
                                @else
                                    <span class="text-slate-300 dark:text-slate-600 font-mono">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <x-badge :variant="$approvalVariant">{{ $approvalLabel }}</x-badge>
                            </td>
                            <td class="px-4 py-3 text-right tabular-nums text-sm font-semibold text-[#0B2545] dark:text-[#F4F4F5] whitespace-nowrap">
                                Rp {{ number_format($budget->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('budgets.show', $budget) }}"
                                       class="rounded-md px-2 py-1 text-xs font-semibold text-[#136F9A] dark:text-cyan-400 hover:bg-[#136F9A]/10 transition">
                                        {{ __('budgets.detail') }}
                                    </a>
                                    <a href="{{ route('budgets.edit', $budget) }}"
                                       class="rounded-md px-2 py-1 text-xs font-semibold text-[#ED7132] hover:bg-[#ED7132]/10 transition">
                                        {{ __('budgets.edit') }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="py-12 flex flex-col items-center justify-center text-center">
                                    <p class="text-[13px] text-[#7F7F7F] dark:text-slate-400 font-medium mb-4">
                                        {{ __('budgets.no_expenses') }}
                                    </p>
                                    <a href="{{ route('budgets.create') }}"
                                       class="bg-[#136F9A] text-white rounded-md px-5 py-2.5 text-[13px] font-bold hover:bg-[#136F9A]/90 transition-colors shadow-none inline-flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        <span>{{ __('budgets.create_first_expense') }}</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($budgets->isNotEmpty())
                    <tfoot>
                        <tr class="font-semibold text-xs text-[#2C3E50] dark:text-slate-300 border-t-2 border-slate-200 dark:border-[#27272A] bg-[#F8FBFD] dark:bg-[#27272A]">
                            <td colspan="7" class="py-3 px-4 text-right uppercase tracking-wider text-[11px] text-[#7F7F7F] dark:text-slate-400">
                                {{ __('budgets.total_expense_this_page') }}
                            </td>
                            <td class="py-3 px-4 text-right tabular font-bold text-[#2C3E50] dark:text-[#F4F4F5]">
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

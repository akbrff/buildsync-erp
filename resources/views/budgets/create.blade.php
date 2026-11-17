@extends('layouts.app')

@section('title', __('budgets.create_title'))

@section('breadcrumbs')
<x-breadcrumb :links="[
    ['label' => __('common.dashboard'), 'href' => route('dashboard')],
    ['label' => __('budgets.nav_breadcrumb'), 'href' => route('budgets.index')],
    ['label' => __('budgets.record_new_cost'), 'href' => '#'],
]"/>
@endsection

@section('content')
    <x-page-header
        :title="__('budgets.create_header')">
        <a href="{{ route('budgets.index', $selectedProjectId ? ['project_id' => $selectedProjectId] : []) }}"
           class="btn-secondary">
            &larr; {{ __('budgets.cancel_and_back') }}
        </a>
    </x-page-header>

    <x-flash-alert/>

    <div class="erp-card max-w-4xl p-5 sm:p-6">
        <form method="POST" action="{{ route('budgets.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="grid gap-4 sm:grid-cols-2">
                {{-- Proyek --}}
                <div class="sm:col-span-2">
                    <label for="project_id" class="erp-label">{{ __('budgets.construction_project') }} <span class="text-red-500">*</span></label>
                    <select id="project_id" name="project_id" class="erp-input @error('project_id') erp-input-error @enderror">
                        <option value="">{{ __('budgets.select_project') }}</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}" @selected(old('project_id', $selectedProjectId) == $project->id)>
                                {{ $project->project_code }} – {{ $project->name }} {{ __('budgets.project_pagu_hint', ['amount' => number_format($project->budget, 0, ',', '.')]) }}
                            </option>
                        @endforeach
                    </select>
                    @error('project_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Tanggal Pengeluaran --}}
                <div>
                    <label for="date" class="erp-label">{{ __('budgets.cash_tx_date') }} <span class="text-red-500">*</span></label>
                    <input id="date" type="date" name="date" value="{{ old('date', now()->toDateString()) }}"
                           class="erp-input tabular @error('date') erp-input-error @enderror">
                    @error('date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Kategori Biaya --}}
                <div>
                    <label for="category" class="erp-label">{{ __('budgets.cost_category') }} <span class="text-red-500">*</span></label>
                    <select id="category" name="category" class="erp-input @error('category') erp-input-error @enderror">
                        <option value="">{{ __('budgets.select_category') }}</option>
                        <option value="Material" @selected(old('category') == 'Material')>Material</option>
                        <option value="Upah Tenaga Kerja" @selected(old('category') == 'Upah Tenaga Kerja')>Upah Tenaga Kerja</option>
                        <option value="Sewa Alat" @selected(old('category') == 'Sewa Alat')>Sewa Alat</option>
                        <option value="Operasional" @selected(old('category') == 'Operasional')>Operasional</option>
                    </select>
                    @error('category')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Nominal Pengeluaran --}}
                <div>
                    <label for="amount" class="erp-label">{{ __('budgets.nominal_expense') }} <span class="text-red-500">*</span></label>
                    <x-erp-currency-input name="amount" id="amount" :value="old('amount')" required placeholder="4.000.000" />
                    @error('amount')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Status Verifikasi Kas --}}
                <div>
                    <label for="status" class="erp-label">{{ __('budgets.verification_status') }}</label>
                    <select id="status" name="status" class="erp-input @error('status') erp-input-error @enderror">
                        <option value="approved" @selected(old('status', 'approved') == 'approved')>{{ __('budgets.status_approved') }}</option>
                        <option value="pending" @selected(old('status') == 'pending')>{{ __('budgets.status_pending') }}</option>
                        <option value="rejected" @selected(old('status') == 'rejected')>{{ __('budgets.status_rejected') }}</option>
                    </select>
                    @error('status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Upload Bukti / Struk --}}
                <div class="sm:col-span-2">
                    <label for="receipt" class="erp-label">{{ __('budgets.receipt_attachment') }}</label>
                    <input id="receipt" type="file" name="receipt" accept=".jpg,.jpeg,.png,.pdf"
                           class="erp-input py-1 text-xs file:mr-3 file:py-0.5 file:px-2.5 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-slate-100 dark:file:bg-[#27272A] file:text-slate-700 dark:file:text-slate-300 hover:file:bg-slate-200 cursor-pointer">
                    <p class="mt-1 text-[11px] text-slate-400">{{ __('budgets.receipt_format_hint') }}</p>
                    @error('receipt')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Keterangan --}}
                <div class="sm:col-span-2">
                    <label for="description" class="erp-label">{{ __('budgets.desc_label') }} <span class="text-red-500">*</span></label>
                    <textarea id="description" name="description" rows="2"
                              placeholder="{{ __('budgets.desc_placeholder') }}"
                              class="w-full rounded-lg border border-[#E5E7EB] dark:border-[#27272A] bg-white dark:bg-[#18181B] p-3 text-sm text-[#0B2545] dark:text-[#F4F4F5] transition focus:border-[#1D4E89] focus:outline-none focus:ring-2 focus:ring-[#1D4E89]/20 @error('description') erp-input-error @enderror">{{ old('description') }}</textarea>
                    @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end gap-3 pt-6 border-t border-[#E5E7EB] dark:border-[#27272A]">
                <a href="{{ route('budgets.index', $selectedProjectId ? ['project_id' => $selectedProjectId] : []) }}"
                   class="btn-secondary">
                    {{ __('common.cancel') }}
                </a>
                <button type="submit"
                        class="btn-primary">
                    {{ __('budgets.save_expense') }}
                </button>
            </div>
        </form>
    </div>
@endsection

@extends('layouts.app')

@section('title', __('budgets.edit_title', ['voucher' => $budget->formatted_voucher]))

@section('breadcrumbs')
<x-breadcrumb :links="[
    ['label' => __('common.dashboard'), 'href' => route('dashboard')],
    ['label' => __('budgets.nav_breadcrumb'), 'href' => route('budgets.index', ['project_id' => $budget->project_id])],
    ['label' => $budget->formatted_voucher, 'href' => route('budgets.show', $budget)],
    ['label' => __('common.edit'), 'href' => '#'],
]"/>
@endsection

@section('content')
    <x-page-header
        :title="__('budgets.edit_header', ['voucher' => $budget->formatted_voucher])">
        <a href="{{ route('budgets.show', $budget) }}"
           class="btn-secondary">
            &larr; {{ __('budgets.back_to_detail') }}
        </a>
    </x-page-header>

    <x-flash-alert/>

    <div class="erp-card max-w-4xl p-5 sm:p-6">
        <form method="POST" action="{{ route('budgets.update', $budget) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid gap-4 sm:grid-cols-2">
                {{-- Proyek --}}
                <div class="sm:col-span-2">
                    <label for="project_id" class="erp-label">{{ __('budgets.construction_project') }} <span class="text-red-500">*</span></label>
                    <select id="project_id" name="project_id" class="erp-input @error('project_id') erp-input-error @enderror">
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}" @selected(old('project_id', $budget->project_id) == $project->id)>
                                {{ $project->project_code }} – {{ $project->name }} {{ __('budgets.project_pagu_hint', ['amount' => number_format($project->budget, 0, ',', '.')]) }}
                            </option>
                        @endforeach
                    </select>
                    @error('project_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Tanggal Pengeluaran --}}
                <div>
                    <label for="date" class="erp-label">{{ __('budgets.cash_tx_date') }} <span class="text-red-500">*</span></label>
                    <input id="date" type="date" name="date" value="{{ old('date', $budget->date?->toDateString()) }}"
                           class="erp-input tabular @error('date') erp-input-error @enderror">
                    @error('date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Kategori Biaya --}}
                <div>
                    <label for="category" class="erp-label">{{ __('budgets.cost_category') }} <span class="text-red-500">*</span></label>
                    <select id="category" name="category" class="erp-input @error('category') erp-input-error @enderror">
                        @foreach(['Material', 'Upah Tenaga Kerja', 'Sewa Alat', 'Operasional'] as $cat)
                            <option value="{{ $cat }}" @selected(old('category', $budget->category) == $cat)>{{ $cat }}</option>
                        @endforeach
                    </select>
                    @error('category')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Nominal Pengeluaran --}}
                <div>
                    <label for="amount" class="erp-label">{{ __('budgets.nominal_expense') }} <span class="text-red-500">*</span></label>
                    <x-erp-currency-input name="amount" id="amount" :value="old('amount', $budget->amount)" required placeholder="4.000.000" />
                    @error('amount')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Status Verifikasi Kas --}}
                <div>
                    <label for="status" class="erp-label">{{ __('budgets.verification_status') }}</label>
                    <select id="status" name="status" class="erp-input @error('status') erp-input-error @enderror">
                        <option value="approved" @selected(old('status', $budget->status) == 'approved')>{{ __('budgets.status_approved') }}</option>
                        <option value="pending" @selected(old('status', $budget->status) == 'pending')>{{ __('budgets.status_pending') }}</option>
                        <option value="rejected" @selected(old('status', $budget->status) == 'rejected')>{{ __('budgets.status_rejected') }}</option>
                    </select>
                    @error('status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Upload Bukti / Struk Baru --}}
                <div class="sm:col-span-2">
                    <label for="receipt" class="erp-label">{{ __('budgets.receipt_attachment') }}</label>
                    @if ($budget->receipt)
                        <div class="mb-2 flex items-center gap-2 rounded px-2.5 py-1.5 text-xs bg-[#F8FAFC] dark:bg-[#27272A] border border-[#E5E7EB] dark:border-[#27272A]">
                            <span class="text-slate-500 dark:text-slate-400">{{ __('budgets.current_file') }}</span>
                            <a href="{{ asset('storage/' . $budget->receipt) }}" target="_blank" class="font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                                {{ __('budgets.view_file') }}
                            </a>
                        </div>
                    @endif
                    <input id="receipt" type="file" name="receipt" accept=".jpg,.jpeg,.png,.pdf"
                           class="erp-input py-1 text-xs file:mr-3 file:py-0.5 file:px-2.5 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-slate-100 dark:file:bg-[#27272A] file:text-slate-700 dark:file:text-slate-300 hover:file:bg-slate-200 cursor-pointer">
                    <p class="mt-1 text-[11px] text-slate-400">{{ __('budgets.new_file_hint') }}</p>
                    @error('receipt')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Keterangan --}}
                <div class="sm:col-span-2">
                    <label for="description" class="erp-label">{{ __('budgets.desc_label') }} <span class="text-red-500">*</span></label>
                    <textarea id="description" name="description" rows="2"
                              class="w-full rounded-lg border border-[#E5E7EB] dark:border-[#27272A] bg-white dark:bg-[#18181B] p-3 text-sm text-[#0B2545] dark:text-[#F4F4F5] transition focus:border-[#1D4E89] focus:outline-none focus:ring-2 focus:ring-[#1D4E89]/20 @error('description') erp-input-error @enderror">{{ old('description', $budget->description) }}</textarea>
                    @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end gap-3 pt-6 border-t border-[#E5E7EB] dark:border-[#27272A]">
                <a href="{{ route('budgets.show', $budget) }}"
                   class="btn-secondary">
                    {{ __('common.cancel') }}
                </a>
                <button type="submit"
                        class="btn-primary">
                    {{ __('budgets.update_expense') }}
                </button>
            </div>
        </form>
    </div>
@endsection

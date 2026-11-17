@extends('layouts.app')

@section('title', __('procurement.title'))

@section('breadcrumbs')
    <x-breadcrumb :links="[
        ['label' => __('common.dashboard'), 'href' => route('dashboard')],
        ['label' => __('procurement.title'), 'href' => '#'],
    ]" />
@endsection

@section('content')
    <x-page-header
        :title="__('procurement.title')"
    />

    <x-flash-alert />

    <div class="grid gap-6 xl:grid-cols-2 mb-6">
        <!-- 1. KARTU TAMBAH SUPPLIER -->
        <section class="bg-white dark:bg-[#18181B] rounded-lg rounded-2xl border border-[#E5E7EB] dark:border-[#27272A] shadow-none p-5 mb-6">
            <h2 class="text-[15px] font-bold text-[#0B2545] dark:text-[#F4F4F5] mb-5">{{ __('procurement.add_supplier') }}</h2>

            <form method="POST" action="{{ route('suppliers.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-[#64748B] dark:text-[#A1A1AA] mb-1.5">{{ __('procurement.supplier_code') }} <span class="text-rose-500">*</span></label>
                    <input class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="supplier_code" placeholder="{{ __('procurement.supplier_code') }}" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#64748B] dark:text-[#A1A1AA] mb-1.5">{{ __('procurement.supplier_name') }} <span class="text-rose-500">*</span></label>
                    <input class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="name" placeholder="{{ __('procurement.supplier_name') }}" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#64748B] dark:text-[#A1A1AA] mb-1.5">{{ __('procurement.contact_person') }}</label>
                    <input class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="contact_person" placeholder="{{ __('procurement.contact_person') }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#64748B] dark:text-[#A1A1AA] mb-1.5">{{ __('procurement.phone') }}</label>
                    <input class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="phone" placeholder="{{ __('procurement.phone') }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#64748B] dark:text-[#A1A1AA] mb-1.5">{{ __('procurement.email') }}</label>
                    <input class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" type="email" name="email" placeholder="{{ __('procurement.email') }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#64748B] dark:text-[#A1A1AA] mb-1.5">{{ __('procurement.cooperation_status') }} <span class="text-rose-500">*</span></label>
                    <select class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="status">
                        <option value="active">{{ __('procurement.active') }}</option>
                        <option value="inactive">{{ __('procurement.inactive') }}</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-[#64748B] dark:text-[#A1A1AA] mb-1.5">{{ __('procurement.operational_address') }}</label>
                    <textarea class="w-full rounded-lg border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="address" rows="2" placeholder="{{ __('procurement.operational_address') }}"></textarea>
                </div>
                <div class="sm:col-span-2 pt-1">
                    <button type="submit" class="bg-[#136F9A] text-white rounded-lg px-5 py-2.5 text-xs font-bold hover:bg-[#136F9A]/90 transition-colors w-max">{{ __('procurement.save_supplier') }}</button>
                </div>
            </form>

            {{-- Tabel Daftar Supplier --}}
            <div class="mt-8 pt-6 border-t border-slate-100 dark:border-[#27272A]">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-[13px] font-bold text-[#2C3E50] dark:text-[#F4F4F5]">{{ __('procurement.supplier_list') }}</h3>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#F8FBFD] dark:bg-[#27272A] text-[#136F9A] dark:text-cyan-400 border border-[#EEF4F8] dark:border-[#27272A]">
                        {{ __('procurement.partners_count', ['count' => $suppliers->count()]) }}
                    </span>
                </div>
                <div class="overflow-x-auto rounded-lg border border-slate-100 dark:border-[#27272A]">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#F8FBFD] dark:bg-[#27272A]">
                                <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A]">{{ __('procurement.code') }}</th>
                                <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A]">{{ __('procurement.supplier') }}</th>
                                <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A]">{{ __('procurement.contact') }}</th>
                                <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] text-center">{{ __('procurement.po_history_col') }}</th>
                                <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A]">{{ __('procurement.status_and_update') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($suppliers as $supplier)
                                <tr class="bg-white dark:bg-transparent text-[13px] text-[#2C3E50] dark:text-slate-300 border-b border-slate-50 dark:border-[#27272A] hover:bg-[#F8FBFD]/50 dark:hover:bg-white/5 transition-colors">
                                    <td class="px-4 py-3 font-mono text-xs text-slate-500 dark:text-slate-400 whitespace-nowrap">{{ $supplier->supplier_code }}</td>
                                    <td class="px-4 py-3 font-medium text-[#2C3E50] dark:text-[#F4F4F5]">
                                        <div>{{ $supplier->name }}</div>
                                        @if($supplier->contact_person)
                                            <div class="text-[11px] text-[#64748B] dark:text-slate-400 font-normal">{{ __('procurement.pic_prefix', ['name' => $supplier->contact_person]) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-slate-600 dark:text-slate-400 text-xs whitespace-nowrap">
                                        <div>{{ $supplier->phone ?? '-' }}</div>
                                        @if($supplier->email)
                                            <div class="text-[11px] text-[#64748B] dark:text-slate-400">{{ $supplier->email }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 dark:bg-[#27272A] text-[#2C3E50] dark:text-slate-300">
                                            {{ __('procurement.po_badge', ['count' => $supplier->purchase_orders_count]) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="flex flex-wrap items-center gap-1.5">
                                            @csrf
                                            @method('PUT')
                                            <input class="w-24 rounded-lg border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-2.5 py-1.5 text-[12px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="supplier_code" value="{{ $supplier->supplier_code }}" title="{{ __('procurement.supplier_code') }}" required>
                                            <input class="w-32 rounded-lg border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-2.5 py-1.5 text-[12px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="name" value="{{ $supplier->name }}" title="{{ __('procurement.supplier_name') }}" required>
                                            <select class="rounded-lg border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-2 py-1.5 text-[12px] text-[#2C3E50] dark:text-[#F4F4F5] focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="status">
                                                <option value="active" @selected($supplier->status === 'active')>{{ __('procurement.active') }}</option>
                                                <option value="inactive" @selected($supplier->status === 'inactive')>{{ __('procurement.inactive') }}</option>
                                            </select>
                                            <input type="hidden" name="phone" value="{{ $supplier->phone }}">
                                            <input type="hidden" name="email" value="{{ $supplier->email }}">
                                            <input type="hidden" name="contact_person" value="{{ $supplier->contact_person }}">
                                            <input type="hidden" name="address" value="{{ $supplier->address }}">
                                            <button type="submit" class="rounded-lg bg-[#136F9A] px-2.5 py-1.5 text-[11px] font-bold text-white hover:bg-[#136F9A]/90 transition-colors">{{ __('common.save') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-400 text-xs">{{ __('procurement.no_suppliers') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- 2. KARTU BUAT PURCHASE ORDER -->
        <section class="bg-white dark:bg-[#18181B] rounded-lg border border-[#E5E7EB] dark:border-[#27272A] shadow-none p-5 mb-6">
            <h2 class="text-[15px] font-bold text-[#0B2545] dark:text-[#F4F4F5] mb-5">{{ __('procurement.create_po') }}</h2>

            <form method="POST" action="{{ route('purchase-orders.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#64748B] dark:text-[#A1A1AA] mb-1.5">{{ __('procurement.select_supplier') }} <span class="text-rose-500">*</span></label>
                        <select class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="supplier_id" required>
                            <option value="">{{ __('procurement.select_supplier_placeholder') }}</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @disabled($supplier->status !== 'active')>
                                    {{ $supplier->supplier_code }} - {{ $supplier->name }}{{ $supplier->status !== 'active' ? __('procurement.inactive_suffix') : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#64748B] dark:text-[#A1A1AA] mb-1.5">{{ __('procurement.construction_project') }} <span class="text-rose-500">*</span></label>
                        <select class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="project_id" required>
                            <option value="">{{ __('procurement.select_project') }}</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->project_code }} - {{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#64748B] dark:text-[#A1A1AA] mb-1.5">{{ __('procurement.order_date') }} <span class="text-rose-500">*</span></label>
                        <input class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" type="date" name="order_date" value="{{ now()->toDateString() }}" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#64748B] dark:text-[#A1A1AA] mb-1.5">{{ __('procurement.expected_delivery') }}</label>
                        <input class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" type="date" name="expected_date">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#64748B] dark:text-[#A1A1AA] mb-1.5">{{ __('procurement.items_and_prices') }} <span class="text-rose-500">*</span></label>
                    <div id="po-items" class="space-y-3">
                        <div class="grid grid-cols-12 gap-4 items-center po-item-row">
                            <div class="col-span-12 sm:col-span-6">
                                <select class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="items[0][material_id]" required>
                                    <option value="">{{ __('procurement.material_placeholder') }}</option>
                                    @foreach($materials as $material)
                                        <option value="{{ $material->id }}">{{ $material->code }} - {{ $material->name }} ({{ $material->unit }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-12 sm:col-span-2">
                                <input class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" type="number" min="1" name="items[0][quantity]" placeholder="{{ __('procurement.qty_placeholder') }}" required>
                            </div>
                            <div class="col-span-12 sm:col-span-4">
                                <input class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" type="number" min="0" step="0.01" name="items[0][unit_price]" placeholder="{{ __('procurement.unit_price_placeholder') }}" required>
                            </div>
                        </div>
                    </div>
                    <button type="button" onclick="addPoItem()" class="text-[11px] font-bold text-[#136F9A] hover:text-[#2C3E50] dark:text-cyan-400 dark:hover:text-cyan-300 cursor-pointer inline-block mt-2">{{ __('procurement.add_material_item') }}</button>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#64748B] dark:text-[#A1A1AA] mb-1.5">{{ __('procurement.po_notes') }}</label>
                    <textarea class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="notes" rows="2" placeholder="{{ __('procurement.po_notes') }}"></textarea>
                </div>

                <div class="pt-1">
                    <button type="submit" class="bg-[#136F9A] text-white rounded-md px-5 py-2.5 text-xs font-bold hover:bg-[#136F9A]/90 transition-colors w-max shadow-none">{{ __('procurement.submit_po') }}</button>
                </div>
            </form>
        </section>
    </div>

    <!-- 3. KARTU RIWAYAT PEMBELIAN & PENERIMAAN -->
    <section class="bg-white dark:bg-[#18181B] rounded-lg border border-[#E5E7EB] dark:border-[#27272A] shadow-none p-5 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
            <h2 class="text-[15px] font-bold text-[#0B2545] dark:text-[#F4F4F5]">{{ __('procurement.purchase_receiving_history') }}</h2>
            @if($orders->total() > 0)
                <span class="text-xs text-[#64748B] dark:text-slate-400">{{ __('procurement.total_orders_badge', ['count' => $orders->total()]) }}</span>
            @endif
        </div>

        <form method="GET" class="mb-5 flex flex-wrap items-center gap-3">
            <div class="w-full sm:w-72">
                <select class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="supplier_id">
                    <option value="">{{ __('procurement.all_suppliers') }}</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @selected(($supplierId ?? 0) == $supplier->id)>
                            {{ $supplier->supplier_code }} - {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-[#2C3E50] text-white rounded-md px-4 py-2.5 text-xs font-bold hover:bg-[#2C3E50]/90 transition-colors shadow-none">{{ __('procurement.filter_history') }}</button>
            @if($supplierId)
                <a href="{{ route('procurement.index') }}" class="text-xs font-semibold text-[#136F9A] hover:text-[#2C3E50] dark:text-cyan-400 dark:hover:text-cyan-300 transition-colors ml-1">{{ __('procurement.reset_filter') }}</a>
            @endif
        </form>

        <div class="overflow-x-auto rounded-lg border border-[#E5E7EB] dark:border-[#27272A]">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8FBFD] dark:bg-[#27272A]">
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A]">{{ __('procurement.po_no') }}</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A]">{{ __('procurement.date') }}</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A]">{{ __('procurement.supplier_project') }}</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A]">{{ __('procurement.value') }}</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] text-center">{{ __('procurement.status') }}</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A]">{{ __('procurement.receiving') }}</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A]">{{ __('procurement.payment') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr class="bg-white dark:bg-transparent text-[13px] text-[#2C3E50] dark:text-slate-300 border-b border-slate-50 dark:border-[#27272A] hover:bg-[#F8FBFD]/50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-4 py-3 font-medium text-[#2C3E50] dark:text-[#F4F4F5] font-mono text-xs whitespace-nowrap">{{ $order->po_number }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-400 text-xs whitespace-nowrap">{{ $order->order_date->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-[#2C3E50] dark:text-[#F4F4F5]">{{ $order->supplier->name }}</div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">{{ $order->project->project_code }} - {{ $order->project->name }}</div>
                            </td>
                            <td class="px-4 py-3 tabular font-semibold text-[#2C3E50] dark:text-[#F4F4F5] whitespace-nowrap">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <x-badge variant="{{ $order->status === 'received' ? 'success' : ($order->status === 'partial' ? 'warning' : 'neutral') }}">
                                    {{ ucfirst($order->status) }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-3">
                                @if($order->status !== 'received' && $order->status !== 'cancelled')
                                    <form method="POST" action="{{ route('purchase-orders.receive', $order) }}" class="space-y-1.5">
                                        @csrf
                                        @foreach($order->items as $item)
                                            <div>
                                                <label class="block text-[11px] text-[#2C3E50] dark:text-slate-300 font-medium leading-tight">
                                                    {{ $item->material->name }} <span class="text-slate-400">({{ $item->received_quantity }}/{{ $item->quantity }})</span>
                                                </label>
                                                <input class="w-24 rounded-lg border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-2.5 py-1 text-[12px] text-[#2C3E50] dark:text-[#F4F4F5] focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all tabular" type="number" min="0" max="{{ $item->quantity - $item->received_quantity }}" name="items[{{ $item->id }}]" value="0">
                                            </div>
                                        @endforeach
                                        <button type="submit" class="block text-[11px] font-bold text-[#136F9A] hover:text-[#2C3E50] dark:text-cyan-400 dark:hover:text-cyan-300 transition-colors mt-1">{{ __('procurement.save_receiving') }}</button>
                                    </form>
                                @else
                                    <div class="text-[11px] text-[#27B1AE] font-semibold flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        {{ __('procurement.fully_received') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @php $remaining = max(0, $order->total_amount - $order->paid_amount); @endphp
                                @if($remaining > 0)
                                    <form method="POST" action="{{ route('purchase-orders.pay', $order) }}" class="space-y-1.5">
                                        @csrf
                                        <div class="flex items-center gap-1.5">
                                            <input class="w-28 rounded-lg border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-2.5 py-1 text-[12px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all tabular" type="number" min="1" max="{{ $remaining }}" step="0.01" name="amount" placeholder="{{ __('procurement.nominal_placeholder') }}">
                                            <input class="w-32 rounded-lg border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-2.5 py-1 text-[12px] text-[#2C3E50] dark:text-[#F4F4F5] focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all tabular" type="date" name="transaction_date" value="{{ now()->toDateString() }}">
                                        </div>
                                        <div class="flex items-center justify-between gap-2 mt-1">
                                            <button type="submit" class="text-[11px] font-bold text-[#136F9A] hover:text-[#2C3E50] dark:text-cyan-400 dark:hover:text-cyan-300 transition-colors">{{ __('procurement.record_payment') }}</button>
                                            <span class="text-[11px] text-slate-500 dark:text-slate-400 tabular">{{ __('procurement.remaining_amount', ['amount' => number_format($remaining, 0, ',', '.')]) }}</span>
                                        </div>
                                    </form>
                                @else
                                    <div class="text-[11px] text-[#27B1AE] font-semibold flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        {{ __('procurement.paid_off') }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400 text-xs">
                                <p class="font-medium text-slate-500 mb-1">{{ __('procurement.no_po_title') }}</p>
                                <p class="text-slate-400">{{ __('procurement.no_po_subtitle') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </section>

    <script>
        let poItemIndex = 1;
        function addPoItem() {
            const w = document.getElementById('po-items');
            const row = w.firstElementChild.cloneNode(true);
            row.querySelectorAll('select, input').forEach(e => {
                e.name = e.name.replace(/items\[\d+\]/, 'items[' + poItemIndex + ']');
                if (e.tagName === 'SELECT') {
                    e.selectedIndex = 0;
                } else {
                    e.value = '';
                }
            });
            w.appendChild(row);
            poItemIndex++;
        }
    </script>
@endsection

@extends('layouts.app')

@section('title', __('inventory.title'))

@section('breadcrumbs')
    <x-breadcrumb :links="[
        ['label' => __('common.dashboard'), 'href' => route('dashboard')],
        ['label' => __('inventory.nav_breadcrumb'), 'href' => '#'],
    ]" />
@endsection

@section('content')
    {{-- Page Header --}}
    <x-page-header
        :title="__('inventory.title')">
        <x-slot:actions>
            <a href="{{ route('materials.create') }}"
               class="btn-primary">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('inventory.add_material') }}
            </a>
        </x-slot:actions>
    </x-page-header>

    {{-- Flash Notifications --}}
    <x-flash-alert />

    {{-- Ringkasan KPI Inventaris --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4 mb-6">
        <x-stat-card
            :label="__('inventory.total_sku')"
            :value="$totalMaterials"
            unit="SKU"
            :subtext="__('inventory.total_valuation', ['amount' => number_format($totalValuation, 0, ',', '.')])"
            subtextColor="text-slate-500"
            color="navy"
        />

        <x-stat-card
            :label="__('inventory.safe_stock')"
            :value="$safeCount"
            unit="SKU"
            :badge="__('inventory.safe')"
            badgeVariant="success"
            color="emerald"
        />

        <x-stat-card
            :label="__('inventory.approaching_reorder')"
            :value="$lowCount"
            unit="SKU"
            :badge="__('inventory.reorder')"
            badgeVariant="warning"
            color="amber"
        />

        <x-stat-card
            :label="__('inventory.critical_stock')"
            :value="$criticalCount"
            unit="SKU"
            :badge="__('inventory.critical')"
            badgeVariant="danger"
            color="rose"
        />
    </div>

    {{-- Filter Bar & Table Panel --}}
    <div class="erp-card overflow-hidden">
        {{-- Toolbar: Filter & Search --}}
        <div class="erp-card-header p-4 sm:p-5">
            <x-filter-bar
                action="{{ route('materials.index') }}"
                :searchable="true"
                searchName="q"
                :searchPlaceholder="__('inventory.search_placeholder')">

                {{-- Status Level Stok --}}
                <select name="status" class="erp-input !h-11 text-sm" style="width: auto; min-width: 170px;" onchange="this.form.submit()">
                    <option value="">{{ __('inventory.all_stock_status') }}</option>
                    <option value="aman" @selected(request('status') === 'aman')>{{ __('inventory.status_safe') }}</option>
                    <option value="menipis" @selected(request('status') === 'menipis')>{{ __('inventory.status_low') }}</option>
                    <option value="kritis" @selected(request('status') === 'kritis')>{{ __('inventory.status_critical') }}</option>
                </select>

                {{-- Filter Satuan --}}
                @if(isset($units) && $units->isNotEmpty())
                    <select name="unit" class="erp-input !h-11 text-sm" style="width: auto; min-width: 130px;" onchange="this.form.submit()">
                        <option value="">{{ __('inventory.all_units') }}</option>
                        @foreach($units as $u)
                            <option value="{{ $u }}" @selected(request('unit') === $u)>{{ $u }}</option>
                        @endforeach
                    </select>
                @endif
            </x-filter-bar>
        </div>

        {{-- Tabel Inventaris Material Berdensitas Tinggi --}}
        <div class="overflow-x-auto">
            <table class="erp-table">
                <thead class="bg-[#F8FAFC] dark:bg-[#27272A]">
                    <tr class="border-b border-[#E5E7EB] dark:border-[#27272A]">
                        <th class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] w-28">{{ __('inventory.sku_code') }}</th>
                        <th class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">{{ __('inventory.name_and_specs') }}</th>
                        <th class="text-right text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">{{ __('inventory.unit_price') }}</th>
                        <th class="text-right text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">{{ __('inventory.min_stock_short') }}</th>
                        <th class="text-right text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">{{ __('inventory.physical_stock') }}</th>
                        <th class="text-center text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] w-32">{{ __('inventory.status_level') }}</th>
                        <th class="text-right text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">{{ __('inventory.asset_valuation') }}</th>
                        <th class="text-right text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] w-44">{{ __('inventory.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E7EB] dark:divide-[#27272A]">
                    @forelse ($materials as $material)
                        @php
                            $isCritical = $material->stock <= $material->min_stock;
                            $isWarning  = !$isCritical && ($material->stock <= ($material->min_stock * 1.2));
                            $rowValuation = $material->stock * $material->price;
                        @endphp
                        <tr class="{{ $isCritical ? 'bg-red-50/40 dark:bg-red-950/20' : ($isWarning ? 'bg-amber-50/30 dark:bg-amber-950/20' : 'bg-white dark:bg-transparent') }} hover:bg-[#F8FAFC] dark:hover:bg-white/5 transition-colors">
                            {{-- Kode SKU --}}
                            <td class="font-mono text-[13px] font-medium text-[#0B2545] dark:text-[#A1A1AA] whitespace-nowrap">
                                <a href="{{ route('materials.show', $material) }}" class="hover:text-[#1D4E89] dark:hover:text-[#CFE3F3] hover:underline">
                                    {{ $material->code }}
                                </a>
                            </td>

                            {{-- Nama Material --}}
                            <td>
                                <div class="text-sm font-medium text-[#0B2545] dark:text-[#F4F4F5] leading-snug">
                                    <a href="{{ route('materials.show', $material) }}" class="hover:text-[#1D4E89] dark:hover:text-[#CFE3F3]">
                                        {{ $material->name }}
                                    </a>
                                </div>
                                <div class="text-[13px] text-[#64748B] dark:text-[#A1A1AA]">
                                    {{ __('inventory.unit_label', ['unit' => $material->unit]) }}
                                </div>
                            </td>

                            {{-- Harga Satuan --}}
                            <td class="text-right tabular-nums whitespace-nowrap">
                                <span class="text-[#0B2545] dark:text-white font-medium text-sm">Rp {{ number_format($material->price, 0, ',', '.') }}</span>
                                <span class="text-xs text-[#64748B] dark:text-slate-400">/{{ $material->unit }}</span>
                            </td>

                            {{-- Stok Min --}}
                            <td class="text-right tabular-nums text-[13px] text-[#64748B] dark:text-slate-400 whitespace-nowrap">
                                {{ number_format($material->min_stock, 0, ',', '.') }} <span class="text-xs">{{ $material->unit }}</span>
                            </td>

                            {{-- Stok Fisik --}}
                            <td class="text-right tabular-nums font-medium text-[#0B2545] dark:text-white whitespace-nowrap text-sm">
                                {{ number_format($material->stock, 0, ',', '.') }} <span class="text-xs font-normal text-[#64748B] dark:text-slate-400">{{ $material->unit }}</span>
                            </td>

                            {{-- Status Level --}}
                            <td class="text-center whitespace-nowrap">
                                @if ($isCritical)
                                    <x-badge variant="danger" dot>{{ __('inventory.status_critical') }}</x-badge>
                                @elseif ($isWarning)
                                    <x-badge variant="warning" dot>{{ __('inventory.low_stock') }}</x-badge>
                                @else
                                    <x-badge variant="success" dot>{{ __('inventory.safe_stock') }}</x-badge>
                                @endif
                            </td>

                            {{-- Valuasi Aset --}}
                            <td class="text-right tabular-nums font-semibold text-[#0B2545] dark:text-white whitespace-nowrap text-sm">
                                Rp {{ number_format($rowValuation, 0, ',', '.') }}
                            </td>

                            {{-- Aksi --}}
                            <td class="text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Quick Mutasi Trigger Button --}}
                                    <button type="button"
                                            onclick="openMutationModal('{{ $material->id }}', '{{ addslashes($material->name) }}', '{{ $material->code }}', '{{ $material->stock }}', '{{ $material->unit }}')"
                                            class="inline-flex items-center gap-1 rounded-md border border-slate-200 dark:border-[#27272A] bg-white dark:bg-[#27272A] px-2 py-1 text-[11px] font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5 transition-colors shadow-none"
                                            title="{{ __('inventory.mutate') }}">
                                        <svg class="h-3 w-3 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                        </svg>
                                        {{ __('inventory.mutate') }}
                                    </button>

                                    <a href="{{ route('materials.show', $material) }}"
                                       class="rounded p-1 text-slate-500 hover:bg-slate-100 dark:hover:bg-white/5 hover:text-slate-800 dark:hover:text-slate-200 transition-colors"
                                       title="{{ __('inventory.detail') }}">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    <a href="{{ route('materials.edit', $material) }}"
                                       class="rounded p-1 text-blue-600 hover:bg-blue-50 dark:hover:bg-white/5 transition-colors"
                                       title="{{ __('inventory.edit') }}">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>

                                    <form action="{{ route('materials.destroy', $material) }}"
                                          method="POST"
                                          onsubmit="return confirm('{{ __('inventory.delete_confirm') }}');"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="rounded p-1 text-red-500 hover:bg-red-50 dark:hover:bg-white/5 hover:text-red-700 transition-colors"
                                                title="{{ __('inventory.delete') }}">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="h-8 w-8 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <p class="text-xs font-medium text-slate-600">{{ __('inventory.no_matching_materials') }}</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">{{ __('inventory.no_matching_materials_hint') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer Pagination --}}
        @if ($materials->hasPages())
            <div class="border-t border-slate-200 px-3.5 py-2 bg-slate-50/50">
                {{ $materials->links() }}
            </div>
        @endif
    </div>

{{-- Quick Stock Mutation Modal --}}
<div id="mutationModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
    <div class="w-full max-w-md rounded-lg border border-[#E5E7EB] dark:border-[#27272A] bg-white dark:bg-[#18181B] shadow-none overflow-hidden" onclick="event.stopPropagation()">
        {{-- Modal Header --}}
        <div class="border-b border-[#E5E7EB] dark:border-[#27272A] px-4 py-3 bg-[#F4F8FB] dark:bg-[#27272A] flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-[#0B2545] dark:text-[#F4F4F5]">{{ __('inventory.record_mutation') }}</h3>
                <p id="modalMaterialDesc" class="text-xs text-[#5A738E] dark:text-[#A1A1AA] mt-0.5 font-code"></p>
            </div>
            <button type="button" onclick="closeMutationModal()" class="text-slate-400 hover:text-slate-600 rounded p-1">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Modal Form --}}
        <form id="mutationForm" method="POST" action="">
            @csrf
            <div class="p-4 space-y-3.5">
                {{-- Jenis Mutasi Radio --}}
                <div>
                    <label class="erp-label mb-1.5 block">{{ __('inventory.mutation_type') }}</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center gap-2 rounded border border-[#E5E7EB] dark:border-[#27272A] p-2.5 cursor-pointer hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                            <input type="radio" name="type" value="in" checked class="text-[#1D4E89] focus:ring-[#1D4E89]">
                            <div>
                                <span class="text-xs font-medium text-[#0B2545] dark:text-slate-200 block">{{ __('inventory.goods_in') }}</span>
                                <span class="text-xs text-[#64748B] dark:text-slate-400 block">{{ __('inventory.inbound_supplier') }}</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-2 rounded border border-[#E5E7EB] dark:border-[#27272A] p-2.5 cursor-pointer hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                            <input type="radio" name="type" value="out" class="text-amber-600 focus:ring-amber-500">
                            <div>
                                <span class="text-xs font-medium text-[#0B2545] dark:text-slate-200 block">{{ __('inventory.goods_out') }}</span>
                                <span class="text-xs text-[#64748B] dark:text-slate-400 block">{{ __('inventory.outbound_project') }}</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Kuantitas & Info Stok --}}
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="modalAmount" class="erp-label">{{ __('inventory.mutation_quantity') }}</label>
                        <span id="modalCurrentStock" class="text-xs text-slate-500 tabular"></span>
                    </div>
                    <div class="relative">
                        <input type="number" id="modalAmount" name="amount" min="1" required
                               class="erp-input pr-14 tabular font-semibold" placeholder="{{ __('inventory.quantity_example') }}">
                        <span id="modalUnitSuffix" class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-xs text-slate-400 font-medium"></span>
                    </div>
                </div>

                {{-- Keterangan / Referensi --}}
                <div>
                    <label for="modalNotes" class="erp-label mb-1 block">{{ __('inventory.notes_label') }}</label>
                    <input type="text" id="modalNotes" name="notes" class="erp-input" placeholder="{{ __('inventory.notes_placeholder') }}">
                </div>
            </div>

            {{-- Modal Actions --}}
            <div class="border-t border-[#E5E7EB] dark:border-[#27272A] bg-[#F4F8FB] dark:bg-[#27272A] px-4 py-2.5 flex items-center justify-end gap-2">
                <button type="button" onclick="closeMutationModal()" class="rounded-md border border-[#E5E7EB] dark:border-[#27272A] bg-white dark:bg-[#18181B] px-3 py-1.5 text-xs font-medium text-[#0B2545] dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                    {{ __('common.cancel') }}
                </button>
                <button type="submit" class="rounded-md bg-[#1D4E89] px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-[#153A66] transition-colors shadow-none">
                    {{ __('inventory.save_mutation') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openMutationModal(id, name, code, stock, unit) {
        const modal = document.getElementById('mutationModal');
        const form  = document.getElementById('mutationForm');
        form.action = `/materials/${id}/adjust`;

        document.getElementById('modalMaterialDesc').textContent = `[${code}] ${name}`;
        document.getElementById('modalCurrentStock').textContent = `{{ __('inventory.current_stock_prefix') }}${stock} ${unit}`;
        document.getElementById('modalUnitSuffix').textContent   = unit;
        document.getElementById('modalAmount').value             = '';
        document.getElementById('modalNotes').value              = '';

        modal.classList.remove('hidden');
    }

    function closeMutationModal() {
        document.getElementById('mutationModal').classList.add('hidden');
    }

    // Tutup modal jika klik backdrop
    document.getElementById('mutationModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeMutationModal();
        }
    });
</script>
@endsection

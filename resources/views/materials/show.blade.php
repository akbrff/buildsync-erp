@extends('layouts.app')

@section('title', __('inventory.show_title', ['code' => $material->code]))

@section('breadcrumbs')
    <x-breadcrumb :links="[
        ['label' => __('common.dashboard'), 'href' => route('dashboard')],
        ['label' => __('inventory.nav_breadcrumb'), 'href' => route('materials.index')],
        ['label' => $material->code, 'href' => '#'],
    ]" />
@endsection

@section('content')
    {{-- Page Header --}}
    <x-page-header
        :title="$material->name">
        <x-slot:badges>
            <span class="font-code rounded border border-slate-300 bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-700">
                {{ $material->code }}
            </span>
            @if ($statusLevel === 'critical')
                <x-badge variant="danger" dot>{{ __('inventory.status_critical') }}</x-badge>
            @elseif ($statusLevel === 'warning')
                <x-badge variant="warning" dot>{{ __('inventory.approaching_reorder') }}</x-badge>
            @else
                <x-badge variant="success" dot>{{ __('inventory.safe_stock') }}</x-badge>
            @endif
        </x-slot:badges>

        <x-slot:actions>
            <a href="{{ route('materials.index') }}"
               class="btn-secondary">
                &larr; {{ __('common.back') }}
            </a>
            <a href="{{ route('materials.edit', $material) }}"
               class="btn-primary">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                {{ __('inventory.edit') }}
            </a>
            <form action="{{ route('materials.destroy', $material) }}"
                  method="POST"
                  onsubmit="return confirm('{{ __('inventory.delete_confirm') }}');"
                  class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-[#DC2626]/20 bg-[#DC2626]/10 px-3.5 py-2 text-xs font-semibold text-[#DC2626] hover:bg-[#DC2626]/20 transition-colors">
                    {{ __('common.delete') }}
                </button>
            </form>
        </x-slot:actions>
    </x-page-header>

    {{-- Flash Notifications --}}
    <x-flash-alert />

    {{-- Ringkasan 4 Kartu Metrik --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4 mb-6">
        <x-stat-card
            :label="__('inventory.current_physical_stock')"
            :value="number_format($material->stock, 0, ',', '.')"
            :unit="$material->unit"
            :subtext="__('inventory.minimum_threshold', ['amount' => number_format($material->min_stock, 0, ',', '.'), 'unit' => $material->unit])"
            :color="$statusLevel === 'critical' ? 'rose' : ($statusLevel === 'warning' ? 'amber' : 'emerald')"
        />

        <x-stat-card
            :label="__('inventory.reorder_limit')"
            :value="number_format($material->min_stock, 0, ',', '.')"
            :unit="$material->unit"
            color="navy"
        />

        <x-stat-card
            :label="__('inventory.unit_price')"
            :value="'Rp ' . number_format($material->price, 0, ',', '.')"
            :unit="'/ ' . $material->unit"
            color="navy"
        />

        <x-stat-card
            :label="__('inventory.total_valuation_card')"
            :value="'Rp ' . number_format($materialValuation, 0, ',', '.')"
            color="emerald"
        />
    </div>

    {{-- Content Grid: Detail & Mutasi Cepat --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        {{-- Kolom Kiri: Spesifikasi & Meter Stok (2 Cols) --}}
        <div class="space-y-4 lg:col-span-2">
            {{-- Spesifikasi Lembar Teknis --}}
            <div class="erp-card overflow-hidden">
                <div class="erp-card-header px-5 py-3.5 flex items-center justify-between">
                    <h3 class="text-base font-bold text-[#1E293B] dark:text-white">{{ __('inventory.tech_specs') }}</h3>
                    <span class="text-xs text-[#64748B] dark:text-slate-400 font-mono">ID #{{ $material->id }}</span>
                </div>
                <div class="p-5">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-xs">
                        <div class="border-b border-[#E5E7EB] dark:border-[#27272A] pb-2.5">
                            <dt class="text-[#64748B] dark:text-[#A1A1AA] font-medium text-[13px]">{{ __('inventory.code_ref') }}</dt>
                            <dd class="mt-0.5 font-mono font-medium text-[#0B2545] dark:text-[#F4F4F5] text-sm">{{ $material->code }}</dd>
                        </div>
                        <div class="border-b border-[#E5E7EB] dark:border-[#27272A] pb-2.5">
                            <dt class="text-[#64748B] dark:text-[#A1A1AA] font-medium text-[13px]">{{ __('inventory.official_name') }}</dt>
                            <dd class="mt-0.5 font-medium text-[#0B2545] dark:text-[#F4F4F5] text-sm">{{ $material->name }}</dd>
                        </div>
                        <div class="border-b border-[#E5E7EB] dark:border-[#27272A] pb-2.5">
                            <dt class="text-[#64748B] dark:text-[#A1A1AA] font-medium text-[13px]">{{ __('inventory.standard_unit') }}</dt>
                            <dd class="mt-0.5 font-medium text-[#0B2545] dark:text-[#F4F4F5] text-sm">{{ $material->unit }}</dd>
                        </div>
                        <div class="border-b border-[#E5E7EB] dark:border-[#27272A] pb-2.5">
                            <dt class="text-[#64748B] dark:text-[#A1A1AA] font-medium text-[13px]">{{ __('inventory.unit_procurement_price') }}</dt>
                            <dd class="mt-0.5 tabular-nums font-medium text-[#0B2545] dark:text-[#F4F4F5] text-sm">Rp {{ number_format($material->price, 0, ',', '.') }} / {{ $material->unit }}</dd>
                        </div>
                        <div class="border-b border-[#E5E7EB] dark:border-[#27272A] pb-2.5">
                            <dt class="text-[#64748B] dark:text-[#A1A1AA] font-medium text-[13px]">{{ __('inventory.date_added') }}</dt>
                            <dd class="mt-0.5 text-[#64748B] dark:text-[#A1A1AA] tabular-nums text-sm">
                                @if($material->created_at)
                                    {{ $material->created_at->format('d M Y, H:i') }}
                                @else
                                    <span class="text-slate-300 dark:text-slate-600 font-mono">-</span>
                                @endif
                            </dd>
                        </div>
                        <div class="border-b border-[#E5E7EB] dark:border-[#27272A] pb-2.5">
                            <dt class="text-[#64748B] dark:text-[#A1A1AA] font-medium text-[13px]">{{ __('inventory.last_updated') }}</dt>
                            <dd class="mt-0.5 text-[#64748B] dark:text-[#A1A1AA] tabular-nums text-sm">
                                @if($material->updated_at)
                                    {{ $material->updated_at->format('d M Y, H:i') }}
                                @else
                                    <span class="text-slate-300 dark:text-slate-600 font-mono">-</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Meter Tingkat Ketersediaan Stok --}}
            <div class="erp-card p-5">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="text-base font-semibold text-[#0B2545] dark:text-white">{{ __('inventory.buffer_meter') }}</h4>
                    <span class="text-xs tabular-nums font-semibold text-[#0B2545] dark:text-[#F4F4F5]">
                        {{ $material->stock }} / {{ $material->min_stock }} {{ $material->unit }}
                    </span>
                </div>

                @php
                    $targetMax = max($material->min_stock * 2, $material->stock, 1);
                    $stockPercent = min(100, round(($material->stock / $targetMax) * 100));
                @endphp

                <div class="relative w-full h-3 rounded bg-slate-100 dark:bg-slate-800 overflow-hidden border border-slate-200 dark:border-[#27272A]">
                    <div class="h-full transition-all duration-300 {{ $statusLevel === 'critical' ? 'bg-red-500' : ($statusLevel === 'warning' ? 'bg-amber-500' : 'bg-emerald-500') }}"
                         style="width: {{ $stockPercent }}%;"></div>
                </div>

                <div class="flex items-center justify-between mt-2.5 text-[11px] text-slate-500 dark:text-slate-400">
                    <span class="flex items-center gap-1">
                        <span class="inline-block h-2 w-2 rounded-full bg-red-400"></span>
                        {{ __('inventory.critical_threshold', ['amount' => $material->min_stock, 'unit' => $material->unit]) }}
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="inline-block h-2 w-2 rounded-full bg-amber-400"></span>
                        {{ __('inventory.reorder_buffer', ['amount' => round($material->min_stock * 1.2), 'unit' => $material->unit]) }}
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
                        {{ __('inventory.safe_level') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Form Cepat Mutasi Stok Fisik (1 Col) --}}
        <div>
            <div class="erp-card overflow-hidden">
                <div class="erp-card-header px-5 py-3.5">
                    <h3 class="text-[15px] font-bold text-[#0B2545] dark:text-[#F4F4F5]">{{ __('inventory.quick_mutation') }}</h3>
                </div>

                <form method="POST" action="{{ route('materials.adjust', $material) }}" class="p-5 space-y-4">
                    @csrf

                    <div>
                        <label class="erp-label mb-1.5 block">{{ __('inventory.mutation_type') }}</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center gap-2 rounded border border-[#E5E7EB] dark:border-[#27272A] p-2.5 cursor-pointer hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                <input type="radio" name="type" value="in" checked class="text-[#1D4E89] focus:ring-[#1D4E89]">
                                <div>
                                    <span class="text-xs font-medium text-[#0B2545] dark:text-slate-200 block">{{ __('inventory.in_receipt') }}</span>
                                    <span class="text-xs text-[#64748B] dark:text-slate-400 block">{{ __('inventory.receipt') }}</span>
                                </div>
                            </label>
                            <label class="flex items-center gap-2 rounded border border-[#E5E7EB] dark:border-[#27272A] p-2.5 cursor-pointer hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                <input type="radio" name="type" value="out" class="text-amber-600 focus:ring-amber-500">
                                <div>
                                    <span class="text-xs font-medium text-[#0B2545] dark:text-slate-200 block">{{ __('inventory.out_project') }}</span>
                                    <span class="text-xs text-[#64748B] dark:text-slate-400 block">{{ __('inventory.project_usage') }}</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="amount" class="erp-label mb-1 block">{{ __('inventory.unit_quantity') }}</label>
                        <div class="relative">
                            <input type="number" id="amount" name="amount" min="1" required
                                   class="erp-input pr-14 tabular font-semibold" placeholder="{{ __('inventory.quantity_example') }}">
                            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-xs text-slate-400 font-medium">
                                {{ $material->unit }}
                            </span>
                        </div>
                        @error('amount')
                            <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="notes" class="erp-label mb-1 block">{{ __('inventory.mutation_notes_label') }}</label>
                        <textarea id="notes" name="notes" rows="2" class="erp-input" placeholder="{{ __('inventory.mutation_notes_placeholder') }}"></textarea>
                        @error('notes')
                            <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                            class="w-full rounded-md bg-[#1D4E89] px-3.5 py-2 text-xs font-semibold text-white hover:bg-[#153A66] transition-colors shadow-none">
                        {{ __('inventory.confirm_mutation') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

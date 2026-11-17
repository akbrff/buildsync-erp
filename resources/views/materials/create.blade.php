@extends('layouts.app')

@section('title', __('inventory.create_title'))

@section('breadcrumbs')
    <x-breadcrumb :links="[
        ['label' => __('common.dashboard'), 'href' => route('dashboard')],
        ['label' => __('inventory.nav_breadcrumb'), 'href' => route('materials.index')],
        ['label' => __('inventory.create_title'), 'href' => '#'],
    ]" />
@endsection

@section('content')
<div class="max-w-4xl">
    {{-- Page Header --}}
    <x-page-header
        :title="__('inventory.register_new_material')">
        <x-slot:actions>
            <a href="{{ route('materials.index') }}"
               class="btn-secondary">
                &larr; {{ __('inventory.cancel_and_back') }}
            </a>
        </x-slot:actions>
    </x-page-header>

    {{-- Flash Notifications --}}
    <x-flash-alert />

    {{-- Form Container --}}
    <div class="erp-card overflow-hidden">
        <div class="erp-card-header px-5 py-3.5">
            <h3 class="text-base font-bold text-[#1E293B] dark:text-white">{{ __('inventory.form_card_title') }}</h3>
        </div>

        <form method="POST" action="{{ route('materials.store') }}" class="p-5 sm:p-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Kode SKU --}}
                <div>
                    <label for="code" class="erp-label mb-1 block">{{ __('inventory.code_label') }} <span class="text-red-500">*</span></label>
                    <input type="text" id="code" name="code" value="{{ old('code') }}" required
                           class="erp-input font-mono uppercase" placeholder="{{ __('inventory.code_placeholder') }}">
                    <p class="mt-1 text-xs text-[#64748B] dark:text-slate-400">{{ __('inventory.code_hint') }}</p>
                    @error('code')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nama Material --}}
                <div>
                    <label for="name" class="erp-label mb-1 block">{{ __('inventory.name_label') }} <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="erp-input" placeholder="{{ __('inventory.name_placeholder') }}">
                    <p class="mt-1 text-[11px] text-slate-400">{{ __('inventory.name_hint') }}</p>
                    @error('name')
                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Satuan --}}
                <div>
                    <label for="unit" class="erp-label mb-1 block">{{ __('inventory.unit_measure') }} <span class="text-red-500">*</span></label>
                    <input type="text" id="unit" name="unit" value="{{ old('unit') }}" required list="units-list"
                           class="erp-input" placeholder="{{ __('inventory.unit_placeholder') }}">
                    <datalist id="units-list">
                        <option value="Sak">
                        <option value="Batang">
                        <option value="m3">
                        <option value="Kg">
                        <option value="Pail">
                        <option value="Lembar">
                        <option value="Roll">
                        <option value="Unit">
                    </datalist>
                    <p class="mt-1 text-[11px] text-slate-400">{{ __('inventory.unit_hint') }}</p>
                    @error('unit')
                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Harga Satuan --}}
                <div>
                    <label for="price" class="erp-label mb-1 block">{{ __('inventory.price_procurement') }} <span class="text-red-500">*</span></label>
                    <x-erp-currency-input name="price" id="price" :value="old('price')" required placeholder="68.000" />
                    <p class="mt-1 text-[11px] text-slate-400">{{ __('inventory.price_hint') }}</p>
                    @error('price')
                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Stok Awal --}}
                <div>
                    <label for="stock" class="erp-label mb-1 block">{{ __('inventory.initial_stock') }} <span class="text-red-500">*</span></label>
                    <input type="number" id="stock" name="stock" value="{{ old('stock', 0) }}" min="0" required
                           class="erp-input tabular font-medium" placeholder="0">
                    <p class="mt-1 text-[11px] text-slate-400">{{ __('inventory.initial_stock_hint') }}</p>
                    @error('stock')
                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Batas Minimal Reorder --}}
                <div>
                    <label for="min_stock" class="erp-label mb-1 block">{{ __('inventory.reorder_point') }} <span class="text-red-500">*</span></label>
                    <input type="number" id="min_stock" name="min_stock" value="{{ old('min_stock', 10) }}" min="0" required
                           class="erp-input tabular font-medium" placeholder="10">
                    <p class="mt-1 text-[11px] text-slate-400">{{ __('inventory.reorder_point_hint') }}</p>
                    @error('min_stock')
                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Form Footer Actions --}}
            <div class="mt-8 border-t border-[#E5E7EB] dark:border-[#27272A] pt-6 flex items-center justify-end gap-3">
                <a href="{{ route('materials.index') }}"
                   class="btn-secondary">
                    {{ __('common.cancel') }}
                </a>
                <button type="submit"
                        class="btn-primary">
                    {{ __('inventory.save_new_material') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

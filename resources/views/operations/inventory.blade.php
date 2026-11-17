@extends('layouts.app')

@section('title', __('inventory.stock_history'))

@section('breadcrumbs')
    <x-breadcrumb :links="[
        ['label' => __('common.dashboard'), 'href' => route('dashboard')],
        ['label' => __('inventory.stock_history'), 'href' => '#'],
    ]" />
@endsection

@section('content')
    <x-page-header
        :title="__('inventory.stock_history')"
    />

    <x-flash-alert />

    <!-- WADAH UTAMA KARTU (CARD CONTAINER) -->
    <div class="bg-white dark:bg-[#18181B] rounded-lg rounded-2xl border border-[#E5E7EB] dark:border-[#27272A] shadow-none p-5 mb-6">
        <h2 class="text-base font-bold text-[#1E293B] dark:text-[#F4F4F5] mb-1">{{ __('inventory.stock_history') }}</h2>
        <p class="text-xs text-[#64748B] dark:text-slate-400 mb-6">{{ __('inventory.audit_trail') }}</p>

        <!-- FILTER BAR -->
        <form method="GET" action="{{ route('inventory.transactions') }}" class="flex flex-col sm:flex-row gap-4 mb-6 items-center">
            <select name="material_id" class="flex-1 w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all">
                <option value="">{{ __('inventory.all_materials') }}</option>
                @foreach($materials as $m)
                    <option value="{{ $m->id }}" @selected(request('material_id') == $m->id)>{{ $m->code }} - {{ $m->name }}</option>
                @endforeach
            </select>

            <select name="type" class="flex-1 w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all">
                <option value="">{{ __('inventory.all_types') }}</option>
                <option value="in" @selected(request('type') === 'in')>{{ __('inventory.type_in') }}</option>
                <option value="out" @selected(request('type') === 'out')>{{ __('inventory.type_out') }}</option>
                <option value="adjustment" @selected(request('type') === 'adjustment')>{{ __('inventory.type_adjustment') }}</option>
            </select>

            <button type="submit" class="bg-[#2C3E50] text-white rounded-md px-6 py-2.5 text-xs font-bold hover:bg-[#2C3E50]/90 transition-colors w-full sm:w-auto shrink-0 shadow-none">
                {{ __('common.filter') }}
            </button>

            @if(request('material_id') || request('type'))
                <a href="{{ route('inventory.transactions') }}" class="text-xs font-semibold text-[#136F9A] hover:text-[#2C3E50] transition-colors shrink-0 whitespace-nowrap">
                    {{ __('inventory.reset_filter') }}
                </a>
            @endif
        </form>

        <!-- TABEL TRANSAKSI -->
        <div class="overflow-x-auto w-full rounded-lg border border-[#E5E7EB] dark:border-[#27272A]">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8FBFD] dark:bg-[#27272A]">
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] whitespace-nowrap">{{ __('inventory.time') }}</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A]">{{ __('inventory.material') }}</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] text-center">{{ __('inventory.type') }}</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A]">{{ __('inventory.quantity') }}</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A]">{{ __('inventory.balance') }}</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A]">{{ __('inventory.source_ref') }}</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A]">{{ __('inventory.project') }}</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A]">{{ __('inventory.notes') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $row)
                        <tr class="bg-white dark:bg-transparent text-[13px] text-[#2C3E50] dark:text-slate-300 border-b border-slate-50 dark:border-[#27272A] hover:bg-[#F8FBFD]/50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-400 text-xs whitespace-nowrap">{{ $row->occurred_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3 font-medium text-[#2C3E50] dark:text-[#F4F4F5]">
                                <div>{{ $row->material->name }}</div>
                                <div class="text-[11px] font-mono text-slate-500 dark:text-slate-400 font-normal">{{ $row->material->code }}</div>
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <x-badge variant="{{ $row->type === 'in' ? 'success' : ($row->type === 'out' ? 'warning' : 'neutral') }}">
                                    {{ strtoupper($row->type) }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-3 tabular font-semibold text-[#2C3E50] dark:text-[#F4F4F5] whitespace-nowrap">
                                {{ $row->type === 'in' ? '+' : ($row->type === 'out' ? '-' : '') }}{{ number_format($row->quantity) }} {{ $row->material->unit }}
                            </td>
                            <td class="px-4 py-3 tabular text-xs text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                <span class="text-slate-400 dark:text-slate-500">{{ $row->stock_before }}</span> <span class="text-slate-300 dark:text-slate-600 mx-1">→</span> <span class="font-medium text-[#2C3E50] dark:text-[#F4F4F5]">{{ $row->stock_after }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-xs font-medium text-[#2C3E50] dark:text-[#F4F4F5]">{{ ucfirst($row->source) }}</div>
                                <div class="text-[11px] font-mono text-slate-500 dark:text-slate-400">{{ $row->reference ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($row->project)
                                    <div class="font-medium text-[#2C3E50] dark:text-[#F4F4F5] text-xs">{{ $row->project->name }}</div>
                                    <div class="text-[11px] font-mono text-slate-500 dark:text-slate-400">{{ $row->project->project_code }}</div>
                                @else
                                    <span class="text-slate-400 dark:text-slate-500 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-400 text-xs">
                                {{ $row->notes ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-[13px] text-[#64748B] dark:text-slate-400 font-medium">{{ __('inventory.no_transactions') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="mt-5">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
@endsection

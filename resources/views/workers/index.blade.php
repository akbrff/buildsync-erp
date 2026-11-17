@extends('layouts.app')

@section('title', __('workforce.title'))

@section('breadcrumbs')
    <x-breadcrumb :links="[
        ['label' => __('common.dashboard'), 'href' => route('dashboard')],
        ['label' => __('workforce.nav_breadcrumb'), 'href' => '#'],
    ]" />
@endsection

@section('content')
    {{-- Page Header --}}
    <x-page-header
        :title="__('workforce.header_title')">
        <x-slot:actions>
            <a href="{{ route('workers.create') }}"
               class="btn-primary">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('workforce.add_worker') }}
            </a>
        </x-slot:actions>
    </x-page-header>

    {{-- Flash Notifications --}}
    <x-flash-alert />

    {{-- Ringkasan KPI SDM --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4 mb-6">
        <x-stat-card
            :label="__('workforce.total_registered')"
            :value="$totalWorkers"
            :unit="__('workforce.unit_person')"
            color="navy"
        />

        <x-stat-card
            :label="__('workforce.active_workers')"
            :value="$activeWorkers"
            :unit="__('workforce.unit_person')"
            :badge="__('workforce.ready_to_work')"
            badgeVariant="success"
            color="emerald"
        />

        <x-stat-card
            :label="__('workforce.standby_workers')"
            :value="$inactiveWorkers"
            :unit="__('workforce.unit_person')"
            :badge="__('workforce.standby')"
            badgeVariant="neutral"
            color="amber"
        />

        <x-stat-card
            :label="__('workforce.est_daily_wages')"
            :value="'Rp ' . number_format($totalDailyWages, 0, ',', '.')"
            color="navy"
        />
    </div>

    {{-- Filter Bar & Table Panel --}}
    <div class="rounded-lg border border-[#E5E7EB] dark:border-[#27272A] bg-white dark:bg-[#18181B] shadow-none overflow-hidden">
        {{-- Toolbar: Filter & Search --}}
        <div class="border-b border-[#E5E7EB] dark:border-[#27272A] p-4 sm:p-5 bg-[#F4F8FB] dark:bg-[#27272A]">
            <x-filter-bar
                action="{{ route('workers.index') }}"
                :searchable="true"
                searchName="q"
                :searchPlaceholder="__('workforce.search_placeholder')">

                {{-- Filter Posisi / Keahlian --}}
                <select name="position" class="erp-input !h-11 text-sm" style="width: auto; min-width: 170px;" onchange="this.form.submit()">
                    <option value="">{{ __('workforce.all_positions') }}</option>
                    @foreach ($positions as $pos)
                        <option value="{{ $pos }}" @selected(request('position') === $pos)>{{ $pos }}</option>
                    @endforeach
                </select>

                {{-- Filter Status --}}
                <select name="status" class="erp-input !h-11 text-sm" style="width: auto; min-width: 130px;" onchange="this.form.submit()">
                    <option value="">{{ __('workforce.all_statuses') }}</option>
                    <option value="active" @selected(request('status') === 'active')>{{ __('workforce.active') }}</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>{{ __('workforce.inactive') }}</option>
                </select>
            </x-filter-bar>
        </div>

        {{-- Tabel Tenaga Kerja Berdensitas Tinggi --}}
        <div class="overflow-x-auto">
            <table class="erp-table">
                <thead class="bg-[#F8FAFC] dark:bg-[#27272A]">
                    <tr class="border-b border-[#E5E7EB] dark:border-[#27272A]">
                        <th class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] w-32">{{ __('workforce.worker_code_nik') }}</th>
                        <th class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">{{ __('workforce.full_name') }}</th>
                        <th class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">{{ __('workforce.role_and_qualification') }}</th>
                        <th class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">{{ __('workforce.phone_contact') }}</th>
                        <th class="text-right text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">{{ __('workforce.daily_rate_col') }}</th>
                        <th class="text-center text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] w-28">{{ __('workforce.status') }}</th>
                        <th class="text-right text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] w-36">{{ __('workforce.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E7EB] dark:divide-[#27272A]">
                    @forelse ($workers as $worker)
                        <tr class="bg-white dark:bg-transparent border-b border-[#E5E7EB] dark:border-[#27272A] hover:bg-[#F8FAFC] dark:hover:bg-white/5 transition-colors">
                            {{-- Kode NIK --}}
                            <td class="font-mono text-[13px] font-medium text-[#0B2545] dark:text-[#A1A1AA] whitespace-nowrap">
                                <a href="{{ route('workers.show', $worker) }}" class="hover:text-[#1D4E89] dark:hover:text-[#CFE3F3] hover:underline">
                                    {{ $worker->worker_code }}
                                </a>
                            </td>

                            {{-- Nama Pekerja --}}
                            <td>
                                <div class="text-sm font-medium text-[#0B2545] dark:text-[#F4F4F5] leading-snug">
                                    <a href="{{ route('workers.show', $worker) }}" class="hover:text-[#1D4E89] dark:hover:text-[#CFE3F3]">
                                        {{ $worker->name }}
                                    </a>
                                </div>
                                @if($worker->notes)
                                    <div class="text-xs text-[#64748B] dark:text-[#A1A1AA] line-clamp-1 max-w-xs mt-0.5" title="{{ $worker->notes }}">
                                        {{ $worker->notes }}
                                    </div>
                                @endif
                            </td>

                            {{-- Jabatan --}}
                            <td class="whitespace-nowrap">
                                <span class="inline-flex items-center rounded-md border border-[#E5E7EB] dark:border-[#27272A] bg-[#F8FAFC] dark:bg-[#27272A] px-2.5 py-1 text-xs font-medium text-[#0B2545] dark:text-slate-300">
                                    {{ $worker->position }}
                                </span>
                            </td>

                            {{-- No Telepon --}}
                            <td class="tabular-nums whitespace-nowrap text-xs text-[#64748B] dark:text-slate-400">
                                @if($worker->phone)
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="h-3.5 w-3.5 text-[#64748B] dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        {{ $worker->formatted_phone ?? $worker->phone }}
                                    </span>
                                @else
                                    <span class="text-slate-300 dark:text-slate-600 font-mono">-</span>
                                @endif
                            </td>

                            {{-- Upah Harian --}}
                            <td class="text-right tabular-nums text-sm font-semibold text-[#0B2545] dark:text-white whitespace-nowrap">
                                Rp {{ number_format($worker->daily_rate, 0, ',', '.') }}
                            </td>

                            {{-- Status Badge --}}
                            <td class="text-center whitespace-nowrap">
                                @if ($worker->status === 'active')
                                    <x-badge variant="success">{{ __('workforce.active') }}</x-badge>
                                @else
                                    <x-badge variant="neutral">{{ __('workforce.inactive') }}</x-badge>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('workers.show', $worker) }}"
                                       class="rounded p-1 text-slate-500 hover:bg-slate-100 dark:hover:bg-white/5 hover:text-slate-800 dark:hover:text-slate-200 transition-colors"
                                       title="{{ __('workforce.detail') }}">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    <a href="{{ route('workers.edit', $worker) }}"
                                       class="rounded p-1 text-blue-600 hover:bg-blue-50 dark:hover:bg-white/5 transition-colors"
                                       title="{{ __('workforce.edit') }}">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>

                                    <form action="{{ route('workers.destroy', $worker) }}"
                                          method="POST"
                                          onsubmit="return confirm('{{ __('workforce.delete_confirm') }}');"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="rounded p-1 text-red-500 hover:bg-red-50 dark:hover:bg-white/5 hover:text-red-700 transition-colors"
                                                title="{{ __('workforce.delete') }}">
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
                            <td colspan="7" class="py-8 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="h-8 w-8 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <p class="text-xs font-medium text-slate-600">{{ __('workforce.no_matching_workers') }}</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">{{ __('workforce.no_matching_hint') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer Pagination --}}
        @if ($workers->hasPages())
            <div class="border-t border-slate-200 px-3.5 py-2 bg-slate-50/50">
                {{ $workers->links() }}
            </div>
        @endif
    </div>
@endsection

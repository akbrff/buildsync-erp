@extends('layouts.app')

@section('title', __('schedules.schedule_details') . ': ' . $schedule->name)

@section('breadcrumbs')
<x-breadcrumb :links="[
    ['label' => __('nav.dashboard'),     'href' => route('dashboard')],
    ['label' => __('schedules.title'),  'href' => route('schedules.index', ['project_id' => $schedule->project_id])],
    ['label' => $schedule->name,         'href' => '#'],
]"/>
@endsection

@section('content')
@php
    $remainingWeight = max(0, 100 - $usedWeight);
    $currentProgress = $latestProgress ? (float) $latestProgress->progress_percentage : 0;
    $duration = max(1, $schedule->start_date->diffInDays($schedule->end_date));

    $statusVariant = match ($schedule->status) {
        'Selesai'     => 'success',
        'Terlambat'   => 'danger',
        'Berlangsung' => 'info',
        default       => 'neutral',
    };

    $statusLabel = match ($schedule->status) {
        'Selesai'     => __('schedules.status_completed'),
        'Terlambat'   => __('schedules.status_delayed'),
        'Berlangsung' => __('schedules.status_in_progress'),
        'Belum Dimulai' => __('schedules.status_not_started'),
        default       => $schedule->status,
    };
@endphp

    {{-- ── Flash Message ──────────────────────────────────────── --}}
    <x-flash-alert/>

    {{-- ── Page Header Detail ─────────────────────────────────── --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('projects.show', $schedule->project) }}"
                   class="text-xs font-medium px-2.5 py-1 rounded-md border border-[#E5E7EB] dark:border-[#27272A] bg-[#F4F8FB] dark:bg-[#27272A] text-[#5A738E] dark:text-slate-300 transition hover:text-[#1D4E89] dark:hover:text-[#CFE3F3]">
                    {{ $schedule->project->project_code }}
                </a>
                <x-badge :variant="$statusVariant" :dot="true">
                    {{ $statusLabel }}
                </x-badge>
                <span class="text-xs text-[#64748B] dark:text-slate-400 truncate max-w-sm">
                    {{ $schedule->project->name }}
                </span>
            </div>

            <h1 class="mt-2 text-2xl font-semibold tracking-tight text-[#0B2545] dark:text-[#F4F4F5] truncate">
                {{ $schedule->name }}
            </h1>

            <p class="mt-1 text-xs text-[#64748B] dark:text-slate-400">
                {{ __('projects.target_deadline') }}:
                <span class="tabular font-medium text-[#1E293B] dark:text-slate-300">{{ $schedule->start_date->format('d M Y') }}</span>
                {{ __('schedules.to') }}
                <span class="tabular font-medium text-[#1E293B] dark:text-slate-300">{{ $schedule->end_date->format('d M Y') }}</span>
                <span class="text-[#64748B]/70 dark:text-slate-500">({{ __('schedules.calendar_days', ['days' => $duration]) }})</span>
            </p>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('schedules.index', ['project_id' => $schedule->project_id]) }}"
               class="btn-secondary">
                {!! __('schedules.back_to_list') !!}
            </a>

            <a href="{{ route('schedules.edit', $schedule) }}"
               class="btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#64748B] dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                {{ __('common.edit') }}
            </a>

            <form action="{{ route('schedules.destroy', $schedule) }}" method="POST"
                  onsubmit="return confirm('{{ __('schedules.delete_confirm_msg') }}');">
                @csrf
                @method('DELETE')
                <button type="submit"
                        title="{{ __('common.delete') }}"
                        class="inline-flex items-center justify-center rounded-md px-2.5 h-10 font-medium transition-colors bg-[#DC2626]/10 text-[#DC2626] border border-[#DC2626]/20 hover:bg-[#DC2626]/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    {{-- ── 3 Kartu Ringkasan Metrik ──────────────────────────── --}}
    <div class="grid gap-4 sm:grid-cols-3 mb-6">
        {{-- Progres Terakhir --}}
        <x-stat-card
            :label="__('schedules.latest_progress')"
            :value="number_format($currentProgress, 2, ',', '.')"
            suffix="%"
            :variant="$currentProgress >= 100 ? 'success' : ($currentProgress > 0 ? 'info' : 'neutral')"
            :note="$latestProgress ? __('schedules.recorded_on', ['date' => $latestProgress->recorded_at->format('d M Y')]) : __('schedules.no_progress_recorded')">
            <div class="mt-2 h-1.5 overflow-hidden rounded-full" style="background:#e2e8f0;">
                <div class="h-full rounded-full transition-all"
                     style="width:{{ min(100, $currentProgress) }}%; background:{{ $currentProgress >= 100 ? '#10b981' : '#2563eb' }};"></div>
            </div>
        </x-stat-card>

        {{-- Bobot Pekerjaan --}}
        <x-stat-card
            :label="__('schedules.weight_contribution')"
            :value="number_format($schedule->weight, 2, ',', '.')"
            suffix="%"
            variant="default"
            :note="__('schedules.project_total_allocation', ['weight' => number_format($usedWeight, 1, ',', '.')])"/>

        {{-- Kuota Bobot Tersisa --}}
        <x-stat-card
            :label="__('schedules.remaining_quota')"
            :value="number_format($remainingWeight, 2, ',', '.')"
            suffix="%"
            :variant="$remainingWeight == 0 ? 'success' : 'warning'"
            :note="$remainingWeight == 0 ? __('schedules.final_allocation_note') : __('schedules.available_for_other_stages')"/>
    </div>

    {{-- ── Grid 2-Kolom: Informasi & Form Rekam Progres ───────── --}}
    <div class="grid gap-5 lg:grid-cols-3 mb-6">
        {{-- Kolom Kiri: Detail Informasi --}}
        <div class="rounded-lg p-5 border border-[#E5E7EB] dark:border-[#27272A] bg-white dark:bg-[#18181B] shadow-none">
            <h3 class="text-[15px] font-semibold text-[#0B2545] dark:text-[#F4F4F5] mb-3">
                {{ __('schedules.work_information') }}
            </h3>
            <dl class="space-y-2.5 text-xs">
                <div class="flex justify-between py-1 border-b border-slate-50 dark:border-[#27272A]">
                    <dt class="text-slate-500 dark:text-[#A1A1AA]">{{ __('schedules.execution_start') }}</dt>
                    <dd class="font-medium tabular text-slate-900 dark:text-[#F4F4F5]">{{ $schedule->start_date->format('d M Y') }}</dd>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50 dark:border-[#27272A]">
                    <dt class="text-slate-500 dark:text-[#A1A1AA]">{{ __('projects.target_end_date') }}</dt>
                    <dd class="font-medium tabular text-slate-900 dark:text-[#F4F4F5]">{{ $schedule->end_date->format('d M Y') }}</dd>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50 dark:border-[#27272A]">
                    <dt class="text-slate-500 dark:text-[#A1A1AA]">{{ __('projects.duration') }}</dt>
                    <dd class="font-medium tabular text-slate-900 dark:text-[#F4F4F5]">{{ __('schedules.calendar_days', ['days' => $duration]) }}</dd>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50 dark:border-[#27272A]">
                    <dt class="text-slate-500 dark:text-[#A1A1AA]">{{ __('schedules.physical_contribution') }}</dt>
                    <dd class="font-medium tabular text-blue-700 dark:text-[#93C5FD]">
                        +{{ number_format(($schedule->weight * $currentProgress) / 100, 2, ',', '.') }}% fisik
                    </dd>
                </div>
                <div class="pt-2">
                    <dt class="text-[11px] font-medium text-slate-500 dark:text-[#A1A1AA] mb-1">{{ __('schedules.scope_and_desc') }}</dt>
                    <dd class="rounded p-2 text-slate-700 dark:text-slate-300 leading-relaxed text-xs bg-slate-50 dark:bg-[#27272A] border border-slate-100 dark:border-[#27272A]">
                        {{ $schedule->description ?: __('schedules.no_detailed_desc') }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Kolom Kanan: Form Catat Progres Baru --}}
        <div class="lg:col-span-2 rounded-lg p-5 border border-[#E5E7EB] dark:border-[#27272A] bg-white dark:bg-[#18181B] shadow-none">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-[#E5E7EB] dark:border-[#27272A]">
                <div>
                    <h3 class="text-base font-semibold text-[#0B2545] dark:text-[#F4F4F5]">
                        {{ __('schedules.record_physical_progress_update') }}
                    </h3>
                </div>
                <span class="text-xs text-[#5A738E] dark:text-[#A1A1AA]">
                    {{ __('schedules.current_progress_label', ['progress' => number_format($currentProgress, 2, ',', '.')]) }}
                </span>
            </div>

            <form action="{{ route('schedules.progress.store', $schedule) }}" method="POST">
                @csrf
                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label for="progress_percentage" class="erp-label">{{ __('schedules.new_progress_percentage') }} <span class="text-red-500">*</span></label>
                        <input id="progress_percentage" name="progress_percentage" type="number" min="0" max="100" step="0.01"
                               value="{{ old('progress_percentage') }}"
                               placeholder="Contoh: 45.5"
                               class="erp-input tabular @error('progress_percentage') erp-input-error @enderror">
                        @error('progress_percentage')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="recorded_at" class="erp-label">{{ __('schedules.record_date') }} <span class="text-red-500">*</span></label>
                        <input id="recorded_at" name="recorded_at" type="date"
                               max="{{ now()->toDateString() }}"
                               value="{{ old('recorded_at', now()->toDateString()) }}"
                               class="erp-input tabular @error('recorded_at') erp-input-error @enderror">
                        @error('recorded_at')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="notes" class="erp-label">{{ __('schedules.field_notes') }}</label>
                        <textarea id="notes" name="notes" rows="2"
                                  placeholder="{{ __('schedules.field_notes_placeholder') }}"
                                  class="w-full rounded-md border border-[#E5E7EB] dark:border-[#27272A] bg-white dark:bg-[#18181B] p-3 text-sm text-[#0B2545] dark:text-[#F4F4F5] transition focus:border-[#1D4E89] focus:outline-none focus:ring-2 focus:ring-[#1D4E89]/20 @error('notes') erp-input-error @enderror">{{ old('notes') }}</textarea>
                        @error('notes')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2 rounded-lg p-3 bg-[#F4F8FB] dark:bg-white/5 border border-[#E5E7EB] dark:border-[#27272A]">
                        <input type="hidden" name="is_correction" value="0">
                        <label class="inline-flex items-center gap-2 text-xs text-[#0B2545] dark:text-slate-300 cursor-pointer">
                            <input type="checkbox" name="is_correction" value="1" @checked(old('is_correction'))
                                   class="rounded border-[#E5E7EB] dark:border-[#27272A] text-[#1D4E89] focus:ring-[#1D4E89]">
                            <span class="font-medium">{{ __('schedules.mark_as_correction') }}</span>
                            <span class="text-[#64748B] dark:text-slate-400">{{ __('schedules.correction_required_note') }}</span>
                        </label>
                        @error('is_correction')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <button type="submit"
                            class="btn-primary">
                        {{ __('schedules.save_progress_note') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Tabel Riwayat Progres ───────────────────────────────── --}}
    <div class="overflow-hidden rounded-lg border border-[#E5E7EB] dark:border-[#27272A] bg-white dark:bg-[#18181B] shadow-none">
        <div class="px-5 py-3.5 border-b border-[#E5E7EB] dark:border-[#27272A]">
            <h3 class="text-base font-semibold text-[#0B2545] dark:text-[#F4F4F5]">
                {{ __('schedules.progress_history') }}
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="erp-table w-full text-left">
                <thead class="bg-[#F8FAFC] dark:bg-[#27272A]">
                    <tr class="border-b border-[#E5E7EB] dark:border-[#27272A]">
                        <th class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]" style="width:130px;">{{ __('schedules.record_date') }}</th>
                        <th class="text-right text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]" style="width:110px;">{{ __('schedules.physical_progress') }}</th>
                        <th class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]" style="width:110px;">{{ __('schedules.entry_type') }}</th>
                        <th class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">{{ __('common.notes') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E7EB] dark:divide-[#27272A]">
                    @forelse ($history as $entry)
                        <tr class="hover:bg-[#F8FAFC] dark:hover:bg-white/5 transition-colors">
                            <td class="tabular-nums text-[13px] text-[#64748B] dark:text-[#A1A1AA] font-medium">
                                {{ $entry->recorded_at->format('d M Y') }}
                            </td>
                            <td class="text-right tabular-nums text-sm font-semibold text-[#0B2545] dark:text-[#F4F4F5]">
                                {{ number_format($entry->progress_percentage, 2, ',', '.') }}%
                            </td>
                            <td>
                                @if ($entry->is_correction)
                                    <x-badge variant="warning">{{ __('schedules.correction') }}</x-badge>
                                @else
                                    <x-badge variant="info">{{ __('schedules.update_entry') }}</x-badge>
                                @endif
                            </td>
                            <td class="text-sm text-[#0B2545] dark:text-slate-300 whitespace-pre-line">
                                {{ $entry->notes ?: '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-10 text-center text-xs text-[#64748B] dark:text-[#A1A1AA]">
                                {{ __('schedules.no_progress_history_for_job') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($history->hasPages())
            <div class="px-6 py-4 border-t border-[#E5E7EB] dark:border-[#27272A]">
                {{ $history->links() }}
            </div>
        @endif
    </div>
@endsection

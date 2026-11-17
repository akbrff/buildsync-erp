@extends('layouts.app')

@section('title', __('projects.title'))

@section('breadcrumbs')
<x-breadcrumb :links="[
    ['label' => __('nav.dashboard'), 'href' => route('dashboard')],
    ['label' => __('nav.projects'),  'href' => route('projects.index')],
]"/>
@endsection

@section('content')

    <x-page-header
        :title="__('projects.list_title')">
        <a href="{{ route('projects.create') }}"
           class="btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('projects.add_project') }}
        </a>
    </x-page-header>

    <x-flash-alert/>

    {{-- ── Data Table ─────────────────────────────────────────── --}}
    <div class="erp-table-container">
        <div class="overflow-x-auto">
            <table class="erp-table w-full text-left dark:[&_tbody_tr:hover_td]:!bg-white/5">
                <thead class="bg-[#F8FAFC] dark:bg-[#27272A]">
                    <tr class="border-b border-[#E5E7EB] dark:border-[#27272A]">
                        <th class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">{{ __('common.code') }}</th>
                        <th class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">{{ __('projects.name') }}</th>
                        <th class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">{{ __('projects.location') }}</th>
                        <th class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">{{ __('projects.start_date') }}</th>
                        <th class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">{{ __('common.status') }}</th>
                        <th class="text-center text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA]">{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E7EB] dark:divide-[#27272A]">
                    @forelse ($projects as $project)
                        @php
                            $statusVariant = match ($project->status) {
                                'ongoing'   => 'info',
                                'completed' => 'success',
                                'planning'  => 'neutral',
                                'on_hold'   => 'warning',
                                default     => 'neutral',
                            };
                            $statusLabel = match ($project->status) {
                                'ongoing'   => __('projects.status_ongoing'),
                                'completed' => __('projects.status_completed'),
                                'planning'  => __('projects.status_planning'),
                                'on_hold'   => __('projects.status_on_hold'),
                                default     => ucfirst(str_replace('_', ' ', $project->status)),
                            };
                        @endphp
                        <tr class="bg-white dark:bg-transparent border-b border-[#E5E7EB] dark:border-[#27272A] hover:bg-[#F8FAFC] dark:hover:bg-white/5 transition-colors">
                            <td class="dark:text-[#A1A1AA]">
                                <span class="font-mono text-[13px] font-medium text-[#0B2545] dark:text-[#A1A1AA]">
                                    {{ $project->project_code }}
                                </span>
                            </td>
                            <td class="text-sm font-medium text-[#0B2545] dark:text-[#F4F4F5]">
                                <a href="{{ route('projects.show', $project) }}" class="hover:text-[#1D4E89] dark:hover:text-[#93C5FD] transition-colors">
                                    {{ $project->name }}
                                </a>
                            </td>
                            <td class="text-[13px] text-[#64748B] dark:text-[#A1A1AA]">
                                @if($project->location)
                                    {{ $project->location }}
                                @else
                                    <span class="text-slate-400 dark:text-slate-600 font-mono">-</span>
                                @endif
                            </td>
                            <td class="tabular-nums text-[13px] text-[#64748B] dark:text-[#A1A1AA]">
                                {{ $project->start_date->format('d M Y') }}
                            </td>
                            <td>
                                <x-badge :variant="$statusVariant">{{ $statusLabel }}</x-badge>
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <a href="{{ route('projects.show', $project) }}"
                                       class="text-[13px] font-medium text-[#1D4E89] dark:text-[#93C5FD] hover:underline">{{ __('common.details') }}</a>
                                    <a href="{{ route('projects.edit', $project) }}"
                                       class="text-[13px] font-medium text-[#0B2545] dark:text-[#A1A1AA] hover:underline">{{ __('common.edit') }}</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="bg-white dark:bg-transparent">
                            <td colspan="6" class="py-10 text-center text-sm text-[#64748B] dark:text-[#A1A1AA]">
                                {{ __('projects.no_projects') }}
                                <br>
                                <a href="{{ route('projects.create') }}"
                                   class="mt-1.5 inline-block text-[13px] font-medium text-[#1D4E89] dark:text-[#93C5FD] hover:underline">+ {{ __('projects.create_project') }}</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($projects->hasPages())
            <div class="px-6 py-4 border-t border-[#E5E7EB] dark:border-[#27272A]">
                {{ $projects->links() }}
            </div>
        @endif
    </div>
@endsection

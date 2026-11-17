@extends('layouts.app')

@section('title', __('schedules.edit_schedule') . ': ' . $schedule->name)

@section('breadcrumbs')
<x-breadcrumb :links="[
    ['label' => __('nav.dashboard'),     'href' => route('dashboard')],
    ['label' => __('schedules.title'),  'href' => route('schedules.index', ['project_id' => $schedule->project_id])],
    ['label' => $schedule->name,         'href' => route('schedules.show', $schedule)],
    ['label' => __('common.edit'),       'href' => '#'],
]"/>
@endsection

@section('content')
    <x-page-header
        :title="__('schedules.edit_schedule') . ': ' . $schedule->name">
        <a href="{{ route('schedules.show', $schedule) }}"
           class="btn-secondary">
            {!! __('schedules.back_to_details') !!}
        </a>
    </x-page-header>

    <x-flash-alert/>

    <div class="max-w-4xl erp-card p-5 sm:p-6">
        <form action="{{ route('schedules.update', $schedule) }}" method="POST">
            @csrf
            @method('PUT')
            @include('schedules.partials.form', ['schedule' => $schedule])

            <div class="mt-8 flex items-center justify-end gap-3 pt-6 border-t border-[#E5E7EB] dark:border-[#27272A]">
                <a href="{{ route('schedules.show', $schedule) }}"
                   class="inline-flex items-center px-3.5 py-1.5 text-[11px] font-medium rounded border border-[#E5E7EB] dark:border-[#27272A] bg-white dark:bg-[#27272A] text-[#0B2545] dark:text-[#F4F4F5] hover:bg-slate-50 dark:hover:bg-white/5 transition-colors shadow-none">
                    {{ __('common.cancel') }}
                </a>
                <button type="submit"
                        class="inline-flex items-center px-3.5 py-1.5 text-[11px] font-medium rounded bg-[#1D4E89] hover:bg-[#153A66] text-white transition-colors shadow-none cursor-pointer">
                    {{ __('schedules.update_schedule') }}
                </button>
            </div>
        </form>
    </div>
@endsection

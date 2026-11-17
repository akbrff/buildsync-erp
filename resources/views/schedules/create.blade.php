@extends('layouts.app')

@section('title', __('schedules.add_schedule'))

@section('breadcrumbs')
<x-breadcrumb :links="[
    ['label' => __('nav.dashboard'),     'href' => route('dashboard')],
    ['label' => __('schedules.title'),  'href' => route('schedules.index')],
    ['label' => __('schedules.add_schedule'), 'href' => '#'],
]"/>
@endsection

@section('content')
    <x-page-header
        :title="__('schedules.add_schedule')">
        <a href="{{ route('schedules.index', $selectedProjectId ? ['project_id' => $selectedProjectId] : []) }}"
           class="btn-secondary">
            {!! __('schedules.cancel_and_back') !!}
        </a>
    </x-page-header>

    <x-flash-alert/>

    <div class="max-w-4xl erp-card p-5 sm:p-6">
        <form action="{{ route('schedules.store') }}" method="POST">
            @csrf
            @include('schedules.partials.form', ['schedule' => null])

            <div class="mt-8 flex items-center justify-end gap-3 pt-6 border-t border-[#E5E7EB] dark:border-[#27272A]">
                <a href="{{ route('schedules.index', $selectedProjectId ? ['project_id' => $selectedProjectId] : []) }}"
                   class="inline-flex items-center px-3.5 py-1.5 text-[11px] font-medium rounded border border-[#E5E7EB] dark:border-[#27272A] bg-white dark:bg-[#27272A] text-[#0B2545] dark:text-[#F4F4F5] hover:bg-slate-50 dark:hover:bg-white/5 transition-colors shadow-none">
                    {{ __('common.cancel') }}
                </a>
                <button type="submit"
                        class="inline-flex items-center px-3.5 py-1.5 text-[11px] font-medium rounded bg-[#1D4E89] hover:bg-[#153A66] text-white transition-colors shadow-none cursor-pointer">
                    {{ __('schedules.save_schedule') }}
                </button>
            </div>
        </form>
    </div>
@endsection

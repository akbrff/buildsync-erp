@extends('layouts.app')

@section('title', __('workforce.edit_title', ['name' => $worker->name]))

@section('breadcrumbs')
    <x-breadcrumb :links="[
        ['label' => __('common.dashboard'), 'href' => route('dashboard')],
        ['label' => __('workforce.nav_breadcrumb'), 'href' => route('workers.index')],
        ['label' => $worker->worker_code, 'href' => route('workers.show', $worker)],
        ['label' => __('common.edit'), 'href' => '#'],
    ]" />
@endsection

@section('content')
<div class="max-w-4xl">
    {{-- Page Header --}}
    <x-page-header
        :title="__('workforce.edit_header')">
        <x-slot:badges>
            <x-badge variant="neutral">{{ $worker->worker_code }}</x-badge>
        </x-slot:badges>
        <x-slot:actions>
            <a href="{{ route('workers.show', $worker) }}"
               class="btn-secondary">
                &larr; {{ __('workforce.cancel_and_back') }}
            </a>
        </x-slot:actions>
    </x-page-header>

    {{-- Flash Notifications --}}
    <x-flash-alert />

    {{-- Form Container --}}
    <div class="rounded-lg border border-[#E5E7EB] dark:border-[#27272A] bg-white dark:bg-[#18181B] shadow-none overflow-hidden">
        <div class="border-b border-[#E5E7EB] dark:border-[#27272A] px-5 py-3.5 bg-[#F4F8FB] dark:bg-[#27272A] flex items-center justify-between">
            <h3 class="text-base font-bold text-[#1E293B] dark:text-white">{{ __('workforce.edit_card_title') }}</h3>
            <span class="text-xs text-[#64748B] dark:text-slate-400 font-mono">ID #{{ $worker->id }}</span>
        </div>

        <form action="{{ route('workers.update', $worker) }}" method="POST" class="p-5 sm:p-6">
            @csrf
            @method('PUT')

            @include('workers.partials.form', ['worker' => $worker])

            {{-- Form Footer Actions --}}
            <div class="mt-8 border-t border-[#E5E7EB] dark:border-[#27272A] pt-6 flex items-center justify-end gap-3">
                <a href="{{ route('workers.show', $worker) }}"
                   class="btn-secondary">
                    {{ __('common.cancel') }}
                </a>
                <button type="submit"
                        class="btn-primary">
                    {{ __('workforce.save_changes') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

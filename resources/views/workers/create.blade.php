@extends('layouts.app')

@section('title', __('workforce.create_title'))

@section('breadcrumbs')
    <x-breadcrumb :links="[
        ['label' => __('common.dashboard'), 'href' => route('dashboard')],
        ['label' => __('workforce.nav_breadcrumb'), 'href' => route('workers.index')],
        ['label' => __('workforce.create_title'), 'href' => '#'],
    ]" />
@endsection

@section('content')
<div class="max-w-4xl">
    {{-- Page Header --}}
    <x-page-header
        :title="__('workforce.register_new')">
        <x-slot:actions>
            <a href="{{ route('workers.index') }}"
               class="btn-secondary">
                &larr; {{ __('workforce.cancel_and_back') }}
            </a>
        </x-slot:actions>
    </x-page-header>

    {{-- Flash Notifications --}}
    <x-flash-alert />

    {{-- Form Container --}}
    <div class="rounded-lg border border-[#E5E7EB] dark:border-[#27272A] bg-white dark:bg-[#18181B] shadow-none overflow-hidden">
        <div class="border-b border-[#E5E7EB] dark:border-[#27272A] px-5 py-3.5 bg-[#F4F8FB] dark:bg-[#27272A]">
            <h3 class="text-base font-bold text-[#1E293B] dark:text-white">{{ __('workforce.form_card_title') }}</h3>
        </div>

        <form action="{{ route('workers.store') }}" method="POST" class="p-5 sm:p-6">
            @csrf

            @include('workers.partials.form', ['worker' => null])

            {{-- Form Footer Actions --}}
            <div class="mt-8 border-t border-[#E5E7EB] dark:border-[#27272A] pt-6 flex items-center justify-end gap-3">
                <a href="{{ route('workers.index') }}"
                   class="btn-secondary">
                    {{ __('common.cancel') }}
                </a>
                <button type="submit"
                        class="btn-primary">
                    {{ __('workforce.save_worker') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

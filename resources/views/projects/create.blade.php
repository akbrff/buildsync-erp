@extends('layouts.app')

@section('title', __('projects.create_project'))

@section('breadcrumbs')
<x-breadcrumb :links="[
    ['label' => __('nav.dashboard'), 'href' => route('dashboard')],
    ['label' => __('nav.projects'),  'href' => route('projects.index')],
    ['label' => __('projects.create_project'), 'href' => '#'],
]"/>
@endsection

@section('content')
    <x-page-header
        :title="__('projects.create_project')">
        <a href="{{ route('projects.index') }}"
           class="btn-secondary">
            &larr; {{ __('common.back') }}
        </a>
    </x-page-header>

    <x-flash-alert/>

    <div class="erp-card max-w-4xl p-5 sm:p-6">
        <form action="{{ route('projects.store') }}" method="POST">
            @csrf
            @include('projects.partials.form', ['project' => null])

            <div class="mt-8 flex items-center justify-end gap-3 pt-6 border-t border-[#E5E7EB] dark:border-[#27272A]">
                <a href="{{ route('projects.index') }}"
                   class="btn-secondary">
                    {{ __('common.cancel') }}
                </a>
                <button type="submit"
                        class="btn-primary">
                    {{ __('common.save') }}
                </button>
            </div>
        </form>
    </div>
@endsection

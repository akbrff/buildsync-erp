@extends('layouts.app')

@section('title', __('settings.title'))

@section('breadcrumbs')
    <x-breadcrumb :links="[
        ['label' => __('nav.dashboard'), 'href' => route('dashboard')],
        ['label' => __('settings.title'), 'href' => '#'],
    ]" />
@endsection

@section('content')
    <div class="mt-2 mb-6">
        <h1 class="text-xl font-bold tracking-tight text-[#0B2545] dark:text-[#F4F4F5]">
            {{ __('settings.title') }}
        </h1>
        <p class="text-xs text-[#64748B] dark:text-[#A1A1AA] mt-0.5">
            {{ __('settings.subtitle') }}
        </p>
    </div>

    <x-flash-alert />

    <div class="max-w-3xl space-y-6">
        {{-- Language Preference Card --}}
        <div class="bg-white dark:bg-[#18181B] rounded-2xl border border-[#E5E7EB] dark:border-[#27272A] shadow-none overflow-hidden">
            <div class="px-6 py-4 border-b border-[#E5E7EB] dark:border-[#27272A] flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-[#1D4E89]/10 dark:bg-[#1D4E89]/20 text-[#1D4E89] dark:text-[#93C5FD] flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-[#0B2545] dark:text-[#F4F4F5]">
                            {{ __('settings.language_section_title') }}
                        </h2>
                        <p class="text-[11px] text-[#64748B] dark:text-[#A1A1AA]">
                            {{ __('settings.language_section_desc') }}
                        </p>
                    </div>
                </div>
            </div>

            <form action="{{ route('settings.language.update') }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <label class="erp-label">
                        {{ __('settings.language_label') }}
                    </label>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        {{-- English Option --}}
                        <label class="relative flex items-center p-4 border rounded-xl cursor-pointer transition-all {{ $currentLocale === 'en' ? 'border-[#1D4E89] bg-[#1D4E89]/5 dark:border-[#93C5FD] dark:bg-[#1D4E89]/15' : 'border-[#E5E7EB] dark:border-[#27272A] hover:bg-slate-50 dark:hover:bg-white/5' }}">
                            <input type="radio" name="locale" value="en" class="text-[#1D4E89] focus:ring-[#1D4E89]" @checked($currentLocale === 'en')>
                            <div class="ml-3">
                                <span class="block text-sm font-semibold text-[#0B2545] dark:text-[#F4F4F5]">
                                    🇺🇸 English
                                </span>
                                <span class="block text-xs text-[#64748B] dark:text-[#A1A1AA]">
                                    {{ __('settings.english_desc') }}
                                </span>
                            </div>
                        </label>

                        {{-- Indonesian Option --}}
                        <label class="relative flex items-center p-4 border rounded-xl cursor-pointer transition-all {{ $currentLocale === 'id' ? 'border-[#1D4E89] bg-[#1D4E89]/5 dark:border-[#93C5FD] dark:bg-[#1D4E89]/15' : 'border-[#E5E7EB] dark:border-[#27272A] hover:bg-slate-50 dark:hover:bg-white/5' }}">
                            <input type="radio" name="locale" value="id" class="text-[#1D4E89] focus:ring-[#1D4E89]" @checked($currentLocale === 'id')>
                            <div class="ml-3">
                                <span class="block text-sm font-semibold text-[#0B2545] dark:text-[#F4F4F5]">
                                    🇮🇩 Bahasa Indonesia
                                </span>
                                <span class="block text-xs text-[#64748B] dark:text-[#A1A1AA]">
                                    {{ __('settings.indonesian_desc') }}
                                </span>
                            </div>
                        </label>
                    </div>

                    <p class="text-xs text-[#64748B] dark:text-[#A1A1AA] pt-1">
                        {{ __('settings.language_help') }}
                    </p>
                </div>

                <div class="mt-6 pt-4 border-t border-[#E5E7EB] dark:border-[#27272A] flex justify-end">
                    <button type="submit" class="px-4 py-2 text-xs font-medium text-white bg-[#1D4E89] hover:bg-[#153A66] rounded-md transition-colors shadow-none cursor-pointer">
                        {{ __('settings.save_preferences') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

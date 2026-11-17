@props([
    'action'            => '',
    'method'            => 'GET',
    'searchable'        => false,
    'searchName'        => 'q',
    'searchPlaceholder' => null,
])
{{--
    Komponen Filter Bar — form GET untuk menyaring data tabel.
    Props:
      $action     → URL target form (required)
      $method     → GET (default, karena ini filter URL)
      $searchable → boolean, tampilkan input search text (default: false)
      $searchName → name attribute input search (default: 'q')
      $searchPlaceholder → placeholder input search
    Slot: isi dengan select/input filter tambahan
--}}
@php
    $currentQuery = request()->except($searchName);
    $hasFilters   = count(array_filter(request()->except(['page', '_token']))) > 0;
    $placeholder  = $searchPlaceholder ?? __('common.search_placeholder');
@endphp

<form method="{{ strtoupper($method) }}"
      action="{{ $action }}"
      class="flex flex-wrap items-center gap-2.5">

    {{-- Search input (opsional) --}}
    @if ($searchable)
        <div class="relative">
            <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-4.35-4.35m0 0A7 7 0 1 0 6.65 6.65a7 7 0 0 0 9.9 9.9z"/>
            </svg>
            <input type="text"
                   name="{{ $searchName }}"
                   value="{{ request($searchName) }}"
                   placeholder="{{ $placeholder }}"
                   class="erp-input !pl-10 !h-11 rounded-md text-sm"
                   style="width:240px; min-width:160px;">
        </div>
    @endif

    {{-- Filter slot (selects, dropdowns, date range, dsb.) --}}
    {{ $slot }}

    {{-- Tombol Apply --}}
    @if (!$searchable && $slot->isEmpty())
    @else
        <button type="submit"
                class="flex items-center justify-center gap-2 !px-4 py-1.5 text-[11px] font-medium rounded border border-[#E5E7EB] dark:border-[#27272A] bg-white dark:bg-[#27272A] text-[#0B2545] dark:text-[#F4F4F5] hover:bg-slate-50 dark:hover:bg-white/5 transition-colors cursor-pointer shadow-none">
            <svg class="h-3 w-3 text-[#64748B] dark:text-[#A1A1AA] shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            <span>{{ __('common.apply') }}</span>
        </button>
    @endif

    {{-- Tombol Reset Filter --}}
    @if ($hasFilters)
        <a href="{{ $action }}"
           class="flex items-center justify-center gap-2 !px-4 py-1.5 text-[11px] font-medium rounded bg-[#DC2626]/10 text-[#DC2626] border border-[#DC2626]/20 hover:bg-[#DC2626]/20 transition-colors shadow-none">
            <svg class="h-3 w-3 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <span>{{ __('common.reset') }}</span>
        </a>
    @endif
</form>

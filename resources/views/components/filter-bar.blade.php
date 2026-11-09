@props([
    'action'            => '',
    'method'            => 'GET',
    'searchable'        => false,
    'searchName'        => 'q',
    'searchPlaceholder' => 'Cari...',
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
@endphp

<form method="{{ strtoupper($method) }}"
      action="{{ $action }}"
      class="flex flex-wrap items-center gap-2.5">

    {{-- Search input (opsional) --}}
    @if ($searchable)
        <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="h-3.5 w-3.5 text-[#7F7F7F]" xmlns="http://www.w3.org/2000/svg"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-4.35-4.35m0 0A7 7 0 1 0 6.65 6.65a7 7 0 0 0 9.9 9.9z"/>
                </svg>
            </div>
            <input type="text"
                   name="{{ $searchName }}"
                   value="{{ request($searchName) }}"
                   placeholder="{{ $searchPlaceholder }}"
                   class="erp-input pl-9 rounded-lg"
                   style="width:220px; min-width:160px;">
        </div>
    @endif

    {{-- Filter slot (selects, dropdowns, date range, dsb.) --}}
    {{ $slot }}

    {{-- Tombol Apply --}}
    @if (!$searchable && $slot->isEmpty())
    @else
        <button type="submit"
                class="btn-secondary h-9">
            <svg class="h-3.5 w-3.5 text-[#7F7F7F]" xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            Terapkan
        </button>
    @endif

    {{-- Tombol Reset Filter --}}
    @if ($hasFilters)
        <a href="{{ $action }}"
           class="inline-flex items-center gap-1.5 rounded-lg px-3 h-9 text-xs font-semibold bg-[#ED7132]/15 text-[#ED7132] border border-[#ED7132]/25 hover:bg-[#ED7132]/25 transition-colors">
            <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Reset
        </a>
    @endif
</form>

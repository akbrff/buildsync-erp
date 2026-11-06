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
      class="flex flex-wrap items-center gap-2">

    {{-- Search input (opsional) --}}
    @if ($searchable)
        <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5">
                <svg class="h-3.5 w-3.5" style="color:#94a3b8;" xmlns="http://www.w3.org/2000/svg"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-4.35-4.35m0 0A7 7 0 1 0 6.65 6.65a7 7 0 0 0 9.9 9.9z"/>
                </svg>
            </div>
            <input type="text"
                   name="{{ $searchName }}"
                   value="{{ request($searchName) }}"
                   placeholder="{{ $searchPlaceholder }}"
                   class="erp-input pl-8"
                   style="width:200px; min-width:160px;">
        </div>
    @endif

    {{-- Filter slot (selects, dropdowns, date range, dsb.) --}}
    {{ $slot }}

    {{-- Tombol Apply (hanya muncul jika filter TIDAK auto-submit) --}}
    @if (!$searchable && $slot->isEmpty())
    @else
        <button type="submit"
                class="flex items-center gap-1.5 rounded px-3 font-medium transition-colors"
                style="height:2.25rem; font-size:0.8125rem; border:1px solid #e2e8f0;
                       background:#fff; color:#475569;"
                onmouseover="this.style.background='#f8fafc';"
                onmouseout="this.style.background='#fff';">
            <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none"
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
           class="flex items-center gap-1.5 rounded px-3 font-medium transition-colors"
           style="height:2.25rem; font-size:0.8125rem; color:#b91c1c; border:1px solid #fecaca;
                  background:#fef2f2;"
           onmouseover="this.style.background='#fee2e2';"
           onmouseout="this.style.background='#fef2f2';">
            <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Reset
        </a>
    @endif
</form>

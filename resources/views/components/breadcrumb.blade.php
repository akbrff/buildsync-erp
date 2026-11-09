@props([
    /**
     * $links atau $items → array of breadcrumb items:
     *   [ ['label' => 'Dashboard', 'href' => '/dashboard'], ... ]
     *   atau [ ['label' => 'Dashboard', 'url' => '/dashboard'], ... ]
     * Item terakhir (atau tanpa href/url) dianggap sebagai "current page".
     */
    'links' => null,
    'items' => null,
])

@php
    $breadcrumbList = $links ?? $items ?? [];
@endphp

@if (is_array($breadcrumbList) && count($breadcrumbList) > 0)
<nav class="flex items-center gap-1.5" aria-label="Breadcrumb">
    @foreach ($breadcrumbList as $index => $item)
        @php
            $isLast = ($index === count($breadcrumbList) - 1);
            $targetUrl = $item['href'] ?? $item['url'] ?? null;
        @endphp

        @if (!$isLast && $targetUrl && $targetUrl !== '#')
            {{-- Ancestor link --}}
            <a href="{{ $targetUrl }}"
               class="text-xs font-medium text-[#136F9A] transition-colors hover:underline">
                {{ $item['label'] }}
            </a>
            {{-- Separator --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 shrink-0 text-[#7F7F7F]"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        @else
            {{-- Current page (tidak bisa diklik) --}}
            <span class="text-xs font-semibold truncate max-w-[220px] text-[#2C3E50]"
                  aria-current="page">
                {{ $item['label'] }}
            </span>
            @if (!$isLast)
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 shrink-0 text-[#7F7F7F]"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            @endif
        @endif
    @endforeach
</nav>
@endif

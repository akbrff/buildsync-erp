@props([
    /**
     * $links → array of breadcrumb items:
     *   [ ['label' => 'Dashboard', 'href' => '/dashboard'], ... ]
     * Item terakhir dianggap sebagai "current page" (tidak diklik).
     */
    'links' => [],
])

@if (count($links) > 0)
<nav class="flex items-center gap-1" aria-label="Breadcrumb">
    @foreach ($links as $index => $link)
        @php $isLast = ($index === count($links) - 1); @endphp

        @if (!$isLast)
            {{-- Ancestor link --}}
            <a href="{{ $link['href'] }}"
               class="text-xs font-medium transition-colors hover:underline"
               style="color:#2563eb;">
                {{ $link['label'] }}
            </a>
            {{-- Separator --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 shrink-0"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor"
                 style="color:#cbd5e1;">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        @else
            {{-- Current page (tidak bisa diklik) --}}
            <span class="text-xs font-semibold truncate max-w-[180px]"
                  style="color:#0f172a;"
                  aria-current="page">
                {{ $link['label'] }}
            </span>
        @endif
    @endforeach
</nav>
@endif

@props([
    /**
     * Komponen PageHeader — header terstandar setiap halaman:
     *   judul, sub-teks, badges, dan slot kanan (tombol aksi).
     */
    'title'    => '',
    'subtitle' => null,
    'badges'   => null,
    'actions'  => null,
])

<div {{ $attributes->merge(['class' => 'mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between']) }}>
    @php
        $hasBadges = !empty($badges) && (is_string($badges) ? trim($badges) !== '' : $badges->isNotEmpty());
        $hasActions = !empty($actions) && (is_string($actions) ? trim($actions) !== '' : $actions->isNotEmpty());
        $hasSlot = isset($slot) && $slot->isNotEmpty();
    @endphp
    <div class="min-w-0 flex-1">
        @if ($hasBadges)
            <div class="mb-2 flex flex-wrap items-center gap-2">
                {{ $badges }}
            </div>
        @endif
        <h2 class="page-title">{{ $title }}</h2>
        @if ($subtitle)
            <p class="page-subtitle">{{ $subtitle }}</p>
        @endif
    </div>
    @if ($hasActions || $hasSlot)
        <div class="flex shrink-0 items-center gap-3">
            {{ $hasActions ? $actions : $slot }}
        </div>
    @endif
</div>

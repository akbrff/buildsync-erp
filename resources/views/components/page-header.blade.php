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
    <div class="min-w-0">
        @if (!empty($badges) && $badges->isNotEmpty())
            <div class="mb-1.5 flex flex-wrap items-center gap-2">
                {{ $badges }}
            </div>
        @endif
        <h2 class="text-lg font-semibold tracking-tight text-slate-900 truncate">{{ $title }}</h2>
        @if ($subtitle)
            <p class="mt-0.5 text-xs text-slate-500">{{ $subtitle }}</p>
        @endif
    </div>
    @if ((!empty($actions) && $actions->isNotEmpty()) || $slot->isNotEmpty())
        <div class="flex shrink-0 items-center gap-2">
            {{ !empty($actions) && $actions->isNotEmpty() ? $actions : $slot }}
        </div>
    @endif
</div>

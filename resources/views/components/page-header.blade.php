@props([
    /**
     * Komponen PageHeader — header terstandar setiap halaman:
     *   judul, sub-teks, dan slot kanan (tombol aksi).
     */
    'title'    => '',
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between']) }}>
    <div class="min-w-0">
        <h2 class="text-base font-semibold leading-tight truncate"
            style="color:#0f172a;">{{ $title }}</h2>
        @if ($subtitle)
            <p class="mt-0.5 text-xs" style="color:#64748b;">{{ $subtitle }}</p>
        @endif
    </div>
    @if (!empty($actions) || $slot->isNotEmpty())
        <div class="flex shrink-0 items-center gap-2">
            {{ $slot }}
        </div>
    @endif
</div>

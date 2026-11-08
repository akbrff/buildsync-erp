@props([
    'label'        => '',       // Label di atas (sub-teks deskriptor)
    'value'        => '',       // Nilai utama (angka / teks besar)
    'note'         => null,     // Teks kecil / keterangan di bawah value
    'subtext'      => null,     // Alias untuk note
    'subtextColor' => null,
    'variant'      => 'default', // default | success | warning | danger | info | neutral
    'color'        => null,     // Alias untuk warna varian
    'icon'         => null,     // SVG path string opsional
    'suffix'       => null,     // Satuan / unit di kanan value (misal: "%" atau "proyek")
    'unit'         => null,     // Alias untuk suffix
    'badge'        => null,     // Opsional badge teks
    'badgeVariant' => 'neutral',
])

@php
    $displaySuffix = $suffix ?? $unit;
    $displayNote   = $note ?? $subtext;
@endphp

<div {{ $attributes->merge(['class' => 'rounded-md border border-slate-200 bg-white']) }}>
    <div class="p-5">
        {{-- Label (descriptor) & Optional Badge --}}
        <div class="flex items-center justify-between gap-1 mb-1">
            <p class="text-[11px] font-medium uppercase tracking-wider text-slate-500 truncate">{{ $label }}</p>
            @if ($badge)
                <x-badge :variant="$badgeVariant" size="xs" :dot="true">{{ $badge }}</x-badge>
            @endif
        </div>

        {{-- Value row --}}
        <div class="mt-1 flex items-baseline gap-1.5">
            @if ($icon)
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-slate-400" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    {!! $icon !!}
                </svg>
            @endif
            <span class="text-2xl font-bold tracking-tight text-slate-900 tabular">
                {{ $value }}
            </span>
            @if ($displaySuffix)
                <span class="text-xs font-medium text-slate-500">{{ $displaySuffix }}</span>
            @endif
        </div>

        {{-- Note / sub info --}}
        @if ($displayNote)
            <p class="mt-2 mb-1 truncate text-[11px] text-slate-500">{{ $displayNote }}</p>
        @endif

        {{-- Slot untuk konten tambahan (progress bar, dll) --}}
        @if ($slot->isNotEmpty())
            <div class="mt-1.5">{{ $slot }}</div>
        @endif
    </div>
</div>

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

    $noteColor = $subtextColor ?? match ($variant) {
        'success'           => 'text-[#27B1AE]',
        'warning', 'danger' => 'text-[#ED7132]',
        'info'              => 'text-[#136F9A]',
        default             => 'text-[#7F7F7F]',
    };
@endphp

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-100 bg-white p-5 shadow-[0_2px_12px_rgba(44,62,80,0.04)]']) }}>
    {{-- Label (descriptor) & Optional Badge --}}
    <div class="flex items-center justify-between gap-1 mb-2">
        <p class="text-xs font-semibold text-[#7F7F7F] uppercase tracking-wider truncate">{{ $label }}</p>
        @if ($badge)
            <x-badge :variant="$badgeVariant" size="xs" :dot="true">{{ $badge }}</x-badge>
        @endif
    </div>

    {{-- Value row --}}
    <div class="flex items-baseline gap-1.5">
        @if ($icon)
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-[#7F7F7F]" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                {!! $icon !!}
            </svg>
        @endif
        <span class="text-2xl font-extrabold text-[#2C3E50] tabular-nums tracking-tight">
            {{ $value }}
        </span>
        @if ($displaySuffix)
            <span class="text-xs font-medium text-[#7F7F7F]">{{ $displaySuffix }}</span>
        @endif
    </div>

    {{-- Note / sub info --}}
    @if ($displayNote)
        <p class="mt-2.5 truncate text-[11px] font-semibold {{ $noteColor }}">
            {{ $displayNote }}
        </p>
    @endif

    {{-- Slot untuk konten tambahan (progress bar, dll) --}}
    @if ($slot->isNotEmpty())
        <div class="mt-2">{{ $slot }}</div>
    @endif
</div>

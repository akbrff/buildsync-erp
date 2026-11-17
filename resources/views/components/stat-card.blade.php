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
    'plain'        => false,    // Tampilan terbuka / minimalis tanpa boks kartu
])

@php
    $displaySuffix = $suffix ?? $unit;
    $displayNote   = $note ?? $subtext;

    $noteColor = $subtextColor ?? match ($variant) {
        'success'           => 'text-[#16A34A] dark:text-emerald-400',
        'warning'           => 'text-[#D97706] dark:text-amber-400',
        'danger'            => 'text-[#DC2626] dark:text-rose-400',
        'info'              => 'text-[#1D4E89] dark:text-[#93C5FD]',
        default             => 'text-[#64748B] dark:text-[#A1A1AA]',
    };

    $containerClass = $plain
        ? 'bg-transparent border-0 p-2 sm:p-3 shadow-none'
        : 'erp-card p-5';
@endphp

<div {{ $attributes->merge(['class' => $containerClass]) }}>
    {{-- Label (descriptor) & Optional Badge --}}
    <div class="flex items-center justify-between gap-1 mb-2">
        <p class="text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] truncate">{{ $label }}</p>
        @if ($badge)
            <x-badge :variant="$badgeVariant">{{ $badge }}</x-badge>
        @endif
    </div>

    {{-- Value row --}}
    <div class="flex items-baseline gap-2">
        @if ($icon)
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-[#64748B] dark:text-[#A1A1AA]" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                {!! $icon !!}
            </svg>
        @endif
        <span class="text-2xl lg:text-[28px] leading-tight font-semibold text-[#0B2545] dark:text-[#F4F4F5] tabular-nums tracking-tight">
            {{ $value }}
        </span>
        @if ($displaySuffix)
            <span class="text-sm font-normal text-[#64748B] dark:text-[#A1A1AA]">{{ $displaySuffix }}</span>
        @endif
    </div>

    {{-- Note / sub info --}}
    @if ($displayNote)
        <p class="mt-2 truncate text-[13px] font-normal {{ $noteColor }}">
            {{ $displayNote }}
        </p>
    @endif

    {{-- Slot untuk konten tambahan (progress bar, dll) --}}
    @if ($slot->isNotEmpty())
        <div class="mt-3">{{ $slot }}</div>
    @endif
</div>

@props([
    'label'   => '',       // Label di atas (sub-teks deskriptor)
    'value'   => '',       // Nilai utama (angka / teks besar)
    'note'    => null,     // Teks kecil / keterangan di bawah value
    'variant' => 'default', // default | success | warning | danger | info
    'icon'    => null,     // SVG path string opsional
    'suffix'  => null,     // Satuan / unit di kanan value (misal: "%" atau "proyek")
])

@php
// Token warna per varian value
$valueStyle = match ($variant) {
    'success' => 'color:#15803d;',
    'warning' => 'color:#b45309;',
    'danger'  => 'color:#b91c1c;',
    'info'    => 'color:#2563eb;',
    default   => 'color:#0f172a;',
};

// Accent bar di sisi kiri kartu
$accentStyle = match ($variant) {
    'success' => 'background:#22c55e;',
    'warning' => 'background:#f59e0b;',
    'danger'  => 'background:#ef4444;',
    'info'    => 'background:#3b82f6;',
    default   => 'background:#e2e8f0;',
};
@endphp

<div {{ $attributes->merge(['class' => 'relative overflow-hidden rounded-md']) }}
     style="background:#fff; border:1px solid #e2e8f0;">
    {{-- Accent bar kiri --}}
    <div class="absolute inset-y-0 left-0 w-0.5 rounded-l-md" style="{{ $accentStyle }}"></div>

    <div class="px-4 py-3 pl-5">
        {{-- Label (descriptor) --}}
        <p class="text-[11px] font-medium uppercase tracking-wider truncate"
           style="color:#64748b; letter-spacing:.05em;">{{ $label }}</p>

        {{-- Value row --}}
        <div class="mt-1 flex items-baseline gap-1.5">
            @if ($icon)
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" style="{{ $valueStyle }} opacity:.6;">
                    {!! $icon !!}
                </svg>
            @endif
            <span class="text-2xl font-bold leading-none tabular"
                  style="{{ $valueStyle }} font-variant-numeric:tabular-nums;">
                {{ $value }}
            </span>
            @if ($suffix)
                <span class="text-xs font-medium" style="color:#94a3b8;">{{ $suffix }}</span>
            @endif
        </div>

        {{-- Note / sub info --}}
        @if ($note)
            <p class="mt-1.5 truncate text-[11px]" style="color:#94a3b8;">{{ $note }}</p>
        @endif

        {{-- Slot untuk konten tambahan (progress bar, dll) --}}
        @if ($slot->isNotEmpty())
            <div class="mt-2">{{ $slot }}</div>
        @endif
    </div>
</div>

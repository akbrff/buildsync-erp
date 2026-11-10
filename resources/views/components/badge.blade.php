@props([
    /**
     * StarAdmin Soft-UI Badge:
     *   success  → Turquoise : #27B1AE
     *   warning  → Orange    : #ED7132
     *   danger   → Orange    : #ED7132
     *   info     → Blue      : #136F9A
     *   neutral  → Dark Blue : #2C3E50
     */
    'variant' => 'neutral',
    'dot'     => false,
    'size'    => null,
])

@php
$classes = match ($variant) {
    'success'           => 'bg-[#27B1AE]/15 text-[#27B1AE]',
    'warning', 'danger' => 'bg-[#ED7132]/15 text-[#ED7132]',
    'info'              => 'bg-[#136F9A]/15 text-[#136F9A]',
    'neutral'           => 'bg-[#2C3E50]/10 text-[#2C3E50]',
    default             => 'bg-[#2C3E50]/10 text-[#2C3E50]',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center justify-center px-2.5 py-1 rounded-md text-[11px] font-bold tracking-wide whitespace-nowrap $classes"]) }}>
    {{ $slot }}
</span>

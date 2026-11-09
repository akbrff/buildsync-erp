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
    'size'    => 'sm',
])

@php
$classes = match ($variant) {
    'success'           => 'bg-[#27B1AE]/15 text-[#27B1AE] border border-[#27B1AE]/25',
    'warning', 'danger' => 'bg-[#ED7132]/15 text-[#ED7132] border border-[#ED7132]/25',
    'info'              => 'bg-[#136F9A]/15 text-[#136F9A] border border-[#136F9A]/25',
    'neutral'           => 'bg-[#2C3E50]/10 text-[#2C3E50] border border-[#2C3E50]/15',
    default             => 'bg-[#2C3E50]/10 text-[#2C3E50] border border-[#2C3E50]/15',
};

$dotColor = match ($variant) {
    'success'           => 'bg-[#27B1AE]',
    'warning', 'danger' => 'bg-[#ED7132]',
    'info'              => 'bg-[#136F9A]',
    'neutral'           => 'bg-[#2C3E50]',
    default             => 'bg-[#2C3E50]',
};

$sizeClass = $size === 'xs'
    ? 'text-[10px] px-2 py-0.5'
    : 'text-[11px] px-2.5 py-0.5';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-md font-semibold leading-none $classes $sizeClass"]) }}>
    @if ($dot)
        <span class="inline-block h-1.5 w-1.5 rounded-full {{ $dotColor }}"></span>
    @endif
    {{ $slot }}
</span>

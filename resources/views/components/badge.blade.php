@props([
    /**
     * Standard ERP Status Badge (Cool Blue System):
     *   success  → Emerald   : #16A34A
     *   warning  → Amber     : #D97706
     *   danger   → Rose      : #DC2626
     *   info     → Sky       : #0EA5E9
     *   cobalt   → Cobalt    : #1D4E89
     *   neutral  → Deep Navy : #0B2545
     */
    'variant' => 'neutral',
    'dot'     => false,
    'size'    => null,
])

@php
$classes = match ($variant) {
    'success'           => 'badge-success',
    'warning'           => 'badge-warning',
    'danger'            => 'badge-danger',
    'info'              => 'badge-info',
    'cobalt', 'burgundy'=> 'badge-cobalt',
    'dusty'             => 'badge-dusty',
    'neutral'           => 'badge-neutral',
    default             => 'badge-neutral',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center justify-center px-2.5 py-0.5 rounded text-xs font-medium tracking-normal whitespace-nowrap $classes"]) }}>
    {{ $slot }}
</span>

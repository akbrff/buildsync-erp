@props(['label', 'value', 'note' => null])

<div {{ $attributes->merge(['class' => 'rounded-lg bg-white p-5 shadow-sm']) }}>
    <p class="text-sm text-slate-500">{{ $label }}</p>
    <p class="mt-2 text-3xl font-bold">{{ $value }}</p>
    @if ($note)
        <p class="mt-1 text-xs text-slate-400">{{ $note }}</p>
    @endif
</div>
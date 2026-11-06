@props([
    /**
     * Varian semantik badge:
     *   success  → Hijau  : Selesai / Aktif / Aman
     *   warning  → Amber  : Pending / Ditunda / Belum Final
     *   danger   → Merah  : Terlambat / Menipis / Overbudget / Hapus
     *   info     → Biru   : Berjalan / Primary / Aksi
     *   neutral  → Slate  : Perencanaan / Tidak Aktif / Default
     */
    'variant' => 'neutral',
    'dot'     => false,   // tampilkan dot indicator di kiri
    'size'    => 'sm',    // sm | xs
])

@php
$styles = match ($variant) {
    'success' => 'background:#f0fdf4; color:#15803d; border-color:#bbf7d0;',
    'warning' => 'background:#fffbeb; color:#b45309; border-color:#fde68a;',
    'danger'  => 'background:#fef2f2; color:#b91c1c; border-color:#fecaca;',
    'info'    => 'background:#eff6ff; color:#1d4ed8; border-color:#bfdbfe;',
    'neutral' => 'background:#f8fafc; color:#475569; border-color:#e2e8f0;',
    default   => 'background:#f8fafc; color:#475569; border-color:#e2e8f0;',
};

$dotColor = match ($variant) {
    'success' => 'background:#22c55e;',
    'warning' => 'background:#f59e0b;',
    'danger'  => 'background:#ef4444;',
    'info'    => 'background:#3b82f6;',
    'neutral' => 'background:#94a3b8;',
    default   => 'background:#94a3b8;',
};

$sizeClass = $size === 'xs'
    ? 'text-[10px] px-1.5 py-0.5'
    : 'text-[11px] px-2 py-0.5';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 rounded font-semibold leading-none border $sizeClass"]) }}
      style="{{ $styles }}">
    @if ($dot)
        <span class="inline-block h-1.5 w-1.5 rounded-full" style="{{ $dotColor }}"></span>
    @endif
    {{ $slot }}
</span>

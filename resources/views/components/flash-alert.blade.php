@props([
    /**
     * Komponen Flash Alert — tampilkan pesan sesi success/error/warning.
     * Mendukung session key: 'success', 'error', 'warning', 'info'
     */
])

@foreach (['success', 'error', 'warning', 'info'] as $type)
    @if (session($type))
        @php
            $style = match ($type) {
                'success' => 'background:#f0fdf4; color:#15803d; border-color:#bbf7d0;',
                'error'   => 'background:#fef2f2; color:#b91c1c; border-color:#fecaca;',
                'warning' => 'background:#fffbeb; color:#b45309; border-color:#fde68a;',
                'info'    => 'background:#eff6ff; color:#1d4ed8; border-color:#bfdbfe;',
            };
            $icon = match ($type) {
                'success' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
                'error'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>',
                'warning' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>',
                'info'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
            };
        @endphp
        <div class="mb-4 flex items-start gap-2.5 rounded-md px-3.5 py-2.5"
             style="border:1px solid; {{ $style }} font-size:.8125rem; line-height:1.4;">
            <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 shrink-0"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                {!! $icon !!}
            </svg>
            <span>{{ session($type) }}</span>
        </div>
    @endif
@endforeach

{{-- Validation errors summary --}}
@if ($errors->any())
    <div class="mb-4 rounded-md px-3.5 py-2.5"
         style="background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; font-size:.8125rem;">
        <p class="font-semibold">Data tidak valid — periksa kembali:</p>
        <ul class="mt-1.5 list-inside list-disc space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

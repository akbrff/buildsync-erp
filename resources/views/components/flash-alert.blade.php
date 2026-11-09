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
                'success' => 'background:rgba(39, 177, 174, 0.12); color:#27B1AE; border-color:rgba(39, 177, 174, 0.3);',
                'error'   => 'background:rgba(237, 113, 50, 0.12); color:#ED7132; border-color:rgba(237, 113, 50, 0.3);',
                'warning' => 'background:rgba(237, 113, 50, 0.12); color:#ED7132; border-color:rgba(237, 113, 50, 0.3);',
                'info'    => 'background:rgba(19, 111, 154, 0.12); color:#136F9A; border-color:rgba(19, 111, 154, 0.3);',
            };
            $icon = match ($type) {
                'success' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
                'error'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>',
                'warning' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>',
                'info'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
            };
        @endphp
        <div class="mb-5 flex items-start gap-2.5 rounded-xl px-4 py-3 shadow-xs"
             style="border:1px solid; {{ $style }} font-size:.8125rem; line-height:1.4;">
            <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 shrink-0"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                {!! $icon !!}
            </svg>
            <span class="font-medium">{{ session($type) }}</span>
        </div>
    @endif
@endforeach

{{-- Validation errors summary --}}
@if ($errors->any())
    <div class="mb-5 rounded-xl px-4 py-3 shadow-xs"
         style="background:rgba(237, 113, 50, 0.12); border:1px solid rgba(237, 113, 50, 0.3); color:#ED7132; font-size:.8125rem;">
        <p class="font-bold">Data tidak valid — periksa kembali:</p>
        <ul class="mt-1.5 list-inside list-disc space-y-0.5 text-xs">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

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
                'success' => 'bg-[#16A34A]/10 text-[#16A34A] border-[#16A34A]/25 dark:bg-[#16A34A]/20 dark:text-emerald-300',
                'error'   => 'bg-[#DC2626]/10 text-[#DC2626] border-[#DC2626]/25 dark:bg-[#DC2626]/20 dark:text-rose-300',
                'warning' => 'bg-[#D97706]/10 text-[#D97706] border-[#D97706]/25 dark:bg-[#D97706]/20 dark:text-amber-300',
                'info'    => 'bg-[#0EA5E9]/10 text-[#0EA5E9] border-[#0EA5E9]/25 dark:bg-[#0EA5E9]/20 dark:text-sky-300',
            };
            $icon = match ($type) {
                'success' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
                'error'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>',
                'warning' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>',
                'info'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
            };
        @endphp
        <div class="mb-5 flex items-start gap-3 rounded-lg px-4 py-3 border shadow-none text-sm leading-relaxed {{ $style }}">
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
    <div class="mb-5 rounded-lg px-4 py-3.5 border border-[#DC2626]/25 bg-[#DC2626]/10 text-[#DC2626] dark:bg-[#DC2626]/20 dark:text-rose-300 shadow-none text-sm">
        <p class="font-bold flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <span>{{ __('common.invalid_data') }}</span>
        </p>
        <ul class="mt-2 list-inside list-disc space-y-1 text-xs">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

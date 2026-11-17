@props([
    /**
     * Secondary Tab Bar — dipakai untuk modul dengan beberapa sub-view.
     * Contoh: modul Jadwal Proyek (Daftar, Gantt, Monitoring, Ringkasan).
     *
     * $tabs → array:
     *   [ ['label' => 'Daftar Jadwal', 'route' => 'schedules.index',
     *      'active_routes' => ['schedules.index'], 'params' => []], ... ]
     *
     * Params: query string yang akan diteruskan ke href tab
     */
    'tabs' => [],
])

<div class="flex items-center gap-1 sm:gap-2 overflow-x-auto border-b border-[#E5E7EB] dark:border-[#27272A] scrollbar-none [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
    @foreach ($tabs as $tab)
        @php
            $activeRoutes = $tab['active_routes'] ?? [$tab['route']];
            $isActive     = Route::has($tab['route']) && request()->routeIs($activeRoutes);
            // Teruskan project_id secara otomatis jika ada di request untuk mempertahankan konteks proyek
            $defaultParams = request()->filled('project_id') ? ['project_id' => request('project_id')] : [];
            $params       = $tab['params'] ?? $defaultParams;
            $href         = Route::has($tab['route']) ? route($tab['route'], $params) : '#';
        @endphp
        <a href="{{ $href }}"
           class="relative z-10 flex shrink-0 items-center gap-2 px-3.5 sm:px-4 py-2.5 text-[13px] font-medium transition-all whitespace-nowrap {{ $isActive
               ? 'text-[#1D4E89] dark:text-[#93C5FD] border-b-2 border-[#1D4E89] dark:border-[#93C5FD] -mb-px'
               : 'text-[#64748B] dark:text-[#A1A1AA] border-b-2 border-transparent -mb-px hover:text-[#0B2545] dark:hover:text-[#F4F4F5] hover:border-slate-300 dark:hover:border-[#3F3F46]' }}">
            @if (!empty($tab['icon']))
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 {{ $isActive ? 'text-[#1D4E89] dark:text-[#93C5FD]' : 'text-[#64748B] dark:text-[#71717A]' }}"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    {!! $tab['icon'] !!}
                </svg>
            @endif
            <span>{{ $tab['label'] }}</span>
        </a>
    @endforeach
</div>

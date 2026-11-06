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

<div class="flex items-center gap-0.5 overflow-x-auto"
     style="border-bottom:1px solid #e2e8f0;">
    @foreach ($tabs as $tab)
        @php
            $activeRoutes = $tab['active_routes'] ?? [$tab['route']];
            $isActive     = Route::has($tab['route']) && request()->routeIs($activeRoutes);
            $params       = $tab['params'] ?? [];
            $href         = Route::has($tab['route']) ? route($tab['route'], $params) : '#';
        @endphp
        <a href="{{ $href }}"
           class="relative flex shrink-0 items-center gap-1.5 px-3 pb-2.5 pt-1 text-xs font-medium transition-colors whitespace-nowrap"
           style="{{ $isActive
               ? 'color:#2563eb; border-bottom:2px solid #2563eb; margin-bottom:-1px;'
               : 'color:#64748b; border-bottom:2px solid transparent; margin-bottom:-1px;' }}"
           onmouseover="if (!this.dataset.active) { this.style.color='#374151'; }"
           onmouseout="if (!this.dataset.active) { this.style.color='#64748b'; }"
           {{ $isActive ? 'data-active=1' : '' }}>
            @if (!empty($tab['icon']))
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    {!! $tab['icon'] !!}
                </svg>
            @endif
            {{ $tab['label'] }}
        </a>
    @endforeach
</div>

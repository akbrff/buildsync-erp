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

<div class="flex items-center gap-1 overflow-x-auto"
     style="border-bottom:1px solid #EEF4F8;">
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
           class="relative flex shrink-0 items-center gap-1.5 px-3.5 pb-2.5 pt-1.5 text-xs font-semibold transition-colors whitespace-nowrap"
           style="{{ $isActive
               ? 'color:#136F9A; border-bottom:2px solid #136F9A; margin-bottom:-1px;'
               : 'color:#7F7F7F; border-bottom:2px solid transparent; margin-bottom:-1px;' }}"
           onmouseover="if (!this.dataset.active) { this.style.color='#2C3E50'; }"
           onmouseout="if (!this.dataset.active) { this.style.color='#7F7F7F'; }"
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

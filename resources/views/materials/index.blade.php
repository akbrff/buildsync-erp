@extends('layouts.app')

@section('title', 'Logistik & Inventaris Material')

@section('breadcrumbs')
    <x-breadcrumb :links="[
        ['label' => 'Dashboard', 'href' => route('dashboard')],
        ['label' => 'Logistik & Material', 'href' => '#'],
    ]" />
@endsection

@section('content')
    {{-- Page Header --}}
    <x-page-header
        title="Logistik & Inventaris Material"
        subtitle="Monitoring stok fisik gudang, batas reorder point material, dan valuasi aset konstruksi secara real-time.">
        <x-slot:actions>
            <a href="{{ route('materials.create') }}"
               class="btn-primary">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Material
            </a>
        </x-slot:actions>
    </x-page-header>

    {{-- Flash Notifications --}}
    <x-flash-alert />

    {{-- Ringkasan KPI Inventaris --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4 mb-6">
        <x-stat-card
            label="Total Jenis Material"
            :value="$totalMaterials"
            unit="SKU"
            subtext="Valuasi Total: Rp {{ number_format($totalValuation, 0, ',', '.') }}"
            subtextColor="text-slate-500"
            color="navy"
        />

        <x-stat-card
            label="Stok Aman"
            :value="$safeCount"
            unit="SKU"
            subtext="> 120% batas minimum reorder"
            badge="Aman"
            badgeVariant="success"
            color="emerald"
        />

        <x-stat-card
            label="Mendekati Reorder"
            :value="$lowCount"
            unit="SKU"
            subtext="Stok menipis (100% - 120% min)"
            badge="Reorder"
            badgeVariant="warning"
            color="amber"
        />

        <x-stat-card
            label="Stok Kritis / Habis"
            :value="$criticalCount"
            unit="SKU"
            subtext="≤ Batas minimum stok fisik"
            badge="Kritis"
            badgeVariant="danger"
            color="rose"
        />
    </div>

    {{-- Filter Bar & Table Panel --}}
    <div class="rounded-2xl border border-slate-100 bg-white shadow-[0_2px_12px_rgba(44,62,80,0.04)] overflow-hidden">
        {{-- Toolbar: Filter & Search --}}
        <div class="border-b border-[#EEF4F8] p-4 sm:p-5 bg-[#F8FBFD]">
            <x-filter-bar
                action="{{ route('materials.index') }}"
                :searchable="true"
                searchName="q"
                searchPlaceholder="Cari SKU, nama material...">

                {{-- Status Level Stok --}}
                <select name="status" class="erp-input" style="width: auto; min-width: 170px;" onchange="this.form.submit()">
                    <option value="">Semua Status Stok</option>
                    <option value="aman" @selected(request('status') === 'aman')>Stok Aman (> 120%)</option>
                    <option value="menipis" @selected(request('status') === 'menipis')>Mendekati Reorder (100-120%)</option>
                    <option value="kritis" @selected(request('status') === 'kritis')>Stok Kritis / Habis (≤ Min)</option>
                </select>

                {{-- Filter Satuan --}}
                @if(isset($units) && $units->isNotEmpty())
                    <select name="unit" class="erp-input" style="width: auto; min-width: 130px;" onchange="this.form.submit()">
                        <option value="">Semua Satuan</option>
                        @foreach($units as $u)
                            <option value="{{ $u }}" @selected(request('unit') === $u)>{{ $u }}</option>
                        @endforeach
                    </select>
                @endif
            </x-filter-bar>
        </div>

        {{-- Tabel Inventaris Material Berdensitas Tinggi --}}
        <div class="overflow-x-auto">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th class="w-28">Kode SKU</th>
                        <th>Nama Material & Spesifikasi</th>
                        <th class="text-right">Harga Satuan</th>
                        <th class="text-right">Stok Min.</th>
                        <th class="text-right">Stok Fisik</th>
                        <th class="text-center w-32">Status Level</th>
                        <th class="text-right">Valuasi Aset</th>
                        <th class="text-right w-44">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($materials as $material)
                        @php
                            $isCritical = $material->stock <= $material->min_stock;
                            $isWarning  = !$isCritical && ($material->stock <= ($material->min_stock * 1.2));
                            $rowValuation = $material->stock * $material->price;
                        @endphp
                        <tr class="{{ $isCritical ? 'bg-red-50/40 hover:bg-red-50/70' : ($isWarning ? 'bg-amber-50/30 hover:bg-amber-50/60' : 'hover:bg-slate-50/70') }}">
                            {{-- Kode SKU --}}
                            <td class="font-code font-semibold text-slate-700 whitespace-nowrap">
                                <a href="{{ route('materials.show', $material) }}" class="hover:text-blue-600 hover:underline">
                                    {{ $material->code }}
                                </a>
                            </td>

                            {{-- Nama Material --}}
                            <td>
                                <div class="text-[13px] font-medium text-slate-900 leading-snug">
                                    {{ $material->name }}
                                </div>
                                <div class="text-[11px] text-slate-500">
                                    Satuan: <span class="font-medium text-slate-700">{{ $material->unit }}</span>
                                </div>
                            </td>

                            {{-- Harga Satuan --}}
                            <td class="text-right tabular whitespace-nowrap">
                                <span class="text-slate-800 font-medium">Rp {{ number_format($material->price, 0, ',', '.') }}</span>
                                <span class="text-[11px] text-slate-400">/{{ $material->unit }}</span>
                            </td>

                            {{-- Stok Min --}}
                            <td class="text-right tabular text-slate-500 whitespace-nowrap">
                                {{ number_format($material->min_stock, 0, ',', '.') }} <span class="text-[11px]">{{ $material->unit }}</span>
                            </td>

                            {{-- Stok Fisik --}}
                            <td class="text-right tabular font-medium text-slate-900 whitespace-nowrap">
                                {{ number_format($material->stock, 0, ',', '.') }} <span class="text-[11px] font-normal text-slate-500">{{ $material->unit }}</span>
                            </td>

                            {{-- Status Level --}}
                            <td class="text-center whitespace-nowrap">
                                @if ($isCritical)
                                    <x-badge variant="danger" dot>Kritis (≤ Min)</x-badge>
                                @elseif ($isWarning)
                                    <x-badge variant="warning" dot>Menipis</x-badge>
                                @else
                                    <x-badge variant="success" dot>Stok Aman</x-badge>
                                @endif
                            </td>

                            {{-- Valuasi Aset --}}
                            <td class="text-right tabular font-semibold text-slate-700 whitespace-nowrap">
                                Rp {{ number_format($rowValuation, 0, ',', '.') }}
                            </td>

                            {{-- Aksi --}}
                            <td class="text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Quick Mutasi Trigger Button --}}
                                    <button type="button"
                                            onclick="openMutationModal('{{ $material->id }}', '{{ addslashes($material->name) }}', '{{ $material->code }}', '{{ $material->stock }}', '{{ $material->unit }}')"
                                            class="inline-flex items-center gap-1 rounded border border-slate-200 bg-white px-2 py-1 text-[11px] font-semibold text-slate-700 hover:bg-slate-100 transition-colors shadow-2xs"
                                            title="Mutasi Masuk / Keluar">
                                        <svg class="h-3 w-3 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                        </svg>
                                        Mutasi
                                    </button>

                                    <a href="{{ route('materials.show', $material) }}"
                                       class="rounded p-1 text-slate-500 hover:bg-slate-100 hover:text-slate-800 transition-colors"
                                       title="Detail Material">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    <a href="{{ route('materials.edit', $material) }}"
                                       class="rounded p-1 text-blue-600 hover:bg-blue-50 transition-colors"
                                       title="Edit Material">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>

                                    <form action="{{ route('materials.destroy', $material) }}"
                                          method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus material ini?');"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="rounded p-1 text-red-500 hover:bg-red-50 hover:text-red-700 transition-colors"
                                                title="Hapus Material">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="h-8 w-8 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <p class="text-xs font-medium text-slate-600">Tidak ada data material yang sesuai filter.</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Ubah kata kunci pencarian atau tambah item material baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer Pagination --}}
        @if ($materials->hasPages())
            <div class="border-t border-slate-200 px-3.5 py-2 bg-slate-50/50">
                {{ $materials->links() }}
            </div>
        @endif
    </div>

{{-- Quick Stock Mutation Modal --}}
<div id="mutationModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
    <div class="w-full max-w-md rounded-md border border-slate-200 bg-white shadow-sm overflow-hidden" onclick="event.stopPropagation()">
        {{-- Modal Header --}}
        <div class="border-b border-slate-200 px-4 py-3 bg-slate-50 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-slate-800">Catat Mutasi Stok Fisik</h3>
                <p id="modalMaterialDesc" class="text-xs text-slate-500 mt-0.5 font-code"></p>
            </div>
            <button type="button" onclick="closeMutationModal()" class="text-slate-400 hover:text-slate-600 rounded p-1">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Modal Form --}}
        <form id="mutationForm" method="POST" action="">
            @csrf
            <div class="p-4 space-y-3.5">
                {{-- Jenis Mutasi Radio --}}
                <div>
                    <label class="erp-label mb-1.5 block">Tipe Mutasi Logistik</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center gap-2 rounded border border-slate-200 p-2.5 cursor-pointer hover:bg-slate-50 transition-colors">
                            <input type="radio" name="type" value="in" checked class="text-emerald-600 focus:ring-emerald-500">
                            <div>
                                <span class="text-xs font-semibold text-slate-800 block">Barang Masuk</span>
                                <span class="text-[10px] text-slate-500 block">Penerimaan dari Supplier</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-2 rounded border border-slate-200 p-2.5 cursor-pointer hover:bg-slate-50 transition-colors">
                            <input type="radio" name="type" value="out" class="text-amber-600 focus:ring-amber-500">
                            <div>
                                <span class="text-xs font-semibold text-slate-800 block">Barang Keluar</span>
                                <span class="text-[10px] text-slate-500 block">Alokasi ke Lapangan/Proyek</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Kuantitas & Info Stok --}}
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="modalAmount" class="erp-label">Kuantitas Mutasi</label>
                        <span id="modalCurrentStock" class="text-xs text-slate-500 tabular"></span>
                    </div>
                    <div class="relative">
                        <input type="number" id="modalAmount" name="amount" min="1" required
                               class="erp-input pr-14 tabular font-semibold" placeholder="Contoh: 50">
                        <span id="modalUnitSuffix" class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-xs text-slate-400 font-medium"></span>
                    </div>
                </div>

                {{-- Keterangan / Referensi --}}
                <div>
                    <label for="modalNotes" class="erp-label mb-1 block">Catatan / Surat Jalan / No. Reff</label>
                    <input type="text" id="modalNotes" name="notes" class="erp-input" placeholder="Contoh: DO-2026/04/Supplier Jaya atau Penggunaan Pengecoran Lt. 2">
                </div>
            </div>

            {{-- Modal Actions --}}
            <div class="border-t border-slate-200 bg-slate-50 px-4 py-2.5 flex items-center justify-end gap-2">
                <button type="button" onclick="closeMutationModal()" class="rounded border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-100 transition-colors">
                    Batal
                </button>
                <button type="submit" class="rounded bg-blue-600 px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-blue-700 transition-colors shadow-xs">
                    Simpan Mutasi Stok
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openMutationModal(id, name, code, stock, unit) {
        const modal = document.getElementById('mutationModal');
        const form  = document.getElementById('mutationForm');
        form.action = `/materials/${id}/adjust`;

        document.getElementById('modalMaterialDesc').textContent = `[${code}] ${name}`;
        document.getElementById('modalCurrentStock').textContent = `Stok saat ini: ${stock} ${unit}`;
        document.getElementById('modalUnitSuffix').textContent   = unit;
        document.getElementById('modalAmount').value             = '';
        document.getElementById('modalNotes').value              = '';

        modal.classList.remove('hidden');
    }

    function closeMutationModal() {
        document.getElementById('mutationModal').classList.add('hidden');
    }

    // Tutup modal jika klik backdrop
    document.getElementById('mutationModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeMutationModal();
        }
    });
</script>
@endsection

@extends('layouts.app')

@section('title', 'Supplier & Pengadaan')

@section('breadcrumbs')
    <x-breadcrumb :links="[
        ['label' => 'Dashboard', 'href' => route('dashboard')],
        ['label' => 'Supplier & Pengadaan', 'href' => '#'],
    ]" />
@endsection

@section('content')
    <x-page-header
        title="Supplier & Pengadaan"
        subtitle="Kelola pemasok, purchase order, penerimaan material, dan riwayat pembelian."
    />

    <x-flash-alert />

    <div class="grid gap-6 xl:grid-cols-2 mb-6">
        <!-- 1. KARTU TAMBAH SUPPLIER -->
        <section class="bg-white rounded-2xl shadow-[0_2px_12px_rgba(44,62,80,0.04)] p-6 mb-6">
            <h2 class="text-[15px] font-bold text-[#2C3E50] mb-5">Tambah Supplier</h2>

            <form method="POST" action="{{ route('suppliers.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-[#7F7F7F] mb-1.5">Kode Supplier <span class="text-rose-500">*</span></label>
                    <input class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="supplier_code" placeholder="Kode supplier" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#7F7F7F] mb-1.5">Nama Supplier <span class="text-rose-500">*</span></label>
                    <input class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="name" placeholder="Nama supplier" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#7F7F7F] mb-1.5">Kontak Person</label>
                    <input class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="contact_person" placeholder="Kontak person">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#7F7F7F] mb-1.5">Nomor Telepon</label>
                    <input class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="phone" placeholder="Telepon">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#7F7F7F] mb-1.5">Email</label>
                    <input class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" type="email" name="email" placeholder="Email">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#7F7F7F] mb-1.5">Status Kerjasama <span class="text-rose-500">*</span></label>
                    <select class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="status">
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-[#7F7F7F] mb-1.5">Alamat Operasional</label>
                    <textarea class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="address" rows="2" placeholder="Alamat"></textarea>
                </div>
                <div class="sm:col-span-2 pt-1">
                    <button type="submit" class="bg-[#136F9A] text-white rounded-lg px-5 py-2.5 text-xs font-bold hover:bg-[#136F9A]/90 transition-colors w-max">Simpan Supplier</button>
                </div>
            </form>

            {{-- Tabel Daftar Supplier --}}
            <div class="mt-8 pt-6 border-t border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-[13px] font-bold text-[#2C3E50]">Daftar Supplier</h3>
                        <p class="text-[11px] text-[#7F7F7F]">Daftar rekanan pengadaan material terdaftar</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#F8FBFD] text-[#136F9A] border border-[#EEF4F8]">
                        {{ $suppliers->count() }} Rekanan
                    </span>
                </div>
                <div class="overflow-x-auto rounded-xl border border-slate-100">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#F8FBFD]">
                                <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100">KODE</th>
                                <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100">SUPPLIER</th>
                                <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100">KONTAK</th>
                                <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 text-center">RIWAYAT PO</th>
                                <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100">STATUS & PEMBARUAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($suppliers as $supplier)
                                <tr class="text-[13px] text-[#2C3E50] border-b border-slate-50 hover:bg-[#F8FBFD]/50 transition-colors">
                                    <td class="px-4 py-3 font-mono text-xs text-slate-500 whitespace-nowrap">{{ $supplier->supplier_code }}</td>
                                    <td class="px-4 py-3 font-medium text-[#2C3E50]">
                                        <div>{{ $supplier->name }}</div>
                                        @if($supplier->contact_person)
                                            <div class="text-[11px] text-[#7F7F7F] font-normal">PIC: {{ $supplier->contact_person }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-slate-600 text-xs whitespace-nowrap">
                                        <div>{{ $supplier->phone ?? '-' }}</div>
                                        @if($supplier->email)
                                            <div class="text-[11px] text-[#7F7F7F]">{{ $supplier->email }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-[#2C3E50]">
                                            {{ $supplier->purchase_orders_count }} PO
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="flex flex-wrap items-center gap-1.5">
                                            @csrf
                                            @method('PUT')
                                            <input class="w-24 rounded-lg border border-slate-200 bg-slate-50/50 px-2.5 py-1.5 text-[12px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="supplier_code" value="{{ $supplier->supplier_code }}" title="Kode Supplier" required>
                                            <input class="w-32 rounded-lg border border-slate-200 bg-slate-50/50 px-2.5 py-1.5 text-[12px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="name" value="{{ $supplier->name }}" title="Nama Supplier" required>
                                            <select class="rounded-lg border border-slate-200 bg-slate-50/50 px-2 py-1.5 text-[12px] text-[#2C3E50] focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="status">
                                                <option value="active" @selected($supplier->status === 'active')>Aktif</option>
                                                <option value="inactive" @selected($supplier->status === 'inactive')>Nonaktif</option>
                                            </select>
                                            <input type="hidden" name="phone" value="{{ $supplier->phone }}">
                                            <input type="hidden" name="email" value="{{ $supplier->email }}">
                                            <input type="hidden" name="contact_person" value="{{ $supplier->contact_person }}">
                                            <input type="hidden" name="address" value="{{ $supplier->address }}">
                                            <button type="submit" class="rounded-lg bg-[#136F9A] px-2.5 py-1.5 text-[11px] font-bold text-white hover:bg-[#136F9A]/90 transition-colors">Simpan</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-400 text-xs">Belum ada data supplier yang terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- 2. KARTU BUAT PURCHASE ORDER -->
        <section class="bg-white rounded-2xl shadow-[0_2px_12px_rgba(44,62,80,0.04)] p-6 mb-6">
            <h2 class="text-[15px] font-bold text-[#2C3E50] mb-5">Buat Purchase Order</h2>

            <form method="POST" action="{{ route('purchase-orders.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#7F7F7F] mb-1.5">Pilih Supplier <span class="text-rose-500">*</span></label>
                        <select class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="supplier_id" required>
                            <option value="">Pilih supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @disabled($supplier->status !== 'active')>
                                    {{ $supplier->supplier_code }} - {{ $supplier->name }}{{ $supplier->status !== 'active' ? ' (Nonaktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#7F7F7F] mb-1.5">Proyek Konstruksi <span class="text-rose-500">*</span></label>
                        <select class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="project_id" required>
                            <option value="">Pilih proyek</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->project_code }} - {{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#7F7F7F] mb-1.5">Tanggal Order <span class="text-rose-500">*</span></label>
                        <input class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" type="date" name="order_date" value="{{ now()->toDateString() }}" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#7F7F7F] mb-1.5">Estimasi Pengiriman</label>
                        <input class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" type="date" name="expected_date">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#7F7F7F] mb-1.5">Item Material & Rincian Harga <span class="text-rose-500">*</span></label>
                    <div id="po-items" class="space-y-3">
                        <div class="grid grid-cols-12 gap-4 items-center po-item-row">
                            <div class="col-span-12 sm:col-span-6">
                                <select class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="items[0][material_id]" required>
                                    <option value="">Material</option>
                                    @foreach($materials as $material)
                                        <option value="{{ $material->id }}">{{ $material->code }} - {{ $material->name }} ({{ $material->unit }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-12 sm:col-span-2">
                                <input class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" type="number" min="1" name="items[0][quantity]" placeholder="Qty" required>
                            </div>
                            <div class="col-span-12 sm:col-span-4">
                                <input class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" type="number" min="0" step="0.01" name="items[0][unit_price]" placeholder="Harga satuan" required>
                            </div>
                        </div>
                    </div>
                    <button type="button" onclick="addPoItem()" class="text-[11px] font-bold text-[#136F9A] hover:text-[#2C3E50] cursor-pointer inline-block mt-2">+ Tambah item material</button>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#7F7F7F] mb-1.5">Catatan PO</label>
                    <textarea class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="notes" rows="2" placeholder="Catatan PO"></textarea>
                </div>

                <div class="pt-1">
                    <button type="submit" class="bg-[#136F9A] text-white rounded-lg px-5 py-2.5 text-xs font-bold hover:bg-[#136F9A]/90 transition-colors w-max">Buat PO</button>
                </div>
            </form>
        </section>
    </div>

    <!-- 3. KARTU RIWAYAT PEMBELIAN & PENERIMAAN -->
    <section class="bg-white rounded-2xl shadow-[0_2px_12px_rgba(44,62,80,0.04)] p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
            <h2 class="text-[15px] font-bold text-[#2C3E50]">Riwayat Pembelian & Penerimaan</h2>
            @if($orders->total() > 0)
                <span class="text-xs text-[#7F7F7F]">Total {{ $orders->total() }} Pesanan</span>
            @endif
        </div>

        <form method="GET" class="mb-5 flex flex-wrap items-center gap-3">
            <div class="w-full sm:w-72">
                <select class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="supplier_id">
                    <option value="">Semua supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @selected(($supplierId ?? 0) == $supplier->id)>
                            {{ $supplier->supplier_code }} - {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-[#2C3E50] text-white rounded-lg px-4 py-2.5 text-xs font-bold hover:bg-[#2C3E50]/90 transition-colors">Filter riwayat</button>
            @if($supplierId)
                <a href="{{ route('procurement.index') }}" class="text-xs font-semibold text-[#136F9A] hover:text-[#2C3E50] transition-colors ml-1">Reset filter</a>
            @endif
        </form>

        <div class="overflow-x-auto rounded-xl border border-slate-100">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8FBFD]">
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100">NO. PO</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100">TANGGAL</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100">SUPPLIER / PROYEK</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100">NILAI</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 text-center">STATUS</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100">PENERIMAAN</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100">PEMBAYARAN</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr class="text-[13px] text-[#2C3E50] border-b border-slate-50 hover:bg-[#F8FBFD]/50 transition-colors">
                            <td class="px-4 py-3 font-medium text-[#2C3E50] font-mono text-xs whitespace-nowrap">{{ $order->po_number }}</td>
                            <td class="px-4 py-3 text-slate-600 text-xs whitespace-nowrap">{{ $order->order_date->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-[#2C3E50]">{{ $order->supplier->name }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">{{ $order->project->project_code }} - {{ $order->project->name }}</div>
                            </td>
                            <td class="px-4 py-3 tabular font-semibold text-[#2C3E50] whitespace-nowrap">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <x-badge variant="{{ $order->status === 'received' ? 'success' : ($order->status === 'partial' ? 'warning' : 'neutral') }}">
                                    {{ ucfirst($order->status) }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-3">
                                @if($order->status !== 'received' && $order->status !== 'cancelled')
                                    <form method="POST" action="{{ route('purchase-orders.receive', $order) }}" class="space-y-1.5">
                                        @csrf
                                        @foreach($order->items as $item)
                                            <div>
                                                <label class="block text-[11px] text-[#2C3E50] font-medium leading-tight">
                                                    {{ $item->material->name }} <span class="text-slate-400">({{ $item->received_quantity }}/{{ $item->quantity }})</span>
                                                </label>
                                                <input class="w-24 rounded-lg border border-slate-200 bg-slate-50/50 px-2.5 py-1 text-[12px] text-[#2C3E50] focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all tabular" type="number" min="0" max="{{ $item->quantity - $item->received_quantity }}" name="items[{{ $item->id }}]" value="0">
                                            </div>
                                        @endforeach
                                        <button type="submit" class="block text-[11px] font-bold text-[#136F9A] hover:text-[#2C3E50] transition-colors mt-1">Simpan penerimaan</button>
                                    </form>
                                @else
                                    <div class="text-[11px] text-[#27B1AE] font-semibold flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Selesai diterima
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @php $remaining = max(0, $order->total_amount - $order->paid_amount); @endphp
                                @if($remaining > 0)
                                    <form method="POST" action="{{ route('purchase-orders.pay', $order) }}" class="space-y-1.5">
                                        @csrf
                                        <div class="flex items-center gap-1.5">
                                            <input class="w-28 rounded-lg border border-slate-200 bg-slate-50/50 px-2.5 py-1 text-[12px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all tabular" type="number" min="1" max="{{ $remaining }}" step="0.01" name="amount" placeholder="Nominal">
                                            <input class="w-32 rounded-lg border border-slate-200 bg-slate-50/50 px-2.5 py-1 text-[12px] text-[#2C3E50] focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all tabular" type="date" name="transaction_date" value="{{ now()->toDateString() }}">
                                        </div>
                                        <div class="flex items-center justify-between gap-2 mt-1">
                                            <button type="submit" class="text-[11px] font-bold text-[#136F9A] hover:text-[#2C3E50] transition-colors">Catat bayar</button>
                                            <span class="text-[11px] text-slate-500 tabular">Sisa Rp {{ number_format($remaining, 0, ',', '.') }}</span>
                                        </div>
                                    </form>
                                @else
                                    <div class="text-[11px] text-[#27B1AE] font-semibold flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Lunas
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400 text-xs">
                                <p class="font-medium text-slate-500 mb-1">Belum ada purchase order</p>
                                <p class="text-slate-400">Gunakan form di atas untuk menerbitkan purchase order baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </section>

    <script>
        let poItemIndex = 1;
        function addPoItem() {
            const w = document.getElementById('po-items');
            const row = w.firstElementChild.cloneNode(true);
            row.querySelectorAll('select, input').forEach(e => {
                e.name = e.name.replace(/items\[\d+\]/, 'items[' + poItemIndex + ']');
                if (e.tagName === 'SELECT') {
                    e.selectedIndex = 0;
                } else {
                    e.value = '';
                }
            });
            w.appendChild(row);
            poItemIndex++;
        }
    </script>
@endsection

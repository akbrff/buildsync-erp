@extends('layouts.app')

@section('title', 'Arus Kas & Keuangan')

@section('breadcrumbs')
    <x-breadcrumb :links="[
        ['label' => 'Dashboard', 'href' => route('dashboard')],
        ['label' => 'Arus Kas', 'href' => '#'],
    ]" />
@endsection

@section('content')
    <div class="mb-6">
        <h1 class="text-xl font-bold text-[#2C3E50] mb-1">Arus Kas & Integrasi Keuangan</h1>
        <p class="text-xs text-[#7F7F7F]">Pantau kas masuk/keluar, pembayaran supplier, biaya tenaga kerja, dan komitmen pengadaan.</p>
    </div>

    <x-flash-alert />

    <!-- STATS CARDS -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 mb-6">
        <x-stat-card label="Kas Masuk" :value="'Rp ' . number_format($income, 0, ',', '.')" variant="success" />
        <x-stat-card label="Kas Keluar" :value="'Rp ' . number_format($outflow, 0, ',', '.')" variant="warning" />
        <x-stat-card label="Saldo Arus Kas" :value="'Rp ' . number_format($income - $outflow, 0, ',', '.')" variant="info" />
        <x-stat-card label="Pengeluaran Anggaran Disetujui" :value="'Rp ' . number_format($approvedExpenses, 0, ',', '.')" variant="neutral" />
        <x-stat-card label="Upah Belum Dibayar" :value="'Rp ' . number_format($pendingPayroll, 0, ',', '.')" variant="warning" />
        <x-stat-card label="Sisa Tagihan PO" :value="'Rp ' . number_format($poOutstanding, 0, ',', '.')" variant="warning" />
    </div>

    <!-- KARTU WADAH: CATAT ARUS KAS MANUAL -->
    <section class="bg-white rounded-2xl shadow-[0_2px_12px_rgba(44,62,80,0.04)] p-6 mb-6">
        <h2 class="text-[15px] font-bold text-[#2C3E50] mb-2">Catat Arus Kas Manual</h2>
        <p class="text-xs text-[#7F7F7F] mb-5">Catat hanya transaksi yang belum tercatat melalui pembayaran PO, pembayaran upah, atau anggaran yang sudah dicatat agar laporan tidak menghitung biaya dua kali.</p>

        <form method="POST" action="{{ route('finance.store') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-[#7F7F7F] mb-1.5">Proyek</label>
                <select class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="project_id">
                    <option value="">Kas umum / tanpa proyek</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}">{{ $p->project_code }} - {{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#7F7F7F] mb-1.5">Jenis Transaksi</label>
                <select class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="type">
                    <option value="in">Kas Masuk</option>
                    <option value="out">Kas Keluar</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#7F7F7F] mb-1.5">Kategori</label>
                <input class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="category" placeholder="Kategori" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#7F7F7F] mb-1.5">Keterangan</label>
                <input class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="description" placeholder="Keterangan" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#7F7F7F] mb-1.5">Nominal (Rp)</label>
                <input class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" type="number" min="1" step="0.01" name="amount" placeholder="Nominal" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#7F7F7F] mb-1.5">Tanggal Transaksi</label>
                <input class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" type="date" name="transaction_date" value="{{ now()->toDateString() }}" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#7F7F7F] mb-1.5">Referensi</label>
                <input class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="reference" placeholder="Referensi">
            </div>
            <div class="flex flex-col justify-end">
                <label class="hidden sm:block text-xs font-semibold text-transparent mb-1.5 select-none">&nbsp;</label>
                <button type="submit" class="bg-[#136F9A] text-white rounded-lg px-5 py-2.5 text-[13px] font-bold hover:bg-[#136F9A]/90 transition-colors w-full h-full shadow-[0_4px_10px_rgba(19,111,154,0.2)]">
                    Simpan Transaksi
                </button>
            </div>
        </form>
    </section>

    <!-- KARTU WADAH: TAGIHAN SUPPLIER YANG BELUM LUNAS -->
    <section class="bg-white rounded-2xl shadow-[0_2px_12px_rgba(44,62,80,0.04)] p-6 mb-6">
        <h2 class="text-[15px] font-bold text-[#2C3E50] mb-2">Tagihan Supplier yang Belum Lunas</h2>
        <p class="text-xs text-[#7F7F7F] mb-5">Daftar pesanan pembelian dengan sisa pembayaran terbuka yang memerlukan penyelesaian.</p>

        <div class="overflow-x-auto w-full rounded-xl border border-slate-100">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8FBFD]">
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap">NO. PO</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap">SUPPLIER</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap">PROYEK</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap">TOTAL PO</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap">SISA TAGIHAN</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap">CATAT PEMBAYARAN</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($unpaidOrders as $po)
                        <tr class="text-[13px] text-[#2C3E50] border-b border-slate-50 hover:bg-[#F8FBFD]/50 transition-colors">
                            <td class="px-4 py-3 font-semibold text-[#136F9A] whitespace-nowrap">{{ $po->po_number }}</td>
                            <td class="px-4 py-3 font-medium text-[#2C3E50] whitespace-nowrap">{{ $po->supplier->name }}</td>
                            <td class="px-4 py-3 text-slate-600 whitespace-nowrap">{{ $po->project->project_code }}</td>
                            <td class="px-4 py-3 tabular text-slate-600 whitespace-nowrap">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 tabular font-bold text-[#ED7132] whitespace-nowrap">Rp {{ number_format($po->total_amount - $po->paid_amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <form method="POST" action="{{ route('purchase-orders.pay', $po) }}" class="flex items-center gap-2">
                                    @csrf
                                    <input class="w-32 rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-1.5 text-[12px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" type="number" min="1" max="{{ $po->total_amount - $po->paid_amount }}" step="0.01" name="amount" placeholder="Nominal" required>
                                    <input class="w-32 rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-1.5 text-[12px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" type="date" name="transaction_date" value="{{ now()->toDateString() }}" required>
                                    <input type="hidden" name="reference" value="{{ $po->po_number }}">
                                    <button type="submit" class="bg-[#136F9A] text-white rounded-lg px-3.5 py-1.5 text-xs font-bold hover:bg-[#136F9A]/90 transition-colors shadow-xs shrink-0">
                                        Bayar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-[13px] text-[#7F7F7F] font-medium">Tidak ada tagihan supplier yang terbuka.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <!-- KARTU WADAH: FILTER & TABEL RIWAYAT ARUS KAS -->
    <section class="bg-white rounded-2xl shadow-[0_2px_12px_rgba(44,62,80,0.04)] p-6 mb-6">
        <h2 class="text-[15px] font-bold text-[#2C3E50] mb-2">Riwayat Transaksi Arus Kas</h2>
        <p class="text-xs text-[#7F7F7F] mb-5">Daftar rekonsiliasi arus kas masuk dan keluar di seluruh proyek.</p>

        <!-- FILTER BAR -->
        <form method="GET" action="{{ route('finance.index') }}" class="flex flex-col sm:flex-row gap-4 mb-6 items-center">
            <select class="flex-1 w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="project_id">
                <option value="">Semua proyek</option>
                @foreach($projects as $p)
                    <option value="{{ $p->id }}" @selected($projectId == $p->id)>{{ $p->project_code }} - {{ $p->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-[#2C3E50] text-white rounded-lg px-6 py-2.5 text-xs font-bold hover:bg-[#2C3E50]/90 transition-colors w-full sm:w-auto shrink-0">
                Filter
            </button>
        </form>

        <div class="overflow-x-auto w-full rounded-xl border border-slate-100">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8FBFD]">
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap">TANGGAL</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap">PROYEK</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap">JENIS</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap">KATEGORI</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap">KETERANGAN</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap">REFERENSI</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 text-right whitespace-nowrap">NOMINAL</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($flows as $f)
                        <tr class="text-[13px] text-[#2C3E50] border-b border-slate-50 hover:bg-[#F8FBFD]/50 transition-colors">
                            <td class="px-4 py-3 text-slate-600 whitespace-nowrap">{{ $f->transaction_date->format('d M Y') }}</td>
                            <td class="px-4 py-3 font-medium text-[#2C3E50] whitespace-nowrap">{{ $f->project?->project_code ?? 'Umum' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <x-badge variant="{{ $f->type === 'in' ? 'success' : 'warning' }}">
                                    {{ strtoupper($f->type) }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">{{ $f->category }}</td>
                            <td class="px-4 py-3 max-w-xs truncate">{{ $f->description }}</td>
                            <td class="px-4 py-3 text-slate-500 font-mono text-xs whitespace-nowrap">{{ $f->reference ?? '-' }}</td>
                            <td class="px-4 py-3 text-right tabular font-bold {{ $f->type === 'in' ? 'text-[#27B1AE]' : 'text-[#2C3E50]' }} whitespace-nowrap">
                                Rp {{ number_format($f->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-[13px] text-[#7F7F7F] font-medium">Belum ada transaksi arus kas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($flows->hasPages())
            <div class="mt-5">
                {{ $flows->links() }}
            </div>
        @endif
    </section>
@endsection

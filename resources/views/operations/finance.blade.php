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
        <h1 class="text-2xl lg:text-[28px] font-bold text-[#1E293B] dark:text-[#F4F4F5] tracking-tight">Arus Kas & Integrasi Keuangan</h1>
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
    <section class="bg-white dark:bg-[#18181B] rounded-lg rounded-2xl border border-[#E5E7EB] dark:border-[#27272A] shadow-none p-5 mb-6">
        <h2 class="text-[15px] font-bold text-[#0B2545] dark:text-[#F4F4F5] mb-2">Catat Arus Kas Manual</h2>
        <p class="text-xs text-[#64748B] mb-5">Catat hanya transaksi yang belum tercatat melalui pembayaran PO, pembayaran upah, atau anggaran yang sudah dicatat agar laporan tidak menghitung biaya dua kali.</p>

        <form method="POST" action="{{ route('finance.store') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-[#64748B] mb-1.5">Proyek</label>
                <select class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="project_id">
                    <option value="">Kas umum / tanpa proyek</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}">{{ $p->project_code }} - {{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#64748B] mb-1.5">Jenis Transaksi</label>
                <select class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="type">
                    <option value="in">Kas Masuk</option>
                    <option value="out">Kas Keluar</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#64748B] mb-1.5">Kategori</label>
                <input class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="category" placeholder="Kategori" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#64748B] mb-1.5">Keterangan</label>
                <input class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="description" placeholder="Keterangan" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#64748B] mb-1.5">Nominal (Rp)</label>
                <input class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" type="number" min="1" step="0.01" name="amount" placeholder="Nominal" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#64748B] mb-1.5">Tanggal Transaksi</label>
                <input class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" type="date" name="transaction_date" value="{{ now()->toDateString() }}" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#64748B] mb-1.5">Referensi</label>
                <input class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="reference" placeholder="Referensi">
            </div>
            <div class="flex flex-col justify-end">
                <label class="hidden sm:block text-xs font-semibold text-transparent mb-1.5 select-none">&nbsp;</label>
                <button type="submit" class="bg-[#136F9A] text-white rounded-md px-5 py-2.5 text-[13px] font-bold hover:bg-[#136F9A]/90 transition-colors w-full h-full shadow-none">
                    Simpan Transaksi
                </button>
            </div>
        </form>
    </section>

    <!-- KARTU WADAH: TAGIHAN SUPPLIER YANG BELUM LUNAS -->
    <section class="bg-white dark:bg-[#18181B] rounded-lg border border-[#E5E7EB] dark:border-[#27272A] shadow-none p-5 mb-6">
        <h2 class="text-[15px] font-bold text-[#0B2545] dark:text-[#F4F4F5] mb-4">Tagihan Supplier yang Belum Lunas</h2>

        <div class="overflow-x-auto w-full rounded-lg border border-[#E5E7EB] dark:border-[#27272A]">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8FBFD] dark:bg-[#27272A]">
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] whitespace-nowrap">No. PO</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] whitespace-nowrap">Supplier</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] whitespace-nowrap">Proyek</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] whitespace-nowrap">Total PO</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] whitespace-nowrap">Sisa Tagihan</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] whitespace-nowrap">Catat Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($unpaidOrders as $po)
                        <tr class="bg-white dark:bg-transparent text-[13px] text-[#2C3E50] dark:text-slate-300 border-b border-slate-50 dark:border-[#27272A] hover:bg-[#F8FBFD]/50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-4 py-3 font-semibold text-[#136F9A] whitespace-nowrap">{{ $po->po_number }}</td>
                            <td class="px-4 py-3 font-medium text-[#2C3E50] whitespace-nowrap">{{ $po->supplier->name }}</td>
                            <td class="px-4 py-3 text-slate-600 whitespace-nowrap">{{ $po->project->project_code }}</td>
                            <td class="px-4 py-3 tabular text-slate-600 whitespace-nowrap">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 tabular font-bold text-[#ED7132] whitespace-nowrap">Rp {{ number_format($po->total_amount - $po->paid_amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <form method="POST" action="{{ route('purchase-orders.pay', $po) }}" class="flex items-center gap-2">
                                    @csrf
                                    <input class="w-32 rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3 py-1.5 text-[12px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" type="number" min="1" max="{{ $po->total_amount - $po->paid_amount }}" step="0.01" name="amount" placeholder="Nominal" required>
                                    <input class="w-32 rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3 py-1.5 text-[12px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" type="date" name="transaction_date" value="{{ now()->toDateString() }}" required>
                                    <input type="hidden" name="reference" value="{{ $po->po_number }}">
                                    <button type="submit" class="bg-[#136F9A] text-white rounded-md px-3.5 py-1.5 text-xs font-bold hover:bg-[#136F9A]/90 transition-colors shadow-none shrink-0">
                                        Bayar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-[13px] text-[#64748B] font-medium">Tidak ada tagihan supplier yang terbuka.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <!-- KARTU WADAH: FILTER & TABEL RIWAYAT ARUS KAS -->
    <section class="bg-white dark:bg-[#18181B] rounded-lg border border-[#E5E7EB] dark:border-[#27272A] shadow-none p-5 mb-6">
        <h2 class="text-[15px] font-bold text-[#0B2545] dark:text-[#F4F4F5] mb-4">Riwayat Transaksi Arus Kas</h2>

        <!-- FILTER BAR -->
        <form method="GET" action="{{ route('finance.index') }}" class="flex flex-col sm:flex-row gap-4 mb-6 items-center">
            <select class="flex-1 w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="project_id">
                <option value="">Semua proyek</option>
                @foreach($projects as $p)
                    <option value="{{ $p->id }}" @selected($projectId == $p->id)>{{ $p->project_code }} - {{ $p->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-[#2C3E50] text-white rounded-md px-6 py-2.5 text-xs font-bold hover:bg-[#2C3E50]/90 transition-colors w-full sm:w-auto shrink-0 shadow-none">
                Filter
            </button>
        </form>

        <div class="overflow-x-auto w-full rounded-lg border border-[#E5E7EB] dark:border-[#27272A]">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8FBFD] dark:bg-[#27272A]">
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] whitespace-nowrap">Tanggal</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] whitespace-nowrap">Proyek</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] whitespace-nowrap">Jenis</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] whitespace-nowrap">Kategori</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] whitespace-nowrap">Keterangan</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] whitespace-nowrap">Referensi</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] text-right whitespace-nowrap">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($flows as $f)
                        <tr class="bg-white dark:bg-transparent text-[13px] text-[#2C3E50] dark:text-slate-300 border-b border-slate-50 dark:border-[#27272A] hover:bg-[#F8FBFD]/50 dark:hover:bg-white/5 transition-colors">
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
                            <td colspan="7" class="py-10 text-center text-[13px] text-[#64748B] font-medium">Belum ada transaksi arus kas.</td>
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

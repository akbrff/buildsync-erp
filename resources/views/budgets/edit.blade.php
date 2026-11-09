@extends('layouts.app')

@section('title', 'Edit Biaya: ' . $budget->formatted_voucher)

@section('breadcrumbs')
<x-breadcrumb :links="[
    ['label' => 'Dashboard',        'href' => route('dashboard')],
    ['label' => 'Anggaran & Biaya', 'href' => route('budgets.index', ['project_id' => $budget->project_id])],
    ['label' => $budget->formatted_voucher, 'href' => route('budgets.show', $budget)],
    ['label' => 'Edit',             'href' => '#'],
]"/>
@endsection

@section('content')
    <x-page-header
        title="Edit Pengeluaran: {{ $budget->formatted_voucher }}"
        subtitle="Perbarui nominal, kategori, verifikasi kas, atau dokumen bukti pengeluaran proyek {{ $budget->project->project_code }}.">
        <a href="{{ route('budgets.show', $budget) }}"
           class="inline-flex items-center gap-1.5 rounded px-3 font-medium transition-colors"
           style="height:2.25rem; font-size:.8125rem; border:1px solid #e2e8f0; background:#fff; color:#475569;"
           onmouseover="this.style.background='#f8fafc';" onmouseout="this.style.background='#fff';">
            &larr; Kembali ke Detail
        </a>
    </x-page-header>

    <x-flash-alert/>

    <div class="max-w-4xl rounded-md p-5" style="border:1px solid #e2e8f0; background:#fff;">
        <form method="POST" action="{{ route('budgets.update', $budget) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid gap-4 sm:grid-cols-2">
                {{-- Proyek --}}
                <div class="sm:col-span-2">
                    <label for="project_id" class="erp-label">Proyek Konstruksi <span class="text-red-500">*</span></label>
                    <select id="project_id" name="project_id" class="erp-input @error('project_id') erp-input-error @enderror">
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}" @selected(old('project_id', $budget->project_id) == $project->id)>
                                {{ $project->project_code }} – {{ $project->name }} (Pagu: Rp {{ number_format($project->budget, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                    @error('project_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Tanggal Pengeluaran --}}
                <div>
                    <label for="date" class="erp-label">Tanggal Transaksi Kas <span class="text-red-500">*</span></label>
                    <input id="date" type="date" name="date" value="{{ old('date', $budget->date?->toDateString()) }}"
                           class="erp-input tabular @error('date') erp-input-error @enderror">
                    @error('date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Kategori Biaya --}}
                <div>
                    <label for="category" class="erp-label">Kategori Biaya <span class="text-red-500">*</span></label>
                    <select id="category" name="category" class="erp-input @error('category') erp-input-error @enderror">
                        @foreach(['Material', 'Upah Tenaga Kerja', 'Sewa Alat', 'Operasional'] as $cat)
                            <option value="{{ $cat }}" @selected(old('category', $budget->category) == $cat)>{{ $cat }}</option>
                        @endforeach
                    </select>
                    @error('category')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Nominal Pengeluaran --}}
                <div>
                    <label for="amount" class="erp-label">Nominal Pengeluaran (Rp) <span class="text-red-500">*</span></label>
                    <x-erp-currency-input name="amount" id="amount" :value="old('amount', $budget->amount)" required placeholder="4.000.000" />
                    @error('amount')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Status Verifikasi Kas --}}
                <div>
                    <label for="status" class="erp-label">Status Verifikasi Kas</label>
                    <select id="status" name="status" class="erp-input @error('status') erp-input-error @enderror">
                        <option value="approved" @selected(old('status', $budget->status) == 'approved')>Disetujui (Approved)</option>
                        <option value="pending" @selected(old('status', $budget->status) == 'pending')>Menunggu Approval (Pending)</option>
                        <option value="rejected" @selected(old('status', $budget->status) == 'rejected')>Ditolak (Rejected)</option>
                    </select>
                    @error('status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Upload Bukti / Struk Baru --}}
                <div class="sm:col-span-2">
                    <label for="receipt" class="erp-label">Lampiran Bukti Struk / Kwitansi Fisik</label>
                    @if ($budget->receipt)
                        <div class="mb-2 flex items-center gap-2 rounded px-2.5 py-1.5 text-xs" style="background:#f8fafc; border:1px solid #e2e8f0;">
                            <span class="text-slate-500">Berkas saat ini:</span>
                            <a href="{{ asset('storage/' . $budget->receipt) }}" target="_blank" class="font-semibold text-blue-600 hover:underline">
                                Lihat Berkas &rarr;
                            </a>
                        </div>
                    @endif
                    <input id="receipt" type="file" name="receipt" accept=".jpg,.jpeg,.png,.pdf"
                           class="erp-input py-1 text-xs file:mr-3 file:py-0.5 file:px-2.5 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                    <p class="mt-1 text-[11px] text-slate-400">Pilih berkas baru jika ingin mengganti bukti transaksi lama (Maksimal 5MB).</p>
                    @error('receipt')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Keterangan --}}
                <div class="sm:col-span-2">
                    <label for="description" class="erp-label">Uraian / Keterangan Pengeluaran <span class="text-red-500">*</span></label>
                    <textarea id="description" name="description" rows="2"
                              class="w-full rounded-md border border-slate-300 p-2.5 text-xs text-slate-800 transition focus:border-blue-600 focus:outline-none focus:ring-1 focus:ring-blue-600 @error('description') erp-input-error @enderror">{{ old('description', $budget->description) }}</textarea>
                    @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-2.5 pt-4" style="border-top:1px solid #f1f5f9;">
                <a href="{{ route('budgets.show', $budget) }}"
                   class="inline-flex items-center justify-center rounded px-3.5 font-medium transition-colors"
                   style="height:2.25rem; font-size:.8125rem; border:1px solid #cbd5e1; background:#fff; color:#475569;"
                   onmouseover="this.style.background='#f8fafc';" onmouseout="this.style.background='#fff';">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center justify-center rounded px-4 font-semibold text-white transition-colors"
                        style="height:2.25rem; font-size:.8125rem; background:#2563eb; border:1px solid #1d4ed8;"
                        onmouseover="this.style.background='#1d4ed8';" onmouseout="this.style.background='#2563eb';">
                    Perbarui Pengeluaran Kas
                </button>
            </div>
        </form>
    </div>
@endsection

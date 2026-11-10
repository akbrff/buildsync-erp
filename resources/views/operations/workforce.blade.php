@extends('layouts.app')

@section('title', 'Penugasan & Kehadiran SDM')

@section('breadcrumbs')
    <x-breadcrumb :links="[
        ['label' => 'Dashboard', 'href' => route('dashboard')],
        ['label' => 'Penugasan SDM', 'href' => '#'],
    ]" />
@endsection

@section('content')
    <div class="mb-6">
        <h1 class="text-xl font-bold text-[#2C3E50] mb-1">Penugasan & Kehadiran Tenaga Kerja</h1>
        <p class="text-xs text-[#7F7F7F] mb-6">Alokasi pekerja per proyek dan pekerjaan, pencatatan jam kerja, serta realisasi biaya upah.</p>
    </div>

    <x-flash-alert />

    <div class="grid gap-6 xl:grid-cols-2 mb-6">
        @if(auth()->user()->hasRole('project_manager'))
            <!-- KARTU BUAT PENUGASAN -->
            <section class="bg-white rounded-2xl shadow-[0_2px_12px_rgba(44,62,80,0.04)] p-6 mb-6">
                <h2 class="text-[15px] font-bold text-[#2C3E50] mb-5">Buat Penugasan</h2>
                <form method="POST" action="{{ route('workforce.assign') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-[#7F7F7F] mb-1.5">Pilih Pekerja <span class="text-rose-500">*</span></label>
                        <select class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="worker_id" required>
                            <option value="">Pilih pekerja</option>
                            @foreach($workers as $w)
                                <option value="{{ $w->id }}">{{ $w->worker_code }} - {{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#7F7F7F] mb-1.5">Pilih Proyek <span class="text-rose-500">*</span></label>
                        <select class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="project_id" required>
                            <option value="">Pilih proyek</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}">{{ $p->project_code }} - {{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-[#7F7F7F] mb-1.5">Nama Pekerjaan / Tugas <span class="text-rose-500">*</span></label>
                        <input class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="task_name" placeholder="Nama pekerjaan / tugas" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#7F7F7F] mb-1.5">Tanggal Mulai <span class="text-rose-500">*</span></label>
                        <input class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" type="date" name="start_date" value="{{ now()->toDateString() }}" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#7F7F7F] mb-1.5">Tanggal Selesai (Opsional)</label>
                        <input class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" type="date" name="end_date">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-[#7F7F7F] mb-1.5">Tahapan Jadwal Proyek (Opsional)</label>
                        <select class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="project_schedule_id">
                            <option value="">Tahapan jadwal (opsional)</option>
                            @foreach($projects as $p)
                                @foreach($p->schedules as $s)
                                    <option value="{{ $s->id }}">{{ $p->project_code }} — {{ $s->name }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2 pt-2">
                        <button type="submit" class="bg-[#136F9A] text-white rounded-lg px-5 py-2.5 text-xs font-bold hover:bg-[#136F9A]/90 transition-colors w-full sm:w-max">
                            Simpan Penugasan
                        </button>
                    </div>
                </form>
            </section>
        @endif

        @if(auth()->user()->hasRole('project_manager','field_staff'))
            <!-- KARTU CATAT KEHADIRAN / JAM KERJA -->
            <section class="bg-white rounded-2xl shadow-[0_2px_12px_rgba(44,62,80,0.04)] p-6 mb-6">
                <h2 class="text-[15px] font-bold text-[#2C3E50] mb-5">Catat Kehadiran / Jam Kerja</h2>
                <form method="POST" action="{{ route('workforce.attendance') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @csrf
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-[#7F7F7F] mb-1.5">Pilih Penugasan <span class="text-rose-500">*</span></label>
                        <select class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="worker_assignment_id" required>
                            <option value="">Pilih penugasan</option>
                            @foreach($assignments as $a)
                                <option value="{{ $a->id }}">{{ $a->worker->name }} — {{ $a->project->project_code }} / {{ $a->task_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-[#7F7F7F] mb-1.5">Tanggal Bekerja <span class="text-rose-500">*</span></label>
                        <input class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" type="date" name="work_date" value="{{ now()->toDateString() }}" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#7F7F7F] mb-1.5">Jam Kerja <span class="text-rose-500">*</span></label>
                        <input class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" type="number" name="hours" min="0.5" max="24" step="0.5" value="8" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#7F7F7F] mb-1.5">Ekuivalen Hari <span class="text-rose-500">*</span></label>
                        <input class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-[13px] text-[#2C3E50] placeholder-slate-400 focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" type="number" name="days_worked" min="0.1" max="1" step="0.1" value="1" required>
                    </div>
                    <div class="sm:col-span-2 pt-2">
                        <button type="submit" class="bg-[#136F9A] text-white rounded-lg px-5 py-2.5 text-xs font-bold hover:bg-[#136F9A]/90 transition-colors w-full sm:w-max">
                            Simpan Kehadiran
                        </button>
                    </div>
                </form>
            </section>
        @endif
    </div>

    <!-- KARTU PENUGASAN AKTIF -->
    <section class="bg-white rounded-2xl shadow-[0_2px_12px_rgba(44,62,80,0.04)] p-6 mb-6">
        <h2 class="text-[15px] font-bold text-[#2C3E50] mb-5">Penugasan Aktif</h2>
        <div class="overflow-x-auto w-full rounded-xl border border-slate-100">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8FBFD]">
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap">PEKERJA</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap">PROYEK</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100">TUGAS</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100">TAHAPAN</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap">PERIODE</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 text-center whitespace-nowrap">KEHADIRAN</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $a)
                        <tr class="text-[13px] text-[#2C3E50] border-b border-slate-50 hover:bg-[#F8FBFD]/50 transition-colors">
                            <td class="px-4 py-3 font-medium text-[#2C3E50] whitespace-nowrap">{{ $a->worker->name }}</td>
                            <td class="px-4 py-3 whitespace-nowrap font-mono text-xs text-slate-600">{{ $a->project->project_code }}</td>
                            <td class="px-4 py-3 font-medium text-[#2C3E50]">{{ $a->task_name }}</td>
                            <td class="px-4 py-3 text-slate-600 text-xs">{{ $a->schedule?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-600 text-xs whitespace-nowrap">{{ $a->start_date->format('d/m/Y') }} - {{ $a->end_date?->format('d/m/Y') ?? 'Berjalan' }}</td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-[#2C3E50]">{{ $a->attendances_count }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-[13px] text-[#7F7F7F] font-medium">Belum ada penugasan aktif.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($assignments->hasPages())
            <div class="mt-4">
                {{ $assignments->links() }}
            </div>
        @endif
    </section>

    <!-- KARTU KEHADIRAN & PEMBAYARAN UPAH -->
    <section class="bg-white rounded-2xl shadow-[0_2px_12px_rgba(44,62,80,0.04)] p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-5">
            <h2 class="text-[15px] font-bold text-[#2C3E50]">
                Kehadiran & Pembayaran Upah
                <span class="font-normal text-xs text-[#ED7132] ml-2">· Pending Rp {{ number_format($pendingPayroll, 0, ',', '.') }}</span>
            </h2>
        </div>
        <div class="overflow-x-auto w-full rounded-xl border border-slate-100">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8FBFD]">
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap">TANGGAL</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap">PEKERJA</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap">PROYEK</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 text-center whitespace-nowrap">JAM</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 text-center whitespace-nowrap">HARI</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap">BIAYA</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 text-center whitespace-nowrap">STATUS</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $a)
                        <tr class="text-[13px] text-[#2C3E50] border-b border-slate-50 hover:bg-[#F8FBFD]/50 transition-colors">
                            <td class="px-4 py-3 text-slate-600 text-xs whitespace-nowrap">{{ $a->work_date->format('d M Y') }}</td>
                            <td class="px-4 py-3 font-medium text-[#2C3E50] whitespace-nowrap">{{ $a->worker->name }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-slate-600 whitespace-nowrap">{{ $a->project->project_code }}</td>
                            <td class="px-4 py-3 text-center tabular whitespace-nowrap">{{ $a->hours }}</td>
                            <td class="px-4 py-3 text-center tabular whitespace-nowrap">{{ $a->days_worked }}</td>
                            <td class="px-4 py-3 tabular font-semibold text-[#2C3E50] whitespace-nowrap">Rp {{ number_format($a->amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <x-badge variant="{{ $a->status === 'paid' ? 'success' : 'warning' }}">{{ ucfirst($a->status) }}</x-badge>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($a->status === 'pending' && auth()->user()->hasRole('project_manager', 'finance'))
                                    <form method="POST" action="{{ route('workforce.attendance.pay', $a) }}">
                                        @csrf
                                        <button type="submit" class="text-xs font-semibold text-[#136F9A] hover:text-[#2C3E50] transition-colors underline">Bayar upah</button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-500">{{ $a->paid_at ? $a->paid_at->format('d/m/Y') : '-' }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-[13px] text-[#7F7F7F] font-medium">Belum ada catatan kehadiran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($attendances->hasPages())
            <div class="mt-4">
                {{ $attendances->links() }}
            </div>
        @endif
    </section>
@endsection

@extends('layouts.app')

@section('title', 'Manajemen Hak Akses')

@section('breadcrumbs')
    <x-breadcrumb :links="[
        ['label' => 'Dashboard', 'href' => route('dashboard')],
        ['label' => 'Hak Akses', 'href' => '#'],
    ]" />
@endsection

@section('content')
    <x-page-header
        title="Manajemen Pengguna & Hak Akses"
        subtitle="Tetapkan peran pengguna sesuai tanggung jawab operasional BuildSync ERP."
    />

    <x-flash-alert />

    <!-- KARTU WADAH TABEL (CARD CONTAINER) -->
    <div class="bg-white rounded-2xl shadow-[0_2px_12px_rgba(44,62,80,0.04)] p-6 mb-6">
        <h2 class="text-[15px] font-bold text-[#2C3E50] mb-1">Manajemen Pengguna & Hak Akses</h2>
        <p class="text-xs text-[#7F7F7F] mb-5">Tetapkan peran pengguna sesuai tanggung jawab operasional BuildSync ERP.</p>

        <div class="overflow-x-auto w-full rounded-xl border border-slate-100">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8FBFD]">
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap">NAMA</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap">EMAIL</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap">ROLE SAAT INI</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#7F7F7F] border-b border-slate-100 whitespace-nowrap">UBAH ROLE</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr class="text-[13px] text-[#2C3E50] border-b border-slate-50 hover:bg-[#F8FBFD]/50 transition-colors">
                            <td class="px-4 py-3 font-medium text-[#2C3E50] whitespace-nowrap">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-[#136F9A]/10 text-[#136F9A] text-xs font-bold flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <span>{{ $u->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-600 text-xs whitespace-nowrap">{{ $u->email }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center bg-[#136F9A]/10 text-[#136F9A] px-2.5 py-1 rounded-md text-[11px] font-bold">
                                    {{ str_replace('_', ' ', ucfirst($u->role)) }}
                                </span>
                                @if($u->worker)
                                    <div class="text-[11px] font-mono text-slate-500 mt-0.5">PIC: {{ $u->worker->name }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <form method="POST" action="{{ route('users.role.update', $u) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select class="rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2 text-[12px] text-[#2C3E50] focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="role">
                                        @foreach(['superadmin' => 'Superadmin', 'project_manager' => 'Project Manager', 'logistics' => 'Logistics', 'finance' => 'Finance', 'field_staff' => 'Field Staff'] as $key => $label)
                                            <option value="{{ $key }}" @selected($u->role === $key)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <select class="rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2 text-[12px] text-[#2C3E50] focus:bg-white focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="worker_id">
                                        <option value="">Tautkan pekerja (khusus Field Staff)</option>
                                        @foreach($workers as $worker)
                                            <option value="{{ $worker->id }}" @selected($u->worker_id == $worker->id)>{{ $worker->worker_code }} - {{ $worker->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="bg-[#136F9A] text-white rounded-lg px-4 py-2 text-xs font-bold hover:bg-[#136F9A]/90 transition-colors shadow-[0_4px_10px_rgba(19,111,154,0.2)] shrink-0">
                                        Simpan
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-10 text-center text-[13px] text-[#7F7F7F] font-medium">Belum ada pengguna terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="mt-5">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection

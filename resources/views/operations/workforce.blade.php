@extends('layouts.app')

@section('title', __('workforce.workforce_title'))

@section('breadcrumbs')
    <x-breadcrumb :links="[
        ['label' => __('common.dashboard'), 'href' => route('dashboard')],
        ['label' => __('workforce.workforce_nav'), 'href' => '#'],
    ]" />
@endsection

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl lg:text-[28px] font-bold text-[#1E293B] dark:text-[#F4F4F5] tracking-tight">{{ __('workforce.workforce_title') }}</h1>
    </div>

    <x-flash-alert />

    <div class="grid gap-6 xl:grid-cols-2 mb-6">
        @if(auth()->user()->hasRole('project_manager'))
            <!-- KARTU BUAT PENUGASAN -->
            <section class="bg-white dark:bg-[#18181B] rounded-lg rounded-2xl border border-[#E5E7EB] dark:border-[#27272A] shadow-none p-5 mb-6">
                <h2 class="text-base font-bold text-[#1E293B] dark:text-[#F4F4F5] mb-5">{{ __('workforce.create_assignment') }}</h2>
                <form method="POST" action="{{ route('workforce.assign') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-[#64748B] dark:text-[#A1A1AA] mb-1.5">{{ __('workforce.select_worker') }} <span class="text-rose-500">*</span></label>
                        <select class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="worker_id" required>
                            <option value="">{{ __('workforce.select_worker_placeholder') }}</option>
                            @foreach($workers as $w)
                                <option value="{{ $w->id }}">{{ $w->worker_code }} - {{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#64748B] dark:text-[#A1A1AA] mb-1.5">{{ __('workforce.select_project') }} <span class="text-rose-500">*</span></label>
                        <select class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="project_id" required>
                            <option value="">{{ __('workforce.select_project_placeholder') }}</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}">{{ $p->project_code }} - {{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-[#64748B] dark:text-[#A1A1AA] mb-1.5">{{ __('workforce.task_name') }} <span class="text-rose-500">*</span></label>
                        <input class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="task_name" placeholder="{{ __('workforce.task_placeholder') }}" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#64748B] dark:text-[#A1A1AA] mb-1.5">{{ __('workforce.start_date') }} <span class="text-rose-500">*</span></label>
                        <input class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" type="date" name="start_date" value="{{ now()->toDateString() }}" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#64748B] dark:text-[#A1A1AA] mb-1.5">{{ __('workforce.end_date_optional') }}</label>
                        <input class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" type="date" name="end_date">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-[#64748B] dark:text-[#A1A1AA] mb-1.5">{{ __('workforce.schedule_optional') }}</label>
                        <select class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="project_schedule_id">
                            <option value="">{{ __('workforce.schedule_placeholder') }}</option>
                            @foreach($projects as $p)
                                @foreach($p->schedules as $s)
                                    <option value="{{ $s->id }}">{{ $p->project_code }} — {{ $s->name }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2 pt-2">
                        <button type="submit" class="bg-[#136F9A] text-white rounded-md px-5 py-2.5 text-xs font-bold hover:bg-[#136F9A]/90 transition-colors w-full sm:w-max shadow-none">
                            {{ __('workforce.save_assignment') }}
                        </button>
                    </div>
                </form>
            </section>
        @endif

        @if(auth()->user()->hasRole('project_manager','field_staff'))
            <!-- KARTU CATAT KEHADIRAN / JAM KERJA -->
            <section class="bg-white dark:bg-[#18181B] rounded-lg border border-[#E5E7EB] dark:border-[#27272A] shadow-none p-5 mb-6">
                <h2 class="text-[15px] font-bold text-[#0B2545] dark:text-[#F4F4F5] mb-5">{{ __('workforce.log_attendance') }}</h2>
                <form method="POST" action="{{ route('workforce.attendance') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @csrf
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-[#64748B] dark:text-[#A1A1AA] mb-1.5">{{ __('workforce.select_assignment') }} <span class="text-rose-500">*</span></label>
                        <select class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" name="worker_assignment_id" required>
                            <option value="">{{ __('workforce.select_assignment_placeholder') }}</option>
                            @foreach($assignments as $a)
                                <option value="{{ $a->id }}">{{ $a->worker->name }} — {{ $a->project->project_code }} / {{ $a->task_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-[#64748B] dark:text-[#A1A1AA] mb-1.5">{{ __('workforce.work_date') }} <span class="text-rose-500">*</span></label>
                        <input class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" type="date" name="work_date" value="{{ now()->toDateString() }}" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#64748B] dark:text-[#A1A1AA] mb-1.5">{{ __('workforce.work_hours') }} <span class="text-rose-500">*</span></label>
                        <input class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" type="number" name="hours" min="0.5" max="24" step="0.5" value="8" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#64748B] dark:text-[#A1A1AA] mb-1.5">{{ __('workforce.day_equivalent') }} <span class="text-rose-500">*</span></label>
                        <input class="w-full rounded-md border border-slate-200 dark:border-[#27272A] bg-slate-50/50 dark:bg-[#27272A] px-3.5 py-2.5 text-[13px] text-[#2C3E50] dark:text-[#F4F4F5] placeholder-slate-400 focus:bg-white dark:focus:bg-[#0E223D] focus:border-[#136F9A] focus:ring-1 focus:ring-[#136F9A] transition-all" type="number" name="days_worked" min="0.1" max="1" step="0.1" value="1" required>
                    </div>
                    <div class="sm:col-span-2 pt-2">
                        <button type="submit" class="bg-[#136F9A] text-white rounded-md px-5 py-2.5 text-xs font-bold hover:bg-[#136F9A]/90 transition-colors w-full sm:w-max shadow-none">
                            {{ __('workforce.save_attendance') }}
                        </button>
                    </div>
                </form>
            </section>
        @endif
    </div>

    <!-- KARTU PENUGASAN AKTIF -->
    <section class="bg-white dark:bg-[#18181B] rounded-lg border border-[#E5E7EB] dark:border-[#27272A] shadow-none p-5 mb-6">
        <h2 class="text-[15px] font-bold text-[#0B2545] dark:text-[#F4F4F5] mb-5">{{ __('workforce.active_assignments') }}</h2>
        <div class="overflow-x-auto w-full rounded-lg border border-[#E5E7EB] dark:border-[#27272A]">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8FBFD] dark:bg-[#27272A]">
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] whitespace-nowrap">{{ __('workforce.worker_col') }}</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] whitespace-nowrap">{{ __('workforce.project_col') }}</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A]">{{ __('workforce.task_col') }}</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A]">{{ __('workforce.stage_col') }}</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] whitespace-nowrap">{{ __('workforce.period_col') }}</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] text-center whitespace-nowrap">{{ __('workforce.attendance_col') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $a)
                        <tr class="bg-white dark:bg-transparent text-[13px] text-[#2C3E50] dark:text-slate-300 border-b border-slate-50 dark:border-[#27272A] hover:bg-[#F8FBFD]/50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-4 py-3 font-medium text-[#2C3E50] dark:text-[#F4F4F5] whitespace-nowrap">{{ $a->worker->name }}</td>
                            <td class="px-4 py-3 whitespace-nowrap font-mono text-xs text-slate-600 dark:text-slate-400">{{ $a->project->project_code }}</td>
                            <td class="px-4 py-3 font-medium text-[#2C3E50] dark:text-[#F4F4F5]">{{ $a->task_name }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-400 text-xs">{{ $a->schedule?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-400 text-xs whitespace-nowrap">{{ $a->start_date->format('d/m/Y') }} - {{ $a->end_date?->format('d/m/Y') ?? __('workforce.ongoing') }}</td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 dark:bg-[#27272A] text-[#2C3E50] dark:text-slate-300">{{ $a->attendances_count }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-[13px] text-[#64748B] dark:text-slate-400 font-medium">{{ __('workforce.no_active_assignments') }}</td>
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
    <section class="bg-white dark:bg-[#18181B] rounded-lg border border-[#E5E7EB] dark:border-[#27272A] shadow-none p-5 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-5">
            <h2 class="text-[15px] font-bold text-[#0B2545] dark:text-[#F4F4F5]">
                {{ __('workforce.attendance_wages') }}
                <span class="font-normal text-xs text-[#ED7132] ml-2">{{ __('workforce.pending_payroll', ['amount' => number_format($pendingPayroll, 0, ',', '.')]) }}</span>
            </h2>
        </div>
        <div class="overflow-x-auto w-full rounded-lg border border-[#E5E7EB] dark:border-[#27272A]">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8FBFD] dark:bg-[#27272A]">
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] whitespace-nowrap">{{ __('common.date') }}</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] whitespace-nowrap">{{ __('workforce.worker_col') }}</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] whitespace-nowrap">{{ __('workforce.project_col') }}</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] text-center whitespace-nowrap">{{ __('workforce.hours_short') }}</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] text-center whitespace-nowrap">{{ __('workforce.days_short') }}</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] whitespace-nowrap">{{ __('workforce.cost_col') }}</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] text-center whitespace-nowrap">{{ __('workforce.status') }}</th>
                        <th class="px-4 py-3 text-[13px] font-medium text-[#64748B] dark:text-[#A1A1AA] border-b border-[#E5E7EB] dark:border-[#27272A] whitespace-nowrap">{{ __('workforce.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $a)
                        <tr class="bg-white dark:bg-transparent text-[13px] text-[#2C3E50] dark:text-slate-300 border-b border-slate-50 dark:border-[#27272A] hover:bg-[#F8FBFD]/50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-400 text-xs whitespace-nowrap">{{ $a->work_date->format('d M Y') }}</td>
                            <td class="px-4 py-3 font-medium text-[#2C3E50] dark:text-[#F4F4F5] whitespace-nowrap">{{ $a->worker->name }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-slate-600 dark:text-slate-400 whitespace-nowrap">{{ $a->project->project_code }}</td>
                            <td class="px-4 py-3 text-center tabular whitespace-nowrap">{{ $a->hours }}</td>
                            <td class="px-4 py-3 text-center tabular whitespace-nowrap">{{ $a->days_worked }}</td>
                            <td class="px-4 py-3 tabular font-semibold text-[#2C3E50] dark:text-[#F4F4F5] whitespace-nowrap">Rp {{ number_format($a->amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <x-badge variant="{{ $a->status === 'paid' ? 'success' : 'warning' }}">{{ ucfirst($a->status) }}</x-badge>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($a->status === 'pending' && auth()->user()->hasRole('project_manager', 'finance'))
                                    <form method="POST" action="{{ route('workforce.attendance.pay', $a) }}">
                                        @csrf
                                        <button type="submit" class="text-xs font-semibold text-[#136F9A] hover:text-[#2C3E50] dark:text-cyan-400 dark:hover:text-cyan-300 transition-colors underline">{{ __('workforce.pay_wages_action') }}</button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-500 dark:text-slate-400">{{ $a->paid_at ? $a->paid_at->format('d/m/Y') : '-' }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-[13px] text-[#64748B] dark:text-slate-400 font-medium">{{ __('workforce.no_attendance_records') }}</td>
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

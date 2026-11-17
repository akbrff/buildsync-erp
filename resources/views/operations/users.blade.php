@extends('layouts.app')

@section('title', __('users.title'))

@section('breadcrumbs')
    <x-breadcrumb :links="[
        ['label' => __('nav.dashboard'), 'href' => route('dashboard')],
        ['label' => __('users.title'), 'href' => '#'],
    ]" />
@endsection

@section('content')
    {{-- ── Page Header ────────────────────────────────────────── --}}
    <div class="mt-2 mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-[#0B2545] dark:text-[#F4F4F5]">{{ __('users.title') }}</h1>
            <p class="text-xs text-[#64748B] dark:text-[#A1A1AA] mt-0.5">
                {{ __('users.subtitle') }}
            </p>
        </div>
        <button type="button"
                onclick="openAddUserModal()"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-medium rounded-md bg-[#1D4E89] bg-[#136F9A] hover:bg-[#153A66] text-white transition-colors cursor-pointer shadow-none shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>{{ __('users.add_user') }}</span>
        </button>
    </div>

    <x-flash-alert />

    {{-- ── Statistik Pengguna ──────────────────────────────────── --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-6">
        <x-stat-card
            :label="__('users.total_users')"
            value="{{ number_format($stats['total']) }}"
            :note="__('users.note_total')"
            icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'/>"
        />
        <x-stat-card
            :label="__('users.active_users')"
            value="{{ number_format($stats['active']) }}"
            variant="success"
            :note="__('users.note_active')"
            icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'/>"
        />
        <x-stat-card
            :label="__('users.inactive_users')"
            value="{{ number_format($stats['inactive']) }}"
            variant="danger"
            :note="__('users.note_inactive')"
            icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636'/>"
        />
    </div>

    {{-- ── Filter Bar ─────────────────────────────────────────── --}}
    <div class="mb-4">
        <x-filter-bar action="{{ route('users.roles.index') }}" searchable="true" :searchPlaceholder="__('users.search_placeholder')">
            {{-- Filter Role --}}
            <select name="role" class="erp-select erp-select-sm w-44" onchange="this.form.submit()">
                <option value="">{{ __('users.all_roles') }}</option>
                <option value="superadmin" @selected(request('role') === 'superadmin')>{{ __('users.role_superadmin') }}</option>
                <option value="project_manager" @selected(request('role') === 'project_manager')>{{ __('users.role_project_manager') }}</option>
                <option value="logistics" @selected(request('role') === 'logistics')>{{ __('users.role_logistics') }}</option>
                <option value="finance" @selected(request('role') === 'finance')>{{ __('users.role_finance') }}</option>
                <option value="field_staff" @selected(request('role') === 'field_staff')>{{ __('users.role_field_staff') }}</option>
            </select>

            {{-- Filter Status --}}
            <select name="status" class="erp-select erp-select-sm w-36" onchange="this.form.submit()">
                <option value="">{{ __('users.all_statuses') }}</option>
                <option value="active" @selected(request('status') === 'active')>{{ __('common.active') }}</option>
                <option value="inactive" @selected(request('status') === 'inactive')>{{ __('common.inactive') }}</option>
            </select>

            @if(request()->filled('q') || request()->filled('role') || request()->filled('status'))
                <a href="{{ route('users.roles.index') }}" class="text-xs font-medium text-[#DC2626] hover:underline px-2 py-1">
                    {{ __('common.reset_filter') }}
                </a>
            @endif
        </x-filter-bar>
    </div>

    {{-- ── Kartu Tabel Pengguna ────────────────────────────────── --}}
    <div class="bg-white dark:bg-[#18181B] rounded-2xl border border-[#E5E7EB] dark:border-[#27272A] shadow-none overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-[#E5E7EB] dark:border-[#27272A] flex items-center justify-between">
            <h2 class="text-sm font-semibold text-[#0B2545] dark:text-[#F4F4F5]">{{ __('users.table_title') }}</h2>
            <span class="text-xs text-[#64748B] dark:text-[#A1A1AA]">
                {{ __('common.showing') }} {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} {{ __('common.of') }} {{ $users->total() }} {{ __('common.results') }}
            </span>
        </div>

        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8FBFD] dark:bg-[#27272A] border-b border-[#E5E7EB] dark:border-[#27272A]">
                        <th class="px-4 py-3 text-xs font-semibold text-[#64748B] dark:text-[#A1A1AA] uppercase tracking-wider">{{ __('users.table_user') }}</th>
                        <th class="px-4 py-3 text-xs font-semibold text-[#64748B] dark:text-[#A1A1AA] uppercase tracking-wider">{{ __('users.table_email') }}</th>
                        <th class="px-4 py-3 text-xs font-semibold text-[#64748B] dark:text-[#A1A1AA] uppercase tracking-wider">{{ __('users.table_role') }}</th>
                        <th class="px-4 py-3 text-xs font-semibold text-[#64748B] dark:text-[#A1A1AA] uppercase tracking-wider text-center">{{ __('users.table_status') }}</th>
                        <th class="px-4 py-3 text-xs font-semibold text-[#64748B] dark:text-[#A1A1AA] uppercase tracking-wider text-center">{{ __('users.table_actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E7EB] dark:divide-[#27272A]">
                    @forelse($users as $u)
                        @php
                            $isPrimary = $u->isPrimaryAdmin();
                            $isSelf = auth()->id() === $u->id;
                            $isLastAdmin = $u->isLastActiveAdmin();

                            $roleBadgeVariant = match($u->role) {
                                'superadmin'       => 'cobalt',
                                'project_manager'  => 'info',
                                'finance'          => 'warning',
                                'logistics'        => 'dusty',
                                'field_staff'      => 'neutral',
                                default            => 'neutral',
                            };
                            $roleName = match($u->role) {
                                'superadmin'       => __('users.role_superadmin'),
                                'project_manager'  => __('users.role_project_manager'),
                                'finance'          => __('users.role_finance'),
                                'logistics'        => __('users.role_logistics'),
                                'field_staff'      => __('users.role_field_staff'),
                                default            => ucfirst($u->role),
                            };
                        @endphp
                        <tr class="bg-white dark:bg-transparent text-[13px] text-[#0B2545] dark:text-slate-200 hover:bg-[#F8FBFD]/50 dark:hover:bg-white/5 transition-colors">
                            {{-- Nama & Avatar --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-[#1D4E89]/10 dark:bg-[#1D4E89]/25 text-[#1D4E89] dark:text-[#93C5FD] text-xs font-bold flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-[#0B2545] dark:text-[#F4F4F5] flex items-center gap-1.5">
                                             <span>{{ $u->name }}</span>
                                             @if($isPrimary)
                                                 <span class="inline-flex items-center px-1.5 py-0.2 text-[10px] font-semibold rounded bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20" title="{{ __('users.primary_badge') }}">
                                                     {{ __('users.primary_badge') }}
                                                 </span>
                                             @endif
                                             @if($isSelf)
                                                 <span class="inline-flex items-center px-1.5 py-0.2 text-[10px] font-semibold rounded bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20" title="{{ __('users.you_badge') }}">
                                                     {{ __('users.you_badge') }}
                                                 </span>
                                             @endif
                                         </div>
                                         <div class="text-[11px] text-[#64748B] dark:text-[#A1A1AA]">
                                             {{ __('users.registered_at', ['date' => $u->created_at?->format('d M Y') ?? '-']) }}
                                         </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Email --}}
                            <td class="px-4 py-3 text-xs text-[#64748B] dark:text-[#A1A1AA] whitespace-nowrap">
                                <span class="font-mono">{{ $u->email }}</span>
                            </td>

                            {{-- Role --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <x-badge :variant="$roleBadgeVariant">
                                    {{ $roleName }}
                                </x-badge>
                                @if($u->worker)
                                    <div class="text-[11px] text-[#64748B] dark:text-[#A1A1AA] mt-1 flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span>{{ __('users.pic_label', ['code' => $u->worker->worker_code, 'name' => $u->worker->name]) }}</span>
                                    </div>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                @if($u->status === 'active')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-xs font-medium bg-[#16A34A]/10 text-[#16A34A] dark:bg-[#16A34A]/20 dark:text-emerald-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#16A34A]"></span>
                                        {{ __('common.active') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-xs font-medium bg-[#DC2626]/10 text-[#DC2626] dark:bg-[#DC2626]/20 dark:text-rose-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#DC2626]"></span>
                                        {{ __('common.inactive') }}
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    {{-- Lihat Detail --}}
                                    <button type="button"
                                            onclick="openViewUserModal({{ json_encode([
                                                'id' => $u->id,
                                                'name' => $u->name,
                                                'email' => $u->email,
                                                'role' => $roleName,
                                                'raw_role' => $u->role,
                                                'status' => $u->status === 'active' ? __('common.active') : __('common.inactive'),
                                                'worker' => $u->worker ? ($u->worker->worker_code . ' – ' . $u->worker->name) : '-',
                                                'created_at' => $u->created_at?->format('d M Y H:i') ?? '-',
                                                'updated_at' => $u->updated_at?->format('d M Y H:i') ?? '-',
                                                'is_primary' => $isPrimary,
                                                'is_self' => $isSelf,
                                            ]) }})"
                                            class="p-1.5 rounded text-slate-500 hover:text-[#1D4E89] hover:bg-slate-100 dark:hover:bg-white/10 transition-colors"
                                            title="{{ __('users.view_user_details') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>

                                    {{-- Edit --}}
                                    <button type="button"
                                            onclick="openEditUserModal({{ json_encode([
                                                'id' => $u->id,
                                                'name' => $u->name,
                                                'email' => $u->email,
                                                'role' => $u->role,
                                                'worker_id' => $u->worker_id,
                                                'status' => $u->status,
                                                'is_primary' => $isPrimary,
                                                'is_self' => $isSelf,
                                                'is_last_admin' => $isLastAdmin,
                                            ]) }})"
                                            class="p-1.5 rounded text-slate-500 hover:text-amber-600 hover:bg-slate-100 dark:hover:bg-white/10 transition-colors"
                                            title="{{ __('users.edit_user') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>

                                    {{-- Reset Password --}}
                                    <button type="button"
                                            onclick="openResetPasswordModal({{ json_encode([
                                                'id' => $u->id,
                                                'name' => $u->name,
                                                'email' => $u->email,
                                            ]) }})"
                                            class="p-1.5 rounded text-slate-500 hover:text-blue-600 hover:bg-slate-100 dark:hover:bg-white/10 transition-colors"
                                            title="{{ __('users.reset_password') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                        </svg>
                                    </button>

                                    {{-- Toggle Aktif / Nonaktif --}}
                                    @if($isPrimary)
                                        <button type="button"
                                                disabled
                                                class="p-1.5 rounded text-slate-300 dark:text-zinc-600 cursor-not-allowed"
                                                title="{{ __('users.cannot_deactivate_primary') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                            </svg>
                                        </button>
                                    @elseif($isSelf)
                                        <button type="button"
                                                disabled
                                                class="p-1.5 rounded text-slate-300 dark:text-zinc-600 cursor-not-allowed"
                                                title="{{ __('users.cannot_deactivate_self') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                            </svg>
                                        </button>
                                    @elseif($isLastAdmin && $u->status === 'active')
                                        <button type="button"
                                                disabled
                                                class="p-1.5 rounded text-slate-300 dark:text-zinc-600 cursor-not-allowed"
                                                title="{{ __('users.last_admin_status_note') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                            </svg>
                                        </button>
                                    @else
                                        <button type="button"
                                                onclick="openStatusModal({{ json_encode([
                                                    'id' => $u->id,
                                                    'name' => $u->name,
                                                    'email' => $u->email,
                                                    'current_status' => $u->status,
                                                    'target_status' => $u->status === 'active' ? 'inactive' : 'active',
                                                ]) }})"
                                                class="p-1.5 rounded text-slate-500 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-white/10 transition-colors"
                                                title="{{ $u->status === 'active' ? __('users.deactivate_account') : __('users.activate_account') }}">
                                            @if($u->status === 'active')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            @endif
                                        </button>
                                    @endif

                                    {{-- Hapus Akun --}}
                                    @if($isPrimary || $isSelf || ($isLastAdmin && $u->status === 'active'))
                                        <button type="button"
                                                disabled
                                                class="p-1.5 rounded text-slate-300 dark:text-zinc-600 cursor-not-allowed"
                                                title="{{ $isPrimary ? __('users.cannot_delete_primary') : ($isSelf ? __('users.cannot_delete_self') : __('users.last_admin_status_note')) }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    @else
                                        <button type="button"
                                                onclick="openDeleteModal({{ json_encode([
                                                    'id' => $u->id,
                                                    'name' => $u->name,
                                                    'email' => $u->email,
                                                ]) }})"
                                                class="p-1.5 rounded text-slate-500 hover:text-red-600 hover:bg-slate-100 dark:hover:bg-white/10 transition-colors"
                                                title="{{ __('users.delete_user') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-xs text-[#64748B] dark:text-[#A1A1AA]">
                                {{ __('users.no_users_found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-5 py-4 border-t border-[#E5E7EB] dark:border-[#27272A]">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         MODALS DIALOG SYSTEM (VANILLA JS + SEMANTIC CSS)
         ═══════════════════════════════════════════════════════════ --}}

    {{-- 1. Modal Tambah Pengguna --}}
    <div id="addUserModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white dark:bg-[#18181B] rounded-xl border border-[#E5E7EB] dark:border-[#27272A] shadow-2xl w-full max-w-lg my-8 overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-[#E5E7EB] dark:border-[#27272A] flex items-center justify-between">
                <h3 class="text-base font-semibold text-[#0B2545] dark:text-[#F4F4F5]">{{ __('users.modal_add_title') }}</h3>
                <button type="button" onclick="closeModal('addUserModal')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form action="{{ route('users.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="erp-label">{{ __('users.full_name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Budi Santoso" class="erp-input">
                </div>
                <div>
                    <label class="erp-label">{{ __('users.email_address') }} <span class="text-red-500">*</span></label>
                    <input type="email" name="email" required placeholder="budi@buildsync.com" class="erp-input">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="erp-label">{{ __('users.table_role') }} <span class="text-red-500">*</span></label>
                        <select name="role" required class="erp-select" onchange="toggleWorkerPicker(this.value, 'addWorkerField')">
                            <option value="">{{ __('users.role_select') }}</option>
                            <option value="superadmin">{{ __('users.role_superadmin') }}</option>
                            <option value="project_manager">{{ __('users.role_project_manager') }}</option>
                            <option value="logistics">{{ __('users.role_logistics') }}</option>
                            <option value="finance">{{ __('users.role_finance') }}</option>
                            <option value="field_staff">{{ __('users.role_field_staff') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="erp-label">{{ __('users.initial_status') }}</label>
                        <select name="status" class="erp-select">
                            <option value="active" selected>{{ __('common.active') }}</option>
                            <option value="inactive">{{ __('common.inactive') }}</option>
                        </select>
                    </div>
                </div>
                <div id="addWorkerField" class="hidden">
                    <label class="erp-label">{{ __('users.link_worker') }} <span class="text-red-500">*</span></label>
                    <select name="worker_id" class="erp-select">
                        <option value="">{{ __('users.select_worker') }}</option>
                        @foreach($workers as $w)
                            <option value="{{ $w->id }}">{{ $w->worker_code }} – {{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="erp-label">{{ __('users.password') }} <span class="text-red-500">*</span></label>
                        <input type="password" name="password" required minlength="8" placeholder="{{ __('users.password_min') }}" class="erp-input">
                    </div>
                    <div>
                        <label class="erp-label">{{ __('users.confirm_password') }} <span class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" required minlength="8" placeholder="{{ __('users.repeat_password') }}" class="erp-input">
                    </div>
                </div>
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-[#E5E7EB] dark:border-[#27272A]">
                    <button type="button" onclick="closeModal('addUserModal')" class="px-4 py-2 text-xs font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/10 rounded-md transition-colors">
                        {{ __('common.cancel') }}
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-medium text-white bg-[#1D4E89] hover:bg-[#153A66] rounded-md transition-colors">
                        {{ __('users.save_user') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 2. Modal Lihat Detail Pengguna --}}
    <div id="viewUserModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white dark:bg-[#18181B] rounded-xl border border-[#E5E7EB] dark:border-[#27272A] shadow-2xl w-full max-w-md my-8 overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-[#E5E7EB] dark:border-[#27272A] flex items-center justify-between">
                <h3 class="text-base font-semibold text-[#0B2545] dark:text-[#F4F4F5]">{{ __('users.modal_view_title') }}</h3>
                <button type="button" onclick="closeModal('viewUserModal')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-6 space-y-4 text-xs">
                <div class="flex items-center gap-3 pb-4 border-b border-[#E5E7EB] dark:border-[#27272A]">
                    <div id="viewAvatar" class="w-12 h-12 rounded-full bg-[#1D4E89]/10 text-[#1D4E89] text-base font-bold flex items-center justify-center shrink-0">
                    </div>
                    <div>
                        <h4 id="viewName" class="text-sm font-bold text-[#0B2545] dark:text-[#F4F4F5]"></h4>
                        <p id="viewEmail" class="text-slate-500 font-mono"></p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <span class="text-slate-400 block font-medium">{{ __('users.table_role') }}:</span>
                        <span id="viewRole" class="font-semibold text-slate-800 dark:text-slate-200"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">{{ __('users.table_status') }}:</span>
                        <span id="viewStatus" class="font-semibold text-slate-800 dark:text-slate-200"></span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-slate-400 block font-medium">{{ __('users.link_worker') }}:</span>
                        <span id="viewWorker" class="font-semibold text-slate-800 dark:text-slate-200"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">{{ __('common.created_at') }}:</span>
                        <span id="viewCreatedAt" class="text-slate-600 dark:text-slate-400"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">{{ __('common.updated_at') }}:</span>
                        <span id="viewUpdatedAt" class="text-slate-600 dark:text-slate-400"></span>
                    </div>
                </div>
                <div class="pt-4 flex justify-end border-t border-[#E5E7EB] dark:border-[#27272A]">
                    <button type="button" onclick="closeModal('viewUserModal')" class="px-4 py-2 text-xs font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/10 rounded-md transition-colors">
                        {{ __('common.close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Modal Edit Pengguna --}}
    <div id="editUserModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white dark:bg-[#18181B] rounded-xl border border-[#E5E7EB] dark:border-[#27272A] shadow-2xl w-full max-w-lg my-8 overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-[#E5E7EB] dark:border-[#27272A] flex items-center justify-between">
                <h3 class="text-base font-semibold text-[#0B2545] dark:text-[#F4F4F5]">{{ __('users.modal_edit_title') }}</h3>
                <button type="button" onclick="closeModal('editUserModal')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form id="editUserForm" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="erp-label">{{ __('users.full_name') }} <span class="text-red-500">*</span></label>
                    <input type="text" id="editName" name="name" required class="erp-input">
                </div>
                <div>
                    <label class="erp-label">{{ __('users.email_address') }} <span class="text-red-500">*</span></label>
                    <input type="email" id="editEmail" name="email" required class="erp-input">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="erp-label">{{ __('users.table_role') }} <span class="text-red-500">*</span></label>
                        <select id="editRole" name="role" required class="erp-select" onchange="toggleWorkerPicker(this.value, 'editWorkerField')">
                            <option value="superadmin">{{ __('users.role_superadmin') }}</option>
                            <option value="project_manager">{{ __('users.role_project_manager') }}</option>
                            <option value="logistics">{{ __('users.role_logistics') }}</option>
                            <option value="finance">{{ __('users.role_finance') }}</option>
                            <option value="field_staff">{{ __('users.role_field_staff') }}</option>
                        </select>
                        <p id="editRoleHint" class="text-[11px] text-amber-600 mt-1 hidden"></p>
                    </div>
                    <div>
                        <label class="erp-label">{{ __('users.table_status') }}</label>
                        <select id="editStatus" name="status" class="erp-select">
                            <option value="active">{{ __('common.active') }}</option>
                            <option value="inactive">{{ __('common.inactive') }}</option>
                        </select>
                        <p id="editStatusHint" class="text-[11px] text-amber-600 mt-1 hidden"></p>
                    </div>
                </div>
                <div id="editWorkerField" class="hidden">
                    <label class="erp-label">{{ __('users.link_worker') }}</label>
                    <select id="editWorkerId" name="worker_id" class="erp-select">
                        <option value="">{{ __('users.select_worker') }}</option>
                        @foreach($workers as $w)
                            <option value="{{ $w->id }}">{{ $w->worker_code }} – {{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-[#E5E7EB] dark:border-[#27272A]">
                    <button type="button" onclick="closeModal('editUserModal')" class="px-4 py-2 text-xs font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/10 rounded-md transition-colors">
                        {{ __('common.cancel') }}
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-medium text-white bg-[#1D4E89] hover:bg-[#153A66] rounded-md transition-colors">
                        {{ __('users.save_changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 4. Modal Reset Kata Sandi --}}
    <div id="resetPasswordModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white dark:bg-[#18181B] rounded-xl border border-[#E5E7EB] dark:border-[#27272A] shadow-2xl w-full max-w-md my-8 overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-[#E5E7EB] dark:border-[#27272A] flex items-center justify-between">
                <h3 class="text-base font-semibold text-[#0B2545] dark:text-[#F4F4F5]">{{ __('users.modal_reset_title') }}</h3>
                <button type="button" onclick="closeModal('resetPasswordModal')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form id="resetPasswordForm" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PATCH')
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 text-xs text-blue-800 dark:text-blue-300" id="resetInfoText">
                </div>
                <div>
                    <label class="erp-label">{{ __('users.new_password') }} <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required minlength="8" placeholder="{{ __('users.password_min') }}" class="erp-input">
                </div>
                <div>
                    <label class="erp-label">{{ __('users.confirm_new_password') }} <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" required minlength="8" placeholder="{{ __('users.repeat_new_password') }}" class="erp-input">
                </div>
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-[#E5E7EB] dark:border-[#27272A]">
                    <button type="button" onclick="closeModal('resetPasswordModal')" class="px-4 py-2 text-xs font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/10 rounded-md transition-colors">
                        {{ __('common.cancel') }}
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-medium text-white bg-[#1D4E89] hover:bg-[#153A66] rounded-md transition-colors">
                        {{ __('users.reset_now_btn') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 5. Modal Konfirmasi Status (Aktif / Nonaktif) --}}
    <div id="statusModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white dark:bg-[#18181B] rounded-xl border border-[#E5E7EB] dark:border-[#27272A] shadow-2xl w-full max-w-md my-8 overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-[#E5E7EB] dark:border-[#27272A] flex items-center justify-between">
                <h3 id="statusModalTitle" class="text-base font-semibold text-[#0B2545] dark:text-[#F4F4F5]">{{ __('users.confirm_status_title') }}</h3>
                <button type="button" onclick="closeModal('statusModal')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form id="statusForm" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PATCH')
                <input type="hidden" id="statusTargetValue" name="status" value="">
                <p id="statusModalDesc" class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed"></p>
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-[#E5E7EB] dark:border-[#27272A]">
                    <button type="button" onclick="closeModal('statusModal')" class="px-4 py-2 text-xs font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/10 rounded-md transition-colors">
                        {{ __('common.cancel') }}
                    </button>
                    <button id="statusSubmitBtn" type="submit" class="px-4 py-2 text-xs font-medium text-white rounded-md transition-colors">
                        {{ __('common.confirm') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 6. Modal Konfirmasi Hapus Akun (Soft Delete) --}}
    <div id="deleteModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white dark:bg-[#18181B] rounded-xl border border-[#E5E7EB] dark:border-[#27272A] shadow-2xl w-full max-w-md my-8 overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-[#E5E7EB] dark:border-[#27272A] flex items-center justify-between">
                <h3 class="text-base font-semibold text-red-600 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>{{ __('users.confirm_delete_title') }}</span>
                </h3>
                <button type="button" onclick="closeModal('deleteModal')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form id="deleteForm" method="POST" class="p-6 space-y-4">
                @csrf
                @method('DELETE')
                <p id="deleteModalText" class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                </p>
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-3 text-xs text-amber-800 dark:text-amber-300">
                    <strong>{{ __('common.notes') }}:</strong> {{ __('users.soft_delete_note') }}
                </div>
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-[#E5E7EB] dark:border-[#27272A]">
                    <button type="button" onclick="closeModal('deleteModal')" class="px-4 py-2 text-xs font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/10 rounded-md transition-colors">
                        {{ __('common.cancel') }}
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition-colors">
                        {{ __('users.confirm_delete_btn') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── JavaScript Modal Handler (Accessible & Zero-Dependency) ── --}}
    <script>
        function openModal(modalId) {
            const el = document.getElementById(modalId);
            if (el) {
                el.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
        }

        function closeModal(modalId) {
            const el = document.getElementById(modalId);
            if (el) {
                el.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        function toggleWorkerPicker(role, fieldId) {
            const el = document.getElementById(fieldId);
            if (el) {
                if (role === 'field_staff') {
                    el.classList.remove('hidden');
                } else {
                    el.classList.add('hidden');
                }
            }
        }

        function openAddUserModal() {
            openModal('addUserModal');
        }

        function openViewUserModal(data) {
            document.getElementById('viewName').textContent = data.name;
            document.getElementById('viewEmail').textContent = data.email;
            document.getElementById('viewRole').textContent = data.role;
            document.getElementById('viewStatus').textContent = data.status;
            document.getElementById('viewWorker').textContent = data.worker || '-';
            document.getElementById('viewCreatedAt').textContent = data.created_at;
            document.getElementById('viewUpdatedAt').textContent = data.updated_at;
            document.getElementById('viewAvatar').textContent = (data.name || 'U').substring(0, 1).toUpperCase();
            openModal('viewUserModal');
        }

        function openEditUserModal(data) {
            const form = document.getElementById('editUserForm');
            form.action = '/users/' + data.id;

            document.getElementById('editName').value = data.name;
            document.getElementById('editEmail').value = data.email;

            const roleSelect = document.getElementById('editRole');
            roleSelect.value = data.role;

            const roleHint = document.getElementById('editRoleHint');
            if (data.is_primary) {
                roleSelect.setAttribute('disabled', 'disabled');
                roleHint.textContent = '{{ __('messages.cannot_demote_primary_admin') }}';
                roleHint.classList.remove('hidden');
            } else if (data.is_self && data.role === 'superadmin') {
                roleSelect.setAttribute('disabled', 'disabled');
                roleHint.textContent = '{{ __('messages.cannot_demote_self') }}';
                roleHint.classList.remove('hidden');
            } else {
                roleSelect.removeAttribute('disabled');
                roleHint.classList.add('hidden');
            }

            const statusSelect = document.getElementById('editStatus');
            statusSelect.value = data.status;

            const statusHint = document.getElementById('editStatusHint');
            if (data.is_primary) {
                statusSelect.setAttribute('disabled', 'disabled');
                statusHint.textContent = '{{ __('messages.cannot_deactivate_primary_admin') }}';
                statusHint.classList.remove('hidden');
            } else if (data.is_self) {
                statusSelect.setAttribute('disabled', 'disabled');
                statusHint.textContent = '{{ __('messages.cannot_deactivate_self') }}';
                statusHint.classList.remove('hidden');
            } else {
                statusSelect.removeAttribute('disabled');
                statusHint.classList.add('hidden');
            }

            const workerSelect = document.getElementById('editWorkerId');
            if (workerSelect) {
                workerSelect.value = data.worker_id || '';
            }

            toggleWorkerPicker(data.role, 'editWorkerField');
            openModal('editUserModal');
        }

        function openResetPasswordModal(data) {
            const form = document.getElementById('resetPasswordForm');
            form.action = '/users/' + data.id + '/reset-password';
            const template = '{{ __('users.reset_info', ['name' => '__NAME__', 'email' => '__EMAIL__']) }}';
            document.getElementById('resetInfoText').textContent = template.replace('__NAME__', data.name).replace('__EMAIL__', data.email);
            openModal('resetPasswordModal');
        }

        function openStatusModal(data) {
            const form = document.getElementById('statusForm');
            form.action = '/users/' + data.id + '/status';
            document.getElementById('statusTargetValue').value = data.target_status;

            const isDeactivating = data.target_status === 'inactive';
            document.getElementById('statusModalTitle').textContent = isDeactivating ? '{{ __('users.deactivate_title') }}' : '{{ __('users.activate_title') }}';

            const deactTemplate = '{{ __('users.confirm_deactivate_text', ['name' => '__NAME__', 'email' => '__EMAIL__']) }}';
            const actTemplate = '{{ __('users.confirm_activate_text', ['name' => '__NAME__', 'email' => '__EMAIL__']) }}';

            const desc = isDeactivating
                ? deactTemplate.replace('__NAME__', data.name).replace('__EMAIL__', data.email)
                : actTemplate.replace('__NAME__', data.name).replace('__EMAIL__', data.email);

            document.getElementById('statusModalDesc').textContent = desc;

            const btn = document.getElementById('statusSubmitBtn');
            if (isDeactivating) {
                btn.className = 'px-4 py-2 text-xs font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-md transition-colors';
                btn.textContent = '{{ __('users.yes_deactivate_btn') }}';
            } else {
                btn.className = 'px-4 py-2 text-xs font-medium text-white bg-[#16A34A] hover:bg-emerald-700 rounded-md transition-colors';
                btn.textContent = '{{ __('users.yes_activate_btn') }}';
            }

            openModal('statusModal');
        }

        function openDeleteModal(data) {
            const form = document.getElementById('deleteForm');
            form.action = '/users/' + data.id;
            const delTemplate = '{{ __('users.confirm_delete_text', ['name' => '__NAME__', 'email' => '__EMAIL__']) }}';
            document.getElementById('deleteModalText').textContent = delTemplate.replace('__NAME__', data.name).replace('__EMAIL__', data.email);
            openModal('deleteModal');
        }

        // Close on ESC key press
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                ['addUserModal', 'viewUserModal', 'editUserModal', 'resetPasswordModal', 'statusModal', 'deleteModal'].forEach(closeModal);
            }
        });
    </script>
@endsection

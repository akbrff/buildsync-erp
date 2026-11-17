<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserRoleController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total' => User::count(),
            'active' => User::where('status', 'active')->count(),
            'inactive' => User::where('status', 'inactive')->count(),
        ];

        $query = User::with('worker')->orderBy('name');

        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->paginate(15)->withQueryString();
        $workers = Worker::orderBy('name')->get();

        return view('operations.users', compact('users', 'workers', 'stats'));
    }

    public function show(User $user)
    {
        $user->load('worker');

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'role_label' => str_replace('_', ' ', ucfirst($user->role)),
                'status' => $user->status,
                'status_label' => $user->status === 'active' ? 'Aktif' : 'Nonaktif',
                'worker' => $user->worker ? [
                    'id' => $user->worker->id,
                    'name' => $user->worker->name,
                    'code' => $user->worker->worker_code,
                ] : null,
                'created_at' => $user->created_at?->format('d/m/Y H:i'),
                'updated_at' => $user->updated_at?->format('d/m/Y H:i'),
                'is_primary' => $user->isPrimaryAdmin(),
            ]);
        }

        return redirect()->route('users.roles.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:superadmin,project_manager,logistics,finance,field_staff'],
            'worker_id' => ['nullable', 'required_if:role,field_staff', 'exists:workers,id'],
            'status' => ['nullable', 'in:active,inactive'],
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'role.required' => 'Peran (role) pengguna wajib dipilih.',
            'worker_id.required_if' => 'Pengguna dengan role Field Staff wajib ditautkan ke data pekerja.',
        ]);

        if ($validated['role'] !== 'field_staff') {
            $validated['worker_id'] = null;
        }

        $validated['status'] = $validated['status'] ?? 'active';
        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return back()->with('success', __('messages.user_created'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'in:superadmin,project_manager,logistics,finance,field_staff'],
            'worker_id' => ['nullable', 'required_if:role,field_staff', 'exists:workers,id'],
            'status' => ['nullable', 'in:active,inactive'],
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh pengguna lain.',
            'role.required' => 'Peran (role) pengguna wajib dipilih.',
            'worker_id.required_if' => 'Pengguna dengan role Field Staff wajib ditautkan ke data pekerja.',
        ]);

        $newRole = $validated['role'];
        $newStatus = $validated['status'] ?? $user->status;

        // Protection: Role changes away from superadmin
        if ($user->role === 'superadmin' && $newRole !== 'superadmin') {
            if ($user->isPrimaryAdmin()) {
                return $this->abortProtection($request, __('messages.cannot_demote_primary_admin'));
            }
            if ($request->user()->is($user)) {
                return $this->abortProtection($request, __('messages.cannot_demote_self'));
            }
            if ($user->isLastActiveAdmin()) {
                return $this->abortProtection($request, __('messages.last_active_admin_required'));
            }
        }

        // Protection: Status change to inactive
        if ($user->status === 'active' && $newStatus === 'inactive') {
            if ($user->isPrimaryAdmin()) {
                return $this->abortProtection($request, __('messages.cannot_deactivate_primary_admin'));
            }
            if ($request->user()->is($user)) {
                return $this->abortProtection($request, __('messages.cannot_deactivate_self'));
            }
            if ($user->isLastActiveAdmin()) {
                return $this->abortProtection($request, __('messages.last_active_admin_required'));
            }

            // Invalidate existing sessions immediately
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        if ($newRole !== 'field_staff') {
            $validated['worker_id'] = null;
        }

        $validated['status'] = $newStatus;
        $user->update($validated);

        return back()->with('success', __('messages.user_updated'));
    }

    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => ['required', 'in:superadmin,project_manager,logistics,finance,field_staff'],
            'worker_id' => ['nullable', 'required_if:role,field_staff', 'exists:workers,id'],
        ]);

        if ($validated['role'] !== 'field_staff') {
            $validated['worker_id'] = null;
        }

        if ($user->role === 'superadmin' && $validated['role'] !== 'superadmin') {
            if ($user->isPrimaryAdmin()) {
                return $this->abortProtection($request, __('messages.cannot_demote_primary_admin'));
            }
            if ($request->user()->is($user)) {
                return $this->abortProtection($request, __('messages.cannot_demote_self'));
            }
            if ($user->isLastActiveAdmin()) {
                return $this->abortProtection($request, __('messages.last_active_admin_required'));
            }
        }

        $user->update($validated);

        return back()->with('success', __('messages.user_role_updated'));
    }

    public function toggleStatus(Request $request, User $user)
    {
        $targetStatus = $request->input('status', $user->status === 'active' ? 'inactive' : 'active');

        if ($targetStatus === 'inactive') {
            if ($user->isPrimaryAdmin()) {
                return $this->abortProtection($request, __('messages.cannot_deactivate_primary_admin'));
            }
            if ($request->user()->is($user)) {
                return $this->abortProtection($request, __('messages.cannot_deactivate_self'));
            }
            if ($user->isLastActiveAdmin()) {
                return $this->abortProtection($request, __('messages.last_active_admin_required'));
            }

            // Invalidate active sessions immediately
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        $user->update(['status' => $targetStatus]);

        $message = $targetStatus === 'active'
            ? __('messages.user_activated')
            : __('messages.user_deactivated');

        return back()->with('success', $message);
    }

    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Invalidate sessions so user must log in with new password
        if (!$request->user()->is($user)) {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        return back()->with('success', __('messages.user_password_reset', ['name' => $user->name]));
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->isPrimaryAdmin()) {
            return $this->abortProtection($request, __('messages.cannot_delete_primary_admin'));
        }

        if ($request->user()->is($user)) {
            return $this->abortProtection($request, __('messages.cannot_delete_self'));
        }

        if ($user->isLastActiveAdmin()) {
            return $this->abortProtection($request, __('messages.last_active_admin_required'));
        }

        // Invalidate active sessions immediately
        DB::table('sessions')->where('user_id', $user->id)->delete();

        // Soft delete user
        $user->delete();

        return back()->with('success', __('messages.user_deleted'));
    }

    protected function abortProtection(Request $request, string $message)
    {
        if ($request->wantsJson() || $request->ajax()) {
            abort(422, $message);
        }

        return back()->with('error', $message);
    }
}

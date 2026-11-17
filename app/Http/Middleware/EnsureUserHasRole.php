<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole {
    public function handle(Request $request, Closure $next, string ...$roles): Response {
        $user = $request->user();
        if ($user && ($user->status !== 'active' || $user->trashed())) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            abort(403, __('auth.deactivated_error'));
        }

        abort_unless($user && ($user->role === 'superadmin' || in_array($user->role, $roles, true)), 403, __('common.access_denied'));
        return $next($request);
    }
}
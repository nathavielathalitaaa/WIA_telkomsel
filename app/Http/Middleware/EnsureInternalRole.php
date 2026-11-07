<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureInternalRole
{
    /**
     * Pastikan pengguna telah login, akun aktif, dan memiliki salah satu role internal yang diizinkan.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->is_active) {
            abort(403, 'Akun internal tidak aktif.');
        }

        if (! empty($roles)) {
            $userRoleValue = $user->role?->value ?? (string) $user->role;

            if (! in_array($userRoleValue, $roles, true)) {
                abort(403, 'Anda tidak memiliki wewenang akses untuk tindakan ini.');
            }
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InternalAuthController extends Controller
{
    /**
     * Tampilkan halaman login tim internal (GET /login).
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Proses autentikasi tim internal (POST /login).
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Alamat email internal wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $throttleKey = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan masuk. Silakan coba kembali dalam {$seconds} detik.",
            ]);
        }

        $email = Str::lower(trim($credentials['email']));
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => 'Kredensial email atau kata sandi tidak sesuai.',
            ]);
        }

        if (! $user->is_active) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => 'Akun tim internal ini sedang dinonaktifkan. Hubungi Administrator TanggapTerang.',
            ]);
        }

        RateLimiter::clear($throttleKey);

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        AuditLog::record(
            activityType: 'auth.login',
            description: "Pengguna {$user->name} ({$user->role?->label()}) berhasil masuk ke sesi internal.",
            actor: $user,
            subject: $user,
            subjectLabel: $user->email,
            metadata: ['email' => $user->email, 'role' => $user->role?->value]
        );

        return redirect()->intended(route('internal.dashboard'))
            ->with('success', "Selamat datang, {$user->name} ({$user->role?->label()}).");
    }

    /**
     * Proses keluar dari sesi internal (POST /logout).
     */
    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::guard('web')->user();

        if ($user) {
            AuditLog::record(
                activityType: 'auth.logout',
                description: "Pengguna {$user->name} ({$user->role?->label()}) keluar dari sesi internal.",
                actor: $user,
                subject: $user,
                subjectLabel: $user->email,
                metadata: ['email' => $user->email, 'role' => $user->role?->value]
            );
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah keluar dari sesi pengelolaan internal.');
    }

    /**
     * Kunci pembatas laju percobaan login berdasarkan email dan alamat IP.
     */
    private function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower((string) $request->input('email')) . '|' . $request->ip());
    }
}

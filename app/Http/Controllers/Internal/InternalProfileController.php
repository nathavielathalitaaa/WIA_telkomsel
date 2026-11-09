<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class InternalProfileController extends Controller
{
    /**
     * Tampilkan halaman profil akun internal (GET /internal/profile).
     */
    public function show(Request $request): View
    {
        $user = $request->user();

        // 10 riwayat aktivitas terbaru milik akun yang sedang masuk
        $recentLogs = AuditLog::query()
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        return view('internal.profile.show', [
            'user' => $user,
            'recentLogs' => $recentLogs,
        ]);
    }

    /**
     * Perbarui data profil akun internal yang diizinkan (PUT /internal/profile).
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
        ], [
            'name.required' => 'Nama lengkap akun wajib diisi.',
            'name.min' => 'Nama lengkap minimal 3 karakter.',
            'name.max' => 'Nama lengkap maksimal 255 karakter.',
        ]);

        $oldName = $user->name;
        $newName = trim($validated['name']);

        if ($oldName !== $newName) {
            $user->update([
                'name' => $newName,
            ]);

            AuditLog::record(
                activityType: 'profile.update',
                description: "Pengguna {$user->name} memperbarui nama profil akun dari \"{$oldName}\" menjadi \"{$newName}\".",
                actor: $user,
                subject: $user,
                subjectLabel: $user->email,
                metadata: [
                    'field' => 'name',
                    'old_value' => $oldName,
                    'new_value' => $newName,
                ]
            );
        }

        return redirect()
            ->route('internal.profile.show')
            ->with('success', 'Profil akun berhasil diperbarui.');
    }

    /**
     * Perbarui kata sandi akun internal pengguna saat ini (PUT /internal/profile/password).
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string', 'current_password:web'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini yang Anda masukkan tidak sesuai.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak sesuai.',
            'password.different' => 'Kata sandi baru harus berbeda dari kata sandi saat ini.',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        AuditLog::record(
            activityType: 'profile.password_change',
            description: "Pengguna {$user->name} ({$user->role?->label()}) berhasil mengganti kata sandi akun.",
            actor: $user,
            subject: $user,
            subjectLabel: $user->email,
            metadata: [
                'action' => 'self_password_change',
            ]
        );

        return redirect()
            ->route('internal.profile.show')
            ->with('success', 'Kata sandi akun Anda berhasil diperbarui.');
    }
}

<?php

namespace App\Http\Controllers\Internal;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InternalUserController extends Controller
{
    /**
     * Tampilkan direktori akun internal TanggapTerang (GET /internal/users).
     * Dikelompokkan berdasarkan peran: Administrator, Verifikator, Operator.
     * Hanya diakses oleh Admin.
     */
    public function index(Request $request): View
    {
        $users = User::query()
            ->orderBy('name')
            ->get();

        $admins = $users->where('role', UserRole::Admin);
        $verifikators = $users->where('role', UserRole::Verifikator);
        $operators = $users->where('role', UserRole::Operator);

        $stats = [
            'total' => $users->count(),
            'active' => $users->where('is_active', true)->count(),
            'inactive' => $users->where('is_active', false)->count(),
            'admin_count' => $admins->count(),
            'verifikator_count' => $verifikators->count(),
            'operator_count' => $operators->count(),
        ];

        return view('internal.users.index', [
            'admins' => $admins,
            'verifikators' => $verifikators,
            'operators' => $operators,
            'stats' => $stats,
            'roles' => UserRole::cases(),
        ]);
    }

    /**
     * Daftarkan akun internal baru (POST /internal/users).
     * Hanya diakses oleh Admin.
     */
    public function store(Request $request): RedirectResponse
    {
        $currentUser = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::enum(UserRole::class)],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Alamat email ini sudah terdaftar untuk akun lain.',
            'role.required' => 'Peran akun internal wajib dipilih.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $roleEnum = UserRole::from($validated['role']);

        $newUser = User::create([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'role' => $roleEnum,
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        AuditLog::record(
            activityType: 'user.create',
            description: "Administrator {$currentUser->name} membuat akun internal baru \"{$newUser->name}\" ({$newUser->email}) dengan peran {$roleEnum->label()}.",
            actor: $currentUser,
            subject: $newUser,
            subjectLabel: "{$newUser->name} ({$newUser->email})",
            metadata: [
                'name' => $newUser->name,
                'email' => $newUser->email,
                'role' => $roleEnum->value,
                'is_active' => true,
            ]
        );

        return redirect()
            ->route('internal.users.index')
            ->with('success', "Akun internal baru untuk {$newUser->name} ({$roleEnum->label()}) berhasil dibuat.");
    }

    /**
     * Perbarui data akun internal (PUT /internal/users/{user}).
     * Hanya diakses oleh Admin.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $currentUser = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'role' => ['required', Rule::enum(UserRole::class)],
            'is_active' => ['required', 'boolean'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'role.required' => 'Peran akun wajib dipilih.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $newRole = UserRole::from($validated['role']);
        $newActive = (bool) $validated['is_active'];

        // Proteksi self-lockout: Admin tidak boleh menonaktifkan atau menurunkan perannya sendiri
        if ($currentUser->id === $user->id) {
            if (! $newActive) {
                return redirect()
                    ->route('internal.users.index')
                    ->with('error', 'Anda tidak dapat menonaktifkan akun Administrator Anda sendiri.');
            }

            if ($newRole !== UserRole::Admin) {
                return redirect()
                    ->route('internal.users.index')
                    ->with('error', 'Anda tidak dapat mengubah peran akun Administrator Anda sendiri.');
            }
        }

        $changes = [];
        if ($user->name !== $validated['name']) {
            $changes['name'] = ['old' => $user->name, 'new' => $validated['name']];
        }
        if ($user->role !== $newRole) {
            $changes['role'] = ['old' => $user->role?->value, 'new' => $newRole->value];
        }
        if ((bool) $user->is_active !== $newActive) {
            $changes['is_active'] = ['old' => (bool) $user->is_active, 'new' => $newActive];
        }

        $updateData = [
            'name' => trim($validated['name']),
            'role' => $newRole,
            'is_active' => $newActive,
        ];

        $resetPasswordNotice = false;
        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
            $changes['password_reset_by_admin'] = true;
            $resetPasswordNotice = true;
        }

        $user->update($updateData);

        if (! empty($changes)) {
            $descParts = [];
            if (isset($changes['role'])) {
                $descParts[] = "peran diubah menjadi {$newRole->label()}";
            }
            if (isset($changes['is_active'])) {
                $descParts[] = $newActive ? 'akun diaktifkan kembali' : 'akun dinonaktifkan';
            }
            if (isset($changes['name'])) {
                $descParts[] = "nama diubah menjadi {$user->name}";
            }
            if ($resetPasswordNotice) {
                $descParts[] = 'kata sandi direset oleh Administrator';
            }

            $detailDesc = implode(', ', $descParts);

            AuditLog::record(
                activityType: 'user.update',
                description: "Administrator {$currentUser->name} memperbarui akun \"{$user->name}\" ({$user->email}): {$detailDesc}.",
                actor: $currentUser,
                subject: $user,
                subjectLabel: "{$user->name} ({$user->email})",
                metadata: $changes
            );
        }

        return redirect()
            ->route('internal.users.index')
            ->with('success', "Akun \"{$user->name}\" berhasil diperbarui.");
    }
}

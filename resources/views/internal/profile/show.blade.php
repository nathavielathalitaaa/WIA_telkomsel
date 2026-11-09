@extends('layouts.internal', [
    'title' => 'Profil Akun — TanggapTerang',
    'internalMenu' => 'profile'
])

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-200 pb-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-900 text-white font-bold text-sm">
                    👤
                </span>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                    Profil Akun Internal
                </h1>
            </div>
            <p class="text-xs sm:text-sm text-slate-600 mt-1">
                Informasi identitas, hak akses peran, dan pengaturan keamanan akun Anda.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('internal.dashboard') }}"
               class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition">
                ← Kembali ke Dashboard
            </a>
        </div>
    </div>

    @php
        $roleValue = $user->role?->value ?? (string) $user->role;
        $roleLabel = $user->role?->label() ?? ucfirst($roleValue);

        [$badgeClass, $roleDescription, $permissionsList] = match ($roleValue) {
            'admin' => [
                'bg-amber-100 text-amber-900 border border-amber-300',
                'Akses Penuh Administrator: Memiliki kewenangan tertinggi untuk mengelola seluruh modul sistem, direktori akun, dan audit log.',
                [
                    'Verifikasi Laporan Masyarakat',
                    'Verifikasi Data Kejadian Bencana',
                    'Verifikasi Respons Bantuan Donasi',
                    'Manajemen Kejadian & Rekomendasi Tindak Lanjut',
                    'Pusat Intelijen Sumber Data Terbuka',
                    'Direktori Akun Internal & Hak Akses',
                    'Audit Log Aktivitas Seluruh Pengguna Internal',
                ]
            ],
            'verifikator' => [
                'bg-blue-100 text-blue-900 border border-blue-300',
                'Akses Verifikator: Bertanggung jawab memeriksa kesahihan laporan masyarakat, status verifikasi kejadian, dan respons bantuan.',
                [
                    'Pemeriksaan & Verifikasi Laporan Masyarakat',
                    'Penolakan & Penandaan Laporan Duplikat',
                    'Verifikasi Fakta Kejadian Bencana',
                    'Pemeriksaan Validitas Respons Bantuan Publik',
                ]
            ],
            'operator' => [
                'bg-emerald-100 text-emerald-900 border border-emerald-300',
                'Akses Operator: Bertanggung jawab mencatat operasional lapangan, pembaruan tindak lanjut, dan penautan laporan ke kejadian.',
                [
                    'Pembuatan Data Kejadian Bencana Baru',
                    'Penautan Laporan Masyarakat ke Kejadian',
                    'Pencatatan Kronologi & Log Tindak Lanjut',
                    'Pembaruan Status Penyaluran Respons Bantuan',
                    'Penambahan Tautan Sumber Terbuka Lapangan',
                ]
            ],
            default => [
                'bg-slate-100 text-slate-800 border border-slate-300',
                'Akses Terbatas: Pengguna internal sistem.',
                ['Akses Dashboard Internal']
            ],
        };
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Kolom Kiri: Ringkasan Akun & Formulir Pembaruan -->
        <div class="lg:col-span-7 space-y-6">
            <!-- Kartu Identitas Akun -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 p-5 text-white">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-full bg-amber-400 text-slate-950 font-black text-2xl flex items-center justify-center shrink-0 border-2 border-white/20">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-lg font-bold text-white truncate">{{ $user->name }}</h2>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $badgeClass }}">
                                    {{ $roleLabel }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-300 font-telemetry mt-0.5">{{ $user->email }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs border-b border-slate-100">
                    <div>
                        <span class="text-slate-500 uppercase tracking-wider font-semibold block">Status Akun</span>
                        <div class="mt-1 flex items-center gap-1.5 font-bold {{ $user->is_active ? 'text-emerald-700' : 'text-red-700' }}">
                            <span class="w-2 h-2 rounded-full {{ $user->is_active ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                            {{ $user->is_active ? 'Aktif Beroperasi' : 'Nonaktif' }}
                        </div>
                    </div>
                    <div>
                        <span class="text-slate-500 uppercase tracking-wider font-semibold block">Waktu Terdaftar</span>
                        <p class="mt-1 text-slate-800 font-telemetry font-medium">
                            {{ $user->created_at ? $user->created_at->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') . ' WIB' : '—' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Formulir Ubah Nama Profil -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <span>✏️</span> Ubah Nama Akun
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Nama lengkap yang akan ditampilkan pada riwayat aktivitas, verifikasi, dan tindak lanjut.
                    </p>
                </div>

                <form method="POST" action="{{ route('internal.profile.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               id="name"
                               value="{{ old('name', $user->name) }}"
                               required
                               class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900 bg-white">
                        @error('name')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">
                            Alamat Email / Identitas Akun
                        </label>
                        <input type="email"
                               value="{{ $user->email }}"
                               disabled
                               class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 bg-slate-100 text-slate-500 cursor-not-allowed font-telemetry">
                        <span class="text-[11px] text-slate-400 mt-0.5 block">
                            Alamat email akun internal hanya dapat diubah oleh Administrator melalui Manajemen Akun.
                        </span>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="submit"
                                class="px-4 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold uppercase tracking-wider transition cursor-pointer">
                            Simpan Perubahan Nama
                        </button>
                    </div>
                </form>
            </div>

            <!-- Formulir Ubah Kata Sandi -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <span>🔒</span> Perbarui Kata Sandi
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Gunakan kata sandi yang kuat dan unik minimal 8 karakter.
                    </p>
                </div>

                <form method="POST" action="{{ route('internal.profile.password.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block text-xs font-semibold text-slate-700 mb-1">
                            Kata Sandi Saat Ini <span class="text-red-500">*</span>
                        </label>
                        <input type="password"
                               name="current_password"
                               id="current_password"
                               required
                               autocomplete="current-password"
                               placeholder="Masukkan kata sandi yang aktif saat ini"
                               class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900 bg-white">
                        @error('current_password')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">
                                Kata Sandi Baru <span class="text-red-500">*</span>
                            </label>
                            <input type="password"
                                   name="password"
                                   id="password"
                                   required
                                   autocomplete="new-password"
                                   placeholder="Minimal 8 karakter"
                                   class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900 bg-white">
                            @error('password')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">
                                Konfirmasi Kata Sandi Baru <span class="text-red-500">*</span>
                            </label>
                            <input type="password"
                                   name="password_confirmation"
                                   id="password_confirmation"
                                   required
                                   autocomplete="new-password"
                                   placeholder="Ketik ulang kata sandi baru"
                                   class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900 bg-white">
                        </div>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="submit"
                                class="px-4 py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold uppercase tracking-wider transition cursor-pointer">
                            Perbarui Kata Sandi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Kolom Kanan: Rincian Hak Akses Peran & Riwayat Aktivitas Pribadi -->
        <div class="lg:col-span-5 space-y-6">
            <!-- Kartu Deskripsi Peran & Hak Akses -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-3">
                <div class="border-b border-slate-100 pb-3">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block font-telemetry">
                        Otorisasi Akun
                    </span>
                    <h3 class="text-sm font-bold text-slate-900 mt-0.5">
                        Kewenangan Peran: {{ $roleLabel }}
                    </h3>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed">
                    {{ $roleDescription }}
                </p>

                <div class="pt-2">
                    <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wider block mb-2 font-telemetry">
                        Cakupan Kewenangan Operasional:
                    </span>
                    <ul class="space-y-1.5">
                        @foreach($permissionsList as $permission)
                            <li class="flex items-start gap-2 text-xs text-slate-700">
                                <span class="text-emerald-600 font-bold shrink-0">✓</span>
                                <span>{{ $permission }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Kartu 10 Catatan Aktivitas Terakhir Akun Ini -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-1.5">
                            <span>📋</span> Riwayat Aktivitas Akun
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Catatan audit aktivitas terbaru akun Anda
                        </p>
                    </div>
                    @if($user->isAdmin())
                        <a href="{{ route('internal.audit-logs.index') }}"
                           class="text-xs font-bold text-amber-600 hover:text-amber-700 underline font-telemetry">
                            Lihat Semua →
                        </a>
                    @endif
                </div>

                @if($recentLogs->isEmpty())
                    <div class="text-center py-6 text-slate-400 text-xs">
                        Belum ada aktivitas tercatat untuk sesi akun ini.
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($recentLogs as $log)
                            <div class="p-3 rounded-lg bg-slate-50 border border-slate-100 space-y-1">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-bold text-xs text-slate-900">
                                        {{ $log->activityLabel() }}
                                    </span>
                                    <span class="font-telemetry text-[10px] text-slate-400 shrink-0">
                                        {{ $log->created_at->timezone('Asia/Jakarta')->translatedFormat('d M, H:i') }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-600 leading-snug">
                                    {{ $log->description }}
                                </p>
                                @if($log->subject_label)
                                    <div class="pt-0.5">
                                        <span class="inline-block px-1.5 py-0.5 rounded bg-slate-200 text-slate-700 font-telemetry text-[10px]">
                                            Sasaran: {{ $log->subject_label }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

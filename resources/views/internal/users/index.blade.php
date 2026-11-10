@extends('layouts.internal', [
    'title' => 'Direktori Akun Internal — TanggapTerang',
    'internalMenu' => 'users'
])

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-200 pb-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-400 text-[#173B70] font-bold text-sm">
                    👥
                </span>
                <h1 class="text-xl sm:text-2xl font-black text-[#202938] tracking-tight">
                    Direktori Akun Internal
                </h1>
            </div>
            <p class="text-xs sm:text-sm text-slate-600 mt-1">
                Daftar akun operasional resmi TanggapTerang yang dikelompokkan berdasarkan peran dan tanggung jawab.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button"
                    onclick="document.getElementById('modal-create-user').classList.remove('hidden')"
                    class="px-3.5 py-2 rounded-lg bg-[#173B70] hover:bg-[#0F2A52] text-white text-xs font-bold uppercase tracking-wider transition flex items-center gap-1.5 cursor-pointer shadow-sm">
                <span>➕</span> Daftarkan Akun Baru
            </button>
        </div>
    </div>

    <!-- Security & Transparency Notice -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-xs text-blue-900 flex items-start gap-3">
        <span class="text-base shrink-0">🛡️</span>
        <div>
            <strong class="font-bold">Prinsip Transparansi & Keamanan Pengelolaan Akun:</strong>
            <p class="mt-0.5 text-blue-800 leading-relaxed">
                Halaman ini ditujukan untuk kejelasan akuntabilitas pengelolaan akun operasional kebencanaan. Kata sandi, token autentikasi, dan informasi rahasia tidak pernah ditampilkan di halaman ini. Setiap perubahan peran dan status akun dicatat secara otomatis dalam Audit Log Aktivitas.
            </p>
        </div>
    </div>

    <!-- Metric Summary Cards -->
    <div class="flex overflow-x-auto snap-x snap-mandatory gap-3 pb-2 -mx-4 px-4 sm:mx-0 sm:px-0 scrollbar-none sm:grid sm:grid-cols-3 lg:grid-cols-6">
        <div class="bg-white rounded-xl shadow-xs border border-slate-100/90 p-4 w-[145px] shrink-0 snap-start sm:w-auto">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 font-telemetry block">Total Akun</span>
            <span class="text-2xl font-black text-[#202938] mt-1 block font-telemetry">{{ $stats['total'] }}</span>
            <span class="text-[10px] text-slate-400 font-telemetry">Seluruh peran</span>
        </div>
        <div class="bg-white rounded-xl shadow-xs border border-slate-100/90 p-4 w-[145px] shrink-0 snap-start sm:w-auto">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 font-telemetry block">Akun Aktif</span>
            <span class="text-2xl font-black text-emerald-600 mt-1 block font-telemetry">{{ $stats['active'] }}</span>
            <span class="text-[10px] text-emerald-700 font-telemetry">Siap bertugas</span>
        </div>
        <div class="bg-white rounded-xl shadow-xs border border-slate-100/90 p-4 w-[145px] shrink-0 snap-start sm:w-auto">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 font-telemetry block">Nonaktif</span>
            <span class="text-2xl font-black text-slate-500 mt-1 block font-telemetry">{{ $stats['inactive'] }}</span>
            <span class="text-[10px] text-slate-400 font-telemetry">Akses ditangguhkan</span>
        </div>
        <div class="bg-white rounded-xl shadow-xs border border-slate-100/90 p-4 w-[145px] shrink-0 snap-start sm:w-auto">
            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 font-telemetry block">Administrator</span>
            <span class="text-2xl font-black text-amber-700 mt-1 block font-telemetry">{{ $stats['admin_count'] }}</span>
            <span class="text-[10px] text-slate-400 font-telemetry">Kelola sistem</span>
        </div>
        <div class="bg-white rounded-xl shadow-xs border border-slate-100/90 p-4 w-[145px] shrink-0 snap-start sm:w-auto">
            <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 font-telemetry block">Verifikator</span>
            <span class="text-2xl font-black text-blue-700 mt-1 block font-telemetry">{{ $stats['verifikator_count'] }}</span>
            <span class="text-[10px] text-slate-400 font-telemetry">Pemeriksa laporan</span>
        </div>
        <div class="bg-white rounded-xl shadow-xs border border-slate-100/90 p-4 w-[145px] shrink-0 snap-start sm:w-auto">
            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 font-telemetry block">Operator</span>
            <span class="text-2xl font-black text-emerald-700 mt-1 block font-telemetry">{{ $stats['operator_count'] }}</span>
            <span class="text-[10px] text-slate-400 font-telemetry">Tindak lanjut</span>
        </div>
    </div>

    <!-- Kelompok 1: Administrator Sistem -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-100/90 overflow-hidden">
        <div class="bg-amber-50/70 border-b border-amber-200 px-5 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="px-2 py-0.5 rounded bg-amber-400 text-[#173B70] font-telemetry font-bold text-xs uppercase">
                    Administrator Sistem ({{ $admins->count() }})
                </span>
                <span class="text-xs text-slate-600 hidden sm:inline">
                    Kewenangan penuh pengelolaan sistem, otorisasi akun, intelijen, dan verifikasi.
                </span>
            </div>
        </div>
        @include('internal.users._table', ['userGroup' => $admins, 'groupRole' => 'admin'])
    </div>

    <!-- Kelompok 2: Verifikator Informasi -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-100/90 overflow-hidden">
        <div class="bg-blue-50/70 border-b border-blue-200 px-5 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="px-2 py-0.5 rounded bg-blue-600 text-white font-telemetry font-bold text-xs uppercase">
                    Verifikator Informasi ({{ $verifikators->count() }})
                </span>
                <span class="text-xs text-slate-600 hidden sm:inline">
                    Pemeriksaan kesahihan laporan masyarakat, penetapan validitas kejadian, dan verifikasi respons bantuan.
                </span>
            </div>
        </div>
        @include('internal.users._table', ['userGroup' => $verifikators, 'groupRole' => 'verifikator'])
    </div>

    <!-- Kelompok 3: Operator Tindak Lanjut -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-100/90 overflow-hidden">
        <div class="bg-emerald-50/70 border-b border-emerald-200 px-5 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="px-2 py-0.5 rounded bg-emerald-600 text-white font-telemetry font-bold text-xs uppercase">
                    Operator Tindak Lanjut ({{ $operators->count() }})
                </span>
                <span class="text-xs text-slate-600 hidden sm:inline">
                    Pengelolaan kejadian di lapangan, penautan laporan warga, dan pencatatan perkembangan respons.
                </span>
            </div>
        </div>
        @include('internal.users._table', ['userGroup' => $operators, 'groupRole' => 'operator'])
    </div>
</div>

<!-- Modal Daftarkan Akun Baru -->
<div id="modal-create-user" class="hidden fixed inset-0 z-50 overflow-y-auto bg-[#173B70]/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-lg w-full overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="bg-[#173B70] text-white px-6 py-4 flex items-center justify-between border-b border-[#315D91]">
            <div class="flex items-center gap-2">
                <span>➕</span>
                <h3 class="text-sm font-bold tracking-tight">Daftarkan Akun Tim Internal Baru</h3>
            </div>
            <button type="button"
                    onclick="document.getElementById('modal-create-user').classList.add('hidden')"
                    class="text-slate-400 hover:text-white text-lg font-bold">
                ✕
            </button>
        </div>

        <form method="POST" action="{{ route('internal.users.store') }}" class="p-6 space-y-4">
            @csrf

            <div>
                <label for="create_name" class="block text-xs font-semibold text-slate-700 mb-1">
                    Nama Lengkap Akun <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="name"
                       id="create_name"
                       required
                       placeholder="Misal: Siti Rahmawati"
                       value="{{ old('name') }}"
                       class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#173B70] bg-white">
            </div>

            <div>
                <label for="create_email" class="block text-xs font-semibold text-slate-700 mb-1">
                    Alamat Email Operasional <span class="text-red-500">*</span>
                </label>
                <input type="email"
                       name="email"
                       id="create_email"
                       required
                       placeholder="nama@tanggap-terang.id"
                       value="{{ old('email') }}"
                       class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#173B70] bg-white font-telemetry">
            </div>

            <div>
                <label for="create_role" class="block text-xs font-semibold text-slate-700 mb-1">
                    Peran Akun <span class="text-red-500">*</span>
                </label>
                <select name="role"
                        id="create_role"
                        required
                        class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#173B70] bg-white">
                    <option value="">-- Pilih Peran Akun --</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->value }}" {{ old('role') === $role->value ? 'selected' : '' }}>
                            {{ $role->label() }} ({{ $role->value }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="create_password" class="block text-xs font-semibold text-slate-700 mb-1">
                        Kata Sandi Awal <span class="text-red-500">*</span>
                    </label>
                    <input type="password"
                           name="password"
                           id="create_password"
                           required
                           placeholder="Minimal 8 karakter"
                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#173B70] bg-white">
                </div>
                <div>
                    <label for="create_password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">
                        Konfirmasi Kata Sandi <span class="text-red-500">*</span>
                    </label>
                    <input type="password"
                           name="password_confirmation"
                           id="create_password_confirmation"
                           required
                           placeholder="Ketik ulang sandi"
                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#173B70] bg-white">
                </div>
            </div>

            <p class="text-[11px] text-slate-500">
                Akun baru akan langsung berstatus <span class="text-emerald-700 font-bold">Aktif</span> dan dapat langsung masuk ke portal internal menggunakan email dan kata sandi yang ditentukan.
            </p>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button"
                        onclick="document.getElementById('modal-create-user').classList.add('hidden')"
                        class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 text-xs font-bold transition hover:bg-slate-50">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 rounded-lg bg-[#173B70] hover:bg-[#0F2A52] text-white text-xs font-bold uppercase tracking-wider transition cursor-pointer">
                    Simpan & Buat Akun
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

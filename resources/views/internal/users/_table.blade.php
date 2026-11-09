@if($userGroup->isEmpty())
    <div class="p-6 text-center text-slate-400 text-xs font-telemetry">
        Belum ada akun dengan peran ini.
    </div>
@else
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-telemetry text-[10px] tracking-wider">
                    <th class="py-3 px-4">Nama Pengguna</th>
                    <th class="py-3 px-4">Identitas / Email</th>
                    <th class="py-3 px-4">Peran</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4">Waktu Terdaftar</th>
                    <th class="py-3 px-4 text-right">Tindakan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($userGroup as $item)
                    @php
                        $isSelf = (auth()->id() === $item->id);
                        $roleValue = $item->role?->value ?? (string) $item->role;
                        $roleLabel = $item->role?->label() ?? ucfirst($roleValue);

                        $badgeRole = match ($roleValue) {
                            'admin' => 'bg-amber-100 text-amber-900 border border-amber-300',
                            'verifikator' => 'bg-blue-100 text-blue-900 border border-blue-300',
                            'operator' => 'bg-emerald-100 text-emerald-900 border border-emerald-300',
                            default => 'bg-slate-100 text-slate-800 border border-slate-300',
                        };
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition {{ $isSelf ? 'bg-amber-50/30' : '' }}">
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-full bg-slate-200 text-slate-700 font-bold flex items-center justify-center shrink-0 text-xs">
                                    {{ strtoupper(substr($item->name, 0, 1)) }}
                                </div>
                                <div>
                                    <span class="font-bold text-slate-900 block">{{ $item->name }}</span>
                                    @if($isSelf)
                                        <span class="inline-block px-1.5 py-0.2 rounded bg-amber-200 text-amber-900 font-telemetry text-[9px] font-bold">
                                            Akun Anda
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4 font-telemetry text-slate-600">
                            {{ $item->email }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $badgeRole }}">
                                {{ $roleLabel }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            @if($item->is_active)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 font-telemetry text-slate-500 text-[11px]">
                            {{ $item->created_at ? $item->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') : '—' }}
                        </td>
                        <td class="py-3 px-4 text-right">
                            <button type="button"
                                    onclick="document.getElementById('modal-edit-user-{{ $item->id }}').classList.remove('hidden')"
                                    class="px-2.5 py-1 rounded border border-slate-300 bg-white hover:bg-slate-100 text-slate-800 text-[11px] font-bold transition cursor-pointer">
                                ⚙️ Kelola
                            </button>
                        </td>
                    </tr>

                    <!-- Modal Edit User -->
                    <div id="modal-edit-user-{{ $item->id }}" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-lg w-full overflow-hidden text-left">
                            <div class="bg-slate-900 text-white px-6 py-4 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span>⚙️</span>
                                    <h3 class="text-sm font-bold tracking-tight">Kelola Akun: {{ $item->name }}</h3>
                                </div>
                                <button type="button"
                                        onclick="document.getElementById('modal-edit-user-{{ $item->id }}').classList.add('hidden')"
                                        class="text-slate-400 hover:text-white text-lg font-bold">
                                    ✕
                                </button>
                            </div>

                            <form method="POST" action="{{ route('internal.users.update', $item) }}" class="p-6 space-y-4">
                                @csrf
                                @method('PUT')

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Nama Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           name="name"
                                           value="{{ old('name', $item->name) }}"
                                           required
                                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-slate-900 bg-white">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">
                                        Alamat Email / Identitas
                                    </label>
                                    <input type="email"
                                           value="{{ $item->email }}"
                                           disabled
                                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 bg-slate-100 text-slate-500 cursor-not-allowed font-telemetry">
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                                            Peran Akun <span class="text-red-500">*</span>
                                        </label>
                                        @if($isSelf)
                                            <input type="hidden" name="role" value="{{ $roleValue }}">
                                            <div class="px-3 py-2 text-xs font-bold bg-slate-100 border border-slate-200 rounded-lg text-slate-600">
                                                {{ $roleLabel }} (Terkunci)
                                            </div>
                                            <span class="text-[10px] text-slate-400 mt-1 block">
                                                Tidak dapat mengubah peran akun sendiri.
                                            </span>
                                        @else
                                            <select name="role"
                                                    required
                                                    class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-slate-900 bg-white">
                                                @foreach(\App\Enums\UserRole::cases() as $r)
                                                    <option value="{{ $r->value }}" {{ old('role', $roleValue) === $r->value ? 'selected' : '' }}>
                                                        {{ $r->label() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                                            Status Akses <span class="text-red-500">*</span>
                                        </label>
                                        @if($isSelf)
                                            <input type="hidden" name="is_active" value="1">
                                            <div class="px-3 py-2 text-xs font-bold bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-800">
                                                Aktif (Terkunci)
                                            </div>
                                            <span class="text-[10px] text-slate-400 mt-1 block">
                                                Tidak dapat menonaktifkan akun sendiri.
                                            </span>
                                        @else
                                            <select name="is_active"
                                                    required
                                                    class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-slate-900 bg-white">
                                                <option value="1" {{ old('is_active', $item->is_active ? '1' : '0') === '1' ? 'selected' : '' }}>
                                                    Aktif
                                                </option>
                                                <option value="0" {{ old('is_active', $item->is_active ? '1' : '0') === '0' ? 'selected' : '' }}>
                                                    Nonaktif
                                                </option>
                                            </select>
                                        @endif
                                    </div>
                                </div>

                                <!-- Reset Password oleh Admin -->
                                <div class="border-t border-slate-100 pt-3 space-y-2">
                                    <label class="block text-xs font-semibold text-slate-700">
                                        Reset Kata Sandi Akun (Opsional)
                                    </label>
                                    <p class="text-[11px] text-slate-500">
                                        Kosongkan jika tidak ingin mengubah kata sandi akun ini.
                                    </p>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <input type="password"
                                               name="password"
                                               placeholder="Kata sandi baru (min. 8)"
                                               class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300 focus:ring-2 focus:ring-slate-900 bg-white">
                                        <input type="password"
                                               name="password_confirmation"
                                               placeholder="Konfirmasi sandi baru"
                                               class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300 focus:ring-2 focus:ring-slate-900 bg-white">
                                    </div>
                                </div>

                                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                                    <button type="button"
                                            onclick="document.getElementById('modal-edit-user-{{ $item->id }}').classList.add('hidden')"
                                            class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 text-xs font-bold transition hover:bg-slate-50">
                                        Batal
                                    </button>
                                    <button type="submit"
                                            class="px-4 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold uppercase tracking-wider transition cursor-pointer">
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

@extends('layouts.internal', [
    'title' => 'Audit Log Aktivitas — TanggapTerang',
    'internalMenu' => 'audit-logs'
])

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-200 pb-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-900 text-white font-bold text-sm">
                    📜
                </span>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                    Audit Log Aktivitas Internal
                </h1>
            </div>
            <p class="text-xs sm:text-sm text-slate-600 mt-1">
                Catatan kronologis dan akuntabilitas seluruh aktivitas akun internal: sesi, verifikasi laporan, tindak lanjut, dan perubahan data.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-2.5 py-1 rounded-full bg-slate-100 border border-slate-300 font-telemetry text-[11px] font-bold text-slate-700">
                Append-Only • Strictly Read-Only
            </span>
        </div>
    </div>

    <!-- Security & Integrity Notice -->
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-xs text-amber-900 flex items-start gap-3">
        <span class="text-base shrink-0">🔒</span>
        <div>
            <strong class="font-bold">Integritas Riwayat Forensik:</strong>
            <p class="mt-0.5 text-amber-800 leading-relaxed">
                Log aktivitas bersifat permanen dan tidak dapat disunting atau dihapus oleh siapapun melalui antarmuka web. Seluruh kredensial sensitif seperti kata sandi telah disanitasi sebelum disimpan ke basis data.
            </p>
        </div>
    </div>

    <!-- Metric Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 font-telemetry block">Total Aktivitas</span>
            <span class="text-2xl font-black text-slate-900 mt-1 block font-telemetry">{{ number_format($stats['total']) }}</span>
            <span class="text-[10px] text-slate-400 font-telemetry">Sejak awal pencatatan</span>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 font-telemetry block">Hari Ini</span>
            <span class="text-2xl font-black text-emerald-600 mt-1 block font-telemetry">{{ number_format($stats['today']) }}</span>
            <span class="text-[10px] text-emerald-700 font-telemetry">Aktivitas 24 jam terakhir</span>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 font-telemetry block">Aksi Admin</span>
            <span class="text-2xl font-black text-amber-700 mt-1 block font-telemetry">{{ number_format($stats['admin_actions']) }}</span>
            <span class="text-[10px] text-slate-400 font-telemetry">Pengelolaan sistem & intelijen</span>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 font-telemetry block">Aksi Verifikator</span>
            <span class="text-2xl font-black text-blue-700 mt-1 block font-telemetry">{{ number_format($stats['verifikator_actions']) }}</span>
            <span class="text-[10px] text-slate-400 font-telemetry">Verifikasi laporan & bantuan</span>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 col-span-2 sm:col-span-1">
            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 font-telemetry block">Aksi Operator</span>
            <span class="text-2xl font-black text-emerald-700 mt-1 block font-telemetry">{{ number_format($stats['operator_actions']) }}</span>
            <span class="text-[10px] text-slate-400 font-telemetry">Operasional kejadian & tindak lanjut</span>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs">
        <form method="GET" action="{{ route('internal.audit-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 text-xs">
            <div class="lg:col-span-4">
                <label for="q" class="block font-semibold text-slate-700 mb-1">Pencarian</label>
                <input type="text"
                       name="q"
                       id="q"
                       placeholder="Cari nama pelaku, label objek, IP, deskripsi..."
                       value="{{ $filters['q'] }}"
                       class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-900 bg-white">
            </div>

            <div class="lg:col-span-3">
                <label for="activity_type" class="block font-semibold text-slate-700 mb-1">Jenis Aktivitas</label>
                <select name="activity_type"
                        id="activity_type"
                        class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-900 bg-white">
                    <option value="">Semua Jenis Aktivitas</option>
                    @foreach($availableActivityTypes as $code => $lbl)
                        <option value="{{ $code }}" {{ $filters['activity_type'] === $code ? 'selected' : '' }}>
                            {{ $lbl }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-2">
                <label for="actor_role" class="block font-semibold text-slate-700 mb-1">Peran Pelaku</label>
                <select name="actor_role"
                        id="actor_role"
                        class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-900 bg-white">
                    <option value="">Semua Peran</option>
                    <option value="admin" {{ $filters['actor_role'] === 'admin' ? 'selected' : '' }}>Administrator</option>
                    <option value="verifikator" {{ $filters['actor_role'] === 'verifikator' ? 'selected' : '' }}>Verifikator</option>
                    <option value="operator" {{ $filters['actor_role'] === 'operator' ? 'selected' : '' }}>Operator</option>
                    <option value="system" {{ $filters['actor_role'] === 'system' ? 'selected' : '' }}>Sistem / Anonim</option>
                </select>
            </div>

            <div class="lg:col-span-3 flex items-end gap-2">
                <div class="w-1/2">
                    <label for="date_from" class="block font-semibold text-slate-700 mb-1">Dari</label>
                    <input type="date"
                           name="date_from"
                           id="date_from"
                           value="{{ $filters['date_from'] }}"
                           class="w-full px-2 py-2 rounded-lg border border-slate-300 text-xs bg-white font-telemetry">
                </div>
                <div class="w-1/2">
                    <label for="date_to" class="block font-semibold text-slate-700 mb-1">Sampai</label>
                    <input type="date"
                           name="date_to"
                           id="date_to"
                           value="{{ $filters['date_to'] }}"
                           class="w-full px-2 py-2 rounded-lg border border-slate-300 text-xs bg-white font-telemetry">
                </div>
            </div>

            <div class="sm:col-span-2 lg:col-span-12 flex justify-end gap-2 pt-1 border-t border-slate-100">
                @if(array_filter($filters))
                    <a href="{{ route('internal.audit-logs.index') }}"
                       class="px-3 py-1.5 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-50 font-bold transition">
                        Reset Filter
                    </a>
                @endif
                <button type="submit"
                        class="px-4 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-bold transition cursor-pointer">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Table Audit Log -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        @if($logs->isEmpty())
            <div class="p-12 text-center space-y-2">
                <span class="text-3xl block">🔍</span>
                <p class="text-sm font-bold text-slate-700">Tidak ada catatan audit log yang sesuai.</p>
                <p class="text-xs text-slate-400">Coba sesuaikan kriteria pencarian atau filter yang digunakan.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-telemetry text-[10px] tracking-wider">
                            <th class="py-3 px-4">Waktu (WIB)</th>
                            <th class="py-3 px-4">Pelaku & Peran</th>
                            <th class="py-3 px-4">Jenis Aktivitas</th>
                            <th class="py-3 px-4">Objek Sasaran</th>
                            <th class="py-3 px-4">Deskripsi Aktivitas</th>
                            <th class="py-3 px-4 text-right">Detail Teknis</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($logs as $log)
                            @php
                                $roleBadge = match ($log->actor_role) {
                                    'admin' => 'bg-amber-100 text-amber-900 border border-amber-300',
                                    'verifikator' => 'bg-blue-100 text-blue-900 border border-blue-300',
                                    'operator' => 'bg-emerald-100 text-emerald-900 border border-emerald-300',
                                    default => 'bg-slate-100 text-slate-800 border border-slate-300',
                                };

                                $actTypeColor = match (true) {
                                    str_starts_with($log->activity_type, 'auth.') => 'text-purple-700 bg-purple-50 border-purple-200',
                                    str_starts_with($log->activity_type, 'report.') => 'text-blue-700 bg-blue-50 border-blue-200',
                                    str_starts_with($log->activity_type, 'incident.') => 'text-amber-800 bg-amber-50 border-amber-200',
                                    str_starts_with($log->activity_type, 'aid.') => 'text-emerald-800 bg-emerald-50 border-emerald-200',
                                    str_starts_with($log->activity_type, 'user.') => 'text-red-700 bg-red-50 border-red-200',
                                    str_starts_with($log->activity_type, 'profile.') => 'text-indigo-700 bg-indigo-50 border-indigo-200',
                                    default => 'text-slate-700 bg-slate-50 border-slate-200',
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-4 whitespace-nowrap font-telemetry text-slate-700 text-[11px]">
                                    <span class="font-bold block">{{ $log->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y') }}</span>
                                    <span class="text-slate-500">{{ $log->created_at->timezone('Asia/Jakarta')->translatedFormat('H:i:s') }} WIB</span>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div>
                                            <span class="font-bold text-slate-900 block leading-tight">{{ $log->actor_name }}</span>
                                            <span class="px-1.5 py-0.2 rounded font-telemetry text-[9px] font-bold uppercase tracking-wider inline-block mt-0.5 {{ $roleBadge }}">
                                                {{ ucfirst($log->actor_role) }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="inline-block px-2 py-0.5 rounded border text-[11px] font-semibold {{ $actTypeColor }}">
                                        {{ $log->activityLabel() }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    @if($log->subject_type || $log->subject_label)
                                        <div class="font-telemetry">
                                            @if($log->subject_type)
                                                <span class="text-[9px] uppercase tracking-wider text-slate-400 block font-bold">
                                                    {{ $log->subject_type }}
                                                </span>
                                            @endif
                                            <span class="font-bold text-slate-800 text-[11px]">
                                                {{ $log->subject_label ?: "ID: {$log->subject_id}" }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-slate-400 font-telemetry">—</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 max-w-md">
                                    <p class="text-slate-700 text-xs leading-relaxed line-clamp-2">
                                        {{ $log->description }}
                                    </p>
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <button type="button"
                                            onclick="document.getElementById('modal-audit-detail-{{ $log->id }}').classList.remove('hidden')"
                                            class="px-2 py-1 rounded border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 text-[11px] font-telemetry font-bold transition cursor-pointer">
                                        Rincian ℹ️
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal Rincian Audit Log -->
                            <div id="modal-audit-detail-{{ $log->id }}" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
                                <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-lg w-full overflow-hidden text-left">
                                    <div class="bg-slate-900 text-white px-6 py-4 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span>📜</span>
                                            <h3 class="text-sm font-bold tracking-tight">Rincian Audit Log #{{ $log->id }}</h3>
                                        </div>
                                        <button type="button"
                                                onclick="document.getElementById('modal-audit-detail-{{ $log->id }}').classList.add('hidden')"
                                                class="text-slate-400 hover:text-white text-lg font-bold">
                                            ✕
                                        </button>
                                    </div>

                                    <div class="p-6 space-y-4 text-xs">
                                        <div class="grid grid-cols-2 gap-3 border-b border-slate-100 pb-3">
                                            <div>
                                                <span class="text-slate-400 uppercase font-telemetry text-[10px] block">Waktu Tercatat</span>
                                                <span class="font-bold text-slate-900 font-telemetry">
                                                    {{ $log->created_at->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i:s') }} WIB
                                                </span>
                                            </div>
                                            <div>
                                                <span class="text-slate-400 uppercase font-telemetry text-[10px] block">Pelaku</span>
                                                <span class="font-bold text-slate-900">
                                                    {{ $log->actor_name }} ({{ ucfirst($log->actor_role) }})
                                                </span>
                                            </div>
                                        </div>

                                        <div>
                                            <span class="text-slate-400 uppercase font-telemetry text-[10px] block">Jenis Aktivitas</span>
                                            <span class="font-bold text-slate-900 text-sm">
                                                {{ $log->activityLabel() }} <span class="text-slate-400 font-normal font-telemetry text-xs">({{ $log->activity_type }})</span>
                                            </span>
                                        </div>

                                        <div>
                                            <span class="text-slate-400 uppercase font-telemetry text-[10px] block">Objek Terdampak</span>
                                            <div class="p-2.5 rounded bg-slate-50 border border-slate-200 mt-1 font-telemetry">
                                                <span class="text-slate-500 block text-[10px]">Tipe: {{ $log->subject_type ?: 'Tidak spesifik' }} | ID: {{ $log->subject_id ?: '—' }}</span>
                                                <span class="font-bold text-slate-900 block mt-0.5">{{ $log->subject_label ?: '—' }}</span>
                                            </div>
                                        </div>

                                        <div>
                                            <span class="text-slate-400 uppercase font-telemetry text-[10px] block">Deskripsi Lengkap</span>
                                            <p class="p-2.5 rounded bg-slate-50 border border-slate-200 mt-1 text-slate-800 leading-relaxed">
                                                {{ $log->description }}
                                            </p>
                                        </div>

                                        @if($log->metadata)
                                            <div>
                                                <span class="text-slate-400 uppercase font-telemetry text-[10px] block">Metadata Kontekstual (Tersanitasi)</span>
                                                <pre class="p-2.5 rounded bg-slate-900 text-emerald-400 text-[11px] font-mono mt-1 overflow-x-auto max-h-40">{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                                            </div>
                                        @endif

                                        <div class="grid grid-cols-2 gap-3 border-t border-slate-100 pt-3 font-telemetry text-[11px] text-slate-500">
                                            <div>
                                                <span class="text-slate-400 text-[10px] block">Alamat IP:</span>
                                                <span class="font-bold text-slate-700">{{ $log->ip_address ?: '—' }}</span>
                                            </div>
                                            <div>
                                                <span class="text-slate-400 text-[10px] block">User Agent:</span>
                                                <span class="truncate block text-slate-700" title="{{ $log->user_agent }}">{{ $log->user_agent ?: '—' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="px-6 py-3 bg-slate-50 border-t border-slate-200 flex justify-end">
                                        <button type="button"
                                                onclick="document.getElementById('modal-audit-detail-{{ $log->id }}').classList.add('hidden')"
                                                class="px-4 py-1.5 rounded-lg bg-slate-900 text-white text-xs font-bold transition hover:bg-slate-800">
                                            Tutup
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

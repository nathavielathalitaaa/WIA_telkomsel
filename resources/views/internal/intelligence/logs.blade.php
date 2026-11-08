@extends('layouts.internal', [
    'title' => 'Log Audit & Telemetri API Eksternal — TanggapTerang',
    'internalMenu' => 'intelligence',
])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-xs border border-slate-200 p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <a href="{{ route('internal.intelligence.index') }}" class="text-xs text-blue-600 hover:underline font-telemetry flex items-center gap-1 mb-1">
                    ← Kembali ke Pusat Intelijen
                </a>
                <h1 class="text-xl sm:text-2xl font-bold font-telemetry text-slate-900 tracking-tight">
                    Log Audit & Telemetri Pengambilan Data Eksternal
                </h1>
                <p class="text-sm text-slate-600 mt-1">
                    Rekam jejak setiap panggilan HTTP keluar ke endpoint resmi: durasi latensi, status HTTP, jumlah data diterima, dan diagnostik error.
                </p>
            </div>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 font-telemetry uppercase text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">Waktu Eksekusi</th>
                        <th class="px-4 py-3">Provider</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">HTTP</th>
                        <th class="px-4 py-3">Latensi</th>
                        <th class="px-4 py-3">Diterima / Tersimpan</th>
                        <th class="px-4 py-3">Dieksekusi Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        @php
                            $badge = match ($log->status) {
                                'success' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                'partial', 'empty', 'rate_limited' => 'bg-amber-100 text-amber-800 border-amber-200',
                                'invalid_response', 'http_error', 'timeout', 'connection_error', 'failed_persist' => 'bg-red-100 text-red-800 border-red-200',
                                'not_integrated' => 'bg-slate-200 text-slate-700 border-slate-300',
                                default => 'bg-slate-100 text-slate-800',
                            };
                            $label = \App\Models\ExternalProvider::statusLabel($log->status, true);
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 whitespace-nowrap font-telemetry text-slate-600">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="font-bold text-slate-900">{{ $log->provider?->name ?? $log->provider_code }}</div>
                                <div class="text-[10px] font-mono text-slate-400 truncate max-w-xs">{{ $log->endpoint }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap font-telemetry text-[11px]">
                                {{ $log->category === 'dalam_negeri' ? 'Domestik' : 'Internasional' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase {{ $badge }} border" title="{{ $log->status }}">
                                    {{ $label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap font-mono font-bold text-slate-800">
                                {{ $log->http_status ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap font-telemetry text-slate-600">
                                {{ $log->duration_ms ? $log->duration_ms.' ms' : '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap font-telemetry text-slate-900">
                                <span class="font-bold">{{ $log->record_count }}</span>
                                <span class="text-[10px] text-slate-500">
                                    / {{ $log->received_count ?? $log->record_count }}
                                    @if($log->rejected_count > 0)
                                        <span class="text-red-600">({{ $log->rejected_count }} ditolak)</span>
                                    @endif
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-slate-500">
                                {{ $log->user?->name ?? 'System' }}
                            </td>
                        </tr>
                        @if($log->error_message)
                            <tr class="bg-red-50/40">
                                <td colspan="8" class="px-4 py-1.5 text-[11px] text-red-700 font-mono">
                                    Diagnostic: {{ $log->error_message }}
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-500 text-xs">
                                Belum ada log pemanggilan API eksternal yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection


@extends('layouts.internal', [
    'title' => 'Detail Rekaman Intelijen Eksternal — TanggapTerang',
    'internalMenu' => 'intelligence',
])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <a href="{{ route('internal.intelligence.index') }}" class="text-xs text-blue-600 hover:underline font-telemetry flex items-center gap-1 mb-1">
                    ← Kembali ke Daftar Rekaman
                </a>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-lg sm:text-xl font-bold font-telemetry text-[#202938] tracking-tight">
                        Detail Rekaman Data Eksternal #{{ $record->id }}
                    </h1>
                    <span class="px-2 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase {{ $record->category === 'dalam_negeri' ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-purple-100 text-purple-800 border border-purple-200' }}">
                        {{ $record->category === 'dalam_negeri' ? 'Dalam Negeri' : 'Internasional' }}
                    </span>
                    <span class="px-2 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase bg-slate-100 text-slate-800 border border-slate-200">
                        {{ $record->info_type }}
                    </span>
                </div>
            </div>
            @if($record->source_url)
                <a href="{{ $record->source_url }}" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-300 hover:bg-slate-50 text-slate-700 font-telemetry text-xs font-bold uppercase transition shrink-0">
                    Buka URL Sumber Asli ↗
                </a>
            @endif
        </div>
    </div>

    <!-- Metadata Card -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 sm:p-6 space-y-4">
        <h2 class="text-base font-bold font-telemetry text-[#202938] border-b border-slate-100 pb-2">
            Informasi Terstandarisasi
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
            <div>
                <span class="text-slate-400 font-telemetry uppercase text-[10px] font-bold block">Provider Sumber</span>
                <span class="font-bold text-[#202938] text-sm">{{ $record->provider?->name ?? $record->provider_code }}</span>
            </div>
            <div>
                <span class="text-slate-400 font-telemetry uppercase text-[10px] font-bold block">External ID</span>
                <span class="font-mono text-slate-800 bg-slate-100 px-1.5 py-0.5 rounded">{{ $record->external_id ?? '-' }}</span>
            </div>
            <div>
                <span class="text-slate-400 font-telemetry uppercase text-[10px] font-bold block">Status Validasi</span>
                <span class="inline-block mt-0.5 px-2 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase {{ $record->validation_status === 'valid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ $record->validation_status }}
                </span>
            </div>
            <div>
                <span class="text-slate-400 font-telemetry uppercase text-[10px] font-bold block">Lokasi Tercatat</span>
                <span class="font-medium text-slate-800">{{ $record->location_name ?? '-' }}</span>
            </div>
            <div>
                <span class="text-slate-400 font-telemetry uppercase text-[10px] font-bold block">Koordinat Geografis</span>
                <span class="font-mono text-slate-800">
                    {{ $record->latitude ?? '-' }}, {{ $record->longitude ?? '-' }}
                </span>
            </div>
            <div>
                <span class="text-slate-400 font-telemetry uppercase text-[10px] font-bold block">Waktu Kejadian / Prakiraan</span>
                <span class="font-telemetry text-slate-800">
                    {{ $record->occurred_at ? $record->occurred_at->format('d M Y H:i:s WIB') : '-' }}
                </span>
            </div>
            <div>
                <span class="text-slate-400 font-telemetry uppercase text-[10px] font-bold block">Waktu Diterbitkan Sumber</span>
                <span class="font-telemetry text-slate-800">
                    {{ $record->published_at ? $record->published_at->format('d M Y H:i:s WIB') : '-' }}
                </span>
            </div>
            <div>
                <span class="text-slate-400 font-telemetry uppercase text-[10px] font-bold block">Waktu Diambil Sistem</span>
                <span class="font-telemetry text-slate-800">
                    {{ $record->fetched_at ? $record->fetched_at->format('d M Y H:i:s WIB') : '-' }}
                </span>
            </div>
            <div>
                <span class="text-slate-400 font-telemetry uppercase text-[10px] font-bold block">Status Kurasi Admin</span>
                <span class="font-medium text-slate-800">
                    {{ $record->is_curated ? 'Telah dikurasi oleh '.$record->curator?->name : 'Belum dikurasi' }}
                </span>
            </div>
        </div>

        <div class="pt-3 border-t border-slate-100 space-y-1">
            <span class="text-slate-400 font-telemetry uppercase text-[10px] font-bold block">Judul Ringkasan</span>
            <div class="font-bold text-[#202938] text-sm">{{ $record->title }}</div>
            <div class="text-xs text-slate-600 mt-1 bg-slate-50 p-3 rounded-xl border border-slate-100">
                {{ $record->summary ?? 'Tidak ada ringkasan teks.' }}
            </div>
        </div>
    </div>

    <!-- Raw Payload JSON Card -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-100/90 overflow-hidden">
        <div class="p-4 bg-[#173B70] text-white flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                <h3 class="font-bold font-telemetry text-xs uppercase tracking-wider">
                    Raw API Payload (JSON Asli dari Provider)
                </h3>
            </div>
            <span class="text-[11px] font-telemetry text-slate-400">Read-Only Telemetri</span>
        </div>
        <div class="p-4 bg-slate-950 text-emerald-400 font-mono text-xs overflow-x-auto max-h-[500px]">
            <pre>{{ json_encode($record->raw_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    </div>
</div>
@endsection


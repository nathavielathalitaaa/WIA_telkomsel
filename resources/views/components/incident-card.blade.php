@props(['incident', 'showActions' => true])

@php
    $severityVal = $incident->severity instanceof \App\Enums\IncidentSeverity
        ? $incident->severity->value
        : (string) $incident->severity;

    $stripeColor = match ($severityVal) {
        'kritis' => 'bg-[#DC2626]',
        'tinggi' => 'bg-[#D97706]',
        'sedang' => 'bg-amber-500',
        default => 'bg-[#059669]',
    };

    $borderClass = $severityVal === 'kritis'
        ? 'border-[#FCA5A5]'
        : 'border-[#E2E8F0]';

    $typeLabel = ucwords(str_replace('_', ' ', $incident->incident_type));
    $reportsCount = $incident->reports_count ?? $incident->reports?->count() ?? 0;
    $sourcesCount = $incident->sources_count ?? $incident->sources?->count() ?? 0;
    $aidResponsesCount = $incident->aid_responses_count ?? $incident->aidResponses?->count() ?? 0;
@endphp

<article class="bg-white border {{ $borderClass }} rounded-lg p-4 sm:p-5 relative overflow-hidden flex flex-col justify-between gap-4 transition hover:border-slate-300">
    <div class="absolute top-0 left-0 bottom-0 w-1.5 {{ $stripeColor }}"></div>

    <div class="pl-2 flex flex-col gap-3">
        <!-- Top Meta Row -->
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div class="flex flex-wrap items-center gap-1.5">
                @if($incident->is_simulation)
                    <span class="px-2 py-0.5 rounded bg-slate-900 text-white font-telemetry text-[10px] font-bold uppercase tracking-wider">
                        Data Simulasi
                    </span>
                @endif
                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200 font-telemetry text-[11px] font-bold uppercase tracking-wider">
                    {{ $typeLabel }}
                </span>
                <x-severity-badge :severity="$incident->severity" />
            </div>

            <time datetime="{{ $incident->occurred_at?->toIso8601String() }}" class="font-telemetry text-xs text-slate-500 font-semibold">
                {{ $incident->occurred_at?->diffForHumans() ?? '-' }}
            </time>
        </div>

        <!-- Title & Location -->
        <div>
            <h3 class="text-base sm:text-lg font-bold text-[#0F172A] leading-snug">
                <a href="{{ route('public.incidents.show', $incident->slug) }}" class="hover:text-[#2563EB] transition-colors">
                    {{ $incident->title }}
                </a>
            </h3>
            <div class="flex items-center gap-1.5 text-slate-600 text-xs sm:text-sm mt-1">
                <svg class="w-4 h-4 text-[#DC2626] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="font-medium">{{ $incident->location_name }}</span>
                @if($incident->wilayahAdministratif)
                    <span class="text-slate-400">•</span>
                    <span class="font-telemetry text-xs text-slate-500">{{ $incident->wilayahAdministratif->nama }}</span>
                @endif
            </div>
        </div>

        <!-- Description Excerpt -->
        <p class="text-sm text-slate-600 line-clamp-2 leading-relaxed">
            {{ $incident->description }}
        </p>

        <!-- Dual Status Separation Box (Verification vs Follow-up) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
            <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded p-2.5 flex flex-col gap-1">
                <span class="font-telemetry text-[10px] font-bold uppercase tracking-wider text-slate-500">
                    1. Status Verifikasi Informasi
                </span>
                <div>
                    <x-verification-badge :status="$incident->verification_status" />
                </div>
            </div>
            <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded p-2.5 flex flex-col gap-1">
                <span class="font-telemetry text-[10px] font-bold uppercase tracking-wider text-slate-500">
                    2. Status Tindak Lanjut Tim
                </span>
                <div>
                    <x-followup-badge :status="$incident->follow_up_status" />
                </div>
            </div>
        </div>
    </div>

    <!-- Card Footer: Counts & Actions -->
    <div class="pl-2 pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 font-telemetry text-xs text-slate-600">
            <span class="inline-flex items-center gap-1 font-semibold">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                {{ $reportsCount }} Laporan Warga
            </span>
            <span class="text-slate-300">|</span>
            <span class="inline-flex items-center gap-1 font-semibold">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                </svg>
                {{ $sourcesCount }} Sumber Terbuka
            </span>
            <span class="text-slate-300">|</span>
            <span class="inline-flex items-center gap-1 font-semibold text-blue-700">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                {{ $aidResponsesCount }} Respons Bantuan
            </span>
        </div>

        @if($showActions)
            <div class="flex items-center gap-2">
                @if(\Illuminate\Support\Facades\Route::has('public.sitrep'))
                    <a href="{{ route('public.sitrep', ['incident' => $incident->slug]) }}" class="px-3 py-1.5 rounded border border-slate-300 text-xs font-telemetry font-bold text-[#0F172A] hover:bg-slate-100 transition">
                        SitRep
                    </a>
                @endif
                <a href="{{ route('public.incidents.show', $incident->slug) }}" class="px-3.5 py-1.5 rounded bg-[#0F172A] text-white text-xs font-telemetry font-bold hover:bg-slate-800 transition inline-flex items-center gap-1">
                    <span>Lihat Detail</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        @endif
    </div>
</article>

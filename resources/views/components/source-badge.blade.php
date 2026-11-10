@props(['type', 'credibility' => null, 'isSimulation' => true])

@php
    $value = $type instanceof \App\Enums\SourceType ? $type->value : (string) $type;
    $label = $type instanceof \App\Enums\SourceType ? $type->label((bool) $isSimulation) : match ($value) {
        'laporan_masyarakat', 'laporan_warga' => 'Laporan Warga',
        'respons_bantuan', 'respons_bantuan_masyarakat' => 'Respons Bantuan Masyarakat',
        'data_pemerintah_terbuka' => 'Data Pemerintah Terbuka',
        'instansi_resmi' => $isSimulation ? 'Sumber Pemerintah (Simulasi)' : 'Data Pemerintah Terbuka',
        'media_massa' => 'Sumber Informasi Terbuka (Media)',
        'media_sosial' => 'Sumber Informasi Terbuka (Publik)',
        default => 'Sumber Informasi Terbuka',
    };

    $classes = match ($value) {
        'instansi_resmi', 'data_pemerintah_terbuka' => 'bg-blue-50 text-[#173B70] border-blue-200/80',
        'media_massa', 'sumber_informasi_terbuka' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'laporan_masyarakat', 'laporan_warga' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'respons_bantuan', 'respons_bantuan_masyarakat' => 'bg-amber-50 text-amber-800 border-amber-300',
        default => 'bg-slate-100 text-slate-700 border-slate-300',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2 py-0.5 rounded border font-telemetry text-[11px] font-bold uppercase tracking-wider {$classes}"]) }}>
    <span>{{ $label }}</span>
    @if($credibility)
        <span class="opacity-70 border-l border-current pl-1.5">Kredibilitas: {{ strtoupper($credibility) }}</span>
    @endif
</span>

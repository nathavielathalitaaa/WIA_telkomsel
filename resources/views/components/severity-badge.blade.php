@props(['severity'])

@php
    $value = $severity instanceof \App\Enums\IncidentSeverity ? $severity->value : (string) $severity;
    $label = $severity instanceof \App\Enums\IncidentSeverity ? $severity->label() : match ($value) {
        'kritis' => 'Kritis / Darurat',
        'tinggi' => 'Tinggi',
        'sedang' => 'Sedang',
        'rendah' => 'Rendah',
        default => ucfirst($value),
    };

    $classes = match ($value) {
        'kritis' => 'bg-[#FEF2F2] text-[#DC2626] border-[#FCA5A5]',
        'tinggi' => 'bg-[#FFFBEB] text-[#D97706] border-[#FCD34D]',
        'sedang' => 'bg-amber-50/80 text-amber-800 border-amber-300',
        default => 'bg-[#ECFDF5] text-[#059669] border-[#6EE7B7]',
    };

    $dotColor = match ($value) {
        'kritis' => 'bg-[#DC2626] animate-ping',
        'tinggi' => 'bg-[#D97706]',
        'sedang' => 'bg-amber-600',
        default => 'bg-[#059669]',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2 py-0.5 rounded border font-telemetry text-[11px] font-bold uppercase tracking-wider {$classes}"]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }} shrink-0"></span>
    <span>Dampak: {{ $label }}</span>
</span>

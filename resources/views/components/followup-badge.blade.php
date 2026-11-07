@props(['status'])

@php
    $value = $status instanceof \App\Enums\FollowUpStatus ? $status->value : (string) $status;
    $label = $status instanceof \App\Enums\FollowUpStatus ? $status->label() : match ($value) {
        'belum_ditindaklanjuti' => 'Belum Ditindaklanjuti',
        'sedang_ditindaklanjuti' => 'Sedang Ditindaklanjuti',
        'dipantau' => 'Dalam Pemantauan',
        'selesai' => 'Selesai Ditindaklanjuti',
        'dalam_proses' => 'Dalam Proses',
        'direncanakan' => 'Direncanakan',
        'dibatalkan' => 'Dibatalkan',
        default => ucfirst(str_replace('_', ' ', $value)),
    };

    $classes = match ($value) {
        'selesai' => 'bg-[#ECFDF5] text-[#059669] border-[#6EE7B7]',
        'sedang_ditindaklanjuti', 'dalam_proses' => 'bg-[#FFFBEB] text-[#D97706] border-[#FCD34D]',
        'dipantau', 'direncanakan' => 'bg-[#EFF6FF] text-[#1D4ED8] border-[#93C5FD]',
        default => 'bg-slate-100 text-slate-700 border-slate-300',
    };

    $dotColor = match ($value) {
        'selesai' => 'bg-[#059669]',
        'sedang_ditindaklanjuti', 'dalam_proses' => 'bg-[#D97706]',
        'dipantau', 'direncanakan' => 'bg-[#1D4ED8]',
        default => 'bg-slate-500',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2 py-0.5 rounded border font-telemetry text-[11px] font-bold uppercase tracking-wider {$classes}"]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }} shrink-0"></span>
    <span>{{ $label }}</span>
</span>

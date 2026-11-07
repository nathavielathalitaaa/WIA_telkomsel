@props(['status'])

@php
    $value = $status instanceof \App\Enums\VerificationStatus ? $status->value : (string) $status;
    $label = $status instanceof \App\Enums\VerificationStatus ? $status->label() : match ($value) {
        'terverifikasi' => 'Terverifikasi',
        'menunggu_verifikasi' => 'Menunggu Verifikasi',
        'ditolak' => 'Ditolak',
        'duplikat' => 'Duplikat',
        'tidak_valid' => 'Tidak Valid',
        default => ucfirst(str_replace('_', ' ', $value)),
    };

    $classes = match ($value) {
        'terverifikasi' => 'bg-[#EFF6FF] text-[#2563EB] border-[#93C5FD]',
        'menunggu_verifikasi' => 'bg-[#FFFBEB] text-[#D97706] border-[#FCD34D]',
        'ditolak', 'tidak_valid' => 'bg-[#FEF2F2] text-[#DC2626] border-[#FCA5A5]',
        default => 'bg-slate-100 text-slate-700 border-slate-300',
    };

    $dotColor = match ($value) {
        'terverifikasi' => 'bg-[#2563EB]',
        'menunggu_verifikasi' => 'bg-[#D97706]',
        'ditolak', 'tidak_valid' => 'bg-[#DC2626]',
        default => 'bg-slate-500',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2 py-0.5 rounded border font-telemetry text-[11px] font-bold uppercase tracking-wider {$classes}"]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }} shrink-0"></span>
    <span>{{ $label }}</span>
</span>

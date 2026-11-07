@props(['status'])

@php
    use App\Enums\AidResponseStatus;

    $enum = $status instanceof AidResponseStatus
        ? $status
        : AidResponseStatus::tryFrom((string) $status);

    [$classes, $dotColor, $label] = match ($enum) {
        AidResponseStatus::Terkonfirmasi => [
            'bg-emerald-50 text-emerald-800 border-emerald-300',
            'bg-emerald-600',
            'Penyerahan Dikonfirmasi',
        ],
        AidResponseStatus::DalamPenyaluran => [
            'bg-blue-50 text-blue-800 border-blue-300',
            'bg-blue-600',
            'Dalam Penyaluran',
        ],
        default => [
            'bg-amber-50 text-amber-800 border-amber-300',
            'bg-amber-600',
            $enum?->label() ?? 'Direncanakan',
        ],
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] border text-[11px] font-bold uppercase tracking-wider font-telemetry {$classes}"]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }}"></span>
    {{ $label }}
</span>

@props([
    'verificationHistories' => collect(),
    'followUpRecords' => collect(),
    'aidResponses' => collect(),
    'isInternal' => false,
])

@php
    $entries = collect();

    foreach ($verificationHistories as $vh) {
        $newStatusStr = $vh->new_status instanceof \BackedEnum ? $vh->new_status->value : (string) $vh->new_status;
        $prevStatusStr = $vh->previous_status instanceof \BackedEnum ? $vh->previous_status->value : (string) $vh->previous_status;

        $entries->push([
            'category' => 'verifikasi',
            'timestamp' => $vh->created_at,
            'title' => 'Perubahan Status Verifikasi: ' . strtoupper(str_replace('_', ' ', $newStatusStr)),
            'subtitle' => $prevStatusStr !== ''
                ? 'Dari ' . str_replace('_', ' ', $prevStatusStr) . ' menjadi ' . str_replace('_', ' ', $newStatusStr)
                : 'Penetapan status verifikasi',
            'description' => $isInternal
                ? ($vh->reason ?: 'Tidak ada catatan tambahan.')
                : 'Status verifikasi diperbarui berdasarkan pemeriksaan kelengkapan informasi dan rujukan sumber terbuka.',
            'actor' => $isInternal
                ? ($vh->user?->name ?? 'Sistem TanggapTerang')
                : 'Tim Verifikator TanggapTerang',
            'badge_status' => $newStatusStr,
            'is_simulation' => (bool) $vh->is_simulation,
        ]);
    }

    foreach ($followUpRecords as $fu) {
        $fuStatusStr = $fu->status instanceof \BackedEnum ? $fu->status->value : (string) $fu->status;

        $entries->push([
            'category' => 'tindak_lanjut',
            'timestamp' => $fu->action_date ?? $fu->created_at,
            'title' => $fu->action_title,
            'subtitle' => 'Catatan Tindak Lanjut Internal Tim (' . strtoupper(str_replace('_', ' ', $fuStatusStr)) . ')',
            'description' => $fu->notes,
            'actor' => $isInternal
                ? ($fu->user?->name ?? 'Tim Operasional TanggapTerang')
                : 'Tim Tindak Lanjut TanggapTerang',
            'badge_status' => $fuStatusStr,
            'evidence_url' => $fu->evidence_url,
            'is_simulation' => (bool) $fu->is_simulation,
        ]);
    }

    foreach ($aidResponses as $aid) {
        $aidStatusStr = $aid->status instanceof \BackedEnum ? $aid->status->value : (string) $aid->status;
        $needLabel = \App\Models\AidResponse::categoryLabel($aid->need_category);
        $subTypeLabel = method_exists($aid, 'submissionTypeLabel') ? $aid->submissionTypeLabel() : 'Respons Bantuan Masyarakat';
        $deliveredLabel = $aid->delivered_at
            ? ' • Waktu Penyerahan (Dilaporkan): ' . $aid->delivered_at->format('d M Y, H:i') . ' WIB'
            : '';
        $recordedLabel = $aid->recorded_at ? $aid->recorded_at->format('d M Y, H:i') . ' WIB' : '-';
        $confirmedLabel = $aid->confirmed_at
            ? ' • Dikonfirmasi: ' . $aid->confirmed_at->format('d M Y, H:i') . ' WIB'
            : '';

        $entries->push([
            'category' => 'respons_bantuan',
            'timestamp' => $aid->confirmed_at ?? $aid->recorded_at ?? $aid->created_at,
            'title' => $aid->aid_item_title . ' (' . $aid->quantity_label . ')',
            'subtitle' => $subTypeLabel . ' [' . $aid->reference_code . '] • Kategori: ' . $needLabel . $deliveredLabel . ' • Dicatat: ' . $recordedLabel . $confirmedLabel,
            'description' => trim(($aid->aid_description ?: 'Respons bantuan masyarakat tercatat.') . ($aid->verification_notes ? ' — Verifikasi: ' . $aid->verification_notes : '') . ($aid->confirmation_notes ? ' — Konfirmasi: ' . $aid->confirmation_notes : '')),
            'actor' => $aid->publicResponderName() . ' • Lokasi Tujuan: ' . $aid->target_location,
            'badge_status' => $aidStatusStr,
            'verification_status' => $aid->verification_status,
            'evidence_url' => $aid->evidence_url,
            'is_simulation' => (bool) $aid->is_simulation,
        ]);
    }

    $sortedEntries = $entries->sortByDesc(fn ($item) => $item['timestamp']?->getTimestamp() ?? 0)->values();
@endphp

@if($sortedEntries->isEmpty())
    <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg p-6 text-center text-sm text-slate-500">
        Belum ada riwayat perubahan verifikasi, catatan tindak lanjut internal, maupun respons bantuan yang tercatat.
    </div>
@else
    <div class="relative pl-6 space-y-5 before:content-[''] before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
        @foreach($sortedEntries as $entry)
            @php
                [$bulletColor, $timeColor, $tagClass, $tagLabel] = match ($entry['category']) {
                    'verifikasi' => [
                        'bg-[#2563EB]',
                        'text-[#2563EB]',
                        'bg-[#EFF6FF] text-[#2563EB] border border-[#93C5FD]',
                        'Jejak Verifikasi',
                    ],
                    'respons_bantuan' => [
                        'bg-amber-600',
                        'text-amber-800',
                        'bg-amber-50 text-amber-800 border border-amber-300',
                        'Respons Bantuan Masyarakat',
                    ],
                    default => [
                        'bg-[#059669]',
                        'text-[#059669]',
                        'bg-[#ECFDF5] text-[#059669] border border-[#6EE7B7]',
                        'Tindak Lanjut Internal',
                    ],
                };
            @endphp
            <div class="relative bg-white border border-[#E2E8F0] rounded-lg p-4">
                <!-- Timeline Bullet -->
                <div class="absolute -left-[22px] top-4 w-4 h-4 rounded-full border-2 border-white shadow-xs {{ $bulletColor }}"></div>

                <div class="flex flex-wrap items-center justify-between gap-2 mb-1.5">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-telemetry text-xs font-bold {{ $timeColor }}">
                            {{ $entry['timestamp'] ? \Illuminate\Support\Carbon::parse($entry['timestamp'])->format('d M Y, H:i') . ' WIB' : '-' }}
                        </span>
                        <span class="px-2 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase tracking-wider {{ $tagClass }}">
                            {{ $tagLabel }}
                        </span>
                        @if($entry['is_simulation'])
                            <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 font-telemetry text-[10px] font-bold uppercase">
                                Simulasi
                            </span>
                        @endif
                    </div>

                    @if($entry['category'] === 'verifikasi')
                        <x-verification-badge :status="$entry['badge_status']" />
                    @elseif($entry['category'] === 'respons_bantuan')
                        <div class="flex flex-wrap items-center gap-1.5">
                            @if(!empty($entry['verification_status']))
                                <x-verification-badge :status="$entry['verification_status']" />
                            @endif
                            <x-aid-status-badge :status="$entry['badge_status']" />
                        </div>
                    @else
                        <x-followup-badge :status="$entry['badge_status']" />
                    @endif
                </div>

                <h4 class="text-sm font-bold text-[#0F172A]">{{ $entry['title'] }}</h4>
                <p class="text-xs font-telemetry text-slate-500 mt-0.5">{{ $entry['subtitle'] }}</p>

                <p class="text-sm text-slate-700 mt-2 leading-relaxed">
                    {{ $entry['description'] }}
                </p>

                <div class="mt-2.5 pt-2 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500 font-telemetry">
                    <span>Pencatat / Pemberi Respons: <strong class="text-slate-700">{{ $entry['actor'] }}</strong></span>
                    @if(!empty($entry['evidence_url']))
                        <a href="{{ $entry['evidence_url'] }}" target="_blank" rel="noopener noreferrer" class="text-[#2563EB] hover:underline font-bold">
                            Lihat Lampiran Dokumentasi &rarr;
                        </a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif

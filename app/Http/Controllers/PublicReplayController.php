<?php

namespace App\Http\Controllers;

use App\Enums\FollowUpStatus;
use App\Enums\VerificationStatus;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class PublicReplayController extends Controller
{
    /**
     * Halaman Mode Replay Simulasi (GET /replay).
     * Menampilkan kronologi perjalanan informasi suatu kejadian berdasarkan data aktual di database.
     */
    public function index(Request $request): View
    {
        $incidents = Incident::query()
            ->where('verification_status', '!=', VerificationStatus::Ditolak->value)
            ->with(['wilayahAdministratif:id,nama'])
            ->withCount([
                'reports' => fn ($q) => $q->where('verification_status', '!=', VerificationStatus::Ditolak->value),
                'incidentSources',
                'verificationHistories',
                'followUpRecords',
                'aidResponses',
            ])
            ->orderByDesc('occurred_at')
            ->get();

        $requestedIncident = trim((string) $request->query('incident', ''));
        $selectedIncident = null;

        if ($requestedIncident !== '') {
            $selectedIncident = $incidents->firstWhere('slug', $requestedIncident);

            if (! $selectedIncident && ctype_digit($requestedIncident)) {
                $selectedIncident = $incidents->firstWhere('id', (int) $requestedIncident);
            }
        }

        if (! $selectedIncident && $incidents->isNotEmpty()) {
            // Prioritaskan kejadian dengan histori peristiwa terlengkap di database (tanpa hardcode ID)
            $selectedIncident = $incidents
                ->filter(fn (Incident $inc) => (
                    (int) $inc->reports_count +
                    (int) $inc->incident_sources_count +
                    (int) $inc->verification_histories_count +
                    (int) $inc->follow_up_records_count
                ) > 0)
                ->sortByDesc(fn (Incident $inc) => (
                    (int) $inc->reports_count +
                    (int) $inc->incident_sources_count +
                    (int) $inc->verification_histories_count +
                    (int) $inc->follow_up_records_count
                ))
                ->first() ?? $incidents->first();
        }

        $timelineEvents = collect();

        if ($selectedIncident) {
            $selectedIncident->load([
                'wilayahAdministratif.parent.parent.parent',
                'reports' => function ($query) {
                    // Hanya ambil kolom yang aman untuk publik (TANPA reporter_name & reporter_contact)
                    $query->select([
                        'id',
                        'ticket_number',
                        'incident_id',
                        'title',
                        'description',
                        'category',
                        'location_name',
                        'latitude',
                        'longitude',
                        'occurred_at',
                        'verification_status',
                        'confidence_level',
                        'is_simulation',
                        'verified_at',
                        'created_at',
                    ])->where('verification_status', '!=', VerificationStatus::Ditolak->value);
                },
                'incidentSources.source',
                'verificationHistories',
                'followUpRecords',
                'aidResponses' => function ($query) {
                    $query->select([
                        'id',
                        'reference_code',
                        'incident_id',
                        'report_id',
                        'need_category',
                        'organization_name',
                        'responder_type',
                        'allow_public_name',
                        'aid_item_title',
                        'aid_description',
                        'quantity_label',
                        'target_location',
                        'latitude',
                        'longitude',
                        'recorded_at',
                        'status',
                        'confirmation_notes',
                        'confirmed_at',
                        'is_simulation',
                        'created_at',
                    ]);
                },
            ]);

            $timelineEvents = $this->buildChronologicalEvents($selectedIncident);
        }

        $hasSufficientHistory = $timelineEvents->count() >= 2;

        return view('public.replay', [
            'incidents' => $incidents,
            'selectedIncident' => $selectedIncident,
            'timelineEvents' => $timelineEvents,
            'hasSufficientHistory' => $hasSufficientHistory,
            'categories' => PublicIncidentController::incidentCategories(),
        ]);
    }

    /**
     * Menyusun peristiwa kronologis dari kejadian terpilih secara terurut berdasarkan waktu aktual di database.
     * Tidak mengarang metrik eksternal dan menyembunyikan data pribadi pelapor serta catatan internal petugas.
     */
    private function buildChronologicalEvents(Incident $incident): \Illuminate\Support\Collection
    {
        $events = collect();

        // 1. Peristiwa Awal: Pencatatan / Waktu Kejadian Awal
        if ($incident->occurred_at) {
            $events->push([
                'uid' => 'inc-init-' . $incident->id,
                'type' => 'kejadian_awal',
                'type_label' => $incident->is_simulation ? 'Pencatatan Awal Kejadian' : 'Waktu Kejadian Bencana (BNPB)',
                'timestamp_iso' => $incident->occurred_at->toIso8601String(),
                'timestamp_ms' => $incident->occurred_at->getTimestampMs(),
                'timestamp_formatted' => $incident->occurred_at->format('d M Y, H:i') . ' WIB',
                'time_short' => $incident->occurred_at->format('H:i') . ' WIB',
                'title' => 'Informasi Awal Kejadian Tercatat: ' . $incident->title,
                'description' => $incident->description,
                'source_label' => $incident->is_simulation
                    ? 'Sistem Pemantauan Informasi TanggapTerang'
                    : 'Pusdatin BNPB — Data Informasi Bencana Indonesia (DIBI)',
                'verification_status' => $incident->is_simulation ? VerificationStatus::MenungguVerifikasi->value : VerificationStatus::Terverifikasi->value,
                'verification_label' => $incident->is_simulation ? VerificationStatus::MenungguVerifikasi->label() : VerificationStatus::Terverifikasi->label(),
                'follow_up_status' => $incident->is_simulation ? FollowUpStatus::BelumDitindaklanjuti->value : FollowUpStatus::Selesai->value,
                'follow_up_label' => $incident->is_simulation ? FollowUpStatus::BelumDitindaklanjuti->label() : FollowUpStatus::Selesai->label(),
                'latitude' => $incident->latitude !== null ? (float) $incident->latitude : null,
                'longitude' => $incident->longitude !== null ? (float) $incident->longitude : null,
                'ticket_number' => null,
            ]);
        }

        // 2. Laporan Masyarakat yang Terhubung ke Kejadian Ini
        foreach ($incident->reports as $report) {
            $reportTime = $report->occurred_at ?? $report->created_at ?? now();
            $vStatus = $report->verification_status instanceof VerificationStatus
                ? $report->verification_status
                : VerificationStatus::tryFrom((string) $report->verification_status);

            $events->push([
                'uid' => 'rep-' . $report->id,
                'type' => 'laporan_masyarakat',
                'type_label' => 'Laporan Masyarakat (' . $report->ticket_number . ')',
                'timestamp_iso' => Carbon::parse($reportTime)->toIso8601String(),
                'timestamp_ms' => Carbon::parse($reportTime)->getTimestampMs(),
                'timestamp_formatted' => Carbon::parse($reportTime)->format('d M Y, H:i') . ' WIB',
                'time_short' => Carbon::parse($reportTime)->format('H:i') . ' WIB',
                'title' => $report->title,
                'description' => $report->description . ' (Lokasi laporan: ' . $report->location_name . ')',
                'source_label' => 'Laporan Masyarakat • Tiket ' . $report->ticket_number,
                'verification_status' => null,
                'verification_label' => 'Status Laporan: ' . ($vStatus?->label() ?? (string) $report->verification_status),
                'follow_up_status' => null,
                'follow_up_label' => null,
                'latitude' => $report->latitude !== null ? (float) $report->latitude : null,
                'longitude' => $report->longitude !== null ? (float) $report->longitude : null,
                'ticket_number' => $report->ticket_number,
            ]);
        }

        // 3. Rujukan Sumber Informasi Terbuka pada Kejadian Ini
        foreach ($incident->incidentSources as $incSource) {
            $sourceTime = $incSource->published_at ?? $incSource->created_at ?? now();
            $sourceName = $incSource->source?->name ?? 'Sumber Informasi Terbuka';
            $isSimulationSource = $incSource->source !== null
                ? (bool) $incSource->source->is_simulation
                : (bool) $incident->is_simulation;
            $sourceTypeLabel = $incSource->source?->source_type?->label($isSimulationSource) ?? 'Sumber Informasi Terbuka';

            $events->push([
                'uid' => 'src-' . $incSource->id,
                'type' => 'sumber_terbuka',
                'type_label' => 'Rujukan ' . $sourceTypeLabel,
                'timestamp_iso' => Carbon::parse($sourceTime)->toIso8601String(),
                'timestamp_ms' => Carbon::parse($sourceTime)->getTimestampMs(),
                'timestamp_formatted' => Carbon::parse($sourceTime)->format('d M Y, H:i') . ' WIB',
                'time_short' => Carbon::parse($sourceTime)->format('H:i') . ' WIB',
                'title' => $incSource->headline ?: $sourceName,
                'description' => $incSource->notes ?: ('Rujukan informasi terbuka dari ' . $sourceName . '.'),
                'source_label' => $sourceName . ' (' . $sourceTypeLabel . ')',
                'verification_status' => null,
                'verification_label' => null,
                'follow_up_status' => null,
                'follow_up_label' => null,
                'latitude' => null,
                'longitude' => null,
                'ticket_number' => null,
            ]);
        }

        // 4. Riwayat Perubahan Status Verifikasi Kejadian (Sanitasi alasan internal & nama petugas)
        foreach ($incident->verificationHistories as $vh) {
            $vhTime = $vh->created_at ?? now();
            $newStatusEnum = $vh->new_status instanceof VerificationStatus
                ? $vh->new_status
                : VerificationStatus::tryFrom((string) $vh->new_status);

            $statusVal = $newStatusEnum?->value ?? (string) $vh->new_status;
            $statusLabel = $newStatusEnum?->label() ?? strtoupper(str_replace('_', ' ', $statusVal));

            $events->push([
                'uid' => 'vh-' . $vh->id,
                'type' => 'verifikasi',
                'type_label' => $incident->is_simulation ? 'Perubahan Status Verifikasi' : 'Verifikasi Arsip Resmi BNPB',
                'timestamp_iso' => Carbon::parse($vhTime)->toIso8601String(),
                'timestamp_ms' => Carbon::parse($vhTime)->getTimestampMs(),
                'timestamp_formatted' => Carbon::parse($vhTime)->format('d M Y, H:i') . ' WIB',
                'time_short' => Carbon::parse($vhTime)->format('H:i') . ' WIB',
                'title' => 'Status Verifikasi Kejadian: ' . $statusLabel,
                'description' => $incident->is_simulation
                    ? 'Status verifikasi diperbarui oleh Tim Verifikator TanggapTerang berdasarkan pemeriksaan silang laporan masyarakat dan sumber informasi terbuka.'
                    : 'Status terverifikasi secara resmi berdasarkan arsip Data Informasi Bencana Indonesia (DIBI) Pusdatin BNPB.',
                'source_label' => $incident->is_simulation ? 'Tim Verifikator TanggapTerang' : 'Pusdatin BNPB (Arsip DIBI)',
                'verification_status' => $statusVal,
                'verification_label' => $statusLabel,
                'follow_up_status' => null,
                'follow_up_label' => null,
                'latitude' => null,
                'longitude' => null,
                'ticket_number' => null,
            ]);
        }

        // 5. Catatan Tindak Lanjut Tim pada Kejadian Ini (Sanitasi nama petugas internal)
        foreach ($incident->followUpRecords as $fu) {
            $fuTime = $fu->action_date ?? $fu->created_at ?? now();
            $rawStatus = $fu->status instanceof \BackedEnum ? $fu->status->value : (string) $fu->status;

            // Map status catatan tindak lanjut ke enum FollowUpStatus kejadian bila sesuai
            $mappedFollowUp = match ($rawStatus) {
                'selesai' => FollowUpStatus::Selesai,
                'dipantau' => FollowUpStatus::Dipantau,
                'dalam_proses', 'sedang_ditindaklanjuti' => FollowUpStatus::SedangDitindaklanjuti,
                default => FollowUpStatus::SedangDitindaklanjuti,
            };

            $events->push([
                'uid' => 'fu-' . $fu->id,
                'type' => 'tindak_lanjut',
                'type_label' => $incident->is_simulation ? 'Catatan Tindak Lanjut Tim' : 'Milestone Penanganan Darurat BNPB/BPBD',
                'timestamp_iso' => Carbon::parse($fuTime)->toIso8601String(),
                'timestamp_ms' => Carbon::parse($fuTime)->getTimestampMs(),
                'timestamp_formatted' => Carbon::parse($fuTime)->format('d M Y, H:i') . ' WIB',
                'time_short' => Carbon::parse($fuTime)->format('H:i') . ' WIB',
                'title' => $fu->action_title,
                'description' => $fu->notes,
                'source_label' => $incident->is_simulation ? 'Tim Tindak Lanjut TanggapTerang' : 'Komando Penanganan Darurat BNPB & BPBD',
                'verification_status' => null,
                'verification_label' => null,
                'follow_up_status' => $mappedFollowUp->value,
                'follow_up_label' => $mappedFollowUp->label(),
                'latitude' => null,
                'longitude' => null,
                'ticket_number' => null,
            ]);
        }

        // 6. Respons Bantuan Masyarakat / Komunitas
        foreach ($incident->aidResponses as $aid) {
            $aidTime = $aid->recorded_at ?? $aid->created_at ?? now();
            $aidStatusLabel = $aid->status instanceof \App\Enums\AidResponseStatus
                ? $aid->status->label()
                : (string) $aid->status;

            $events->push([
                'uid' => 'aid-' . $aid->id,
                'type' => 'respons_bantuan',
                'type_label' => 'Respons Bantuan Masyarakat (' . $aid->reference_code . ')',
                'timestamp_iso' => Carbon::parse($aidTime)->toIso8601String(),
                'timestamp_ms' => Carbon::parse($aidTime)->getTimestampMs(),
                'timestamp_formatted' => Carbon::parse($aidTime)->format('d M Y, H:i') . ' WIB',
                'time_short' => Carbon::parse($aidTime)->format('H:i') . ' WIB',
                'title' => $aid->aid_item_title . ' — ' . $aid->quantity_label,
                'description' => trim(($aid->aid_description ?? 'Respons bantuan tercatat.') . ' Lokasi tujuan: ' . $aid->target_location . ' • Status: ' . $aidStatusLabel),
                'source_label' => $aid->publicResponderName() . ' (' . $aid->reference_code . ')',
                'verification_status' => null,
                'verification_label' => 'Status Respons: ' . $aidStatusLabel,
                'follow_up_status' => null,
                'follow_up_label' => null,
                'latitude' => $aid->latitude !== null ? (float) $aid->latitude : null,
                'longitude' => $aid->longitude !== null ? (float) $aid->longitude : null,
                'ticket_number' => null,
            ]);
        }

        // Urutkan seluruh peristiwa secara kronologis dari yang paling awal ke paling akhir
        return $events
            ->sortBy('timestamp_ms')
            ->values()
            ->map(function (array $item, int $index) {
                $item['step_number'] = $index + 1;
                return $item;
            });
    }
}

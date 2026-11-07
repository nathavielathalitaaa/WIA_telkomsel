<?php

namespace App\Http\Controllers\Internal;

use App\Enums\AidResponseStatus;
use App\Enums\FollowUpStatus;
use App\Enums\IncidentSeverity;
use App\Enums\SourceType;
use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PublicIncidentController;
use App\Models\AidResponse;
use App\Models\FollowUpRecord;
use App\Models\Incident;
use App\Models\IncidentSource;
use App\Models\Report;
use App\Models\Source;
use App\Models\VerificationHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InternalIncidentController extends Controller
{
    /**
     * Daftar kejadian internal dengan filter dan pencarian (GET /internal/incidents).
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $verificationStatus = trim((string) $request->query('verification_status', ''));
        $followUpStatus = trim((string) $request->query('follow_up_status', ''));
        $incidentType = trim((string) $request->query('incident_type', ''));
        $severity = trim((string) $request->query('severity', ''));

        $query = Incident::query()
            ->with(['wilayahAdministratif:id,nama', 'creator:id,name'])
            ->withCount(['reports', 'sources', 'followUpRecords', 'aidResponses']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('location_name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($verificationStatus !== '' && VerificationStatus::tryFrom($verificationStatus)) {
            $query->where('verification_status', $verificationStatus);
        }

        if ($followUpStatus !== '' && FollowUpStatus::tryFrom($followUpStatus)) {
            $query->where('follow_up_status', $followUpStatus);
        }

        if ($incidentType !== '') {
            $query->where('incident_type', $incidentType);
        }

        if ($severity !== '' && IncidentSeverity::tryFrom($severity)) {
            $query->where('severity', $severity);
        }

        $incidents = $query
            ->orderByDesc('occurred_at')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('internal.incidents.index', [
            'incidents' => $incidents,
            'categories' => PublicIncidentController::incidentCategories(),
            'filters' => [
                'q' => $search,
                'verification_status' => $verificationStatus,
                'follow_up_status' => $followUpStatus,
                'incident_type' => $incidentType,
                'severity' => $severity,
            ],
        ]);
    }

    /**
     * Formulir pembuatan kejadian baru (GET /internal/incidents/create).
     * Hanya untuk Operator dan Admin.
     */
    public function create(Request $request): View
    {
        $user = $request->user();
        if (! $user || ! $user->canManageOperations()) {
            abort(403, 'Hanya Operator atau Admin yang berwenang membuat data kejadian baru.');
        }

        $unlinkedReports = Report::query()
            ->whereNull('incident_id')
            ->where('verification_status', '!=', VerificationStatus::Ditolak->value)
            ->orderByDesc('created_at')
            ->get(['id', 'ticket_number', 'title', 'location_name', 'verification_status', 'created_at']);

        $sourceReport = null;
        if ($request->filled('from_report')) {
            $sourceReport = Report::with('wilayahAdministratif')->find((int) $request->query('from_report'));
        }

        return view('internal.incidents.create', [
            'categories' => PublicIncidentController::incidentCategories(),
            'unlinkedReports' => $unlinkedReports,
            'sourceReport' => $sourceReport,
        ]);
    }

    /**
     * Simpan kejadian baru ke database (POST /internal/incidents).
     * Hanya untuk Operator dan Admin.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user || ! $user->canManageOperations()) {
            abort(403, 'Hanya Operator atau Admin yang berwenang membuat data kejadian baru.');
        }

        $categories = array_keys(PublicIncidentController::incidentCategories());
        $severities = array_column(IncidentSeverity::cases(), 'value');

        $validated = $request->validate([
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'incident_type' => ['required', 'string', Rule::in($categories)],
            'severity' => ['required', 'string', Rule::in($severities)],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            'location_name' => ['required', 'string', 'min:5', 'max:255'],
            'wilayah_administratif_id' => ['nullable', 'integer', 'exists:wilayah_administratif,id'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_with:longitude'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180', 'required_with:latitude'],
            'occurred_at' => ['required', 'date'],
            'report_ids' => ['nullable', 'array'],
            'report_ids.*' => ['integer', 'exists:reports,id'],
        ], [
            'title.required' => 'Nama/judul kejadian wajib diisi.',
            'incident_type.required' => 'Jenis kejadian wajib dipilih.',
            'severity.required' => 'Tingkat dampak wajib dipilih.',
            'description.required' => 'Deskripsi kejadian wajib diisi.',
            'location_name.required' => 'Lokasi kejadian wajib diisi.',
            'occurred_at.required' => 'Waktu kejadian wajib diisi.',
        ]);

        $incident = DB::transaction(function () use ($validated, $user) {
            $slug = $this->generateUniqueSlug($validated['title']);

            $incident = Incident::create([
                'title' => $validated['title'],
                'slug' => $slug,
                'incident_type' => $validated['incident_type'],
                'description' => $validated['description'],
                'location_name' => $validated['location_name'],
                'wilayah_administratif_id' => $validated['wilayah_administratif_id'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'occurred_at' => $validated['occurred_at'],
                'verification_status' => VerificationStatus::MenungguVerifikasi,
                'follow_up_status' => FollowUpStatus::BelumDitindaklanjuti,
                'severity' => $validated['severity'],
                'is_simulation' => true,
                'created_by_user_id' => $user->id,
            ]);

            VerificationHistory::create([
                'verifiable_type' => Incident::class,
                'verifiable_id' => $incident->id,
                'previous_status' => null,
                'new_status' => VerificationStatus::MenungguVerifikasi->value,
                'user_id' => $user->id,
                'reason' => 'Data kejadian baru dibuat oleh tim internal dan menunggu penetapan verifikasi.',
                'is_simulation' => true,
            ]);

            if (! empty($validated['report_ids'])) {
                $reports = Report::whereIn('id', $validated['report_ids'])->get();
                foreach ($reports as $report) {
                    $currentStatus = $report->verification_status instanceof VerificationStatus
                        ? $report->verification_status->value
                        : (string) $report->verification_status;

                    $report->update(['incident_id' => $incident->id]);

                    VerificationHistory::create([
                        'verifiable_type' => Report::class,
                        'verifiable_id' => $report->id,
                        'previous_status' => $currentStatus,
                        'new_status' => $currentStatus,
                        'user_id' => $user->id,
                        'reason' => "Laporan dihubungkan saat pembuatan kejadian baru [{$incident->title}].",
                        'is_simulation' => true,
                    ]);
                }
            }

            return $incident;
        });

        return redirect()
            ->route('internal.incidents.edit', $incident)
            ->with('success', "Kejadian baru \"{$incident->title}\" berhasil dibuat.");
    }

    /**
     * Halaman pengelolaan detail kejadian (GET /internal/incidents/{incident}/edit).
     * Dapat diakses oleh Admin, Verifikator (untuk panel verifikasi), dan Operator (untuk panel data, tindak lanjut, sumber, & laporan).
     */
    public function edit(Incident $incident): View
    {
        $incident->load([
            'wilayahAdministratif.parent.parent.parent',
            'creator:id,name,role',
            'reports' => fn ($q) => $q->with('verifier:id,name')->orderByDesc('created_at'),
            'incidentSources.source',
            'verificationHistories' => fn ($q) => $q->with('user:id,name,role')->orderByDesc('created_at'),
            'followUpRecords' => fn ($q) => $q->with('user:id,name,role')->orderByDesc('action_date')->orderByDesc('created_at'),
            'aidResponses' => fn ($q) => $q->with(['confirmedBy:id,name,role', 'verifiedBy:id,name,role'])->orderByDesc('recorded_at'),
        ]);

        $unlinkedReports = Report::query()
            ->whereNull('incident_id')
            ->where('verification_status', '!=', VerificationStatus::Ditolak->value)
            ->orderByDesc('created_at')
            ->get(['id', 'ticket_number', 'title', 'location_name', 'verification_status', 'created_at']);

        $availableSources = Source::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('internal.incidents.edit', [
            'incident' => $incident,
            'categories' => PublicIncidentController::incidentCategories(),
            'unlinkedReports' => $unlinkedReports,
            'availableSources' => $availableSources,
        ]);
    }

    /**
     * Perbarui informasi dasar kejadian & hubungkan laporan relevan (PUT /internal/incidents/{incident}).
     * Hanya untuk Operator dan Admin.
     */
    public function update(Request $request, Incident $incident): RedirectResponse
    {
        $user = $request->user();
        if (! $user || ! $user->canManageOperations()) {
            abort(403, 'Hanya Operator atau Admin yang berwenang mengubah informasi kejadian.');
        }

        $categories = array_keys(PublicIncidentController::incidentCategories());
        $severities = array_column(IncidentSeverity::cases(), 'value');

        $validated = $request->validate([
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'incident_type' => ['required', 'string', Rule::in($categories)],
            'severity' => ['required', 'string', Rule::in($severities)],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            'location_name' => ['required', 'string', 'min:5', 'max:255'],
            'wilayah_administratif_id' => ['nullable', 'integer', 'exists:wilayah_administratif,id'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_with:longitude'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180', 'required_with:latitude'],
            'occurred_at' => ['required', 'date'],
            'report_ids' => ['nullable', 'array'],
            'report_ids.*' => ['integer', 'exists:reports,id'],
        ]);

        DB::transaction(function () use ($incident, $validated, $user) {
            $incident->update([
                'title' => $validated['title'],
                'incident_type' => $validated['incident_type'],
                'severity' => $validated['severity'],
                'description' => $validated['description'],
                'location_name' => $validated['location_name'],
                'wilayah_administratif_id' => $validated['wilayah_administratif_id'] ?? $incident->wilayah_administratif_id,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'occurred_at' => $validated['occurred_at'],
            ]);

            if (! empty($validated['report_ids'])) {
                $reports = Report::whereIn('id', $validated['report_ids'])->get();
                foreach ($reports as $report) {
                    if ($report->incident_id !== $incident->id) {
                        $currentStatus = $report->verification_status instanceof VerificationStatus
                            ? $report->verification_status->value
                            : (string) $report->verification_status;

                        $report->update(['incident_id' => $incident->id]);

                        VerificationHistory::create([
                            'verifiable_type' => Report::class,
                            'verifiable_id' => $report->id,
                            'previous_status' => $currentStatus,
                            'new_status' => $currentStatus,
                            'user_id' => $user->id,
                            'reason' => "Laporan dihubungkan ke kejadian [{$incident->title}] melalui halaman pengelolaan kejadian.",
                            'is_simulation' => (bool) $report->is_simulation,
                        ]);
                    }
                }
            }
        });

        return redirect()
            ->route('internal.incidents.edit', $incident)
            ->with('success', 'Informasi kejadian berhasil diperbarui.');
    }

    /**
     * Perbarui status verifikasi kejadian (PUT /internal/incidents/{incident}/verification).
     * Hanya untuk Verifikator dan Admin. Operator TIDAK diizinkan (403).
     */
    public function updateVerification(Request $request, Incident $incident): RedirectResponse
    {
        $user = $request->user();
        if (! $user || ! $user->canVerify()) {
            abort(403, 'Hanya Verifikator atau Admin yang berwenang mengubah status verifikasi kejadian.');
        }

        $statuses = array_column(VerificationStatus::cases(), 'value');

        $validated = $request->validate([
            'verification_status' => ['required', 'string', Rule::in($statuses)],
            'reason' => ['required', 'string', 'min:5', 'max:1000'],
        ], [
            'verification_status.required' => 'Status verifikasi baru wajib dipilih.',
            'reason.required' => 'Alasan atau dasar perubahan status verifikasi wajib diisi.',
            'reason.min' => 'Alasan verifikasi minimal 5 karakter.',
        ]);

        DB::transaction(function () use ($incident, $validated, $user) {
            $previousStatus = $incident->verification_status instanceof VerificationStatus
                ? $incident->verification_status->value
                : (string) $incident->verification_status;

            $incident->update([
                'verification_status' => $validated['verification_status'],
            ]);

            VerificationHistory::create([
                'verifiable_type' => Incident::class,
                'verifiable_id' => $incident->id,
                'previous_status' => $previousStatus,
                'new_status' => $validated['verification_status'],
                'user_id' => $user->id,
                'reason' => $validated['reason'],
                'is_simulation' => (bool) $incident->is_simulation,
            ]);
        });

        return redirect()
            ->route('internal.incidents.edit', $incident)
            ->with('success', 'Status verifikasi kejadian berhasil diperbarui dan dicatat dalam histori.');
    }

    /**
     * Perbarui status tindak lanjut kejadian secara terpisah (PUT /internal/incidents/{incident}/follow-up).
     * Hanya untuk Operator dan Admin. Verifikator TIDAK diizinkan (403).
     */
    public function updateFollowUp(Request $request, Incident $incident): RedirectResponse
    {
        $user = $request->user();
        if (! $user || ! $user->canManageOperations()) {
            abort(403, 'Hanya Operator atau Admin yang berwenang mengelola status tindak lanjut kejadian.');
        }

        $statuses = array_column(FollowUpStatus::cases(), 'value');

        $validated = $request->validate([
            'follow_up_status' => ['required', 'string', Rule::in($statuses)],
            'notes' => ['required', 'string', 'min:5', 'max:1000'],
        ], [
            'follow_up_status.required' => 'Status tindak lanjut wajib dipilih.',
            'notes.required' => 'Catatan perubahan status tindak lanjut wajib diisi.',
            'notes.min' => 'Catatan perubahan status tindak lanjut minimal 5 karakter.',
        ]);

        DB::transaction(function () use ($incident, $validated, $user) {
            $newStatusEnum = FollowUpStatus::from($validated['follow_up_status']);

            $incident->update([
                'follow_up_status' => $newStatusEnum,
            ]);

            FollowUpRecord::create([
                'incident_id' => $incident->id,
                'report_id' => null,
                'user_id' => $user->id,
                'action_title' => 'Pembaruan Status Tindak Lanjut: ' . $newStatusEnum->label(),
                'action_date' => now(),
                'status' => $newStatusEnum->value,
                'notes' => $validated['notes'],
                'evidence_url' => null,
                'is_simulation' => (bool) $incident->is_simulation,
            ]);
        });

        return redirect()
            ->route('internal.incidents.edit', $incident)
            ->with('success', 'Status tindak lanjut kejadian berhasil diperbarui dan dicatat dalam riwayat.');
    }

    /**
     * Tambahkan catatan tindak lanjut baru pada kejadian (POST /internal/incidents/{incident}/follow-ups).
     * Hanya untuk Operator dan Admin. Verifikator TIDAK diizinkan (403).
     */
    public function storeFollowUpRecord(Request $request, Incident $incident): RedirectResponse
    {
        $user = $request->user();
        if (! $user || ! $user->canManageOperations()) {
            abort(403, 'Hanya Operator atau Admin yang berwenang menambahkan catatan tindak lanjut.');
        }

        $followUpStatuses = array_column(FollowUpStatus::cases(), 'value');

        $validated = $request->validate([
            'action_title' => ['required', 'string', 'min:5', 'max:255'],
            'action_date' => ['required', 'date'],
            'status' => ['required', 'string', 'max:50'],
            'notes' => ['required', 'string', 'min:5', 'max:2000'],
            'evidence_url' => ['nullable', 'url:http,https', 'max:255'],
            'update_incident_follow_up_status' => ['nullable', 'string', Rule::in($followUpStatuses)],
        ], [
            'action_title.required' => 'Judul tindakan/catatan tindak lanjut wajib diisi.',
            'action_date.required' => 'Waktu pelaksanaan tindak lanjut wajib diisi.',
            'notes.required' => 'Rincian catatan tindak lanjut wajib diisi.',
            'evidence_url.url' => 'Tautan dokumentasi harus berupa URL HTTP/HTTPS yang valid.',
        ]);

        DB::transaction(function () use ($incident, $validated, $user) {
            FollowUpRecord::create([
                'incident_id' => $incident->id,
                'report_id' => null,
                'user_id' => $user->id,
                'action_title' => $validated['action_title'],
                'action_date' => $validated['action_date'],
                'status' => $validated['status'],
                'notes' => $validated['notes'],
                'evidence_url' => $validated['evidence_url'] ?? null,
                'is_simulation' => (bool) $incident->is_simulation,
            ]);

            if (! empty($validated['update_incident_follow_up_status'])) {
                $incident->update([
                    'follow_up_status' => $validated['update_incident_follow_up_status'],
                ]);
            }
        });

        return redirect()
            ->route('internal.incidents.edit', $incident)
            ->with('success', 'Catatan tindak lanjut baru berhasil ditambahkan.');
    }

    /**
     * Tambahkan sumber informasi terbuka pada kejadian (POST /internal/incidents/{incident}/sources).
     * Hanya untuk Operator dan Admin.
     */
    public function storeSource(Request $request, Incident $incident): RedirectResponse
    {
        $user = $request->user();
        if (! $user || ! $user->canManageOperations()) {
            abort(403, 'Hanya Operator atau Admin yang berwenang menambahkan sumber informasi kejadian.');
        }

        $sourceTypes = array_column(SourceType::cases(), 'value');

        $validated = $request->validate([
            'source_id' => ['nullable', 'integer', 'exists:sources,id', 'required_without:new_source_name'],
            'new_source_name' => ['nullable', 'string', 'max:255', 'required_without:source_id'],
            'new_source_type' => ['nullable', 'string', Rule::in($sourceTypes), 'required_with:new_source_name'],
            'headline' => ['required', 'string', 'min:5', 'max:255'],
            'reference_url' => ['nullable', 'url:http,https', 'max:255'],
            'published_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'source_id.required_without' => 'Pilih sumber informasi yang tersedia atau isi nama sumber baru.',
            'new_source_name.required_without' => 'Nama sumber informasi baru wajib diisi jika tidak memilih dari daftar.',
            'headline.required' => 'Judul rujukan/headline sumber wajib diisi.',
            'reference_url.url' => 'Tautan referensi sumber harus berupa URL HTTP/HTTPS yang valid.',
        ]);

        DB::transaction(function () use ($incident, $validated) {
            if (! empty($validated['source_id'])) {
                $sourceId = (int) $validated['source_id'];
            } else {
                $newSource = Source::create([
                    'name' => $validated['new_source_name'],
                    'source_type' => $validated['new_source_type'] ?? SourceType::MediaMassa->value,
                    'base_url' => $validated['reference_url'] ?? null,
                    'description' => 'Sumber informasi ditambahkan melalui panel pengelolaan kejadian',
                    'credibility_score' => 'tinggi',
                    'is_active' => true,
                    'is_simulation' => true,
                ]);
                $sourceId = $newSource->id;
            }

            IncidentSource::create([
                'incident_id' => $incident->id,
                'source_id' => $sourceId,
                'headline' => $validated['headline'],
                'reference_url' => $validated['reference_url'] ?? null,
                'published_at' => $validated['published_at'] ?? now(),
                'notes' => $validated['notes'] ?? null,
                'is_simulation' => (bool) $incident->is_simulation,
            ]);
        });

        return redirect()
            ->route('internal.incidents.edit', $incident)
            ->with('success', 'Sumber informasi terbuka berhasil ditautkan ke kejadian.');
    }

    /**
     * Perbarui status pemeriksaan/verifikasi respons bantuan (PUT /internal/incidents/{incident}/aid-responses/{aidResponse}/verification).
     * Hanya untuk Verifikator dan Admin. Operator TIDAK diizinkan (403).
     */
    public function updateAidResponseVerification(Request $request, Incident $incident, AidResponse $aidResponse): RedirectResponse
    {
        $user = $request->user();
        if (! $user || ! $user->canVerify()) {
            abort(403, 'Hanya Verifikator atau Admin yang berwenang memverifikasi respons bantuan.');
        }

        if ($aidResponse->incident_id !== $incident->id) {
            abort(404);
        }

        $verificationStatuses = array_column(VerificationStatus::cases(), 'value');

        $validated = $request->validate([
            'verification_status' => ['required', 'string', Rule::in($verificationStatuses)],
            'verification_notes' => ['required', 'string', 'min:5', 'max:1000'],
        ], [
            'verification_status.required' => 'Status pemeriksaan respons bantuan wajib dipilih.',
            'verification_status.in' => 'Status pemeriksaan yang dipilih tidak valid.',
            'verification_notes.required' => 'Catatan verifikasi respons bantuan wajib diisi.',
            'verification_notes.min' => 'Catatan verifikasi minimal 5 karakter.',
        ]);

        $newVerifStatus = VerificationStatus::from($validated['verification_status']);

        DB::transaction(function () use ($aidResponse, $newVerifStatus, $validated, $user) {
            $updates = [
                'verification_status' => $newVerifStatus,
                'verification_notes' => $validated['verification_notes'],
                'verified_at' => now(),
                'verified_by_user_id' => $user->id,
            ];

            // Jika status pemeriksaan diubah menjadi ditolak, batalkan status operasional terkonfirmasi jika ada
            if ($newVerifStatus === VerificationStatus::Ditolak) {
                $updates['status'] = AidResponseStatus::Direncanakan;
                $updates['confirmed_at'] = null;
                $updates['confirmed_by_user_id'] = null;
            }

            $aidResponse->update($updates);
        });

        return redirect()
            ->route('internal.incidents.edit', $incident)
            ->with('success', "Status pemeriksaan respons bantuan [{$aidResponse->reference_code}] berhasil diperbarui menjadi {$newVerifStatus->label()}.");
    }

    /**
     * Perbarui status atau konfirmasi catatan operasional Respons Bantuan Masyarakat (PUT /internal/incidents/{incident}/aid-responses/{aidResponse}).
     * Hanya untuk Operator dan Admin (Verifikator tidak diizinkan mengubah status operasional bantuan).
     */
    public function updateAidResponse(Request $request, Incident $incident, AidResponse $aidResponse): RedirectResponse
    {
        $user = $request->user();
        if (! $user || ! $user->canManageOperations()) {
            abort(403, 'Hanya Operator atau Admin yang berwenang memperbarui status respons bantuan.');
        }

        if ($aidResponse->incident_id !== $incident->id) {
            abort(404);
        }

        $statuses = array_column(AidResponseStatus::cases(), 'value');

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in($statuses)],
            'confirmation_notes' => [
                Rule::requiredIf(fn () => $request->input('status') === AidResponseStatus::Terkonfirmasi->value),
                'nullable',
                'string',
                'min:5',
                'max:1000',
            ],
            'evidence_url' => ['nullable', 'url:http,https', 'max:255'],
        ], [
            'status.required' => 'Status respons bantuan wajib dipilih.',
            'confirmation_notes.required' => 'Catatan bukti atau konfirmasi wajib diisi saat menetapkan status Penyerahan Dikonfirmasi.',
            'confirmation_notes.min' => 'Catatan konfirmasi minimal 5 karakter.',
            'evidence_url.url' => 'Tautan dokumentasi harus berupa URL HTTP/HTTPS yang valid.',
        ]);

        $newStatus = AidResponseStatus::from($validated['status']);

        // ATURAN 6: Status operasional tidak boleh diset menjadi 'terkonfirmasi' jika status pemeriksaan belum 'terverifikasi'
        if ($newStatus === AidResponseStatus::Terkonfirmasi) {
            $currentVerif = $aidResponse->verification_status instanceof VerificationStatus
                ? $aidResponse->verification_status
                : VerificationStatus::tryFrom((string) $aidResponse->verification_status);

            if ($currentVerif !== VerificationStatus::Terverifikasi) {
                return redirect()
                    ->route('internal.incidents.edit', $incident)
                    ->withErrors([
                        'status' => "Respons bantuan [{$aidResponse->reference_code}] belum dapat ditetapkan sebagai 'Penyerahan Dikonfirmasi' karena status pemeriksaan informasi belum 'Terverifikasi' oleh Verifikator (status saat ini: {$aidResponse->verificationStatusLabel()}).",
                    ]);
            }
        }

        $evidenceUrl = array_key_exists('evidence_url', $validated)
            ? ($validated['evidence_url'] ?: null)
            : $aidResponse->evidence_url;

        $confirmationNotes = ($validated['confirmation_notes'] ?? null) !== null && trim((string) $validated['confirmation_notes']) !== ''
            ? $validated['confirmation_notes']
            : $aidResponse->confirmation_notes;

        $aidResponse->update([
            'status' => $newStatus,
            'confirmation_notes' => $confirmationNotes,
            'evidence_url' => $evidenceUrl,
            'confirmed_at' => $newStatus === AidResponseStatus::Terkonfirmasi ? now() : null,
            'confirmed_by_user_id' => $newStatus === AidResponseStatus::Terkonfirmasi ? $user->id : null,
        ]);

        return redirect()
            ->route('internal.incidents.edit', $incident)
            ->with('success', "Status operasional respons bantuan [{$aidResponse->reference_code}] berhasil diperbarui menjadi {$newStatus->label()}.");
    }

    /**
     * Hasilkan slug unik untuk kejadian baru.
     */
    private function generateUniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        if ($base === '') {
            $base = 'kejadian-simulasi';
        }

        $candidate = $base;
        $counter = 1;

        while (Incident::withTrashed()->where('slug', $candidate)->exists()) {
            $counter++;
            $candidate = "{$base}-{$counter}";
        }

        return $candidate;
    }
}

<?php

namespace App\Http\Controllers;

use App\Enums\AidResponseStatus;
use App\Enums\VerificationStatus;
use App\Models\AidResponse;
use App\Models\Incident;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PublicAidResponseController extends Controller
{
    /**
     * Formulir Pencatatan Respons Bantuan Masyarakat / Komunitas (GET /aid-responses/create)
     */
    public function create(Request $request): View
    {
        $incidents = Incident::query()
            ->where('verification_status', '!=', VerificationStatus::Ditolak)
            ->with([
                'wilayahAdministratif',
                'reports' => fn ($q) => $q
                    ->select(['id', 'ticket_number', 'incident_id', 'title', 'category', 'location_name', 'verification_status'])
                    ->where('verification_status', '!=', VerificationStatus::Ditolak)
                    ->orderByDesc('created_at'),
            ])
            ->orderByDesc('occurred_at')
            ->get();

        $selectedIncidentParam = trim((string) $request->query('incident', ''));
        $selectedIncident = null;

        if ($selectedIncidentParam !== '') {
            $selectedIncident = $incidents->first(function (Incident $inc) use ($selectedIncidentParam) {
                return $inc->slug === $selectedIncidentParam || (string) $inc->id === $selectedIncidentParam;
            });
        }

        if (! $selectedIncident && $incidents->isNotEmpty()) {
            $selectedIncident = $incidents->first();
        }

        $selectedReportId = $request->filled('report') ? (int) $request->query('report') : null;

        $selectedSubmissionType = trim((string) $request->query('submission_type', AidResponse::SUBMISSION_TAWARAN_RENCANA));
        if (! array_key_exists($selectedSubmissionType, AidResponse::submissionTypes())) {
            $selectedSubmissionType = AidResponse::SUBMISSION_TAWARAN_RENCANA;
        }

        $selectedNeedCategory = trim((string) $request->query('need_category', ''));
        if ($selectedNeedCategory !== '' && ! array_key_exists($selectedNeedCategory, AidResponse::needCategories())) {
            $selectedNeedCategory = '';
        }

        if ($selectedNeedCategory === '' && $selectedReportId !== null && $selectedIncident) {
            $matchedReport = $selectedIncident->reports->firstWhere('id', $selectedReportId);
            if ($matchedReport) {
                $selectedNeedCategory = AidResponse::mapReportCategoryToNeedCategory((string) $matchedReport->category);
            }
        }

        return view('public.aid-responses.create', [
            'incidents' => $incidents,
            'selectedIncident' => $selectedIncident,
            'selectedReportId' => $selectedReportId,
            'selectedSubmissionType' => $selectedSubmissionType,
            'selectedNeedCategory' => $selectedNeedCategory,
            'submissionTypes' => AidResponse::submissionTypes(),
            'needCategories' => AidResponse::needCategories(),
            'responderTypes' => AidResponse::responderTypes(),
        ]);
    }

    /**
     * Simpan Catatan Respons Bantuan Baru Tanpa Akun Internal (POST /aid-responses)
     *
     * Catatan penting:
     * Baik "Tawaran / Rencana Bantuan" maupun "Laporan Bantuan Sudah Diberikan" dari formulir publik
     * SELALU berstatus awal `Direncanakan` (direncanakan / belum terkonfirmasi)
     * dan tidak pernah langsung menjadi `Terkonfirmasi` tanpa pemeriksaan/konfirmasi petugas internal.
     */
    public function store(Request $request): RedirectResponse
    {
        $needCategoryKeys = array_keys(AidResponse::needCategories());
        $responderTypeKeys = array_keys(AidResponse::responderTypes());
        $submissionTypeKeys = array_keys(AidResponse::submissionTypes());

        $validated = $request->validate([
            'submission_type' => ['nullable', 'string', 'max:40', Rule::in($submissionTypeKeys)],
            'incident_id' => ['required', 'integer', 'exists:incidents,id'],
            'report_id' => ['nullable', 'integer', 'exists:reports,id'],
            'need_category' => ['required', 'string', 'max:100', Rule::in($needCategoryKeys)],
            'organization_name' => ['required', 'string', 'min:3', 'max:150'],
            'responder_type' => ['required', 'string', 'max:60', Rule::in($responderTypeKeys)],
            'contact_name' => ['nullable', 'string', 'max:150'],
            'contact_info' => ['nullable', 'string', 'max:150'],
            'allow_public_name' => ['nullable', 'boolean'],
            'aid_item_title' => ['required', 'string', 'min:3', 'max:255'],
            'aid_description' => ['nullable', 'string', 'max:2000'],
            'quantity_label' => ['required', 'string', 'min:2', 'max:150'],
            'target_location' => ['required', 'string', 'min:5', 'max:255'],
            'delivered_at' => [
                Rule::requiredIf(fn () => $request->input('submission_type') === AidResponse::SUBMISSION_LAPORAN_PENYERAHAN),
                'nullable',
                'date',
            ],
            'latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_with:longitude'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180', 'required_with:latitude'],
            'evidence_url' => ['nullable', 'url:http,https', 'max:255'],
        ], [
            'submission_type.in' => 'Jenis pencatatan respons bantuan tidak valid.',
            'incident_id.required' => 'Kejadian terkait wajib dipilih.',
            'incident_id.exists' => 'Kejadian yang dipilih tidak ditemukan.',
            'need_category.required' => 'Kategori kebutuhan / bantuan wajib dipilih.',
            'need_category.in' => 'Kategori kebutuhan yang dipilih tidak valid.',
            'organization_name.required' => 'Nama organisasi, komunitas, atau inisiatif wajib diisi.',
            'organization_name.min' => 'Nama organisasi/komunitas minimal 3 karakter.',
            'organization_name.max' => 'Nama organisasi/komunitas maksimal 150 karakter.',
            'responder_type.required' => 'Jenis pemberi respons wajib dipilih.',
            'aid_item_title.required' => 'Jenis bantuan yang direncanakan/disalurkan wajib diisi.',
            'aid_item_title.min' => 'Jenis bantuan minimal 3 karakter.',
            'quantity_label.required' => 'Jumlah atau kapasitas bantuan wajib diisi.',
            'target_location.required' => 'Lokasi atau wilayah tujuan penyaluran wajib diisi.',
            'target_location.min' => 'Lokasi tujuan minimal 5 karakter.',
            'delivered_at.required' => 'Waktu penyerahan bantuan wajib diisi untuk laporan bantuan yang sudah diberikan.',
            'delivered_at.date' => 'Format waktu penyerahan bantuan tidak valid.',
            'evidence_url.url' => 'Tautan dokumentasi harus berupa URL HTTP/HTTPS yang valid.',
        ]);

        $incident = Incident::findOrFail($validated['incident_id']);

        $submissionType = $validated['submission_type'] ?? AidResponse::SUBMISSION_TAWARAN_RENCANA;
        if (! in_array($submissionType, $submissionTypeKeys, true)) {
            $submissionType = AidResponse::SUBMISSION_TAWARAN_RENCANA;
        }

        // Pastikan report_id (jika diisi) memang milik incident tersebut
        $reportId = $validated['report_id'] ?? null;
        if ($reportId !== null) {
            $belongsToIncident = Report::where('id', $reportId)
                ->where('incident_id', $incident->id)
                ->exists();
            if (! $belongsToIncident) {
                $reportId = null;
            }
        }

        $aidResponse = AidResponse::create([
            'reference_code' => AidResponse::generateUniqueReferenceCode(true),
            'submission_type' => $submissionType,
            'incident_id' => $incident->id,
            'report_id' => $reportId,
            'need_category' => $validated['need_category'],
            'organization_name' => $validated['organization_name'],
            'responder_type' => $validated['responder_type'],
            'contact_name' => $validated['contact_name'] ?? null,
            'contact_info' => $validated['contact_info'] ?? null,
            'allow_public_name' => $request->boolean('allow_public_name', false),
            'aid_item_title' => $validated['aid_item_title'],
            'aid_description' => $validated['aid_description'] ?? null,
            'quantity_label' => $validated['quantity_label'],
            'target_location' => $validated['target_location'],
            'wilayah_administratif_id' => $incident->wilayah_administratif_id,
            'latitude' => $validated['latitude'] ?? $incident->latitude,
            'longitude' => $validated['longitude'] ?? $incident->longitude,
            'recorded_at' => now(),
            'delivered_at' => $validated['delivered_at'] ?? null,
            // Selalu dimulai dengan status Menunggu Verifikasi (pemeriksaan) dan Direncanakan (operasional)
            'verification_status' => VerificationStatus::MenungguVerifikasi,
            'status' => AidResponseStatus::Direncanakan,
            'evidence_url' => $validated['evidence_url'] ?? null,
            'confirmation_notes' => null,
            'confirmed_at' => null,
            'confirmed_by_user_id' => null,
            'is_simulation' => true,
        ]);

        $typeLabel = $aidResponse->submissionTypeLabel();

        return redirect()
            ->route('public.track', ['ticket' => $aidResponse->reference_code])
            ->with('created_ticket', $aidResponse->reference_code)
            ->with(
                'success',
                "{$typeLabel} berhasil dicatat dengan kode referensi {$aidResponse->reference_code}. Status awal pemeriksaan: Menunggu Verifikasi, status operasional: Direncanakan. Anda dapat memantau perkembangan statusnya di halaman ini."
            );
    }
}

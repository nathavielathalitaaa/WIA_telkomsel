<?php

namespace App\Http\Controllers;

use App\Enums\ConfidenceLevel;
use App\Enums\VerificationStatus;
use App\Models\Report;
use App\Models\VerificationHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PublicReportController extends Controller
{
    /**
     * Halaman Formulir Pelaporan Masyarakat (GET /reports/create)
     */
    public function create(): View
    {
        return view('public.reports.create', [
            'categories' => PublicIncidentController::incidentCategories(),
        ]);
    }

    /**
     * Simpan Laporan Masyarakat Baru Tanpa Login (POST /reports)
     */
    public function store(Request $request): RedirectResponse
    {
        $categories = array_keys(PublicIncidentController::incidentCategories());

        $validated = $request->validate([
            'reporter_name' => ['nullable', 'string', 'max:150'],
            'reporter_contact' => ['nullable', 'string', 'max:100'],
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            'category' => ['required', 'string', Rule::in($categories)],
            'occurred_at' => ['required', 'date', 'before_or_equal:now'],
            'wilayah_administratif_id' => ['nullable', 'integer', 'exists:wilayah_administratif,id'],
            'location_name' => ['required', 'string', 'min:5', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_with:longitude'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180', 'required_with:latitude'],
            'evidence_url' => ['nullable', 'url:http,https', 'max:255'],
        ], [
            'title.required' => 'Judul laporan wajib diisi.',
            'title.min' => 'Judul laporan minimal 5 karakter.',
            'description.required' => 'Deskripsi kejadian wajib diisi.',
            'description.min' => 'Deskripsi kejadian minimal 10 karakter.',
            'category.required' => 'Jenis kejadian wajib dipilih.',
            'category.in' => 'Jenis kejadian yang dipilih tidak valid.',
            'occurred_at.required' => 'Waktu kejadian wajib diisi.',
            'occurred_at.date' => 'Format waktu kejadian tidak valid.',
            'occurred_at.before_or_equal' => 'Waktu kejadian tidak boleh melebihi waktu saat ini.',
            'location_name.required' => 'Alamat atau keterangan lokasi wajib diisi.',
            'location_name.min' => 'Alamat atau keterangan lokasi minimal 5 karakter.',
            'latitude.between' => 'Koordinat lintang (latitude) harus berada di antara -90 hingga 90.',
            'latitude.required_with' => 'Koordinat lintang wajib diisi jika bujur diisi.',
            'longitude.between' => 'Koordinat bujur (longitude) harus berada di antara -180 hingga 180.',
            'longitude.required_with' => 'Koordinat bujur wajib diisi jika lintang diisi.',
            'evidence_url.url' => 'Tautan bukti/sumber harus berupa URL HTTP/HTTPS yang valid.',
        ]);

        $report = DB::transaction(function () use ($validated) {
            $ticketNumber = Report::generateTicketNumber(true);

            // Laporan publik baru TIDAK otomatis membuat kejadian baru (incident_id = null)
            // dan selalu berstatus awal menunggu_verifikasi
            $report = Report::create([
                'ticket_number' => $ticketNumber,
                'incident_id' => null,
                'user_id' => null,
                'reporter_name' => $validated['reporter_name'] ?? null,
                'reporter_contact' => $validated['reporter_contact'] ?? null,
                'title' => $validated['title'],
                'description' => $validated['description'],
                'category' => $validated['category'],
                'location_name' => $validated['location_name'],
                'wilayah_administratif_id' => $validated['wilayah_administratif_id'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'occurred_at' => $validated['occurred_at'],
                'evidence_url' => $validated['evidence_url'] ?? null,
                'verification_status' => VerificationStatus::MenungguVerifikasi,
                'confidence_level' => ConfidenceLevel::Sedang,
                'is_simulation' => true,
            ]);

            VerificationHistory::create([
                'verifiable_type' => Report::class,
                'verifiable_id' => $report->id,
                'previous_status' => null,
                'new_status' => VerificationStatus::MenungguVerifikasi->value,
                'user_id' => null,
                'reason' => 'Laporan masyarakat diterima melalui formulir publik dan menunggu pemeriksaan tim verifikator.',
                'is_simulation' => true,
            ]);

            return $report;
        });

        return redirect()
            ->route('public.track', ['ticket' => $report->ticket_number])
            ->with('created_ticket', $report->ticket_number)
            ->with('success', "Laporan berhasil dikirim dengan nomor tiket {$report->ticket_number}. Simpan nomor tiket ini untuk melacak perkembangan verifikasi.");
    }

    /**
     * Halaman Lacak Tiket Laporan (GET /track)
     */
    public function track(Request $request): View
    {
        $rawTicket = (string) $request->query('ticket', '');
        $normalizedTicket = strtoupper(trim($rawTicket));
        $report = null;
        $searched = $normalizedTicket !== '';

        // Validasi pola tiket sebelum query untuk mencegah enumerasi sembarang input
        if ($searched && preg_match('/^[A-Z0-9\-]{6,32}$/', $normalizedTicket)) {
            $report = Report::query()
                ->select([
                    'id',
                    'ticket_number',
                    'incident_id',
                    'title',
                    'description',
                    'category',
                    'location_name',
                    'wilayah_administratif_id',
                    'latitude',
                    'longitude',
                    'occurred_at',
                    'evidence_url',
                    'verification_status',
                    'confidence_level',
                    'is_simulation',
                    'verified_at',
                    'created_at',
                    'updated_at',
                ])
                ->where('ticket_number', $normalizedTicket)
                ->with([
                    'incident.wilayahAdministratif',
                    'wilayahAdministratif.parent.parent.parent',
                    'verificationHistories' => fn ($q) => $q->orderByDesc('created_at'),
                    'followUpRecords' => fn ($q) => $q->orderByDesc('action_date')->orderByDesc('created_at'),
                    'aidResponses' => fn ($q) => $q->select([
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
                        'recorded_at',
                        'status',
                        'evidence_url',
                        'confirmation_notes',
                        'confirmed_at',
                        'is_simulation',
                        'created_at',
                    ])->orderByDesc('recorded_at'),
                ])
                ->first();
        }

        return view('public.track', [
            'ticketQuery' => $normalizedTicket,
            'searched' => $searched,
            'report' => $report,
            'categories' => PublicIncidentController::incidentCategories(),
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Enums\ConfidenceLevel;
use App\Enums\FollowUpStatus;
use App\Enums\IncidentSeverity;
use App\Enums\VerificationStatus;
use App\Models\Incident;
use App\Models\Report;
use App\Models\VerificationHistory;
use Database\Seeders\SimulationDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicFeaturesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. Halaman Pantau (/) dapat dibuka, menampilkan data dari database, mendukung filter & pencarian,
     *    serta tidak membuat koordinat palsu jika koordinat kejadian bernilai null.
     */
    public function test_monitor_page_renders_database_incidents_and_supports_filtering(): void
    {
        $this->seed(SimulationDataSeeder::class);

        // Tambahkan 1 kejadian tanpa koordinat untuk memastikan tidak ada koordinat palsu
        $noCoordIncident = Incident::create([
            'title' => 'Gangguan Drainase Sektor Timur Tanpa Titik GPS',
            'slug' => 'gangguan-drainase-sektor-timur-tanpa-gps',
            'incident_type' => 'banjir',
            'description' => 'Laporan genangan lokal yang belum memiliki titik koordinat lintang bujur.',
            'location_name' => 'Kawasan Sektor Timur',
            'latitude' => null,
            'longitude' => null,
            'occurred_at' => now()->subHour(),
            'verification_status' => VerificationStatus::MenungguVerifikasi,
            'follow_up_status' => FollowUpStatus::BelumDitindaklanjuti,
            'severity' => IncidentSeverity::Rendah,
            'is_simulation' => true,
        ]);

        $response = $this->get(route('public.monitor'));

        $response->assertStatus(200);
        $response->assertSee('[SIMULASI] Banjir Luapan Sungai Ciliwung Bojonggede');
        $response->assertSee('[SIMULASI] Tanah Longsor Tebing Jalur Puncak KM 82');
        $response->assertSee($noCoordIncident->title);
        $response->assertSee(route('public.reports.create'));

        // Pastikan kejadian tanpa koordinat tidak dimasukkan ke daftar marker peta
        $mapIncidents = $response->viewData('mapIncidents');
        $this->assertNotNull($mapIncidents);
        $this->assertFalse(
            collect($mapIncidents)->contains('slug', $noCoordIncident->slug),
            'Kejadian tanpa koordinat tidak boleh diberi koordinat palsu pada peta.'
        );

        // Uji pencarian & filter status verifikasi
        $filteredResponse = $this->get(route('public.monitor', [
            'q' => 'Puncak KM 82',
            'verification_status' => VerificationStatus::Terverifikasi->value,
        ]));

        $filteredResponse->assertStatus(200);
        $filteredResponse->assertSee('[SIMULASI] Tanah Longsor Tebing Jalur Puncak KM 82');
        $filteredResponse->assertDontSee('[SIMULASI] Banjir Luapan Sungai Ciliwung Bojonggede');
    }

    /**
     * 2. Detail kejadian (/incidents/{slug}) ditemukan (200) dan tidak ditemukan (404),
     *    serta menampilkan status verifikasi dan tindak lanjut secara terpisah.
     */
    public function test_incident_detail_page_found_and_not_found(): void
    {
        $this->seed(SimulationDataSeeder::class);

        $incident = Incident::where('slug', 'sim-banjir-luapan-ciliwung-bojonggede-2026')->firstOrFail();

        $response = $this->get(route('public.incidents.show', $incident->slug));

        $response->assertStatus(200);
        $response->assertSee($incident->title);
        $response->assertSee($incident->location_name);
        $response->assertSee('1. Status Verifikasi Informasi');
        $response->assertSee('2. Status Tindak Lanjut Tim TanggapTerang');
        $response->assertSee('Ketiadaan catatan tindak lanjut pada TanggapTerang tidak berarti instansi atau pihak berwenang eksternal tidak sedang menangani kejadian di lapangan.');
        // Menampilkan laporan masyarakat yang terhubung & sumber informasi terbuka
        $response->assertSee('RPT-SIM-8K4M9P2X');
        $response->assertSee('Siaran Informasi Kebencanaan Daerah (Sumber Terbuka)');

        // Slug tidak ditemukan -> 404
        $notFoundResponse = $this->get(route('public.incidents.show', 'kejadian-tidak-terdaftar-999'));
        $notFoundResponse->assertStatus(404);
    }

    /**
     * 3 & 5. Form laporan berhasil menyimpan ke database tanpa login,
     *        tidak otomatis membuat kejadian baru (incident_id = null),
     *        berstatus menunggu_verifikasi, dan membuat riwayat verifikasi awal.
     */
    public function test_public_report_submission_saves_to_database_without_login_and_without_creating_incident(): void
    {
        $initialIncidentCount = Incident::count();

        $formPage = $this->get(route('public.reports.create'));
        $formPage->assertStatus(200);
        $formPage->assertSee('Formulir Pelaporan Kejadian Masyarakat');

        $payload = [
            'reporter_name' => 'Warga Pelapor Uji',
            'reporter_contact' => '081299998888',
            'title' => 'Pohon Tumbang Menutup Jalur Utama Kecamatan Coblong',
            'description' => 'Pohon berukuran besar tumbang akibat hujan deras disertai angin kencang dan menutup satu lajur jalan.',
            'category' => 'cuaca_ekstrem',
            'occurred_at' => now()->subMinutes(30)->format('Y-m-d H:i:s'),
            'location_name' => 'Jl. Ir. H. Juanda dekat Simpang Dago, Kecamatan Coblong',
            'latitude' => '-6.8894000',
            'longitude' => '107.6134000',
            'evidence_url' => 'https://contoh-sumber-terbuka.id/foto-pohon-tumbang',
        ];

        $response = $this->post(route('public.reports.store'), $payload);

        // Pastikan tidak ada Incident baru yang dibuat otomatis oleh laporan masyarakat
        $this->assertSame($initialIncidentCount, Incident::count());

        $report = Report::where('title', $payload['title'])->first();
        $this->assertNotNull($report);
        $this->assertNull($report->user_id, 'Laporan publik harus dapat dikirim tanpa login (user_id null).');
        $this->assertNull($report->incident_id, 'Laporan publik baru tidak boleh otomatis membuat atau terhubung ke kejadian.');
        $this->assertSame(VerificationStatus::MenungguVerifikasi, $report->verification_status);
        $this->assertTrue($report->is_simulation);
        $this->assertMatchesRegularExpression('/^RPT-SIM-[23456789ABCDEFGHJKLMNPQRSTUVWXYZ]{8}$/', $report->ticket_number);

        // Pastikan riwayat verifikasi awal tercatat
        $this->assertDatabaseHas('verification_histories', [
            'verifiable_type' => Report::class,
            'verifiable_id' => $report->id,
            'previous_status' => null,
            'new_status' => VerificationStatus::MenungguVerifikasi->value,
        ]);

        $response->assertRedirect(route('public.track', ['ticket' => $report->ticket_number]));
        $response->assertSessionHas('created_ticket', $report->ticket_number);
    }

    /**
     * 4. Validasi laporan gagal jika input tidak valid.
     */
    public function test_report_submission_fails_validation_with_invalid_inputs(): void
    {
        $invalidPayload = [
            'title' => 'Abc', // kurang dari 5 karakter
            'description' => 'Pendek', // kurang dari 10 karakter
            'category' => 'kategori_palsu', // tidak ada dalam daftar kategori
            'occurred_at' => now()->addDays(2)->format('Y-m-d H:i:s'), // waktu di masa depan
            'location_name' => 'Jl', // kurang dari 5 karakter
            'latitude' => '125.000', // di luar rentang -90 s/d 90
            'longitude' => '210.000', // di luar rentang -180 s/d 180
            'evidence_url' => 'bukan-url-valid',
        ];

        $response = $this->from(route('public.reports.create'))
            ->post(route('public.reports.store'), $invalidPayload);

        $response->assertRedirect(route('public.reports.create'));
        $response->assertSessionHasErrors([
            'title',
            'description',
            'category',
            'occurred_at',
            'location_name',
            'latitude',
            'longitude',
            'evidence_url',
        ]);

        $this->assertSame(0, Report::count());
    }

    /**
     * 6. Nomor tiket unik dan tidak sekuensial.
     */
    public function test_generated_ticket_numbers_are_unique_and_non_sequential(): void
    {
        $tickets = [];
        for ($i = 0; $i < 25; $i++) {
            $tickets[] = Report::generateTicketNumber(true);
        }

        // Semua tiket harus unik
        $this->assertCount(25, array_unique($tickets));

        // Format harus sesuai RPT-SIM-XXXXXXXX (tanpa karakter ambigu 0, O, 1, I)
        foreach ($tickets as $ticket) {
            $this->assertMatchesRegularExpression('/^RPT-SIM-[23456789ABCDEFGHJKLMNPQRSTUVWXYZ]{8}$/', $ticket);
        }

        // Pastikan tidak berurutan secara sekuensial (mis. bukan RPT-SIM-00000001, RPT-SIM-00000002)
        $sorted = $tickets;
        sort($sorted);
        $this->assertNotSame($tickets, $sorted);
    }

    /**
     * 7. Pelacakan tiket berhasil untuk tiket valid dan menampilkan pesan aman untuk tiket tidak ditemukan.
     */
    public function test_ticket_tracking_succeeds_for_valid_ticket_and_handles_missing_ticket_safely(): void
    {
        $this->seed(SimulationDataSeeder::class);

        // Tiket valid yang sudah terhubung ke kejadian
        $validResponse = $this->get(route('public.track', ['ticket' => 'RPT-SIM-8K4M9P2X']));
        $validResponse->assertStatus(200);
        $validResponse->assertSee('RPT-SIM-8K4M9P2X');
        $validResponse->assertSee('[SIMULASI] Air meluap masuk pekarangan dan rumah warga RT 02/RW 04');
        $validResponse->assertSee('[SIMULASI] Banjir Luapan Sungai Ciliwung Bojonggede');
        $validResponse->assertSee('Terhubung ke Kejadian');

        // Tiket valid yang belum terhubung ke kejadian (mandiri)
        $unlinkedResponse = $this->get(route('public.track', ['ticket' => 'RPT-SIM-6T9W4L8R']));
        $unlinkedResponse->assertStatus(200);
        $unlinkedResponse->assertSee('RPT-SIM-6T9W4L8R');
        $unlinkedResponse->assertSee('Belum Terhubung ke Kejadian');

        // Tiket tidak ditemukan -> tampilkan pesan aman tanpa membocorkan tiket lain
        $missingResponse = $this->get(route('public.track', ['ticket' => 'RPT-SIM-ZZZZ9999']));
        $missingResponse->assertStatus(200);
        $missingResponse->assertSee('Nomor Tiket / Kode Referensi Tidak Ditemukan');
        $missingResponse->assertSee('RPT-SIM-ZZZZ9999');
        $missingResponse->assertDontSee('[SIMULASI] Air meluap masuk pekarangan dan rumah warga RT 02/RW 04');
    }

    /**
     * 8. Informasi pribadi pelapor (nama & kontak) serta nama/alasan internal petugas
     *    tidak bocor di halaman publik (/, /incidents/{slug}, dan /track).
     */
    public function test_reporter_personal_information_and_internal_notes_never_leak_on_public_pages(): void
    {
        $this->seed(SimulationDataSeeder::class);

        $secretReporterName = 'Rahasia Nama Pelapor Warga 991';
        $secretReporterPhone = '081377776666';
        $secretInternalReason = 'CATATAN INTERNAL SENSITIF VERIFIKATOR 882';

        $incident = Incident::where('slug', 'sim-banjir-luapan-ciliwung-bojonggede-2026')->firstOrFail();

        $report = Report::create([
            'ticket_number' => 'RPT-SIM-7V5N8K3Q',
            'incident_id' => $incident->id,
            'user_id' => null,
            'reporter_name' => $secretReporterName,
            'reporter_contact' => $secretReporterPhone,
            'title' => 'Laporan Genangan Tambahan di Sekitar Pasar Bojonggede',
            'description' => 'Genangan air setinggi 40 cm di sekitar area pasar lama.',
            'category' => 'banjir',
            'location_name' => 'Pasar Lama Bojonggede',
            'occurred_at' => now()->subHours(2),
            'verification_status' => VerificationStatus::Terverifikasi,
            'confidence_level' => ConfidenceLevel::Tinggi,
            'is_simulation' => true,
        ]);

        VerificationHistory::create([
            'verifiable_type' => Report::class,
            'verifiable_id' => $report->id,
            'previous_status' => VerificationStatus::MenungguVerifikasi->value,
            'new_status' => VerificationStatus::Terverifikasi->value,
            'user_id' => null,
            'reason' => $secretInternalReason,
            'is_simulation' => true,
        ]);

        // Cek Halaman Pantau (/)
        $monitorRes = $this->get(route('public.monitor'));
        $monitorRes->assertStatus(200);
        $monitorRes->assertDontSee($secretReporterName);
        $monitorRes->assertDontSee($secretReporterPhone);
        $monitorRes->assertDontSee($secretInternalReason);

        // Cek Halaman Detail Kejadian (/incidents/{slug})
        $detailRes = $this->get(route('public.incidents.show', $incident->slug));
        $detailRes->assertStatus(200);
        $detailRes->assertSee($report->ticket_number);
        $detailRes->assertSee($report->title);
        $detailRes->assertDontSee($secretReporterName);
        $detailRes->assertDontSee($secretReporterPhone);
        $detailRes->assertDontSee($secretInternalReason);
        // Pastikan nama petugas internal dari seeder juga tidak bocor di timeline publik
        $detailRes->assertDontSee('Budi Pratama (Verifikator)');
        $detailRes->assertDontSee('Siti Aisyah (Operator)');

        // Cek Halaman Lacak Tiket (/track?ticket=...)
        $trackRes = $this->get(route('public.track', ['ticket' => $report->ticket_number]));
        $trackRes->assertStatus(200);
        $trackRes->assertSee($report->ticket_number);
        $trackRes->assertSee($report->title);
        $trackRes->assertDontSee($secretReporterName);
        $trackRes->assertDontSee($secretReporterPhone);
        $trackRes->assertDontSee($secretInternalReason);
    }
}

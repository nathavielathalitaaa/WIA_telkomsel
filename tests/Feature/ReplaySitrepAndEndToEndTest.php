<?php

namespace Tests\Feature;

use App\Enums\ConfidenceLevel;
use App\Enums\FollowUpStatus;
use App\Enums\IncidentSeverity;
use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Models\Incident;
use App\Models\Report;
use App\Models\User;
use App\Models\VerificationHistory;
use Database\Seeders\SimulationDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ReplaySitrepAndEndToEndTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1, 2, 3, & 4. /replay dapat dibuka, menampilkan banner Mode Simulasi Prototype,
     *               hanya menampilkan histori kejadian yang dipilih, dan terurut berdasarkan waktu.
     */
    public function test_replay_page_loads_selected_incident_history_sorted_chronologically(): void
    {
        $this->seed(SimulationDataSeeder::class);

        $incident1 = Incident::where('slug', 'sim-banjir-luapan-ciliwung-bojonggede-2026')->firstOrFail();
        $incident2 = Incident::where('slug', 'sim-longsor-jalur-puncak-km82-2026')->firstOrFail();

        $response = $this->get(route('public.replay', ['incident' => $incident1->slug]));

        $response->assertStatus(200);
        $response->assertSee('Mode Simulasi Prototype');
        $response->assertDontSee('DEMO SIMULASI — BUKAN DATA REAL-TIME');
        $response->assertSee($incident1->title);

        // Menampilkan laporan dan tindak lanjut milik incident1
        $response->assertSee('RPT-SIM-8K4M9P2X');
        $response->assertSee('Pengelompokan Laporan & Pemetaan Titik Genangan');

        // TIDAK mencampur laporan atau tindak lanjut milik incident2 pada timeline
        $response->assertDontSee('RPT-SIM-4D9H2V7K');
        $response->assertDontSee('Pemantauan Berkala Informasi Arus Lalu Lintas KM 82');

        // Pastikan timelineEvents terurut secara kronologis (ascending berdasarkan timestamp_ms)
        $timelineEvents = $response->viewData('timelineEvents');
        $this->assertNotNull($timelineEvents);
        $this->assertGreaterThanOrEqual(5, $timelineEvents->count());

        $timestamps = $timelineEvents->pluck('timestamp_ms')->all();
        $sortedTimestamps = $timestamps;
        sort($sortedTimestamps);
        $this->assertSame($sortedTimestamps, $timestamps, 'Peristiwa pada Replay harus diurutkan berdasarkan timestamp aktual.');

        // Pastikan atribut data-timestamp-ms dan mekanisme filter masa depan tersedia untuk kontrol slider
        $response->assertSee('data-timestamp-ms=');
        $response->assertSee('future-hidden-indicator');

        // Default /replay tanpa query memilih kejadian dengan histori terlengkap (incident1)
        $defaultResponse = $this->get(route('public.replay'));
        $defaultResponse->assertStatus(200);
        $this->assertSame($incident1->id, $defaultResponse->viewData('selectedIncident')?->id);
        $this->assertTrue($defaultResponse->viewData('hasSufficientHistory'));

        // Parameter ?incident= mendukung ID numerik maupun fallback aman saat ID tidak valid
        $idResponse = $this->get(route('public.replay', ['incident' => $incident2->id]));
        $idResponse->assertStatus(200);
        $this->assertSame($incident2->id, $idResponse->viewData('selectedIncident')?->id);

        $invalidIdResponse = $this->get(route('public.replay', ['incident' => '999999']));
        $invalidIdResponse->assertStatus(200);
        $this->assertSame($incident1->id, $invalidIdResponse->viewData('selectedIncident')?->id);
    }

    /**
     * 4b & 10. Kejadian tanpa koordinat dan dengan histori terbatas ditampilkan secara jujur di /replay.
     */
    public function test_replay_handles_incident_without_coordinates_and_limited_history_honestly(): void
    {
        $incidentNoCoord = Incident::create([
            'title' => '[SIMULASI] Gangguan Akses Desa Tanpa Koordinat GPS',
            'slug' => 'sim-gangguan-akses-desa-tanpa-koordinat',
            'incident_type' => 'penutupan_akses_jalan',
            'description' => 'Kejadian baru yang belum memiliki koordinat dan belum memiliki laporan lanjutan.',
            'location_name' => 'Desa Sukamaju',
            'latitude' => null,
            'longitude' => null,
            'occurred_at' => now()->subHour(),
            'verification_status' => VerificationStatus::MenungguVerifikasi,
            'follow_up_status' => FollowUpStatus::BelumDitindaklanjuti,
            'severity' => IncidentSeverity::Rendah,
            'is_simulation' => true,
        ]);

        $response = $this->get(route('public.replay', ['incident' => $incidentNoCoord->slug]));

        $response->assertStatus(200);
        $response->assertSee($incidentNoCoord->title);
        $response->assertSee('Pemberitahuan Riwayat Terbatas');
        $response->assertSee('Koordinat Peta Tidak Tersedia');
    }

    /**
     * 5, 6, & 7. /sitrep dapat dibuka, statistik sesuai dengan database,
     *            mendukung filter wilayah dan rentang waktu, serta siap cetak PDF.
     */
    public function test_sitrep_page_displays_accurate_database_statistics_and_supports_filters(): void
    {
        $this->seed(SimulationDataSeeder::class);

        $response = $this->get(route('public.sitrep'));

        $response->assertStatus(200);
        $response->assertSee('Laporan Ringkasan Situasi (SitRep)');
        $response->assertSee('Cetak / Simpan PDF');
        $response->assertSee('window.print()', false);

        $stats = $response->viewData('stats');
        $this->assertNotNull($stats);

        $expectedTotal = Incident::where('verification_status', '!=', VerificationStatus::Ditolak->value)->count();
        $expectedVerified = Incident::where('verification_status', VerificationStatus::Terverifikasi->value)->count();
        $expectedPending = Incident::where('verification_status', VerificationStatus::MenungguVerifikasi->value)->count();
        $expectedCompleted = Incident::where('follow_up_status', FollowUpStatus::Selesai->value)->count();

        $this->assertSame($expectedTotal, $stats['total_incidents']);
        $this->assertSame($expectedVerified, $stats['terverifikasi']);
        $this->assertSame($expectedPending, $stats['menunggu_verifikasi']);
        $this->assertSame($expectedCompleted, $stats['selesai']);

        // Uji filter wilayah pada SitRep
        $filteredByWilayah = $this->get(route('public.sitrep', ['wilayah' => 'Cisarua']));
        $filteredByWilayah->assertStatus(200);
        $filteredByWilayah->assertSee('Tebing setinggi 15 meter mengalami longsor');
        $filteredByWilayah->assertDontSee('Terjadi luapan aliran Sungai Ciliwung akibat hujan deras di hulu');
        $this->assertCount(1, $filteredByWilayah->viewData('incidents'));

        // Uji filter rentang waktu (hanya hari ini)
        $filteredByDate = $this->get(route('public.sitrep', [
            'date_from' => now()->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
        ]));
        $filteredByDate->assertStatus(200);
        $filteredByDate->assertDontSee('Angin kencang disertai hujan deras menyebabkan pohon pelindung jalan tumbang'); // terjadi 26 jam lalu
        $this->assertFalse(
            $filteredByDate->viewData('incidents')->contains('slug', 'sim-pohon-tumbang-akses-cibinong-2026')
        );
    }

    /**
     * 8 & 9. Data pribadi pelapor dan catatan internal petugas tidak bocor pada /replay maupun /sitrep.
     */
    public function test_replay_and_sitrep_never_leak_reporter_pii_or_internal_notes(): void
    {
        $this->seed(SimulationDataSeeder::class);

        $secretReporterName = 'Nama Rahasia Pelapor Replay Sitrep 771';
        $secretReporterContact = '081900009999';
        $secretInternalReason = 'ALASAN INTERNAL VERIFIKATOR RAHASIA 554';

        $incident = Incident::where('slug', 'sim-banjir-luapan-ciliwung-bojonggede-2026')->firstOrFail();

        $report = Report::create([
            'ticket_number' => 'RPT-SIM-5K8N3P9W',
            'incident_id' => $incident->id,
            'reporter_name' => $secretReporterName,
            'reporter_contact' => $secretReporterContact,
            'title' => 'Laporan Genangan Jalan Lingkungan RT 05',
            'description' => 'Air tergenang setinggi 50 cm di dekat pos ronda.',
            'category' => 'banjir',
            'location_name' => 'RT 05 Bojonggede',
            'occurred_at' => now()->subHours(4),
            'verification_status' => VerificationStatus::Terverifikasi,
            'confidence_level' => ConfidenceLevel::Tinggi,
            'is_simulation' => true,
        ]);

        VerificationHistory::create([
            'verifiable_type' => Incident::class,
            'verifiable_id' => $incident->id,
            'previous_status' => VerificationStatus::MenungguVerifikasi->value,
            'new_status' => VerificationStatus::Terverifikasi->value,
            'reason' => $secretInternalReason,
            'is_simulation' => true,
        ]);

        $replayRes = $this->get(route('public.replay', ['incident' => $incident->slug]));
        $replayRes->assertStatus(200);
        $replayRes->assertSee($report->ticket_number);
        $replayRes->assertDontSee($secretReporterName);
        $replayRes->assertDontSee($secretReporterContact);
        $replayRes->assertDontSee($secretInternalReason);
        $replayRes->assertDontSee('Budi Pratama (Verifikator)');
        $replayRes->assertDontSee('Siti Aisyah (Operator)');

        $sitrepRes = $this->get(route('public.sitrep', ['incident' => $incident->slug]));
        $sitrepRes->assertStatus(200);
        $sitrepRes->assertDontSee($secretReporterName);
        $sitrepRes->assertDontSee($secretReporterContact);
        $sitrepRes->assertDontSee($secretInternalReason);
    }

    /**
     * 11. Halaman kosong memiliki empty state yang layak pada /, /replay, dan /sitrep.
     */
    public function test_empty_database_renders_graceful_empty_states_across_public_pages(): void
    {
        // Tanpa menjalankan seeder (database kosong)
        $monitorRes = $this->get(route('public.monitor'));
        $monitorRes->assertStatus(200);
        $monitorRes->assertSee('Tidak Ada Kejadian yang Sesuai Filter');

        $replayRes = $this->get(route('public.replay'));
        $replayRes->assertStatus(200);
        $replayRes->assertSee('Belum Ada Kejadian Simulasi untuk Mode Replay');

        $sitrepRes = $this->get(route('public.sitrep'));
        $sitrepRes->assertStatus(200);
        $sitrepRes->assertSee('Tidak Ada Data Kejadian pada Kriteria Ini');

        $welcomeRes = $this->get(route('public.welcome'));
        $welcomeRes->assertStatus(200);
        $welcomeRes->assertSee('TanggapTerang');
    }

    /**
     * Alur End-to-End 12 Langkah (Bagian E):
     * Pantau -> Detail Kejadian -> Lapor Tanpa Login -> Terima Tiket -> Lacak Tiket ->
     * Verifikator Memeriksa Laporan -> Operator Menghubungkan ke Kejadian & Memperbarui Tindak Lanjut ->
     * Perubahan Tercermin di Halaman Publik, Replay, dan SitRep.
     */
    public function test_complete_end_to_end_user_and_internal_workflow(): void
    {
        $this->seed(SimulationDataSeeder::class);

        $verifikator = User::where('email', 'verifikator@tanggap-terang.id')->firstOrFail();
        $operator = User::where('email', 'operator@tanggap-terang.id')->firstOrFail();
        $incident = Incident::where('slug', 'sim-indikasi-genangan-drainase-kota-2026')->firstOrFail();

        // 1 & 2. Pengunjung membuka Pantau & Detail Kejadian
        $this->get(route('public.monitor'))->assertStatus(200);
        $this->get(route('public.incidents.show', $incident->slug))->assertStatus(200);

        // 3 & 4. Pengunjung mengirim laporan tanpa login & menerima nomor tiket
        $postReport = $this->post(route('public.reports.store'), [
            'reporter_name' => 'Pelapor Alur E2E',
            'reporter_contact' => '081234560000',
            'title' => 'Air Drainase Meluap ke Trotoar Taman Sempur',
            'description' => 'Genangan air menutup jalur pejalan kaki sepanjang 30 meter.',
            'category' => 'banjir',
            'occurred_at' => now()->subMinutes(20)->format('Y-m-d H:i:s'),
            'location_name' => 'Sisi Utara Taman Sempur, Kota Bogor',
        ]);

        $newReport = Report::where('title', 'Air Drainase Meluap ke Trotoar Taman Sempur')->firstOrFail();
        $postReport->assertRedirect(route('public.track', ['ticket' => $newReport->ticket_number]));

        // 5. Pengunjung melacak nomor tiket
        $this->get(route('public.track', ['ticket' => $newReport->ticket_number]))
            ->assertStatus(200)
            ->assertSee($newReport->ticket_number)
            ->assertSee('Menunggu Verifikasi');

        // 6. Verifikator login dan memverifikasi laporan serta kejadian
        $this->actingAs($verifikator)->post(route('internal.reports.verify', $newReport), [
            'confidence_level' => ConfidenceLevel::Tinggi->value,
            'reason' => 'Terkonfirmasi dari dokumentasi warga sekitar Sempur.',
        ])->assertRedirect(route('internal.reports.show', $newReport));

        $this->actingAs($verifikator)->put(route('internal.incidents.verification.update', $incident), [
            'verification_status' => VerificationStatus::Terverifikasi->value,
            'reason' => 'Kejadian genangan Sempur kini terverifikasi.',
        ])->assertRedirect(route('internal.incidents.edit', $incident));

        // 7 & 8. Operator menghubungkan laporan dengan kejadian & memperbarui tindak lanjut
        $this->actingAs($operator)->post(route('internal.reports.link-incident', $newReport), [
            'incident_id' => $incident->id,
            'notes' => 'Lokasi laporan berada tepat di titik kejadian Sempur.',
        ])->assertRedirect(route('internal.reports.show', $newReport));

        $this->actingAs($operator)->put(route('internal.incidents.follow-up.update', $incident), [
            'follow_up_status' => FollowUpStatus::SedangDitindaklanjuti->value,
            'notes' => 'Tim TanggapTerang memetakan titik genangan dan memperbarui informasi akses.',
        ])->assertRedirect(route('internal.incidents.edit', $incident));

        // 9. Perubahan tercermin pada halaman publik (tanpa membocorkan PII pelapor)
        $publicIncident = $this->get(route('public.incidents.show', $incident->slug));
        $publicIncident->assertStatus(200);
        $publicIncident->assertSee($newReport->ticket_number);
        $publicIncident->assertSee('Air Drainase Meluap ke Trotoar Taman Sempur');
        $publicIncident->assertDontSee('Pelapor Alur E2E');
        $publicIncident->assertDontSee('081234560000');

        // 10 & 11. Pengunjung dapat melihat perkembangan melalui Replay dan SitRep
        $this->get(route('public.replay', ['incident' => $incident->slug]))
            ->assertStatus(200)
            ->assertSee($newReport->ticket_number)
            ->assertSee('Tim TanggapTerang memetakan titik genangan dan memperbarui informasi akses.');

        $this->get(route('public.sitrep', ['incident' => $incident->slug]))
            ->assertStatus(200)
            ->assertSee($incident->title);
    }

    /**
     * 7. Pengujian Komprehensif Respons Bantuan Masyarakat, Klasifikasi Sumber Data,
     * Visualisasi 3 Bagian Terpisah, Bukti Konfirmasi (evidence_url), Pembatasan Role,
     * serta Konsistensi Identitas Visual & Navbar.
     */
    public function test_aid_responses_source_classifications_and_brand_gradient_consistency(): void
    {
        $this->seed(SimulationDataSeeder::class);

        $incident = Incident::where('slug', 'sim-banjir-luapan-ciliwung-bojonggede-2026')->firstOrFail();
        $admin = User::where('email', 'admin@tanggap-terang.id')->firstOrFail();
        $verifikator = User::where('email', 'verifikator@tanggap-terang.id')->firstOrFail();
        $operator = User::where('email', 'operator@tanggap-terang.id')->firstOrFail();

        // A & B & C. Halaman Pantau menampilkan klasifikasi sumber yang tepat (bukan klaim OGD pada laporan/respons warga)
        // serta memisahkan visualisasi menjadi 3 bagian (Kebutuhan Tercatat, Respons Bantuan Tercatat, Status Respons Bantuan)
        $monitorResponse = $this->get(route('public.monitor'));
        $monitorResponse->assertStatus(200);
        $monitorResponse->assertSee('nav-active-gradient', false);
        $monitorResponse->assertSee('btn-brand-gradient', false);
        $monitorResponse->assertSee('Alur Transparansi Informasi &amp; Klasifikasi Sumber Data', false);
        $monitorResponse->assertSee('Sumber Pemerintah (Simulasi)');
        $monitorResponse->assertSee('Sumber Informasi Terbuka');
        $monitorResponse->assertSee('Laporan Warga');
        $monitorResponse->assertSee('Respons Bantuan Masyarakat');
        $monitorResponse->assertSee('Kebutuhan Tercatat per Kategori');
        $monitorResponse->assertSee('Respons Bantuan Tercatat per Kategori');
        $monitorResponse->assertSee('Distribusi Status Respons Bantuan');
        $monitorResponse->assertDontSee('Open Government Data');

        // F. Navbar pada /reports/create dan /aid-responses/create TIDAK menandai "1. Pantau" sebagai aktif
        $reportFormResponse = $this->get(route('public.reports.create'));
        $reportFormResponse->assertStatus(200);
        $reportFormResponse->assertSee('Kirim Laporan Masyarakat');
        $reportFormResponse->assertSee('btn-brand-gradient', false);
        $reportFormResponse->assertDontSee('nav-active-gradient', false);

        $aidFormResponse = $this->get(route('public.aid-responses.create', ['incident' => $incident->slug]));
        $aidFormResponse->assertStatus(200);
        $aidFormResponse->assertDontSee('nav-active-gradient', false);
        $aidFormResponse->assertDontSee('Open Government Data');

        // A.5.1: Pengiriman dengan `aid_description` kosong (nullable) berhasil
        $postEmptyDesc = $this->post(route('public.aid-responses.store'), [
            'incident_id' => $incident->id,
            'need_category' => 'air_bersih_sanitasi',
            'organization_name' => 'Komunitas Air Bersih Warga Bojonggede',
            'responder_type' => 'komunitas_warga',
            'contact_name' => 'Budi Santoso',
            'contact_info' => '081200001111',
            'allow_public_name' => '1',
            'aid_item_title' => 'Tandon Air Bersih Darurat 2000 Liter',
            'aid_description' => '', // Kosong -> disimpan sebagai null
            'quantity_label' => '2 Unit Tandon (4.000 Liter)',
            'target_location' => 'Posko RW 03 Bojonggede',
        ]);

        $emptyDescAid = \App\Models\AidResponse::where('aid_item_title', 'Tandon Air Bersih Darurat 2000 Liter')->firstOrFail();
        $postEmptyDesc->assertRedirect(route('public.track', ['ticket' => $emptyDescAid->reference_code]));
        $postEmptyDesc->assertSessionHas('created_ticket', $emptyDescAid->reference_code);
        $this->assertNull($emptyDescAid->aid_description);
        $this->assertSame(\App\Enums\AidResponseStatus::Direncanakan, $emptyDescAid->status);

        // A.5.2: Pengiriman dengan `aid_description` terisi serta proteksi status awal `direncanakan`
        $postAid = $this->post(route('public.aid-responses.store'), [
            'incident_id' => $incident->id,
            'need_category' => 'kesehatan_medis',
            'organization_name' => 'Relawan Medis Nusantara Rahasia',
            'responder_type' => 'organisasi_relawan',
            'contact_name' => 'dr. Rahmat Hidayat (Rahasia)',
            'contact_info' => '081199887766',
            'allow_public_name' => '0',
            'aid_item_title' => 'Pos Pemeriksaan Kesehatan Keliling & Obat Kulit',
            'aid_description' => 'Layanan pemeriksaan kesehatan warga terdampak genangan.',
            'quantity_label' => '1 Tim Medis & 150 Paket Obat',
            'target_location' => 'Balai Warga RW 04 Bojonggede',
            'status' => 'terkonfirmasi', // Percobaan bypass status oleh publik harus diabaikan
        ]);

        $newAid = \App\Models\AidResponse::where('aid_item_title', 'Pos Pemeriksaan Kesehatan Keliling & Obat Kulit')->firstOrFail();
        $postAid->assertRedirect(route('public.track', ['ticket' => $newAid->reference_code]));
        $postAid->assertSessionHas('created_ticket', $newAid->reference_code);

        $this->assertSame('Layanan pemeriksaan kesehatan warga terdampak genangan.', $newAid->aid_description);
        $this->assertSame(\App\Enums\AidResponseStatus::Direncanakan, $newAid->status);
        $this->assertNull($newAid->confirmed_at);
        $this->assertNull($newAid->confirmed_by_user_id);

        // A.5.3: Pengiriman dengan `organization_name` tepat 150 karakter berhasil
        $org150 = str_repeat('K', 150);
        $postOrg150 = $this->post(route('public.aid-responses.store'), [
            'incident_id' => $incident->id,
            'need_category' => 'logistik_pangan',
            'organization_name' => $org150,
            'responder_type' => 'komunitas_warga',
            'allow_public_name' => '1',
            'aid_item_title' => 'Paket Logistik Dapur Warga 150 Karakter',
            'aid_description' => null,
            'quantity_label' => '100 Paket Sembako',
            'target_location' => 'Dapur Umum RW 02',
        ]);
        $org150Aid = \App\Models\AidResponse::where('aid_item_title', 'Paket Logistik Dapur Warga 150 Karakter')->firstOrFail();
        $postOrg150->assertRedirect(route('public.track', ['ticket' => $org150Aid->reference_code]));
        $postOrg150->assertSessionHas('created_ticket', $org150Aid->reference_code);
        $this->assertDatabaseHas('aid_responses', [
            'aid_item_title' => 'Paket Logistik Dapur Warga 150 Karakter',
            'organization_name' => $org150,
        ]);

        // A.5.4: Penolakan `organization_name` sepanjang 151 karakter
        $org151 = str_repeat('X', 151);
        $postOrg151 = $this->from(route('public.aid-responses.create'))->post(route('public.aid-responses.store'), [
            'incident_id' => $incident->id,
            'need_category' => 'logistik_pangan',
            'organization_name' => $org151,
            'responder_type' => 'komunitas_warga',
            'allow_public_name' => '1',
            'aid_item_title' => 'Paket Logistik Ditolak 151 Karakter',
            'quantity_label' => '50 Paket',
            'target_location' => 'Posko Utama',
        ]);
        $postOrg151->assertRedirect(route('public.aid-responses.create'));
        $postOrg151->assertSessionHasErrors(['organization_name']);

        // Pastikan halaman publik TIDAK membocorkan nama kontak pribadi maupun nama organisasi jika allow_public_name = false
        $incidentShow = $this->get(route('public.incidents.show', $incident->slug));
        $incidentShow->assertStatus(200);
        $incidentShow->assertSee($newAid->reference_code);
        $incidentShow->assertSee('Pos Pemeriksaan Kesehatan Keliling &amp; Obat Kulit', false);
        $incidentShow->assertSee('Inisiatif Masyarakat / Pemberi Respons Anonim');
        $incidentShow->assertDontSee('Relawan Medis Nusantara Rahasia');
        $incidentShow->assertDontSee('dr. Rahmat Hidayat (Rahasia)');
        $incidentShow->assertDontSee('081199887766');

        // E. Pembatasan Role: Verifikator TIDAK diizinkan mengubah status operasional respons bantuan (403 Forbidden)
        $this->actingAs($verifikator)->put(route('internal.incidents.aid-responses.update', [$incident, $newAid]), [
            'status' => \App\Enums\AidResponseStatus::DalamPenyaluran->value,
            'confirmation_notes' => 'Percobaan ubah oleh verifikator.',
        ])->assertStatus(403);

        // Operator TIDAK diizinkan memverifikasi respons bantuan (403 Forbidden)
        $this->actingAs($operator)->put(route('internal.incidents.aid-responses.verification.update', [$incident, $newAid]), [
            'verification_status' => \App\Enums\VerificationStatus::Terverifikasi->value,
            'verification_notes' => 'Percobaan verifikasi oleh operator.',
        ])->assertStatus(403);

        // Operator TIDAK dapat menetapkan status 'terkonfirmasi' jika respons belum diverifikasi oleh Verifikator
        $this->actingAs($operator)->from(route('internal.incidents.edit', $incident))
            ->put(route('internal.incidents.aid-responses.update', [$incident, $newAid]), [
                'status' => \App\Enums\AidResponseStatus::Terkonfirmasi->value,
                'confirmation_notes' => 'Catatan konfirmasi sebelum diverifikasi.',
            ])
            ->assertRedirect(route('internal.incidents.edit', $incident))
            ->assertSessionHasErrors(['status']);

        // Verifikator memverifikasi respons bantuan terlebih dahulu
        $this->actingAs($verifikator)->put(route('internal.incidents.aid-responses.verification.update', [$incident, $newAid]), [
            'verification_status' => \App\Enums\VerificationStatus::Terverifikasi->value,
            'verification_notes' => 'Data dan penanggung jawab respons bantuan telah divalidasi.',
        ])->assertRedirect(route('internal.incidents.edit', $incident));

        $newAid->refresh();
        $this->assertSame(\App\Enums\VerificationStatus::Terverifikasi, $newAid->verification_status);

        // D.3: Perubahan ke `terkonfirmasi` tanpa `confirmation_notes` harus ditolak validasi
        $this->actingAs($operator)->from(route('internal.incidents.edit', $incident))
            ->put(route('internal.incidents.aid-responses.update', [$incident, $newAid]), [
                'status' => \App\Enums\AidResponseStatus::Terkonfirmasi->value,
                'confirmation_notes' => '',
            ])
            ->assertRedirect(route('internal.incidents.edit', $incident))
            ->assertSessionHasErrors(['confirmation_notes']);

        // D.1, D.2, D.3: Operator mengonfirmasi status dengan `confirmation_notes` dan `evidence_url` valid
        $evidenceUrl = 'https://contoh-sumber-terbuka.id/dokumentasi/serah-terima-rw04';
        $this->actingAs($operator)->put(route('internal.incidents.aid-responses.update', [$incident, $newAid]), [
            'status' => \App\Enums\AidResponseStatus::Terkonfirmasi->value,
            'confirmation_notes' => 'Dikonfirmasi telah melayani warga di Balai Warga RW 04.',
            'evidence_url' => $evidenceUrl,
        ])->assertRedirect(route('internal.incidents.edit', $incident));

        $newAid->refresh();
        $this->assertSame(\App\Enums\AidResponseStatus::Terkonfirmasi, $newAid->status);
        $this->assertNotNull($newAid->confirmed_at);
        $this->assertSame($operator->id, $newAid->confirmed_by_user_id);
        $this->assertSame($evidenceUrl, $newAid->evidence_url);

        // D.4 & D.5: Pastikan tautan `evidence_url` dan waktu pencatatan + waktu konfirmasi tampil di halaman publik
        $incidentShowAfter = $this->get(route('public.incidents.show', $incident->slug));
        $incidentShowAfter->assertStatus(200);
        $incidentShowAfter->assertSee($evidenceUrl, false);
        $incidentShowAfter->assertSee('Lihat Tautan Bukti Dokumentasi');
        $incidentShowAfter->assertSee('Dicatat:');
        $incidentShowAfter->assertSee('Dikonfirmasi:');

        // Uji perilaku `evidence_url` ketika field TIDAK dikirim: nilai lama tetap dipertahankan
        $this->actingAs($operator)->put(route('internal.incidents.aid-responses.update', [$incident, $newAid]), [
            'status' => \App\Enums\AidResponseStatus::Terkonfirmasi->value,
            'confirmation_notes' => 'Catatan konfirmasi diperbarui tanpa menyentuh field URL.',
        ])->assertRedirect(route('internal.incidents.edit', $incident));

        $newAid->refresh();
        $this->assertSame($evidenceUrl, $newAid->evidence_url);
        $this->assertSame('Catatan konfirmasi diperbarui tanpa menyentuh field URL.', $newAid->confirmation_notes);

        // Uji penghapusan `evidence_url` ketika field dikirim kosong ('') dan pastikan `confirmation_notes` tetap terjaga
        $this->actingAs($operator)->put(route('internal.incidents.aid-responses.update', [$incident, $newAid]), [
            'status' => \App\Enums\AidResponseStatus::DalamPenyaluran->value,
            'confirmation_notes' => '',
            'evidence_url' => '',
        ])->assertRedirect(route('internal.incidents.edit', $incident));

        $newAid->refresh();
        $this->assertNull($newAid->evidence_url);
        $this->assertSame('Catatan konfirmasi diperbarui tanpa menyentuh field URL.', $newAid->confirmation_notes);

        // Admin juga diizinkan memperbarui status respons bantuan
        $this->actingAs($admin)->put(route('internal.incidents.aid-responses.update', [$incident, $emptyDescAid]), [
            'status' => \App\Enums\AidResponseStatus::DalamPenyaluran->value,
            'confirmation_notes' => 'Armada tangki air sedang menuju Posko RW 03.',
        ])->assertRedirect(route('internal.incidents.edit', $incident));

        // Uji eksplisit badge sumber pemerintah: `is_simulation = true` vs `is_simulation = false`
        $officialSimSource = \App\Models\Source::where('source_type', \App\Enums\SourceType::InstansiResmi)->firstOrFail();
        $this->assertTrue((bool) $officialSimSource->is_simulation);

        $officialRealSource = \App\Models\Source::create([
            'name' => 'Portal Data Terbuka Nasional (Non-Simulasi)',
            'source_type' => \App\Enums\SourceType::InstansiResmi,
            'base_url' => 'https://data.go.id',
            'description' => 'Sumber data pemerintah terbuka non-simulasi untuk pengujian badge',
            'credibility_score' => 'resmi',
            'is_active' => true,
            'is_simulation' => false,
        ]);

        \App\Models\IncidentSource::create([
            'incident_id' => $incident->id,
            'source_id' => $officialRealSource->id,
            'headline' => 'Referensi Data Terbuka Hidrologi Non-Simulasi',
            'reference_url' => 'https://data.go.id/dataset/hidrologi',
            'published_at' => now()->subMinutes(10),
            'notes' => 'Pengujian sumber pemerintah non-simulasi.',
            'is_simulation' => false,
        ]);

        $incidentShowWithBothSources = $this->get(route('public.incidents.show', $incident->slug));
        $incidentShowWithBothSources->assertStatus(200);
        $incidentShowWithBothSources->assertSee('Sumber Pemerintah (Simulasi)');
        $incidentShowWithBothSources->assertSee('Data Pemerintah Terbuka');

        $replayWithBothSources = $this->get(route('public.replay', ['incident' => $incident->slug]));
        $replayWithBothSources->assertStatus(200);
        $replayWithBothSources->assertSee('Sumber Pemerintah (Simulasi)');
        $replayWithBothSources->assertSee('Data Pemerintah Terbuka');
    }
}

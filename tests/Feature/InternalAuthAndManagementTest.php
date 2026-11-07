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

class InternalAuthAndManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createInternalUsers(): array
    {
        $admin = User::create([
            'name' => 'Admin Uji Internal',
            'email' => 'admin.uji@tanggap-terang.id',
            'password' => Hash::make('password123'),
            'role' => UserRole::Admin,
            'is_active' => true,
        ]);

        $verifikator = User::create([
            'name' => 'Verifikator Uji Internal',
            'email' => 'verifikator.uji@tanggap-terang.id',
            'password' => Hash::make('password123'),
            'role' => UserRole::Verifikator,
            'is_active' => true,
        ]);

        $operator = User::create([
            'name' => 'Operator Uji Internal',
            'email' => 'operator.uji@tanggap-terang.id',
            'password' => Hash::make('password123'),
            'role' => UserRole::Operator,
            'is_active' => true,
        ]);

        return compact('admin', 'verifikator', 'operator');
    }

    /**
     * 1. Login berhasil untuk pengguna internal aktif, meregenerasi sesi,
     *    mengarahkan pengguna yang sudah login dari /login ke /internal/dashboard,
     *    dan logout menginvalidasi sesi.
     */
    public function test_login_succeeds_for_active_internal_user_and_logout_invalidates_session(): void
    {
        $users = $this->createInternalUsers();

        $loginPage = $this->get(route('login'));
        $loginPage->assertStatus(200);
        $loginPage->assertSee('Masuk ke Panel Pengelolaan TanggapTerang');

        $response = $this->post(route('login.attempt'), [
            'email' => $users['verifikator']->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('internal.dashboard'));
        $this->assertAuthenticatedAs($users['verifikator']);

        // Pengguna yang sudah login tidak perlu kembali ke halaman login
        $revisitLogin = $this->actingAs($users['verifikator'])->get(route('login'));
        $revisitLogin->assertRedirect(route('internal.dashboard'));

        // Logout menginvalidasi sesi
        $logoutResponse = $this->actingAs($users['verifikator'])->post(route('logout'));
        $logoutResponse->assertRedirect(route('login'));
        $this->assertGuest();
    }

    /**
     * 2. Login gagal jika kredensial salah.
     */
    public function test_login_fails_with_invalid_credentials(): void
    {
        $users = $this->createInternalUsers();

        $response = $this->from(route('login'))->post(route('login.attempt'), [
            'email' => $users['admin']->email,
            'password' => 'password-salah-999',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * 3. Akun nonaktif ditolak saat login maupun saat mencoba mengakses route internal.
     */
    public function test_inactive_user_cannot_login_or_access_internal_routes(): void
    {
        $inactiveUser = User::create([
            'name' => 'Petugas Nonaktif',
            'email' => 'nonaktif@tanggap-terang.id',
            'password' => Hash::make('password123'),
            'role' => UserRole::Operator,
            'is_active' => false,
        ]);

        // Ditolak saat mencoba login
        $loginAttempt = $this->from(route('login'))->post(route('login.attempt'), [
            'email' => $inactiveUser->email,
            'password' => 'password123',
        ]);

        $loginAttempt->assertRedirect(route('login'));
        $loginAttempt->assertSessionHasErrors('email');
        $this->assertGuest();

        // Ditolak dengan 403 jika sesi aktif namun status akun dinonaktifkan
        $dashboardAttempt = $this->actingAs($inactiveUser)->get(route('internal.dashboard'));
        $dashboardAttempt->assertStatus(403);
    }

    /**
     * 4. Pengguna guest ditolak dari seluruh route internal (redirect ke /login).
     */
    public function test_guest_user_is_denied_access_to_all_internal_routes(): void
    {
        $this->seed(SimulationDataSeeder::class);
        $report = Report::firstOrFail();
        $incident = Incident::firstOrFail();

        $this->get(route('internal.dashboard'))->assertRedirect(route('login'));
        $this->get(route('internal.reports.index'))->assertRedirect(route('login'));
        $this->get(route('internal.reports.show', $report))->assertRedirect(route('login'));
        $this->post(route('internal.reports.verify', $report), [])->assertRedirect(route('login'));
        $this->post(route('internal.reports.reject', $report), [])->assertRedirect(route('login'));
        $this->post(route('internal.reports.duplicate', $report), [])->assertRedirect(route('login'));
        $this->post(route('internal.reports.link-incident', $report), [])->assertRedirect(route('login'));

        $this->get(route('internal.incidents.index'))->assertRedirect(route('login'));
        $this->get(route('internal.incidents.create'))->assertRedirect(route('login'));
        $this->post(route('internal.incidents.store'), [])->assertRedirect(route('login'));
        $this->get(route('internal.incidents.edit', $incident))->assertRedirect(route('login'));
        $this->put(route('internal.incidents.update', $incident), [])->assertRedirect(route('login'));
        $this->put(route('internal.incidents.verification.update', $incident), [])->assertRedirect(route('login'));
        $this->put(route('internal.incidents.follow-up.update', $incident), [])->assertRedirect(route('login'));
        $this->post(route('internal.incidents.follow-ups.store', $incident), [])->assertRedirect(route('login'));
        $this->post(route('internal.incidents.sources.store', $incident), [])->assertRedirect(route('login'));
    }

    /**
     * 5. Admin dapat mengakses seluruh fitur verifikasi laporan, manajemen kejadian, tindak lanjut, dan sumber.
     */
    public function test_admin_can_access_all_internal_features(): void
    {
        $this->seed(SimulationDataSeeder::class);
        $users = $this->createInternalUsers();
        $admin = $users['admin'];

        $this->actingAs($admin)->get(route('internal.dashboard'))->assertStatus(200);
        $this->actingAs($admin)->get(route('internal.reports.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('internal.incidents.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('internal.incidents.create'))->assertStatus(200);

        // Admin membuat kejadian baru
        $createRes = $this->actingAs($admin)->post(route('internal.incidents.store'), [
            'title' => '[SIMULASI] Genangan Banjir Lokal Jalan Baru oleh Admin',
            'incident_type' => 'banjir',
            'severity' => IncidentSeverity::Sedang->value,
            'description' => 'Data kejadian simulasi yang dibuat oleh Admin untuk menguji akses penuh.',
            'location_name' => 'Jl. Sholeh Iskandar, Kota Bogor',
            'latitude' => '-6.5561000',
            'longitude' => '106.7912000',
            'occurred_at' => now()->subHour()->format('Y-m-d H:i:s'),
        ]);

        $incident = Incident::where('title', '[SIMULASI] Genangan Banjir Lokal Jalan Baru oleh Admin')->firstOrFail();
        $createRes->assertRedirect(route('internal.incidents.edit', $incident));

        // Admin memverifikasi kejadian
        $this->actingAs($admin)->put(route('internal.incidents.verification.update', $incident), [
            'verification_status' => VerificationStatus::Terverifikasi->value,
            'reason' => 'Dikonfirmasi oleh Admin dari laporan dan sumber terbuka.',
        ])->assertRedirect(route('internal.incidents.edit', $incident));

        // Admin mengubah tindak lanjut kejadian
        $this->actingAs($admin)->put(route('internal.incidents.follow-up.update', $incident), [
            'follow_up_status' => FollowUpStatus::SedangDitindaklanjuti->value,
            'notes' => 'Tim sedang memantau tinggi genangan.',
        ])->assertRedirect(route('internal.incidents.edit', $incident));

        $incident->refresh();
        $this->assertSame(VerificationStatus::Terverifikasi, $incident->verification_status);
        $this->assertSame(FollowUpStatus::SedangDitindaklanjuti, $incident->follow_up_status);
    }

    /**
     * 6 & 11. Verifikator dapat memverifikasi laporan, menolak laporan dengan alasan,
     *         menandai duplikat (tanpa menghapus laporan), mengubah verifikasi kejadian,
     *         dan seluruh perubahan tercatat dalam verification_histories.
     */
    public function test_verifikator_can_verify_reject_and_mark_duplicate_reports_with_audit_history(): void
    {
        $this->seed(SimulationDataSeeder::class);
        $users = $this->createInternalUsers();
        $verifikator = $users['verifikator'];

        $reportToVerify = Report::where('ticket_number', 'RPT-SIM-6T9W4L8R')->firstOrFail();

        // Verifikasi laporan
        $resVerify = $this->actingAs($verifikator)->post(route('internal.reports.verify', $reportToVerify), [
            'confidence_level' => ConfidenceLevel::Tinggi->value,
            'reason' => 'Foto tiang miring terkonfirmasi sesuai lokasi simpang Sukahati.',
        ]);

        $resVerify->assertRedirect(route('internal.reports.show', $reportToVerify));
        $reportToVerify->refresh();
        $this->assertSame(VerificationStatus::Terverifikasi, $reportToVerify->verification_status);
        $this->assertSame($verifikator->id, $reportToVerify->verified_by_user_id);

        $this->assertDatabaseHas('verification_histories', [
            'verifiable_type' => Report::class,
            'verifiable_id' => $reportToVerify->id,
            'previous_status' => VerificationStatus::MenungguVerifikasi->value,
            'new_status' => VerificationStatus::Terverifikasi->value,
            'user_id' => $verifikator->id,
            'reason' => 'Foto tiang miring terkonfirmasi sesuai lokasi simpang Sukahati.',
        ]);

        // Penandaan Duplikat (Laporan TIDAK dihapus)
        $resDuplicate = $this->actingAs($verifikator)->post(route('internal.reports.duplicate', $reportToVerify), [
            'reason' => 'Duplikat dari laporan tiket sebelumnya pada lokasi yang sama.',
        ]);

        $resDuplicate->assertRedirect(route('internal.reports.show', $reportToVerify));
        $this->assertDatabaseHas('reports', [
            'id' => $reportToVerify->id,
            'verification_status' => VerificationStatus::Duplikat->value,
            'deleted_at' => null,
        ]);

        // Penolakan Laporan dengan alasan (Laporan TIDAK dihapus)
        $resReject = $this->actingAs($verifikator)->post(route('internal.reports.reject', $reportToVerify), [
            'reason' => 'Dokumentasi tidak relevan dengan lokasi yang diklaim.',
        ]);

        $resReject->assertRedirect(route('internal.reports.show', $reportToVerify));
        $this->assertDatabaseHas('reports', [
            'id' => $reportToVerify->id,
            'verification_status' => VerificationStatus::Ditolak->value,
            'deleted_at' => null,
        ]);
    }

    /**
     * 7 & 12. Verifikator TIDAK dapat mengubah tindak lanjut kejadian, membuat/mengedit kejadian,
     *         atau menghubungkan laporan ke kejadian (menerima respons 403 Forbidden).
     */
    public function test_verifikator_cannot_manage_follow_up_or_operations_and_receives_403(): void
    {
        $this->seed(SimulationDataSeeder::class);
        $users = $this->createInternalUsers();
        $verifikator = $users['verifikator'];

        $incident = Incident::where('slug', 'sim-banjir-luapan-ciliwung-bojonggede-2026')->firstOrFail();
        $report = Report::where('ticket_number', 'RPT-SIM-6T9W4L8R')->firstOrFail();

        // Verifikator mencoba mengubah status tindak lanjut kejadian -> 403
        $this->actingAs($verifikator)->put(route('internal.incidents.follow-up.update', $incident), [
            'follow_up_status' => FollowUpStatus::Selesai->value,
            'notes' => 'Percobaan ilegal oleh verifikator.',
        ])->assertStatus(403);

        // Verifikator mencoba menambahkan catatan tindak lanjut -> 403
        $this->actingAs($verifikator)->post(route('internal.incidents.follow-ups.store', $incident), [
            'action_title' => 'Tindak Lanjut Ilegal',
            'action_date' => now()->format('Y-m-d H:i:s'),
            'status' => 'selesai',
            'notes' => 'Catatan oleh verifikator yang tidak berhak.',
        ])->assertStatus(403);

        // Verifikator mencoba membuat kejadian baru -> 403
        $this->actingAs($verifikator)->get(route('internal.incidents.create'))->assertStatus(403);
        $this->actingAs($verifikator)->post(route('internal.incidents.store'), [])->assertStatus(403);

        // Verifikator mencoba menghubungkan laporan ke kejadian -> 403
        $this->actingAs($verifikator)->post(route('internal.reports.link-incident', $report), [
            'incident_id' => $incident->id,
        ])->assertStatus(403);
    }

    /**
     * 8, 10, & 11. Operator dapat mengelola tindak lanjut kejadian, menambahkan catatan tindak lanjut,
     *              menambahkan sumber informasi terbuka, serta menghubungkan laporan dengan kejadian.
     */
    public function test_operator_can_manage_follow_up_link_reports_and_add_sources(): void
    {
        $this->seed(SimulationDataSeeder::class);
        $users = $this->createInternalUsers();
        $operator = $users['operator'];

        $incident = Incident::where('slug', 'sim-indikasi-genangan-drainase-kota-2026')->firstOrFail();
        $unlinkedReport = Report::where('ticket_number', 'RPT-SIM-6T9W4L8R')->firstOrFail();

        // Operator menghubungkan laporan ke kejadian
        $linkRes = $this->actingAs($operator)->post(route('internal.reports.link-incident', $unlinkedReport), [
            'incident_id' => $incident->id,
            'notes' => 'Menghubungkan laporan infrastruktur ke kejadian kawasan terdekat.',
        ]);

        $linkRes->assertRedirect(route('internal.reports.show', $unlinkedReport));
        $unlinkedReport->refresh();
        $this->assertSame($incident->id, $unlinkedReport->incident_id);

        // Operator mengubah status tindak lanjut kejadian
        $followUpStatusRes = $this->actingAs($operator)->put(route('internal.incidents.follow-up.update', $incident), [
            'follow_up_status' => FollowUpStatus::SedangDitindaklanjuti->value,
            'notes' => 'Tim operator mulai memantau perkembangan genangan di Sempur.',
        ]);

        $followUpStatusRes->assertRedirect(route('internal.incidents.edit', $incident));
        $incident->refresh();
        $this->assertSame(FollowUpStatus::SedangDitindaklanjuti, $incident->follow_up_status);

        // Pastikan tercatat di follow_up_records
        $this->assertDatabaseHas('follow_up_records', [
            'incident_id' => $incident->id,
            'user_id' => $operator->id,
            'status' => FollowUpStatus::SedangDitindaklanjuti->value,
        ]);

        // Operator menambahkan catatan tindak lanjut detail
        $addRecordRes = $this->actingAs($operator)->post(route('internal.incidents.follow-ups.store', $incident), [
            'action_title' => 'Pemetaan Titik Genangan Saluran Sempur',
            'action_date' => now()->format('Y-m-d H:i:s'),
            'status' => 'dipantau',
            'notes' => 'Dokumentasi lapangan telah dihimpun dan ditambahkan pada halaman kejadian.',
            'evidence_url' => 'https://contoh-sumber-terbuka.id/dokumentasi-sempur',
        ]);

        $addRecordRes->assertRedirect(route('internal.incidents.edit', $incident));
        $this->assertDatabaseHas('follow_up_records', [
            'incident_id' => $incident->id,
            'action_title' => 'Pemetaan Titik Genangan Saluran Sempur',
            'user_id' => $operator->id,
        ]);

        // Operator menambahkan sumber informasi terbuka baru ke kejadian
        $addSourceRes = $this->actingAs($operator)->post(route('internal.incidents.sources.store', $incident), [
            'new_source_name' => 'Kanal Informasi Warga Kota Bogor',
            'new_source_type' => 'media_sosial',
            'headline' => '[Simulasi] Laporan Pantauan Genangan Drainase Sempur Sore Hari',
            'reference_url' => 'https://komunitas-bogor.example.org/pantauan-sempur',
            'notes' => 'Unggahan terbuka komunitas warga sekitar.',
        ]);

        $addSourceRes->assertRedirect(route('internal.incidents.edit', $incident));
        $this->assertDatabaseHas('incident_sources', [
            'incident_id' => $incident->id,
            'headline' => '[Simulasi] Laporan Pantauan Genangan Drainase Sempur Sore Hari',
        ]);
    }

    /**
     * 9 & 12. Operator TIDAK dapat mengubah keputusan verifikasi laporan maupun status verifikasi kejadian (403 Forbidden).
     */
    public function test_operator_cannot_change_verification_decisions_and_receives_403(): void
    {
        $this->seed(SimulationDataSeeder::class);
        $users = $this->createInternalUsers();
        $operator = $users['operator'];

        $report = Report::where('ticket_number', 'RPT-SIM-6T9W4L8R')->firstOrFail();
        $incident = Incident::where('slug', 'sim-indikasi-genangan-drainase-kota-2026')->firstOrFail();

        // Operator mencoba memverifikasi laporan -> 403
        $this->actingAs($operator)->post(route('internal.reports.verify', $report), [
            'reason' => 'Percobaan verifikasi oleh operator.',
        ])->assertStatus(403);

        // Operator mencoba menolak laporan -> 403
        $this->actingAs($operator)->post(route('internal.reports.reject', $report), [
            'reason' => 'Percobaan penolakan oleh operator.',
        ])->assertStatus(403);

        // Operator mencoba menandai duplikat laporan -> 403
        $this->actingAs($operator)->post(route('internal.reports.duplicate', $report), [
            'reason' => 'Percobaan duplikat oleh operator.',
        ])->assertStatus(403);

        // Operator mencoba mengubah status verifikasi kejadian -> 403
        $this->actingAs($operator)->put(route('internal.incidents.verification.update', $incident), [
            'verification_status' => VerificationStatus::Terverifikasi->value,
            'reason' => 'Percobaan verifikasi kejadian oleh operator.',
        ])->assertStatus(403);

        // Pastikan status di database tidak berubah
        $report->refresh();
        $incident->refresh();
        $this->assertSame(VerificationStatus::MenungguVerifikasi, $report->verification_status);
        $this->assertSame(VerificationStatus::MenungguVerifikasi, $incident->verification_status);
    }

    /**
     * 13. Halaman publik tidak membocorkan catatan verifikasi internal atau identitas petugas
     *     setelah perubahan dilakukan melalui panel internal.
     */
    public function test_public_pages_do_not_leak_internal_verification_notes_after_internal_updates(): void
    {
        $this->seed(SimulationDataSeeder::class);
        $users = $this->createInternalUsers();
        $verifikator = $users['verifikator'];

        $incident = Incident::where('slug', 'sim-indikasi-genangan-drainase-kota-2026')->firstOrFail();
        $sensitiveReason = 'CATATAN RAHASIA INTERNAL: Dikonfirmasi via nomor telepon Ketua RW 081299887766';

        $this->actingAs($verifikator)->put(route('internal.incidents.verification.update', $incident), [
            'verification_status' => VerificationStatus::Terverifikasi->value,
            'reason' => $sensitiveReason,
        ])->assertRedirect(route('internal.incidents.edit', $incident));

        // Pada halaman internal, catatan internal dan nama petugas tampil untuk audit
        $internalView = $this->actingAs($verifikator)->get(route('internal.incidents.edit', $incident));
        $internalView->assertStatus(200);
        $internalView->assertSee($sensitiveReason);
        $internalView->assertSee($verifikator->name);

        // Pada halaman publik, catatan internal dan nama petugas TIDAK boleh bocor
        $publicDetail = $this->get(route('public.incidents.show', $incident->slug));
        $publicDetail->assertStatus(200);
        $publicDetail->assertDontSee($sensitiveReason);
        $publicDetail->assertDontSee($verifikator->name);
    }
}

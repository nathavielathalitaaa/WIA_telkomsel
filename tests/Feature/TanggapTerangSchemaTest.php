<?php

namespace Tests\Feature;

use App\Enums\ConfidenceLevel;
use App\Enums\FollowUpStatus;
use App\Enums\IncidentSeverity;
use App\Enums\SourceType;
use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Models\FollowUpRecord;
use App\Models\Incident;
use App\Models\IncidentSource;
use App\Models\Report;
use App\Models\Source;
use App\Models\User;
use App\Models\VerificationHistory;
use App\Models\WilayahAdministratif;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TanggapTerangSchemaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Uji 1: Satu kejadian dapat memiliki beberapa laporan masyarakat.
     */
    public function test_incident_has_many_reports(): void
    {
        $incident = Incident::create([
            'title' => 'Banjir Luapan',
            'slug' => 'banjir-luapan-test',
            'incident_type' => 'banjir',
            'description' => 'Luapan sungai',
            'location_name' => 'Kecamatan Cibinong',
            'occurred_at' => now(),
            'verification_status' => VerificationStatus::Terverifikasi,
            'follow_up_status' => FollowUpStatus::SedangDitindaklanjuti,
            'severity' => IncidentSeverity::Sedang,
            'is_simulation' => true,
        ]);

        $report1 = Report::create([
            'ticket_number' => 'RPT-TEST-001',
            'incident_id' => $incident->id,
            'title' => 'Laporan Genangan 1',
            'description' => 'Air masuk halaman',
            'category' => 'korban_terdampak',
            'location_name' => 'RT 01',
            'verification_status' => VerificationStatus::Terverifikasi,
            'is_simulation' => true,
        ]);

        $report2 = Report::create([
            'ticket_number' => 'RPT-TEST-002',
            'incident_id' => $incident->id,
            'title' => 'Laporan Genangan 2',
            'description' => 'Arus deras',
            'category' => 'penutupan_akses_jalan',
            'location_name' => 'RT 02',
            'verification_status' => VerificationStatus::Terverifikasi,
            'is_simulation' => true,
        ]);

        $this->assertCount(2, $incident->reports);
        $this->assertEquals($incident->id, $report1->incident->id);
        $this->assertEquals($incident->id, $report2->incident->id);
    }

    /**
     * Uji 2: Satu laporan dapat belum terhubung dengan kejadian mana pun (nullable incident_id).
     */
    public function test_report_can_exist_without_incident(): void
    {
        $looseReport = Report::create([
            'ticket_number' => 'RPT-LOOSE-001',
            'incident_id' => null,
            'title' => 'Laporan Mandiri Belum Di-assign',
            'description' => 'Ada asap pekat di bukit',
            'category' => 'kebakaran',
            'location_name' => 'Bukit Sentul',
            'verification_status' => VerificationStatus::MenungguVerifikasi,
            'is_simulation' => true,
        ]);

        $this->assertNull($looseReport->incident_id);
        $this->assertNull($looseReport->incident);
        $this->assertTrue(Report::unassigned()->where('id', $looseReport->id)->exists());
    }

    /**
     * Uji 3: Pelapor tidak diwajibkan memiliki akun (nullable user_id, tapi menyimpan reporter_name & reporter_contact).
     */
    public function test_reporter_does_not_require_user_account(): void
    {
        $anonymousReport = Report::create([
            'ticket_number' => 'RPT-ANON-001',
            'user_id' => null,
            'reporter_name' => 'Warga Anonim',
            'reporter_contact' => '081299998888',
            'title' => 'Laporan Anonim',
            'description' => 'Kabel terputus',
            'category' => 'kerusakan_infrastruktur',
            'location_name' => 'Jl. Merdeka',
            'verification_status' => VerificationStatus::MenungguVerifikasi,
            'is_simulation' => true,
        ]);

        $this->assertNull($anonymousReport->user_id);
        $this->assertNull($anonymousReport->user);
        $this->assertEquals('Warga Anonim', $anonymousReport->reporter_name);
        $this->assertEquals('081299998888', $anonymousReport->reporter_contact);
    }

    /**
     * Uji 4: Tim TanggapTerang memiliki akun internal dengan role (admin, verifikator, operator).
     */
    public function test_internal_users_have_roles(): void
    {
        $admin = User::create([
            'name' => 'Admin Internal',
            'email' => 'admin.test@tanggap-terang.id',
            'password' => bcrypt('secret'),
            'role' => UserRole::Admin,
            'is_active' => true,
        ]);

        $verifikator = User::create([
            'name' => 'Verifikator Lapangan',
            'email' => 'verifikator.test@tanggap-terang.id',
            'password' => bcrypt('secret'),
            'role' => UserRole::Verifikator,
            'is_active' => true,
        ]);

        $this->assertEquals(UserRole::Admin, $admin->role);
        $this->assertEquals(UserRole::Verifikator, $verifikator->role);
        $this->assertTrue($admin->is_active);
    }

    /**
     * Uji 5: Status verifikasi dan status tindak lanjut terpisah pada incident dan report.
     */
    public function test_verification_status_and_follow_up_status_are_distinct(): void
    {
        $incident = Incident::create([
            'title' => 'Gempa Bumi Dangkal',
            'slug' => 'gempa-dangkal-test',
            'incident_type' => 'gempa_bumi',
            'description' => 'Magnitudo 4.2 kedalaman 10 km',
            'location_name' => 'Pusat gempa 15 km barat daya',
            'occurred_at' => now(),
            'verification_status' => VerificationStatus::Terverifikasi,
            'follow_up_status' => FollowUpStatus::BelumDitindaklanjuti,
            'severity' => IncidentSeverity::Sedang,
            'is_simulation' => true,
        ]);

        // Kejadian terverifikasi secara faktual tapi tindak lanjut baru dimulai
        $this->assertEquals(VerificationStatus::Terverifikasi, $incident->verification_status);
        $this->assertEquals(FollowUpStatus::BelumDitindaklanjuti, $incident->follow_up_status);

        // Update tindak lanjut tanpa mengubah status verifikasi
        $incident->update(['follow_up_status' => FollowUpStatus::SedangDitindaklanjuti]);
        $this->assertEquals(FollowUpStatus::SedangDitindaklanjuti, $incident->fresh()->follow_up_status);
        $this->assertEquals(VerificationStatus::Terverifikasi, $incident->fresh()->verification_status);
    }

    /**
     * Uji 6: Sumber informasi dapat berasal dari laporan masyarakat maupun sumber terbuka (media, instansi resmi).
     */
    public function test_sources_support_citizen_reports_and_open_sources(): void
    {
        $incident = Incident::create([
            'title' => 'Angin Puting Beliung',
            'slug' => 'angin-puting-beliung-test',
            'incident_type' => 'cuaca_ekstrem',
            'description' => 'Angin kencang merusak atap rumah',
            'location_name' => 'Desa Sukamaju',
            'occurred_at' => now(),
            'verification_status' => VerificationStatus::Terverifikasi,
            'follow_up_status' => FollowUpStatus::Dipantau,
            'is_simulation' => true,
        ]);

        $sourceCitizen = Source::create([
            'name' => 'Laporan Mandiri Warga',
            'source_type' => SourceType::LaporanMasyarakat,
            'credibility_score' => 'sedang',
            'is_simulation' => true,
        ]);

        $sourceOfficial = Source::create([
            'name' => 'Twitter BMKG',
            'source_type' => SourceType::InstansiResmi,
            'base_url' => 'https://twitter.com/infoBMKG',
            'credibility_score' => 'resmi',
            'is_simulation' => true,
        ]);

        IncidentSource::create([
            'incident_id' => $incident->id,
            'source_id' => $sourceCitizen->id,
            'headline' => 'Warga melaporkan atap terhempas',
            'is_simulation' => true,
        ]);

        IncidentSource::create([
            'incident_id' => $incident->id,
            'source_id' => $sourceOfficial->id,
            'reference_url' => 'https://twitter.com/infoBMKG/status/123456',
            'headline' => 'Peringatan Dini Cuaca Jabodetabek',
            'is_simulation' => true,
        ]);

        $this->assertCount(2, $incident->sources);
        $this->assertTrue($incident->sources->contains($sourceCitizen));
        $this->assertTrue($incident->sources->contains($sourceOfficial));
    }

    /**
     * Uji 7: Setiap perubahan status penting memiliki riwayat (VerificationHistory).
     */
    public function test_verification_history_audit_trail(): void
    {
        $verifier = User::create([
            'name' => 'Verifikator Ahli',
            'email' => 'verifikator.audit@tanggap-terang.id',
            'password' => bcrypt('secret'),
            'role' => UserRole::Verifikator,
        ]);

        $report = Report::create([
            'ticket_number' => 'RPT-AUDIT-001',
            'title' => 'Jembatan Putus',
            'description' => 'Jembatan kayu patah',
            'category' => 'kerusakan_infrastruktur',
            'location_name' => 'Kampung Cibogo',
            'verification_status' => VerificationStatus::MenungguVerifikasi,
            'is_simulation' => true,
        ]);

        // Simpan riwayat
        $history = VerificationHistory::create([
            'verifiable_type' => Report::class,
            'verifiable_id' => $report->id,
            'previous_status' => VerificationStatus::MenungguVerifikasi->value,
            'new_status' => VerificationStatus::Terverifikasi->value,
            'user_id' => $verifier->id,
            'reason' => 'Foto dikonfirmasi dengan kepala dusun setempat',
            'is_simulation' => true,
        ]);

        $report->update([
            'verification_status' => VerificationStatus::Terverifikasi,
            'verified_at' => now(),
            'verified_by_user_id' => $verifier->id,
        ]);

        $this->assertCount(1, $report->verificationHistories);
        $this->assertEquals(VerificationStatus::Terverifikasi->value, $report->verificationHistories->first()->new_status);
        $this->assertEquals($verifier->id, $report->verificationHistories->first()->user->id);
    }

    /**
     * Uji 8: Catatan tindak lanjut (FollowUpRecord) tersimpan terpisah.
     */
    public function test_follow_up_records_attached_to_incident_and_report(): void
    {
        $operator = User::create([
            'name' => 'Petugas Ops',
            'email' => 'ops@tanggap-terang.id',
            'password' => bcrypt('secret'),
            'role' => UserRole::Operator,
        ]);

        $incident = Incident::create([
            'title' => 'Tanah Bergerak',
            'slug' => 'tanah-bergerak-test',
            'incident_type' => 'tanah_longsor',
            'description' => 'Retakan tanah 20 meter',
            'location_name' => 'Desa Sukaresmi',
            'occurred_at' => now(),
            'verification_status' => VerificationStatus::Terverifikasi,
            'follow_up_status' => FollowUpStatus::SedangDitindaklanjuti,
            'is_simulation' => true,
        ]);

        $followUp = FollowUpRecord::create([
            'incident_id' => $incident->id,
            'user_id' => $operator->id,
            'action_title' => 'Pengiriman Tim Tinjau Cepat',
            'action_date' => now(),
            'status' => 'selesai',
            'notes' => 'Tim memasang patok pantauan rekahan',
            'is_simulation' => true,
        ]);

        $this->assertCount(1, $incident->followUpRecords);
        $this->assertEquals('Pengiriman Tim Tinjau Cepat', $incident->followUpRecords->first()->action_title);
        $this->assertEquals($operator->id, $followUp->user->id);
    }

    /**
     * Uji 9: Data simulasi dapat dibedakan dari data lainnya (is_simulation flag).
     */
    public function test_simulation_data_filtering(): void
    {
        Incident::create([
            'title' => 'Insiden Simulasi',
            'slug' => 'insiden-simulasi-test',
            'incident_type' => 'banjir',
            'description' => 'Data simulasi uji coba',
            'location_name' => 'Lokasi Uji',
            'occurred_at' => now(),
            'is_simulation' => true,
        ]);

        Incident::create([
            'title' => 'Insiden Nyata',
            'slug' => 'insiden-nyata-test',
            'incident_type' => 'banjir',
            'description' => 'Data operasional riil',
            'location_name' => 'Lokasi Riil',
            'occurred_at' => now(),
            'is_simulation' => false,
        ]);

        $this->assertEquals(1, Incident::simulation()->where('slug', 'insiden-simulasi-test')->count());
        $this->assertEquals(0, Incident::real()->where('slug', 'insiden-simulasi-test')->count());
        $this->assertEquals(1, Incident::real()->where('slug', 'insiden-nyata-test')->count());
    }

    /**
     * Uji 10: Generator nomor tiket bersifat acak non-sekuensial dan serialisasi Report menyembunyikan data pribadi pelapor.
     */
    public function test_ticket_number_is_non_sequential_and_hides_private_reporter_data(): void
    {
        $ticket1 = Report::generateTicketNumber(true);
        $ticket2 = Report::generateTicketNumber(true);

        $this->assertMatchesRegularExpression('/^RPT-SIM-[23456789ABCDEFGHJKLMNPQRSTUVWXYZ]{8}$/', $ticket1);
        $this->assertMatchesRegularExpression('/^RPT-SIM-[23456789ABCDEFGHJKLMNPQRSTUVWXYZ]{8}$/', $ticket2);
        $this->assertNotEquals($ticket1, $ticket2);

        $report = Report::create([
            'ticket_number' => $ticket1,
            'reporter_name' => 'Rahasia Pelapor',
            'reporter_contact' => '081111111111',
            'title' => 'Laporan Uji Privasi',
            'description' => 'Deskripsi uji',
            'category' => 'lainnya',
            'location_name' => 'Lokasi Uji',
            'verification_status' => VerificationStatus::MenungguVerifikasi,
            'is_simulation' => true,
        ]);

        $serialized = $report->toArray();
        $this->assertArrayNotHasKey('reporter_name', $serialized);
        $this->assertArrayNotHasKey('reporter_contact', $serialized);
        $this->assertArrayNotHasKey('user_id', $serialized);
        $this->assertArrayNotHasKey('verified_by_user_id', $serialized);
    }

    /**
     * Uji 11: Matriks otorisasi role pada model User dan sanitasi komponen audit-timeline publik vs internal.
     */
    public function test_role_authorization_helpers_and_public_audit_timeline_privacy(): void
    {
        $admin = User::create([
            'name' => 'Nama Rahasia Admin',
            'email' => 'admin.auth@tanggap-terang.id',
            'password' => bcrypt('secret'),
            'role' => UserRole::Admin,
            'is_active' => true,
        ]);

        $verifikator = User::create([
            'name' => 'Nama Rahasia Verifikator',
            'email' => 'verifikator.auth@tanggap-terang.id',
            'password' => bcrypt('secret'),
            'role' => UserRole::Verifikator,
            'is_active' => true,
        ]);

        $operator = User::create([
            'name' => 'Nama Rahasia Operator',
            'email' => 'operator.auth@tanggap-terang.id',
            'password' => bcrypt('secret'),
            'role' => UserRole::Operator,
            'is_active' => true,
        ]);

        $this->assertTrue($admin->canVerify());
        $this->assertTrue($admin->canManageOperations());

        $this->assertTrue($verifikator->canVerify());
        $this->assertFalse($verifikator->canManageOperations());

        $this->assertFalse($operator->canVerify());
        $this->assertTrue($operator->canManageOperations());

        $vh = new VerificationHistory([
            'previous_status' => 'menunggu_verifikasi',
            'new_status' => 'terverifikasi',
            'reason' => 'Catatan internal nomor kontak ketua RT 0812xxx',
            'is_simulation' => true,
        ]);
        $vh->created_at = now();
        $vh->setRelation('user', $verifikator);

        $publicHtml = view('components.audit-timeline', [
            'verificationHistories' => collect([$vh]),
            'followUpRecords' => collect(),
            'isInternal' => false,
        ])->render();

        $internalHtml = view('components.audit-timeline', [
            'verificationHistories' => collect([$vh]),
            'followUpRecords' => collect(),
            'isInternal' => true,
        ])->render();

        // Mode publik tidak boleh membocorkan nama petugas maupun catatan alasan internal sensitif
        $this->assertStringNotContainsString('Nama Rahasia Verifikator', $publicHtml);
        $this->assertStringNotContainsString('Catatan internal nomor kontak ketua RT 0812xxx', $publicHtml);
        $this->assertStringContainsString('Tim Verifikator TanggapTerang', $publicHtml);

        // Mode internal menampilkan nama petugas dan alasan verifikasi
        $this->assertStringContainsString('Nama Rahasia Verifikator', $internalHtml);
        $this->assertStringContainsString('Catatan internal nomor kontak ketua RT 0812xxx', $internalHtml);
    }
}

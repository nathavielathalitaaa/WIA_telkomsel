<?php

namespace Database\Seeders;

use App\Enums\AidResponseStatus;
use App\Enums\ConfidenceLevel;
use App\Enums\FollowUpStatus;
use App\Enums\IncidentSeverity;
use App\Enums\SourceType;
use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Models\AidResponse;
use App\Models\FollowUpRecord;
use App\Models\Incident;
use App\Models\IncidentSource;
use App\Models\Report;
use App\Models\Source;
use App\Models\User;
use App\Models\VerificationHistory;
use App\Models\WilayahAdministratif;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SimulationDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Akun Tim Internal TanggapTerang
        $admin = User::updateOrCreate(
            ['email' => 'admin@tanggap-terang.id'],
            [
                'name' => 'Admin TanggapTerang',
                'password' => Hash::make('password123'),
                'role' => UserRole::Admin,
                'is_active' => true,
            ]
        );

        $verifikator = User::updateOrCreate(
            ['email' => 'verifikator@tanggap-terang.id'],
            [
                'name' => 'Budi Pratama (Verifikator)',
                'password' => Hash::make('password123'),
                'role' => UserRole::Verifikator,
                'is_active' => true,
            ]
        );

        $operator = User::updateOrCreate(
            ['email' => 'operator@tanggap-terang.id'],
            [
                'name' => 'Siti Aisyah (Operator)',
                'password' => Hash::make('password123'),
                'role' => UserRole::Operator,
                'is_active' => true,
            ]
        );

        // Ambil wilayah administratif sampel (fallback jika tidak ada)
        $wilayahBogor = WilayahAdministratif::where('nama', 'like', '%Kabupaten Bogor%')->first();
        $wilayahKotaBogor = WilayahAdministratif::where('nama', 'like', '%Kota Bogor%')->first();
        $wilayahId = $wilayahBogor?->id;
        $wilayahKotaId = $wilayahKotaBogor?->id ?? $wilayahId;

        // 2. Master Sumber Informasi (Sources)
        $sourceWarga = Source::updateOrCreate(
            ['name' => 'Laporan Masyarakat Mandiri (Crowdsourcing)'],
            [
                'source_type' => SourceType::LaporanMasyarakat,
                'base_url' => 'https://tanggap-terang.local/reports/create',
                'description' => 'Kanal laporan masyarakat terbuka yang masuk ke sistem TanggapTerang',
                'credibility_score' => 'sedang',
                'is_active' => true,
                'is_simulation' => true,
            ]
        );

        $sourceCuaca = Source::updateOrCreate(
            ['name' => 'Rilis Informasi Cuaca Publik Terbuka'],
            [
                'source_type' => SourceType::InstansiResmi,
                'base_url' => 'https://info-cuaca-publik.example.org',
                'description' => 'Publikasi terbuka prakiraan cuaca dan peringatan hidrometeorologi',
                'credibility_score' => 'resmi',
                'is_active' => true,
                'is_simulation' => true,
            ]
        );

        $sourceDaerah = Source::updateOrCreate(
            ['name' => 'Siaran Informasi Kebencanaan Daerah (Sumber Terbuka)'],
            [
                'source_type' => SourceType::InstansiResmi,
                'base_url' => 'https://info-bencana-daerah.example.org',
                'description' => 'Rilis informasi publik terkait perkembangan situasi kejadian daerah',
                'credibility_score' => 'resmi',
                'is_active' => true,
                'is_simulation' => true,
            ]
        );

        $sourceMedia = Source::updateOrCreate(
            ['name' => 'Portal Berita Lokal Terbuka Radar Wilayah'],
            [
                'source_type' => SourceType::MediaMassa,
                'base_url' => 'https://radarbogor.example.com',
                'description' => 'Pemberitaan media massa daring lokal mengenai kondisi lapangan',
                'credibility_score' => 'tinggi',
                'is_active' => true,
                'is_simulation' => true,
            ]
        );

        // 3. Kejadian Simulasi (Incidents)
        $incident1 = Incident::updateOrCreate(
            ['slug' => 'sim-banjir-luapan-ciliwung-bojonggede-2026'],
            [
                'title' => '[SIMULASI] Banjir Luapan Sungai Ciliwung Bojonggede',
                'incident_type' => 'banjir',
                'description' => 'Data simulasi: Terjadi luapan aliran Sungai Ciliwung akibat hujan deras di hulu. Ketinggian genangan dilaporkan mencapai 80-120 cm di area pemukiman bantaran sungai dan menghambat akses gang warga.',
                'location_name' => 'Bantaran Sungai Ciliwung, Desa Bojonggede, Kab. Bogor',
                'wilayah_administratif_id' => $wilayahId,
                'latitude' => -6.4912000,
                'longitude' => 106.7975000,
                'occurred_at' => now()->subHours(8),
                'verification_status' => VerificationStatus::Terverifikasi,
                'follow_up_status' => FollowUpStatus::SedangDitindaklanjuti,
                'severity' => IncidentSeverity::Kritis,
                'is_simulation' => true,
                'created_by_user_id' => $operator->id,
            ]
        );

        $incident2 = Incident::updateOrCreate(
            ['slug' => 'sim-longsor-jalur-puncak-km82-2026'],
            [
                'title' => '[SIMULASI] Tanah Longsor Tebing Jalur Puncak KM 82',
                'incident_type' => 'tanah_longsor',
                'description' => 'Data simulasi: Tebing setinggi 15 meter mengalami longsor dan menutup sebagian badan jalan raya alternatif arah Puncak. Arus kendaraan diberlakukan buka-tutup sementara.',
                'location_name' => 'Jalur Raya Puncak KM 82, Cisarua, Kab. Bogor',
                'wilayah_administratif_id' => $wilayahId,
                'latitude' => -6.6978000,
                'longitude' => 106.9456000,
                'occurred_at' => now()->subHours(14),
                'verification_status' => VerificationStatus::Terverifikasi,
                'follow_up_status' => FollowUpStatus::Dipantau,
                'severity' => IncidentSeverity::Tinggi,
                'is_simulation' => true,
                'created_by_user_id' => $operator->id,
            ]
        );

        $incident3 = Incident::updateOrCreate(
            ['slug' => 'sim-pohon-tumbang-akses-cibinong-2026'],
            [
                'title' => '[SIMULASI] Pohon Tumbang Menutup Akses Jalan Raya Cibinong',
                'incident_type' => 'cuaca_ekstrem',
                'description' => 'Data simulasi: Angin kencang disertai hujan deras menyebabkan pohon pelindung jalan tumbang melintang di jalur utama. Saat ini dahan pohon telah dibersihkan dan jalur kembali normal.',
                'location_name' => 'Jl. Raya Mayor Oking, Cibinong, Kab. Bogor',
                'wilayah_administratif_id' => $wilayahId,
                'latitude' => -6.4815000,
                'longitude' => 106.8542000,
                'occurred_at' => now()->subHours(26),
                'verification_status' => VerificationStatus::Terverifikasi,
                'follow_up_status' => FollowUpStatus::Selesai,
                'severity' => IncidentSeverity::Sedang,
                'is_simulation' => true,
                'created_by_user_id' => $operator->id,
            ]
        );

        $incident4 = Incident::updateOrCreate(
            ['slug' => 'sim-indikasi-genangan-drainase-kota-2026'],
            [
                'title' => '[SIMULASI] Laporan Awal Genangan Saluran Drainase Sempur',
                'incident_type' => 'banjir',
                'description' => 'Data simulasi: Informasi awal mengenai luapan saluran drainase jalan protokol pasca hujan sore. Tim verifikator masih mengumpulkan bukti dokumentasi dari sumber lapangan.',
                'location_name' => 'Kawasan Sempur, Kota Bogor',
                'wilayah_administratif_id' => $wilayahKotaId,
                'latitude' => -6.5918000,
                'longitude' => 106.8012000,
                'occurred_at' => now()->subHours(2),
                'verification_status' => VerificationStatus::MenungguVerifikasi,
                'follow_up_status' => FollowUpStatus::BelumDitindaklanjuti,
                'severity' => IncidentSeverity::Rendah,
                'is_simulation' => true,
                'created_by_user_id' => $operator->id,
            ]
        );

        // 4. Sumber Informasi Terkait Kejadian (Incident Sources)
        IncidentSource::updateOrCreate(
            [
                'incident_id' => $incident1->id,
                'source_id' => $sourceDaerah->id,
                'reference_url' => 'https://info-bencana-daerah.example.org/simulasi-banjir-bojonggede',
            ],
            [
                'headline' => '[Simulasi] Rilis Informasi Pemantauan Tinggi Muka Air Bantaran Ciliwung',
                'published_at' => now()->subHours(7),
                'notes' => 'Rujukan informasi terbuka kenaikan debit air hulu',
                'is_simulation' => true,
            ]
        );

        IncidentSource::updateOrCreate(
            [
                'incident_id' => $incident1->id,
                'source_id' => $sourceMedia->id,
                'reference_url' => 'https://radarbogor.example.com/berita/simulasi-banjir-bojonggede',
            ],
            [
                'headline' => '[Simulasi] Genangan Air Rendam Pemukiman Warga di Bojonggede Usai Hujan Deras',
                'published_at' => now()->subHours(6),
                'notes' => 'Artikel berita terbuka disertai foto kondisi jalan lingkungan',
                'is_simulation' => true,
            ]
        );

        IncidentSource::updateOrCreate(
            [
                'incident_id' => $incident2->id,
                'source_id' => $sourceCuaca->id,
                'reference_url' => 'https://info-cuaca-publik.example.org/simulasi-curah-hujan-puncak',
            ],
            [
                'headline' => '[Simulasi] Peringatan Potensi Pergerakan Tanah Kawasan Perbukitan',
                'published_at' => now()->subHours(13),
                'notes' => 'Data curah hujan ekstrem kawasan Puncak',
                'is_simulation' => true,
            ]
        );

        IncidentSource::updateOrCreate(
            [
                'incident_id' => $incident3->id,
                'source_id' => $sourceWarga->id,
                'reference_url' => 'https://tanggap-terang.local/incidents/sim-pohon-tumbang-akses-cibinong-2026',
            ],
            [
                'headline' => '[Simulasi] Kompilasi Laporan Warga Mengenai Pohon Tumbang di Cibinong',
                'published_at' => now()->subHours(25),
                'notes' => 'Dikonfirmasi dari kesesuaian dua laporan masyarakat di titik yang sama',
                'is_simulation' => true,
            ]
        );

        // Bersihkan nomor tiket sekuensial lama jika ada dari seeder versi sebelumnya
        Report::withTrashed()->whereIn('ticket_number', [
            'RPT-SIM-20261106-0001',
            'RPT-SIM-20261106-0002',
            'RPT-SIM-20261106-0003',
        ])->forceDelete();

        // 5. Laporan Masyarakat Simulasi (Reports dengan kode non-enumerable)
        // Kasus A: Terhubung ke Incident 1, pelapor tanpa akun, Terverifikasi
        $report1 = Report::updateOrCreate(
            ['ticket_number' => 'RPT-SIM-8K4M9P2X'],
            [
                'incident_id' => $incident1->id,
                'user_id' => null,
                'reporter_name' => 'Ahmad Subarjo (Simulasi)',
                'reporter_contact' => '081200001111',
                'title' => '[SIMULASI] Air meluap masuk pekarangan dan rumah warga RT 02/RW 04',
                'description' => 'Air sungai mulai naik ke jalan lingkungan sejak pukul 05.00 pagi setinggi 80 cm. Warga mulai memindahkan barang ke tempat lebih tinggi.',
                'category' => 'korban_terdampak',
                'location_name' => 'RT 02 / RW 04 Dusun 2, Bojonggede',
                'wilayah_administratif_id' => $wilayahId,
                'latitude' => -6.4915000,
                'longitude' => 106.7978000,
                'occurred_at' => now()->subHours(7),
                'evidence_url' => 'https://images.unsplash.com/photo-1547683905-f686c993aae5?auto=format&fit=crop&w=800&q=80',
                'verification_status' => VerificationStatus::Terverifikasi,
                'confidence_level' => ConfidenceLevel::Tinggi,
                'is_simulation' => true,
                'verified_at' => now()->subHours(6),
                'verified_by_user_id' => $verifikator->id,
            ]
        );

        // Kasus B: Terhubung ke Incident 1, pelapor tanpa akun, Terverifikasi
        $report2 = Report::updateOrCreate(
            ['ticket_number' => 'RPT-SIM-3X7N2Q5W'],
            [
                'incident_id' => $incident1->id,
                'user_id' => null,
                'reporter_name' => 'Siti Nurhaliza (Simulasi)',
                'reporter_contact' => 'siti.simulasi@example.org',
                'title' => '[SIMULASI] Akses jalan gang terputus oleh genangan arus deras',
                'description' => 'Akses keluar jalan gang menuju jalan utama tidak dapat dilalui kendaraan roda dua karena genangan air.',
                'category' => 'penutupan_akses_jalan',
                'location_name' => 'Gang Melati RT 03/04, Bojonggede',
                'wilayah_administratif_id' => $wilayahId,
                'latitude' => -6.4920000,
                'longitude' => 106.7981000,
                'occurred_at' => now()->subHours(6),
                'evidence_url' => 'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?auto=format&fit=crop&w=800&q=80',
                'verification_status' => VerificationStatus::Terverifikasi,
                'confidence_level' => ConfidenceLevel::Tinggi,
                'is_simulation' => true,
                'verified_at' => now()->subHours(5),
                'verified_by_user_id' => $verifikator->id,
            ]
        );

        // Kasus C: Laporan masyarakat yang BELUM TERHUBUNG ke kejadian mana pun (Loose Report) & Menunggu Verifikasi
        $report3 = Report::updateOrCreate(
            ['ticket_number' => 'RPT-SIM-6T9W4L8R'],
            [
                'incident_id' => null,
                'user_id' => null,
                'reporter_name' => 'Pelapor Anonim (Simulasi)',
                'reporter_contact' => null,
                'title' => '[SIMULASI] Tiang penerangan jalan miring pasca angin kencang',
                'description' => 'Terdapat tiang lampu jalan umum yang miring ke arah bahu jalan setelah hujan angin di dekat simpang Sukahati.',
                'category' => 'kerusakan_infrastruktur',
                'location_name' => 'Simpang Sukahati, Cibinong',
                'wilayah_administratif_id' => $wilayahId,
                'latitude' => -6.4821000,
                'longitude' => 106.8290000,
                'occurred_at' => now()->subHours(3),
                'evidence_url' => null,
                'verification_status' => VerificationStatus::MenungguVerifikasi,
                'confidence_level' => ConfidenceLevel::Sedang,
                'is_simulation' => true,
                'verified_at' => null,
                'verified_by_user_id' => null,
            ]
        );

        // Kasus D: Terhubung ke Incident 2, Terverifikasi
        $report4 = Report::updateOrCreate(
            ['ticket_number' => 'RPT-SIM-4D9H2V7K'],
            [
                'incident_id' => $incident2->id,
                'user_id' => null,
                'reporter_name' => 'Hendra Wijaya (Simulasi)',
                'reporter_contact' => '081300002222',
                'title' => '[SIMULASI] Material tanah dan batu menutup separuh lajur jalan',
                'description' => 'Pengendara harap berhati-hati karena material longsoran tebing menutup lajur kiri jalan arah puncak.',
                'category' => 'penutupan_akses_jalan',
                'location_name' => 'Jalur Puncak KM 82, Cisarua',
                'wilayah_administratif_id' => $wilayahId,
                'latitude' => -6.6979000,
                'longitude' => 106.9458000,
                'occurred_at' => now()->subHours(13),
                'evidence_url' => 'https://images.unsplash.com/photo-1541888946425-d09bb18086f6?auto=format&fit=crop&w=800&q=80',
                'verification_status' => VerificationStatus::Terverifikasi,
                'confidence_level' => ConfidenceLevel::Tinggi,
                'is_simulation' => true,
                'verified_at' => now()->subHours(12),
                'verified_by_user_id' => $verifikator->id,
            ]
        );

        // Kasus E: Laporan Ditolak (untuk menguji transparansi status ditolak)
        $report5 = Report::updateOrCreate(
            ['ticket_number' => 'RPT-SIM-9P3F6Y2M'],
            [
                'incident_id' => null,
                'user_id' => null,
                'reporter_name' => 'Warga Simulasi',
                'reporter_contact' => null,
                'title' => '[SIMULASI] Kabar Jembatan Ambruk di Sektor Barat',
                'description' => 'Meneruskan pesan berantai mengenai jembatan putus akibat arus sungai.',
                'category' => 'kerusakan_infrastruktur',
                'location_name' => 'Sektor Barat, Kab. Bogor',
                'wilayah_administratif_id' => $wilayahId,
                'latitude' => -6.5100000,
                'longitude' => 106.7500000,
                'occurred_at' => now()->subHours(5),
                'evidence_url' => null,
                'verification_status' => VerificationStatus::Ditolak,
                'confidence_level' => ConfidenceLevel::Rendah,
                'is_simulation' => true,
                'verified_at' => now()->subHours(4),
                'verified_by_user_id' => $verifikator->id,
            ]
        );

        // 6. Riwayat Verifikasi (Verification Histories)
        VerificationHistory::updateOrCreate(
            [
                'verifiable_type' => Report::class,
                'verifiable_id' => $report1->id,
                'new_status' => VerificationStatus::Terverifikasi->value,
            ],
            [
                'previous_status' => VerificationStatus::MenungguVerifikasi->value,
                'user_id' => $verifikator->id,
                'reason' => 'Dokumentasi visual dan titik lokasi sesuai dengan rilis informasi terbuka dan laporan warga sekitar.',
                'is_simulation' => true,
            ]
        );

        VerificationHistory::updateOrCreate(
            [
                'verifiable_type' => Report::class,
                'verifiable_id' => $report2->id,
                'new_status' => VerificationStatus::Terverifikasi->value,
            ],
            [
                'previous_status' => VerificationStatus::MenungguVerifikasi->value,
                'user_id' => $verifikator->id,
                'reason' => 'Terkonfirmasi silang dengan laporan masyarakat lain pada klaster lokasi yang sama.',
                'is_simulation' => true,
            ]
        );

        VerificationHistory::updateOrCreate(
            [
                'verifiable_type' => Report::class,
                'verifiable_id' => $report4->id,
                'new_status' => VerificationStatus::Terverifikasi->value,
            ],
            [
                'previous_status' => VerificationStatus::MenungguVerifikasi->value,
                'user_id' => $verifikator->id,
                'reason' => 'Sesuai dengan pemberitaan media lokal dan laporan pengguna jalan.',
                'is_simulation' => true,
            ]
        );

        VerificationHistory::updateOrCreate(
            [
                'verifiable_type' => Report::class,
                'verifiable_id' => $report5->id,
                'new_status' => VerificationStatus::Ditolak->value,
            ],
            [
                'previous_status' => VerificationStatus::MenungguVerifikasi->value,
                'user_id' => $verifikator->id,
                'reason' => 'Setelah ditelusuri melalui sumber terbuka, informasi tersebut merupakan foto kejadian lama di wilayah berbeda.',
                'is_simulation' => true,
            ]
        );

        VerificationHistory::updateOrCreate(
            [
                'verifiable_type' => Incident::class,
                'verifiable_id' => $incident1->id,
                'new_status' => VerificationStatus::Terverifikasi->value,
            ],
            [
                'previous_status' => VerificationStatus::MenungguVerifikasi->value,
                'user_id' => $verifikator->id,
                'reason' => 'Terverifikasi melalui 2 sumber informasi terbuka dan 2 laporan masyarakat di lokasi.',
                'is_simulation' => true,
                'created_at' => now()->subMinutes(330),
                'updated_at' => now()->subMinutes(330),
            ]
        );

        VerificationHistory::updateOrCreate(
            [
                'verifiable_type' => Incident::class,
                'verifiable_id' => $incident2->id,
                'new_status' => VerificationStatus::Terverifikasi->value,
            ],
            [
                'previous_status' => VerificationStatus::MenungguVerifikasi->value,
                'user_id' => $verifikator->id,
                'reason' => 'Terverifikasi dari laporan pengguna jalur Puncak dan rilis informasi cuaca terbuka.',
                'is_simulation' => true,
                'created_at' => now()->subHours(12),
                'updated_at' => now()->subHours(12),
            ]
        );

        VerificationHistory::updateOrCreate(
            [
                'verifiable_type' => Incident::class,
                'verifiable_id' => $incident3->id,
                'new_status' => VerificationStatus::Terverifikasi->value,
            ],
            [
                'previous_status' => VerificationStatus::MenungguVerifikasi->value,
                'user_id' => $verifikator->id,
                'reason' => 'Dikonfirmasi dari laporan masyarakat setempat.',
                'is_simulation' => true,
                'created_at' => now()->subHours(24),
                'updated_at' => now()->subHours(24),
            ]
        );

        // 7. Catatan Tindak Lanjut Tim TanggapTerang (Follow Up Records)
        FollowUpRecord::updateOrCreate(
            [
                'incident_id' => $incident1->id,
                'action_title' => 'Pengelompokan Laporan & Pemetaan Titik Genangan',
            ],
            [
                'report_id' => $report1->id,
                'user_id' => $operator->id,
                'action_date' => now()->subHours(5),
                'status' => 'selesai',
                'notes' => 'Tim TanggapTerang menggabungkan laporan warga di RT 02 dan RT 03 ke dalam satu halaman informasi kejadian agar mudah diakses masyarakat.',
                'evidence_url' => null,
                'is_simulation' => true,
            ]
        );

        FollowUpRecord::updateOrCreate(
            [
                'incident_id' => $incident1->id,
                'action_title' => 'Pembaruan Informasi Akses Jalan Lingkungan',
            ],
            [
                'report_id' => $report2->id,
                'user_id' => $operator->id,
                'action_date' => now()->subHours(3),
                'status' => 'dalam_proses',
                'notes' => 'Menambahkan catatan jalan alternatif yang masih dapat dilalui warga pada ringkasan kejadian.',
                'evidence_url' => null,
                'is_simulation' => true,
            ]
        );

        FollowUpRecord::updateOrCreate(
            [
                'incident_id' => $incident2->id,
                'action_title' => 'Pemantauan Berkala Informasi Arus Lalu Lintas KM 82',
            ],
            [
                'report_id' => $report4->id,
                'user_id' => $operator->id,
                'action_date' => now()->subHours(10),
                'status' => 'dalam_proses',
                'notes' => 'Mencatat perkembangan pembersihan material longsor berdasarkan sumber berita terbuka.',
                'evidence_url' => null,
                'is_simulation' => true,
            ]
        );

        FollowUpRecord::updateOrCreate(
            [
                'incident_id' => $incident3->id,
                'action_title' => 'Penutupan Pemantauan Kejadian Pohon Tumbang',
            ],
            [
                'report_id' => null,
                'user_id' => $operator->id,
                'action_date' => now()->subHours(20),
                'status' => 'selesai',
                'notes' => 'Akses jalan telah kembali terbuka normal. Status tindak lanjut kejadian ditandai selesai.',
                'evidence_url' => null,
                'is_simulation' => true,
            ]
        );

        // 8. Respons Bantuan Masyarakat / Komunitas (Aid Responses)
        // Respons 1: Laporan Bantuan Sudah Diberikan -> Terverifikasi (Pemeriksaan) -> Penyerahan Dikonfirmasi (Operasional)
        AidResponse::updateOrCreate(
            ['reference_code' => 'RSP-SIM-7M2K8N4Q'],
            [
                'submission_type' => AidResponse::SUBMISSION_LAPORAN_PENYERAHAN,
                'incident_id' => $incident1->id,
                'report_id' => $report1->id,
                'need_category' => 'logistik_pangan',
                'organization_name' => 'Komunitas Dapur Warga Bojonggede (Simulasi)',
                'responder_type' => 'komunitas_warga',
                'contact_name' => 'Rina Kartika (Simulasi)',
                'contact_info' => '081299990001',
                'allow_public_name' => true,
                'aid_item_title' => '[SIMULASI] Paket Makanan Siap Saji & Air Mineral',
                'aid_description' => 'Penyaluran makanan hangat siap santap dan air minum kemasan bagi warga terdampak genangan di RT 02 dan RT 03.',
                'quantity_label' => '120 Paket Makanan & 10 Dus Air Mineral',
                'target_location' => 'Pos Singgah Warga RT 02/RW 04 Dusun 2, Bojonggede',
                'wilayah_administratif_id' => $wilayahId,
                'latitude' => -6.4916000,
                'longitude' => 106.7979000,
                'recorded_at' => now()->subHours(4),
                'delivered_at' => now()->subHours(5),
                'verification_status' => VerificationStatus::Terverifikasi,
                'verification_notes' => 'Informasi jenis bantuan, lokasi, dan bukti penyerahan telah diperiksa oleh Verifikator dan dinyatakan valid.',
                'verified_at' => now()->subHours(3),
                'verified_by_user_id' => $verifikator->id,
                'status' => AidResponseStatus::Terkonfirmasi,
                'evidence_url' => 'https://images.unsplash.com/photo-1593113598332-cd288d649433?auto=format&fit=crop&w=800&q=80',
                'confirmation_notes' => 'Penyerahan bantuan telah dikonfirmasi bersama perwakilan pengurus RT 02/RW 04 disertai dokumentasi serah terima.',
                'confirmed_at' => now()->subHours(2),
                'confirmed_by_user_id' => $operator->id,
                'is_simulation' => true,
            ]
        );

        // Respons 2: Tawaran / Rencana Bantuan -> Terverifikasi (Pemeriksaan) -> Dalam Penyaluran (Operasional)
        AidResponse::updateOrCreate(
            ['reference_code' => 'RSP-SIM-9V5P3W6X'],
            [
                'submission_type' => AidResponse::SUBMISSION_TAWARAN_RENCANA,
                'incident_id' => $incident1->id,
                'report_id' => $report1->id,
                'need_category' => 'air_bersih_sanitasi',
                'organization_name' => 'Donatur Warga Mandiri (Simulasi)',
                'responder_type' => 'inisiatif_individu',
                'contact_name' => 'Bambang Pamungkas (Simulasi)',
                'contact_info' => '081299990002',
                'allow_public_name' => false,
                'aid_item_title' => '[SIMULASI] Galon Air Bersih & Paket Kebersihan Keluarga',
                'aid_description' => 'Pengiriman air bersih isi ulang dan sabun antiseptik untuk warga yang sumurnya terdampak luapan sungai.',
                'quantity_label' => '35 Galon Air & 40 Paket Sanitasi',
                'target_location' => 'Mushola Al-Ikhlas RT 03/RW 04, Bojonggede',
                'wilayah_administratif_id' => $wilayahId,
                'latitude' => -6.4921000,
                'longitude' => 106.7982000,
                'recorded_at' => now()->subHours(2),
                'delivered_at' => null,
                'verification_status' => VerificationStatus::Terverifikasi,
                'verification_notes' => 'Tawaran penyaluran air bersih divalidasi sesuai kebutuhan mendesak warga RT 03.',
                'verified_at' => now()->subHours(2),
                'verified_by_user_id' => $verifikator->id,
                'status' => AidResponseStatus::DalamPenyaluran,
                'evidence_url' => null,
                'confirmation_notes' => 'Armada roda tiga sedang menuju titik kumpul RT 03 di luar jalur genangan.',
                'confirmed_at' => null,
                'confirmed_by_user_id' => null,
                'is_simulation' => true,
            ]
        );

        // Respons 3: Tawaran / Rencana Bantuan -> Menunggu Verifikasi (Pemeriksaan) -> Direncanakan (Operasional)
        AidResponse::updateOrCreate(
            ['reference_code' => 'RSP-SIM-2H8R4Y7L'],
            [
                'submission_type' => AidResponse::SUBMISSION_TAWARAN_RENCANA,
                'incident_id' => $incident2->id,
                'report_id' => $report4->id,
                'need_category' => 'evakuasi_penyelamatan',
                'organization_name' => 'Relawan Siaga Puncak (Simulasi)',
                'responder_type' => 'organisasi_relawan',
                'contact_name' => 'Dedi Kurniawan (Simulasi)',
                'contact_info' => '081388880003',
                'allow_public_name' => true,
                'aid_item_title' => '[SIMULASI] Peralatan Penerangan Darurat & Rambu Peringatan Portable',
                'aid_description' => 'Rencana pemasangan lampu sorot portable dan kerucut lalu lintas tambahan untuk membantu visibilitas malam hari di sekitar lokasi longsor.',
                'quantity_label' => '4 Unit Lampu Sorot & 15 Traffic Cone',
                'target_location' => 'Jalur Puncak KM 82, Cisarua',
                'wilayah_administratif_id' => $wilayahId,
                'latitude' => -6.6980000,
                'longitude' => 106.9459000,
                'recorded_at' => now()->subHours(6),
                'delivered_at' => null,
                'verification_status' => VerificationStatus::MenungguVerifikasi,
                'verification_notes' => null,
                'verified_at' => null,
                'verified_by_user_id' => null,
                'status' => AidResponseStatus::Direncanakan,
                'evidence_url' => null,
                'confirmation_notes' => null,
                'confirmed_at' => null,
                'confirmed_by_user_id' => null,
                'is_simulation' => true,
            ]
        );

        // Respons 4: Laporan Bantuan Sudah Diberikan -> Terverifikasi (Pemeriksaan) -> Penyerahan Dikonfirmasi (Operasional)
        AidResponse::updateOrCreate(
            ['reference_code' => 'RSP-SIM-5T9C3K2P'],
            [
                'submission_type' => AidResponse::SUBMISSION_LAPORAN_PENYERAHAN,
                'incident_id' => $incident3->id,
                'report_id' => null,
                'need_category' => 'pemulihan_akses',
                'organization_name' => 'Kelompok Gotong Royong Warga Pakansari (Simulasi)',
                'responder_type' => 'komunitas_warga',
                'contact_name' => 'Rudi Hartono (Simulasi)',
                'contact_info' => '081577770004',
                'allow_public_name' => true,
                'aid_item_title' => '[SIMULASI] Dukungan Gergaji Mesin & Pembersihan Ranting Jalan',
                'aid_description' => 'Partisipasi warga setempat membantu pemotongan dahan pohon tumbang agar bahu jalan kembali bersih.',
                'quantity_label' => '2 Unit Chainsaw & 8 Relawan Warga',
                'target_location' => 'Jl. Alternatif Pakansari, Cibinong',
                'wilayah_administratif_id' => $wilayahId,
                'latitude' => -6.4816000,
                'longitude' => 106.8328000,
                'recorded_at' => now()->subHours(22),
                'delivered_at' => now()->subHours(23),
                'verification_status' => VerificationStatus::Terverifikasi,
                'verification_notes' => 'Klaim pembersihan ranting pohon oleh warga telah diperiksa dan dikonfirmasi dengan dokumentasi lapangan.',
                'verified_at' => now()->subHours(21),
                'verified_by_user_id' => $verifikator->id,
                'status' => AidResponseStatus::Terkonfirmasi,
                'evidence_url' => null,
                'confirmation_notes' => 'Dikonfirmasi selesai bersama warga di lokasi; jalur sudah bersih dari ranting pohon.',
                'confirmed_at' => now()->subHours(20),
                'confirmed_by_user_id' => $operator->id,
                'is_simulation' => true,
            ]
        );
    }
}

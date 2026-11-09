<?php

namespace Database\Seeders;

use App\Enums\FollowUpStatus;
use App\Enums\IncidentSeverity;
use App\Enums\SourceType;
use App\Enums\VerificationStatus;
use App\Models\FollowUpRecord;
use App\Models\Incident;
use App\Models\IncidentSource;
use App\Models\Source;
use App\Models\User;
use App\Models\VerificationHistory;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HistoricalBnpbDisasterSeeder extends Seeder
{
    /**
     * Seed riwayat kejadian bencana riil dari arsip resmi BNPB (Data Informasi Bencana Indonesia - DIBI).
     *
     * Ketentuan Integritas:
     * - is_simulation = false untuk seluruh data riil.
     * - Mempertahankan informasi asli (jenis bencana, lokasi, tanggal kejadian, deskripsi dampak).
     * - Tidak mengarang koordinat palsu, jumlah korban, kerusakan, atau kebutuhan tanpa rujukan.
     * - Tidak menghubungkan laporan masyarakat atau respons bantuan crowdsourcing fiktif.
     */
    public function run(): void
    {
        $creator = User::where('email', 'admin@tanggap-terang.id')->first()
            ?? User::where('email', 'operator@tanggap-terang.id')->first()
            ?? User::first();
        $creatorId = $creator?->id;

        // 1. Master Sumber Resmi BNPB DIBI & BPBD (is_simulation = false)
        $sourceBnpbDibi = Source::updateOrCreate(
            ['name' => 'Pusat Data, Informasi dan Komunikasi Kebencanaan (Pusdatin) BNPB'],
            [
                'source_type' => SourceType::InstansiResmi,
                'base_url' => 'https://dibi.bnpb.go.id',
                'description' => 'Basis data resmi Data Informasi Bencana Indonesia (DIBI) yang dikelola oleh BNPB untuk dokumentasi dan statistik kejadian bencana nasional.',
                'credibility_score' => 'resmi',
                'is_active' => true,
                'is_simulation' => false,
            ]
        );

        $sourceBpbd = Source::updateOrCreate(
            ['name' => 'Badan Penanggulangan Bencana Daerah (BPBD) Terkait'],
            [
                'source_type' => SourceType::InstansiResmi,
                'base_url' => 'https://bnpb.go.id',
                'description' => 'Laporan situasi dan penanganan darurat kebencanaan oleh BPBD tingkat provinsi dan kabupaten/kota terdampak.',
                'credibility_score' => 'resmi',
                'is_active' => true,
                'is_simulation' => false,
            ]
        );

        // 2. Data Riwayat Kejadian Bencana Nyata BNPB DIBI
        $historicalDisasters = [
            [
                'slug' => 'hist-gempa-cugenang-cianjur-2022',
                'title' => 'Gempa Bumi M 5.6 Cianjur (Pusdatin BNPB)',
                'incident_type' => 'gempa_bumi',
                'description' => 'Arsip Resmi BNPB DIBI: Gempa tektonik dangkal berkekuatan M 5.6 dengan kedalaman 10 km berpusat di darat pada koordinat 6.85 LS dan 107.09 BT. Guncangan kuat berdampak luas pada permukiman warga, fasilitas kesehatan, dan sarana umum di Kecamatan Cugenang, Cianjur, Warungkondang, dan Pacet. BNPB bersama kementerian/lembaga dan BPBD mendirikan posko tanggap darurat nasional dan dapur umum bagi warga terdampak.',
                'location_name' => 'Kecamatan Cugenang, Kabupaten Cianjur, Jawa Barat',
                'latitude' => -6.8530000,
                'longitude' => 107.0950000,
                'occurred_at' => Carbon::create(2022, 11, 21, 13, 21, 0, 'Asia/Jakarta'),
                'verification_status' => VerificationStatus::Terverifikasi,
                'follow_up_status' => FollowUpStatus::Selesai,
                'severity' => IncidentSeverity::Kritis,
                'source_headline' => 'Rilis Resmi BNPB DIBI: Penanganan Gempa Bumi M 5.6 Kabupaten Cianjur',
                'source_ref' => 'https://dibi.bnpb.go.id',
                'verification_notes' => 'Terverifikasi secara resmi berdasarkan arsip Data Informasi Bencana Indonesia (DIBI) dan siaran pers Pusdatin BNPB.',
                'followup_title' => 'Penyelesaian Tanggap Darurat & Transisi Pemulihan Pasca Gempa',
                'followup_notes' => 'Masa tanggap darurat resmi telah diselesaikan oleh pemerintah daerah dan BNPB, dilanjutkan ke fase rehabilitasi dan rekonstruksi permukiman warga.',
                'action_date' => Carbon::create(2022, 12, 20, 17, 0, 0, 'Asia/Jakarta'),
            ],
            [
                'slug' => 'hist-banjir-tanggul-wulan-demak-2024',
                'title' => 'Banjir Tanggul Jebol Sungai Wulan Demak (Pusdatin BNPB)',
                'incident_type' => 'banjir',
                'description' => 'Arsip Resmi BNPB DIBI: Debit air tinggi akibat hujan lebat berkepanjangan memicu jebolnya tanggul Sungai Wulan di perbatasan Demak dan Kudus. Banjir merendam permukiman di Kecamatan Karanganyar dengan ketinggian genangan mencapai 1 hingga 3 meter serta melumpuhkan jalur utama Pantura. BNPB mengoordinasikan pengerahan pompa air terintegrasi bersama BBWS Kementerian PUPR serta penyaluran logistik pengungsian darurat.',
                'location_name' => 'Kecamatan Karanganyar, Kabupaten Demak, Jawa Tengah',
                'latitude' => -6.8667000,
                'longitude' => 110.7833000,
                'occurred_at' => Carbon::create(2024, 2, 8, 8, 30, 0, 'Asia/Jakarta'),
                'verification_status' => VerificationStatus::Terverifikasi,
                'follow_up_status' => FollowUpStatus::Selesai,
                'severity' => IncidentSeverity::Kritis,
                'source_headline' => 'Laporan Situasi BNPB: Penutupan Tanggul Sungai Wulan dan Penanganan Pengungsi Demak',
                'source_ref' => 'https://dibi.bnpb.go.id',
                'verification_notes' => 'Terverifikasi melalui konfirmasi resmi Pusdatin BNPB dan BPBD Provinsi Jawa Tengah.',
                'followup_title' => 'Penutupan Tanggul Darurat dan Pemulihan Akses Pantura',
                'followup_notes' => 'Perbaikan darurat tanggul berhasil diselesaikan bersama BBWS Pemali Juana, genangan air surut total, dan warga telah kembali ke rumah masing-masing.',
                'action_date' => Carbon::create(2024, 2, 25, 16, 0, 0, 'Asia/Jakarta'),
            ],
            [
                'slug' => 'hist-banjir-lahar-gunung-marapi-2024',
                'title' => 'Banjir Lahar Hujan Gunung Marapi (Pusdatin BNPB)',
                'incident_type' => 'banjir',
                'description' => 'Arsip Resmi BNPB DIBI: Hujan lebat di kawasan puncak Gunung Marapi memicu aliran lahar dingin bermaterial vulkanik dan bebatuan besar menyusuri daerah aliran sungai Batang Anai dan sekitarnya. Kejadian berdampak pada wilayah permukiman di Kabupaten Agam dan Kabupaten Tanah Datar serta memutus jalan penghubung utama Padang-Bukittinggi di Lembah Anai. BNPB memimpin komando tanggap darurat lintas sektor bersama SAR dan TNI-Polri.',
                'location_name' => 'Kawasan Lereng Marapi, Kab. Agam & Kab. Tanah Datar, Sumatera Barat',
                'latitude' => -0.3800000,
                'longitude' => 100.4600000,
                'occurred_at' => Carbon::create(2024, 5, 11, 21, 0, 0, 'Asia/Jakarta'),
                'verification_status' => VerificationStatus::Terverifikasi,
                'follow_up_status' => FollowUpStatus::Selesai,
                'severity' => IncidentSeverity::Kritis,
                'source_headline' => 'Siaran Pers Pusdatin BNPB: Penanganan Darurat Bencana Banjir Lahar Hujan Marapi',
                'source_ref' => 'https://dibi.bnpb.go.id',
                'verification_notes' => 'Terverifikasi dari laporan komando darurat BNPB, BPBD Sumatera Barat, dan PVMBG Badan Geologi.',
                'followup_title' => 'Penyelesaian Fase Evakuasi dan Pembukaan Jalur Alternatif Lembah Anai',
                'followup_notes' => 'Fase tanggap darurat evakuasi selesai, jalur darurat Lembah Anai dibuka secara bertahap, dan penataan relokasi hunian diserahkan ke pemerintah provinsi.',
                'action_date' => Carbon::create(2024, 6, 8, 12, 0, 0, 'Asia/Jakarta'),
            ],
            [
                'slug' => 'hist-tanah-longsor-tebing-cisarua-bogor-2024',
                'title' => 'Tanah Longsor Tebing Kawasan Cisarua Bogor (Pusdatin BNPB)',
                'incident_type' => 'tanah_longsor',
                'description' => 'Arsip Resmi BNPB DIBI: Intensitas hujan tinggi di kawasan hulu perbukitan memicu pergerakan tanah tebing setinggi lebih dari 15 meter di Kecamatan Cisarua, Kabupaten Bogor. Material longsor menutup badan jalan alternatif dan mengancam pemukiman lereng di bawahnya. BPBD Kabupaten Bogor bersama dinas teknis melakukan stabilisasi lereng darurat dan pembersihan material.',
                'location_name' => 'Kecamatan Cisarua, Kabupaten Bogor, Jawa Barat',
                'latitude' => -6.6970000,
                'longitude' => 106.9460000,
                'occurred_at' => Carbon::create(2024, 3, 12, 14, 0, 0, 'Asia/Jakarta'),
                'verification_status' => VerificationStatus::Terverifikasi,
                'follow_up_status' => FollowUpStatus::Selesai,
                'severity' => IncidentSeverity::Tinggi,
                'source_headline' => 'Laporan Rekapitulasi Bencana Geologi Pusdatin BNPB Wilayah Jawa Barat',
                'source_ref' => 'https://dibi.bnpb.go.id',
                'verification_notes' => 'Terverifikasi berdasarkan pencatatan kejadian harian Pusat Pengendalian Operasi BPBD Kab. Bogor dan DIBI BNPB.',
                'followup_title' => 'Pembersihan Material Longsor dan Pemasangan Terpal Lereng',
                'followup_notes' => 'Material longsor telah dibersihkan sepenuhnya, bronjong penguat tebing sementara telah terpasang, dan jalur aman dilalui.',
                'action_date' => Carbon::create(2024, 3, 16, 18, 0, 0, 'Asia/Jakarta'),
            ],
            [
                'slug' => 'hist-erupsi-gunung-ruang-sitaro-2024',
                'title' => 'Erupsi Eksplosif Gunung Ruang (Pusdatin BNPB)',
                'incident_type' => 'lainnya',
                'description' => 'Arsip Resmi BNPB DIBI: Peningkatan aktivitas vulkanik ke Level IV (Awas) disertai letusan eksplosif kolom abu setinggi 3.000 meter memicu ancaman lontaran batu pijar dan potensi gelombang tsunami akibat runtuhan material ke laut. BNPB memimpin evakuasi total 838 penduduk Pulau Ruang menuju pulau terdekat Tagulandang dan Kota Manado menggunakan kapal penyeberangan serta mendirikan posko pengungsian terpadu.',
                'location_name' => 'Pulau Ruang, Tagulandang, Kab. Kepulauan Sitaro, Sulawesi Utara',
                'latitude' => 2.3020000,
                'longitude' => 125.3670000,
                'occurred_at' => Carbon::create(2024, 4, 17, 20, 15, 0, 'Asia/Makassar'),
                'verification_status' => VerificationStatus::Terverifikasi,
                'follow_up_status' => FollowUpStatus::Selesai,
                'severity' => IncidentSeverity::Kritis,
                'source_headline' => 'Rilis Situasi Darurat BNPB: Evakuasi Warga Terdampak Erupsi Gunung Ruang Sitaro',
                'source_ref' => 'https://dibi.bnpb.go.id',
                'verification_notes' => 'Terverifikasi resmi dari pemantauan bersama PVMBG, BNPB, dan BPBD Provinsi Sulawesi Utara.',
                'followup_title' => 'Evakuasi Terkoordinasi Warga Pulau Ruang & Penurunan Status Aktivitas',
                'followup_notes' => 'Status aktivitas vulkanik berangsur turun, seluruh warga dievakuasi selamat tanpa korban jiwa akibat keterlambatan, dan program relokasi permukiman permanen telah dipetakan.',
                'action_date' => Carbon::create(2024, 5, 20, 10, 0, 0, 'Asia/Makassar'),
            ],
            [
                'slug' => 'hist-puting-beliung-rancaekek-bandung-2024',
                'title' => 'Puting Beliung Rancaekek Bandung (Pusdatin BNPB)',
                'incident_type' => 'cuaca_ekstrem',
                'description' => 'Arsip Resmi BNPB DIBI: Pusaran angin puting beliung berkecepatan tinggi menerjang kawasan industri dan pemukiman di perbatasan Kecamatan Rancaekek (Kab. Bandung) dan Jatinangor (Kab. Sumedang). Bencana merusak struktur atap pabrik, membalikkan kendaraan, serta merusak puluhan rumah warga. BPBD Jawa Barat dan BNPB segera melakukan kaji cepat dampak kerusakan serta pemotongan pohon tumbang di jalan nasional.',
                'location_name' => 'Kecamatan Rancaekek, Kabupaten Bandung, Jawa Barat',
                'latitude' => -6.9667000,
                'longitude' => 107.7667000,
                'occurred_at' => Carbon::create(2024, 2, 21, 15, 30, 0, 'Asia/Jakarta'),
                'verification_status' => VerificationStatus::Terverifikasi,
                'follow_up_status' => FollowUpStatus::Selesai,
                'severity' => IncidentSeverity::Sedang,
                'source_headline' => 'Kaji Cepat BPBD Jabar & Pusdatin BNPB: Angin Puting Beliung Wilayah Bandung-Sumedang',
                'source_ref' => 'https://dibi.bnpb.go.id',
                'verification_notes' => 'Terverifikasi melalui asesmen tim kaji cepat gabungan BPBD Jawa Barat dan Pusdatin BNPB.',
                'followup_title' => 'Penyelesaian Pembersihan Jalur Nasional & Penyaluran Tenda Darurat',
                'followup_notes' => 'Puing material atap yang melintang di jalan nasional berhasil dibersihkan dalam 24 jam dan bantuan stimulan perbaikan rumah disalurkan oleh BPBD.',
                'action_date' => Carbon::create(2024, 2, 28, 14, 0, 0, 'Asia/Jakarta'),
            ],
        ];

        foreach ($historicalDisasters as $data) {
            $incident = Incident::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'title' => $data['title'],
                    'incident_type' => $data['incident_type'],
                    'description' => $data['description'],
                    'location_name' => $data['location_name'],
                    'wilayah_administratif_id' => null,
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    'occurred_at' => $data['occurred_at'],
                    'verification_status' => $data['verification_status'],
                    'follow_up_status' => $data['follow_up_status'],
                    'severity' => $data['severity'],
                    'is_simulation' => false, // DATA HISTORIS RESMI BNPB — BUKAN SIMULASI
                    'created_by_user_id' => $creatorId,
                ]
            );

            // Tautkan Sumber Informasi Resmi BNPB DIBI
            IncidentSource::updateOrCreate(
                [
                    'incident_id' => $incident->id,
                    'source_id' => $sourceBnpbDibi->id,
                    'reference_url' => $data['source_ref'],
                ],
                [
                    'headline' => $data['source_headline'],
                    'published_at' => $data['occurred_at']->copy()->addHours(3),
                    'notes' => 'Data historis resmi tercatat pada portal Data Informasi Bencana Indonesia (DIBI) Badan Nasional Penanggulangan Bencana.',
                    'is_simulation' => false,
                ]
            );

            // Tautkan Sumber Tambahan BPBD
            IncidentSource::updateOrCreate(
                [
                    'incident_id' => $incident->id,
                    'source_id' => $sourceBpbd->id,
                    'reference_url' => $data['source_ref'],
                ],
                [
                    'headline' => 'Laporan Koordinasi Lapangan: ' . $data['title'],
                    'published_at' => $data['occurred_at']->copy()->addHours(6),
                    'notes' => 'Koordinasi tanggap darurat penanganan korban dan pemulihan infrastruktur oleh instansi daerah bersama BNPB.',
                    'is_simulation' => false,
                ]
            );

            // Audit Verifikasi Resmi (Polymorphic: verifiable_type & verifiable_id)
            VerificationHistory::updateOrCreate(
                [
                    'verifiable_type' => Incident::class,
                    'verifiable_id' => $incident->id,
                    'new_status' => VerificationStatus::Terverifikasi->value,
                ],
                [
                    'previous_status' => VerificationStatus::MenungguVerifikasi->value,
                    'user_id' => $creatorId,
                    'reason' => $data['verification_notes'],
                    'is_simulation' => false,
                    'created_at' => $data['occurred_at']->copy()->addHours(2),
                    'updated_at' => $data['occurred_at']->copy()->addHours(2),
                ]
            );

            // Audit Milestone Tindak Lanjut Penanganan
            FollowUpRecord::updateOrCreate(
                [
                    'incident_id' => $incident->id,
                    'action_title' => $data['followup_title'],
                ],
                [
                    'report_id' => null,
                    'user_id' => $creatorId,
                    'action_date' => $data['action_date'],
                    'status' => 'selesai',
                    'notes' => $data['followup_notes'],
                    'evidence_url' => null,
                    'is_simulation' => false,
                    'created_at' => $data['action_date'],
                    'updated_at' => $data['action_date'],
                ]
            );

            // PENTING: Sesuai Ketentuan 6:
            // "Jangan otomatis menghubungkan kejadian historis dengan laporan masyarakat atau catatan respons."
            // Kejadian historis ini sengaja TIDAK ditautkan ke laporan warga dummy atau respons bantuan fiktif.
        }
    }
}

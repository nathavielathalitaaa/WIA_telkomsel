# TanggapTerang

> **Tagline:** Menyinari Penanganan Bencana dengan Transparansi Nyata

TanggapTerang adalah platform observabilitas dan transparansi respons bencana yang memungkinkan masyarakat, pemerintah, dan pemangku kepentingan memantau perkembangan penanganan kebutuhan, pihak yang merespons, alokasi logistik, serta kebutuhan warga yang belum terpenuhi secara berbasis data hierarkis dan spasial.

Platform ini bukan aplikasi penyalur donasi komersial maupun kanal pelaporan darurat instan (bukan pengganti 112/SAR), melainkan instrumen pemantauan, evaluasi, dan audit publik terhadap siklus penanganan bencana.

---

## Daftar Isi
- [Latar Belakang & Permasalahan](#latar-belakang--permasalahan)
- [Tujuan & Alur Kerja Utama](#tujuan--alur-kerja-utama)
- [Status Pengembangan Fitur](#status-pengembangan-fitur)
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Struktur Database & Wilayah Administratif](#struktur-database--wilayah-administratif)
- [Sumber Data, Atribusi, & Provenance Data Wilayah](#sumber-data-atribusi--provenance-data-wilayah)
- [Struktur Direktori Relevan](#struktur-direktori-relevan)
- [Persyaratan & Petunjuk Instalasi](#persyaratan--petunjuk-instalasi)
- [Konfigurasi Database & Menjalankan Aplikasi](#konfigurasi-database--menjalankan-aplikasi)
- [Development Progress Log](#development-progress-log)

---

## Latar Belakang & Permasalahan
Pada masa tanggap darurat bencana di Indonesia, seringkali terjadi kesenjangan informasi yang menghambat efisiensi pemulihan warga:
1. **Asimetri Informasi Respons:** Masyarakat dan relawan sulit melacak apakah kebutuhan yang dilaporkan warga sudah ditindaklanjuti, masih dalam perjalanan, atau belum ada pihak yang bersedia merespons.
2. **Penumpukan Bantuan (Mismatch Logistik):** Bantuan kerap menumpuk di posko pengungsian utama yang mudah diakses transportasi, sementara kantong-kantong pengungsian terisolasi di tingkat dusun/desa luput dari perhatian.
3. **Ketiadaan Data Spasial Administratif yang Valid:** Koordinasi logistik sering terkendala ambiguitas batas desa/kelurahan, perbedaan penamaan wilayah lokal, dan ketidakpastian kepastian batas (definitif vs indikatif).
4. **Kebutuhan Jejak Audit Publik:** Diperlukan sistem observasi terbuka yang mampu memverifikasi bantuan mulai dari pencatatan komitmen, pengiriman fisik, hingga konfirmasi penerimaan langsung oleh warga.

---

## Tujuan & Alur Kerja Utama
TanggapTerang dibangun untuk menyajikan data penanganan bencana yang dapat dipertanggungjawabkan secara terbuka dan dapat ditelusuri riwayatnya (*auditable*).

```
[Laporan Kondisi Warga] ──> [Verifikasi & Klasterisasi] ──> [Tiket Kebutuhan]
                                                                  │
                                                                  ▼
[Konfirmasi Penerimaan] <── [Pengiriman Bantuan Fisik] <── [Respons Bantuan]
 (Warga / Koordinator)        (Titik Posko Bantuan)          (Organisasi/Donatur)
```

### Alur Kerja Utama:
1. **Pelaporan Kondisi Warga (`LaporanWarga`):** Pengumpulan data kondisi dan kebutuhan mendesak langsung dari warga atau relawan di lapangan, terikat dengan kode wilayah administratif resmi.
2. **Verifikasi & Konsolidasi (`TiketKebutuhan`):** Laporan warga yang sejenis di satu zona diverifikasi dan digabungkan menjadi tiket kebutuhan terstruktur untuk menghindari tumpang tindih.
3. **Penentuan Titik Distribusi (`TitikBantuan`):** Pemetaan lokasi fisik posko bantuan atau titik serah logistik yang terintegrasi koordinat lintang/bujur.
4. **Komitmen & Penyaluran (`ResponsBantuan`):** Pencatatan komitmen bantuan dari relawan/lembaga (*dijanjikan*, *dikirim*, hingga *diterima*).
5. **Konfirmasi Penerimaan (`KonfirmasiPenerimaan`):** Validasi lapangan oleh warga atau perwakilan posko bahwa logistik telah benar-benar tiba.
6. **Riwayat Jejak Status (`RiwayatStatusTiket`):** Setiap perubahan status tiket terekam secara otomatis sebagai riwayat publik yang tidak dapat dimanipulasi.

---

## Status Pengembangan Fitur

| Modul / Fitur | Status | Keterangan |
|---|:---:|---|
| **Fondasi Skema Database & Relasi Eloquent** | Selesai | 8 entitas operasional bencana dengan foreign key cascades dan unique constraints |
| **Integrasi Master Data Wilayah Administratif 4 Tingkat** | Selesai | 91.363 wilayah nasional resmi (Provinsi, Kabupaten, Kecamatan, Desa/Kelurahan) |
| **Pipeline CLI Import Wilayah Resumable** | Selesai | Arsitektur *Fetch -> Stage -> Validate -> Promote* via `php artisan wilayah:import` |
| **Normalisasi Kategori Status Batas Wilayah** | Selesai | Pemetaan akurat enum: `definitif`, `indikatif`, `hasil_kesepakatan`, `tidak_terdefinisi` |
| **Automated Test Suite (Feature & Unit)** | Selesai | 10 test cases, 54 assertions menguji idempotensi, integrasi staging, dan rollback |
| **Data Seeder Simulasi Bencana (Tirta Jaya)** | Selesai | Data simulasi idempotent untuk pengujian skenario bencana lokal |
| **Controller & REST API Respons Bencana** | Direncanakan | Endpoint pengajuan laporan, penanganan tiket, dan pemantauan respons |
| **Antarmuka Publik & Visualisasi Peta (Leaflet.js)** | Direncanakan | Dashboard peta interaktif berbasis Leaflet dan OpenStreetMap |
| **Autentikasi & Verifikasi Responden/Relawan** | Direncanakan | Sistem verifikasi identitas lembaga kemanusiaan dan relawan lapangan |

> **Catatan Kejujuran Implementasi:** Repositori saat ini telah menyelesaikan fondasi arsitektur data, model, migrasi, pipeline data wilayah administratif nasional, serta pengujian automated test. Antarmuka pengguna (UI), rute web, dan controller operasional saat ini berada pada tahap perencanaan teknis selanjutnya.

---

## Teknologi yang Digunakan
- **Backend Framework:** Laravel 12 (PHP 8.2+)
- **Database Engine:** MySQL 8.4 (InnoDB Storage Engine dengan transaksi ACID)
- **Testing Framework:** PHPUnit 11 (In-Memory SQLite untuk eksekusi testing cepat dan terisolasi)
- **Frontend Assets & Styling (Rencana Integrasi):** Blade Templating, Tailwind CSS v4, JavaScript (ES6+), Vite
- **Peta Spasial:** Leaflet.js & OpenStreetMap tile layer
- **HTTP Client:** Guzzle / Laravel Http Client dengan mekanisme Exponential Backoff

---

## Struktur Database & Wilayah Administratif

### 1. Entitas Operasional Bencana
- `kejadian_bencana`: Data induk peristiwa bencana (jenis, waktu kejadian, status tanggap darurat).
- `laporan_warga`: Laporan kondisi riil yang dilaporkan warga atau relawan lapangan.
- `tiket_kebutuhan`: Kumpulan kebutuhan terverifikasi yang memerlukan respons logistik.
- `tiket_laporan`: Tabel pivot menghubungkan tiket dengan satu atau beberapa laporan warga.
- `titik_bantuan`: Titik fisik distribusi logistik / posko bantuan.
- `responden`: Profil organisasi, instansi, atau relawan yang memberikan bantuan.
- `respons_bantuan`: Catatan komitmen dan realisasi penyaluran bantuan ke titik/tiket tertentu.
- `riwayat_status_tiket`: Audit log kronologis perubahan status tiket kebutuhan.
- `konfirmasi_penerimaan`: Bukti konfirmasi penerimaan logistik di lapangan.

### 2. Master Data Wilayah Administratif (`wilayah_administratif`)
Basis data telah memuat **91.363 entitas wilayah resmi Indonesia** secara berjenjang (*self-referencing parent-child*):

| Tingkat Administrasi | Jumlah Entitas | Status Parent ID | Contoh Kodifikasi |
|---|---:|---|---|
| **Provinsi** | 38 | `NULL` (Root) | `11` (Aceh), `32` (Jawa Barat) |
| **Kabupaten / Kota** | 514 | Mengarah ke ID Provinsi | `1101` (Aceh Selatan), `3201` (Bogor) |
| **Kecamatan** | 7.282 | Mengarah ke ID Kabupaten | `110101` (Bakongan), `320101` (Cibinong) |
| **Desa / Kelurahan** | 83.529 | Mengarah ke ID Kecamatan | `1101012001` (Keude Bakongan) |
| **Total Record** | **91.363** | 100% Valid (0 Orphan) | Hirarki lengkap nasional |

### 3. Distribusi Status Batas Wilayah
Tabel `wilayah_administratif` menyimpan status keabsahan batas kartometrik pada tingkat desa/kelurahan:
- **`definitif` (6.349 desa):** Batas wilayah yang telah ditetapkan melalui Peraturan Menteri Dalam Negeri atau Peraturan Kepala Daerah definitif.
- **`indikatif` (57.338 desa):** Garis batas indikatif hasil pemetaan sementara yang masih memerlukan verifikasi penetapan batas.
- **`hasil_kesepakatan` (19.842 desa):** Batas wilayah yang disepakati antar desa/kelurahan bertetangga melalui musyawarah resmi.
- **`NULL` (7.834 wilayah):** Entitas tingkat provinsi, kabupaten, dan kecamatan yang tidak memiliki atribusi status batas dari API sumber.

---

## Sumber Data, Atribusi, & Provenance Data Wilayah

- **Sumber Data:** Data wilayah administratif bersumber dari *Wilayah ID API* (`https://wilayah-id-api.dhanypedia.com/api/v1`), yang merujuk pada kodifikasi resmi Kementerian Dalam Negeri Republik Indonesia (Permendagri).
- **Provenance & Integritas:**
  1. Pengambilan data dijalankan menggunakan session ID `e08b26de-6e92-474b-b9f5-069f929b18d5`.
  2. Data mentah ditampung terlebih dahulu di tabel `wilayah_staging` sebanyak 91.363 baris dan diverifikasi melalui 7.835 request fetch bertingkat pada tabel `wilayah_fetch_progress` (100% selesai tanpa kegagalan).
  3. Validasi staging memastikan tidak ada kode kosong, 0 duplikat, dan 0 relasi parent orphan.
  4. Pemindahan data ke tabel utama menggunakan transaksi atomic database (`DB::beginTransaction()` & `DB::commit()`) dengan mekanisme batch upsert berukuran 500 baris.
  5. Seluruh riwayat eksekusi tercatat dalam tabel audit `wilayah_import_logs`.
- **Keterbatasan Data:**
  - Status batas hanya tersedia untuk level desa/kelurahan; tingkat kecamatan ke atas bernilai `NULL`.
  - Kodifikasi wilayah mencerminkan snapshot saat import dilakukan (Oktober 2026); pemekaran wilayah baru di masa mendatang memerlukan sinkronisasi ulang.

---

## Struktur Direktori Relevan
```
tanggap-terang/
├── app/
│   ├── Console/Commands/
│   │   └── ImportWilayahAdministratif.php   # Pipeline CLI Import: Fetch -> Stage -> Validate -> Promote
│   └── Models/
│       ├── KejadianBencana.php
│       ├── KonfirmasiPenerimaan.php
│       ├── LaporanWarga.php
│       ├── Responden.php
│       ├── ResponsBantuan.php
│       ├── RiwayatStatusTiket.php
│       ├── TiketKebutuhan.php
│       ├── TitikBantuan.php
│       ├── WilayahAdministratif.php         # Model wilayah dengan self-referencing parent & children
│       ├── WilayahFetchProgress.php         # Tracking progres fetch per parent kode
│       ├── WilayahImportLog.php             # Log audit eksekusi promote
│       └── WilayahStaging.php               # Penyimpanan sementara raw API
├── database/
│   ├── migrations/                          # Migrasi tabel operasional, wilayah, staging, dan enum
│   └── seeders/                             # Seeder idempotent simulasi skenario bencana
├── tests/
│   ├── Feature/
│   │   └── WilayahImportStagingTest.php     # 10 automated test cases (54 assertions)
│   └── TestCase.php
├── resources/
│   ├── css/app.css                          # Tailwind CSS v4 entrypoint
│   └── js/app.js                            # Inisialisasi frontend Leaflet
└── README.md                                # Dokumentasi proyek & Development Progress Log
```

---

## Persyaratan & Petunjuk Instalasi

### Prasyarat Sistem:
- **PHP** >= 8.2 (ekstensi: `pdo`, `pdo_mysql`, `curl`, `mbstring`, `openssl`)
- **Composer** >= 2.x
- **Node.js** >= 18.x & **npm**
- **MySQL Server** >= 8.0 (disarankan MySQL 8.4)

### Langkah Instalasi:
1. **Clone repository:**
   ```bash
   git clone https://github.com/nathavielathalitaaa/WIA_telkomsel.git
   cd WIA_telkomsel/tanggap-terang
   ```

2. **Install dependensi PHP & Node.js:**
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment:**
   Salin `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

---

## Konfigurasi Database & Menjalankan Aplikasi

1. **Konfigurasi Database MySQL di `.env`:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=tanggap_terang
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

2. **Jalankan Migrasi Database:**
   ```bash
   php artisan migrate
   ```

3. **Sinkronisasi Data Wilayah Administratif (Bila Diperlukan):**
   *Tabel `wilayah_administratif` pada database produksi/lokal dapat diisi langsung melalui pipeline import:*
   ```bash
   # Jalankan simulasi promote tanpa mengubah database (Dry-Run):
   php artisan wilayah:import --promote --dry

   # Jalankan promote permanen dari data staging:
   php artisan wilayah:import --promote

   # Mengambil data baru dari API secara bertahap (Resumable):
   php artisan wilayah:import --fetch-only --resume
   ```

4. **Jalankan Seeder Simulasi Bencana (Opsional):**
   ```bash
   php artisan db:seed
   ```

5. **Jalankan Pengujian Automated Tests:**
   ```bash
   php artisan test
   ```

6. **Menjalankan Server Pengembangan:**
   ```bash
   npm run dev &
   php artisan serve
   ```

---

## Development Progress Log

*Keterangan: Progress Log merupakan ringkasan aktivitas pengembangan berkelanjutan, sedangkan Git commit menyediakan bukti perubahan kode.*

| Tanggal | Progres Pengerjaan | Hasil | Bukti Commit |
|---|---|---|---|
| 2026-10-30 | Inisialisasi awal proyek TanggapTerang dengan framework Laravel 12 | Kerangka dasar aplikasi Laravel 12 berhasil di-setup dan siap dikembangkan | `f7026c8` (Initial Laravel 12 project) |
| 2026-10-30 | Instalasi dan konfigurasi Tailwind CSS v4 | Engine styling Tailwind CSS terintegrasi untuk kebutuhan UI visual respons bencana | `9a2cf24` (Install and configure Tailwind CSS) |
| 2026-10-30 | Instalasi dan konfigurasi Leaflet.js | Modul peta interaktif terpasang untuk visualisasi spasial titik bencana dan bantuan | `7fe5e15` (Install and configure Leaflet) |
| 2026-10-30 | Refaktor model dan seeder respons bantuan | Implementasi seeder idempotent untuk pengujian alur donasi dan relasi bantuan | `6dc14d4` (Perubahan progres) |
| 2026-10-30 | Finalisasi fondasi database relasional dan seeder skenario bencana | 8 tabel operasional bencana, constraint unique, foreign key, dan seeder tervalidasi | `503d589` (Finalize database structure and update project documentation) |
| 2026-10-31 | Perancangan arsitektur import wilayah 4 tahap (*Fetch -> Stage -> Validate -> Promote*) | Migrasi tabel `wilayah_staging`, `wilayah_fetch_progress`, dan command CLI `wilayah:import` | Belum di-commit |
| 2026-10-31 | Eksekusi fetch data wilayah nasional dari API Wilayah Indonesia ke staging | 91.363 record staging terkumpul (7.835 progress berstatus done, 0 gagal) | Belum di-commit |
| 2026-10-31 | Perbaikan kategori status batas dan ekspansi enum `wilayah_administratif` | Migrasi penambahan `hasil_kesepakatan`, normalisasi cerdas, dan automated test lulus (10 passed, 54 assertions) | Belum di-commit |
| 2026-10-31 | Eksekusi promote permanen 91.363 master data wilayah administratif ke tabel utama | 38 provinsi, 514 kabupaten, 7.282 kecamatan, 83.529 desa terhubung hierarkis (0 orphan, 100% unik) | Belum di-commit |

---
*Dokumen diperbarui secara berkala sebagai bagian dari standar transparansi pengembangan TanggapTerang.*

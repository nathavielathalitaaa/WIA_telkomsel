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
- [Dokumentasi REST API Wilayah Administratif](#dokumentasi-rest-api-wilayah-administratif)
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
| **REST API Wilayah Administratif 4 Tingkat** | Selesai | REST API read-only hierarkis (Provinsi, Kabupaten, Kecamatan, Desa) dengan pagination dan validasi ketat |
| **Automated Test Suite (Feature & Unit)** | Selesai | 20 test cases, 183 assertions menguji import staging, enum, integrasi API, dan hierarki parent |
| **Data Seeder Simulasi Bencana (Tirta Jaya)** | Selesai | Data simulasi idempotent untuk pengujian skenario bencana lokal |
| **Controller & REST API Respons Bencana** | Direncanakan | Endpoint pengajuan laporan, penanganan tiket, dan pemantauan respons |
| **Antarmuka Publik & Visualisasi Peta (Leaflet.js)** | Direncanakan | Dashboard peta interaktif berbasis Leaflet dan OpenStreetMap |
| **Autentikasi & Verifikasi Responden/Relawan** | Direncanakan | Sistem verifikasi identitas lembaga kemanusiaan dan relawan lapangan |

> **Catatan Kejujuran Implementasi:** Repositori saat ini telah menyelesaikan fondasi arsitektur data, model, migrasi, pipeline import data wilayah nasional, REST API wilayah administratif 4 tingkat, serta rangkaian 20 automated tests. Antarmuka pengguna (UI), rute web, dan controller operasional saat ini berada pada tahap perencanaan teknis selanjutnya.

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

## Dokumentasi REST API Wilayah Administratif

### 1. Tujuan API
REST API Wilayah Administratif dirancang sebagai layanan *read-only* untuk mengakses master data 91.363 wilayah administratif Indonesia secara hierarkis bertingkat:

$$\text{Provinsi} \longrightarrow \text{Kabupaten/Kota} \longrightarrow \text{Kecamatan} \longrightarrow \text{Desa/Kelurahan}$$

API ini menjadi fondasi operasional sistem TanggapTerang untuk:
- Pemilihan lokasi presisi saat warga atau relawan mengajukan laporan kondisi bencana (`LaporanWarga`).
- Penentuan cakupan wilayah pada tiket konsolidasi kebutuhan logistik (`TiketKebutuhan`).
- Pemetaan spasial titik posko distribusi logistik (`TitikBantuan`).
- Agregasi, klasterisasi, dan visualisasi pemantauan bencana berdasarkan batas administratif resmi.

### 2. Daftar Endpoint Utama
Seluruh rute API menggunakan prefix standar `/api/wilayah`:

| No | Method | Endpoint | Deskripsi | Pagination |
|:---:|:---:|---|---|:---:|
| 1 | `GET` | `/api/wilayah/provinsi` | Mengambil seluruh 38 provinsi nasional | Tidak (Data terbatas) |
| 2 | `GET` | `/api/wilayah/{id}/kabupaten` | Mengambil kabupaten/kota dalam provinsi `{id}` | Tidak |
| 3 | `GET` | `/api/wilayah/{id}/kecamatan` | Mengambil kecamatan dalam kabupaten/kota `{id}` | Tidak |
| 4 | `GET` | `/api/wilayah/{id}/desa` | Mengambil desa/kelurahan dalam kecamatan `{id}` | **Ya** (Default 50, Max 100) |

> **PENTING Mengenai Identifier `{id}`:**
> Parameter `{id}` pada path rute adalah **Primary Key basis data** (`id`, integer auto-increment pada tabel `wilayah_administratif`), **bukan** kode wilayah Kemendagri (`kode_wilayah`). Hal ini menjamin konsistensi integritas relasi foreign key pada arsitektur database.

### 3. Parameter Rute & Query

#### Path Parameter `{id}`
- Digunakan pada endpoint: `/api/wilayah/{id}/kabupaten`, `/api/wilayah/{id}/kecamatan`, dan `/api/wilayah/{id}/desa`.
- Tipe data: Integer positif (Primary Key).
- Validasi: Harus merujuk pada entitas wilayah yang ada dan memiliki `tingkat` parent yang sesuai dengan endpoint terkait.

#### Query Parameter (Khusus Endpoint Desa `/api/wilayah/{id}/desa`)
| Parameter | Tipe | Wajib | Default | Batasan / Validasi | Keterangan |
|---|:---:|:---:|:---:|---|---|
| `page` | Integer | Tidak | `1` | `min:1` | Nomor halaman data |
| `per_page` | Integer | Tidak | `50` | `min:1`, `max:100` | Jumlah data per halaman |

### 4. Contoh Request & Response JSON

#### A. Mengambil Daftar Provinsi
- **Request:**
  ```bash
  curl -s -X GET "http://localhost:8000/api/wilayah/provinsi" \
       -H "Accept: application/json"
  ```
- **Response Sukses (HTTP 200 OK):**
  ```json
  {
      "success": true,
      "message": "Data wilayah berhasil diambil.",
      "data": [
          {
              "id": 100378,
              "kode_wilayah": "11",
              "kode": "11",
              "nama": "Aceh",
              "tingkat": "provinsi",
              "status_batas": null
          },
          {
              "id": 100389,
              "kode_wilayah": "32",
              "kode": "32",
              "nama": "Jawa Barat",
              "tingkat": "provinsi",
              "status_batas": null
          }
      ]
  }
  ```

#### B. Mengambil Kabupaten/Kota Berdasarkan ID Provinsi
- **Request (misal: Provinsi Jawa Barat ID `100389`):**
  ```bash
  curl -s -X GET "http://localhost:8000/api/wilayah/100389/kabupaten" \
       -H "Accept: application/json"
  ```
- **Response Sukses (HTTP 200 OK):**
  ```json
  {
      "success": true,
      "message": "Data wilayah berhasil diambil.",
      "data": [
          {
              "id": 100416,
              "kode_wilayah": "3201",
              "kode": "3201",
              "nama": "Kabupaten Bogor",
              "tingkat": "kabupaten",
              "status_batas": null
          }
      ]
  }
  ```

#### C. Mengambil Kecamatan Berdasarkan ID Kabupaten
- **Request (misal: Kabupaten Bogor ID `100416`):**
  ```bash
  curl -s -X GET "http://localhost:8000/api/wilayah/100416/kecamatan" \
       -H "Accept: application/json"
  ```
- **Response Sukses (HTTP 200 OK):**
  ```json
  {
      "success": true,
      "message": "Data wilayah berhasil diambil.",
      "data": [
          {
              "id": 100930,
              "kode_wilayah": "320101",
              "kode": "320101",
              "nama": "Cibinong",
              "tingkat": "kecamatan",
              "status_batas": null
          }
      ]
  }
  ```

#### D. Mengambil Desa/Kelurahan Berdasarkan ID Kecamatan (Dengan Pagination)
- **Request (misal: Kecamatan Cibinong ID `100930` dengan parameter halaman):**
  ```bash
  curl -s -X GET "http://localhost:8000/api/wilayah/100930/desa?page=1&per_page=50" \
       -H "Accept: application/json"
  ```
- **Response Sukses (HTTP 200 OK):**
  ```json
  {
      "success": true,
      "message": "Data wilayah berhasil diambil.",
      "data": [
          {
              "id": 108212,
              "kode_wilayah": "3201011001",
              "kode": "3201011001",
              "nama": "Cirimekar",
              "tingkat": "desa",
              "status_batas": "definitif"
          },
          {
              "id": 108213,
              "kode_wilayah": "3201011002",
              "kode": "3201011002",
              "nama": "Ciriung",
              "tingkat": "desa",
              "status_batas": "indikatif"
          }
      ],
      "pagination": {
          "current_page": 1,
          "per_page": 50,
          "total": 13,
          "last_page": 1,
          "from": 1,
          "to": 13
      }
  }
  ```

#### E. Respon Data Kosong
Bila parent valid namun tidak memiliki anak wilayah pada database, sistem mengembalikan status 200 dengan array kosong:
```json
{
    "success": true,
    "message": "Data wilayah berhasil diambil.",
    "data": []
}
```

#### F. Respon Error & Validasi

- **HTTP 404 Not Found (Parent Tidak Ditemukan atau Format ID Salah):**
  ```json
  {
      "success": false,
      "message": "Wilayah parent tidak ditemukan."
  }
  ```

- **HTTP 422 Unprocessable Content (Tingkat Hierarki Parent Tidak Sesuai):**
  *Contoh jika ID kecamatan dikirimkan ke endpoint kabupaten:*
  ```json
  {
      "success": false,
      "message": "Wilayah parent harus bertingkat provinsi."
  }
  ```

- **HTTP 422 Unprocessable Content (Parameter Pagination Melanggar Batasan):**
  *Contoh jika per_page diminta lebih dari 100:*
  ```json
  {
      "success": false,
      "message": "Parameter pagination tidak valid.",
      "errors": {
          "per_page": [
              "Parameter per_page tidak boleh lebih dari 100."
          ]
      }
  }
  ```

### 5. Ketentuan Pagination & Performa
1. **Penerapan Terarah:** Pagination hanya diterapkan pada endpoint desa/kelurahan (`/api/wilayah/{id}/desa`). Mengingat satu kecamatan dapat memiliki banyak desa (dan total desa nasional mencapai 83.529), pembatasan per halaman menjaga alokasi memori server dan kecepatan respon.
2. **Batas Ukuran Halaman:** Parameter `per_page` memiliki nilai default `50` dan batas atas `100`. Nilai di atas 100 akan ditolak oleh validator untuk mencegah serangan denial-of-service melalui over-fetching.
3. **Pilihan Kolom Eksplisit:** Query API hanya memuat kolom `id`, `kode_wilayah`, `nama`, `tingkat`, dan `status_batas`. Kolom internal seperti `parent_id`, `created_at`, dan `updated_at` tidak diekspos ke klien.
4. **Optimasi Indeks Database:** Query relasi parent memanfaatkan indeks `wilayah_administratif_parent_id_foreign` yang telah tersedia di MySQL, memastikan eksekusi sub-milidetik tanpa perlu memindai seluruh 91.363 baris tabel.

### 6. Keamanan & Sifat Read-Only
- Seluruh endpoint API wilayah hanya beroperasi pada metode `GET` (bersifat *read-only*).
- Tidak ada mekanisme publik untuk memanipulasi, menambah, atau menghapus master data wilayah melalui API.
- Seluruh input dan query difilter menggunakan query builder Eloquent dengan parameter binding otomatis untuk mencegah SQL injection.

---

## Struktur Direktori Relevan
```
tanggap-terang/
├── app/
│   ├── Console/Commands/
│   │   └── ImportWilayahAdministratif.php   # Pipeline CLI Import: Fetch -> Stage -> Validate -> Promote
│   ├── Http/Controllers/
│   │   └── Api/
│   │       └── WilayahController.php        # REST API Controller untuk akses hierarki wilayah administratif
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
├── routes/
│   ├── api.php                              # Rute REST API wilayah (/api/wilayah/*)
│   ├── console.php
│   └── web.php
├── tests/
│   ├── Feature/
│   │   ├── WilayahApiTest.php               # 10 automated test cases (122 assertions) REST API wilayah
│   │   └── WilayahImportStagingTest.php     # 10 automated test cases (54 assertions) pipeline staging
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
| 2026-11-01 | Implementasi REST API Wilayah Administratif 4 Tingkat (Provinsi, Kabupaten, Kecamatan, Desa) | 4 endpoint bertingkat (`/api/wilayah/provinsi`, `/{id}/kabupaten`, `/{id}/kecamatan`, `/{id}/desa`), validasi hierarki parent, pagination desa, automated tests lulus (20 passed, 183 assertions) | Belum di-commit |

---
*Dokumen diperbarui secara berkala sebagai bagian dari standar transparansi pengembangan TanggapTerang.*

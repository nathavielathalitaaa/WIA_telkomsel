# TanggapTerang

> **Tagline:** Terlihat, Terlacak, dan Terukur.
> **Platform:** Papan Transparansi Informasi, Observabilitas Publik, dan Intelijen Respons Kebencanaan.

[![Website Demo](https://img.shields.io/badge/Website%20Demo-wia--telkomsel.ethiodeploy.app-0070f3?style=for-the-badge&logo=google-chrome&logoColor=white)](https://wia-telkomsel.ethiodeploy.app/)
[![Laravel 12](https://img.shields.io/badge/Framework-Laravel%2012-FF2D20?style=flat&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP 8.2+](https://img.shields.io/badge/PHP-%3E%3D8.2-777BB4?style=flat&logo=php&logoColor=white)](https://php.net)
[![MySQL 8.4](https://img.shields.io/badge/Database-MySQL%208.4-4479A1?style=flat&logo=mysql&logoColor=white)](https://www.mysql.com)
[![Tailwind CSS v4](https://img.shields.io/badge/Styling-Tailwind%20CSS%20v4-38B2AC?style=flat&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Tests Passing](https://img.shields.io/badge/Tests-79%20Passed%20(746%20Assertions)-success?style=flat&logo=php)](tests)
[![Git Status](https://img.shields.io/badge/Git%20Branch-main%20%40%2026d034b-blue?style=flat&logo=github)](https://github.com/nathavielathalitaaa/WIA_telkomsel.git)

---

## 🌐 Akses Website Demo & Akun Pengujian Juri

> **Website Demo:** [https://wia-telkomsel.ethiodeploy.app/](https://wia-telkomsel.ethiodeploy.app/)
> **Halaman Login Internal:** [https://wia-telkomsel.ethiodeploy.app/login](https://wia-telkomsel.ethiodeploy.app/login)
> **Papan Transparansi Sumber Data:** [https://wia-telkomsel.ethiodeploy.app/sumber-data](https://wia-telkomsel.ethiodeploy.app/sumber-data)

Tabel berikut menyajikan akun demonstrasi resmi yang disediakan bagi Dewan Juri WIA / MIA 2026 untuk menguji alur kerja internal TanggapTerang sesuai batasan hak akses masing-masing peran:

| Role | Email / Username | Password | Hak Akses Utama |
|---|---|:---:|---|
| **Administrator** | `admin@tanggap-terang.id` | `password123` | **Akses Penuh Seluruh Sistem.** Memiliki hak akses verifikasi laporan warga, manajemen insiden bencana, serta akses eksklusif ke **Pusat Intelijen Sumber Data** (`/internal/intelligence`), trigger penarikan data live API (*Fetch Now*), penampil struktur raw JSON payload, komparasi cuaca BMKG vs Open-Meteo, dan log audit HTTP penarikan data. |
| **Verifikator** | `verifikator@tanggap-terang.id` | `password123` | **Validasi Integritas Fakta.** Memeriksa laporan masyarakat masuk dari lapangan, menetapkan status verifikasi (*Terverifikasi*, *Ditolak*, atau *Duplikat*), dan memverifikasi komitmen bantuan logistik dari relawan/lembaga. Dibatasi dari mengedit operasional insiden dan diblokir dari Pusat Intelijen (HTTP 403). |
| **Operator** | `operator@tanggap-terang.id` | `password123` | **Koordinasi Respons Lapangan.** Memperbarui status tindak lanjut kejadian bencana, mencatat kronologi perkembangan lapangan (*follow-up*), menautkan laporan warga yang valid ke insiden, menautkan sumber informasi pendukung, serta memperbarui penyaluran logistik. Dibatasi dari mengubah keputusan verifikasi laporan (HTTP 403). |

> [!NOTE]
> **Catatan Autentikasi Demo:**
> 1. Akun dan kata sandi di atas merupakan kredensial simulasi yang secara sengaja disediakan pada berkas [`SimulationDataSeeder.php`](database/seeders/SimulationDataSeeder.php) dan diverifikasi melalui rangkaian pengujian otomatis [`InternalAuthAndManagementTest.php`](tests/Feature/InternalAuthAndManagementTest.php).
> 2. Akun demo di atas **bukan akun pribadi** dan aman untuk dipublikasikan pada proses penjurian kompetisi.
> 3. Seluruh fitur publik (Dashboard Monitor, Peta Interaktif, Buat Laporan Mandiri, Pelacakan Status Tiket Anonim, Replay Kronologi, Sitrep, dan Papan Transparansi Sumber Data) dapat dicoba langsung **tanpa memerlukan login**.

---

## Daftar Isi
- [🌐 Akses Website Demo & Akun Pengujian Juri](#-akses-website-demo--akun-pengujian-juri)
- [A. Project Overview](#a-project-overview)
  - [Latar Belakang & Masalah yang Diselesaikan](#latar-belakang--masalah-yang-diselesaikan)
  - [Tujuan & Nilai Utama Proyek](#tujuan--nilai-utama-proyek)
  - [Sasaran Pengguna & Manfaat Publik](#sasaran-pengguna--manfaat-publik)
  - [Batasan Ruang Lingkup Sistem](#batasan-ruang-lingkup-sistem)
- [B. Core Features](#b-core-features)
  - [1. Dashboard Publik (Monitor Kejadian Bencana)](#1-dashboard-publik-monitor-kejadian-bencana)
  - [2. Peta Spasial & Informasi Kejadian](#2-peta-spasial--informasi-kejadian)
  - [3. Detail Kejadian & Atribusi Sumber Informasi](#3-detail-kejadian--atribusi-sumber-informasi)
  - [4. Pelaporan Masyarakat Tanpa Login](#4-pelaporan-masyarakat-tanpa-login)
  - [5. Pelacakan Status Laporan (Tiket Anonim)](#5-pelacakan-status-laporan-tiket-anonim)
  - [6. Status Dokumentasi Respons & Penyaluran Logistik](#6-status-dokumentasi-respons--penyaluran-logistik)
  - [7. Kronologi & Riwayat Informasi (Replay & Sitrep)](#7-kronologi--riwayat-informasi-replay--sitrep)
  - [8. Transparansi Sumber Data Publik (`/sumber-data`)](#8-transparansi-sumber-data-publik-sumber-data)
  - [9. Pusat Intelijen Sumber Data untuk Administrator (`/internal/intelligence`)](#9-pusat-intelijen-sumber-data-untuk-administrator-internalintelligence)
  - [10. Perbandingan Cuaca BMKG vs Open-Meteo](#10-perbandingan-cuaca-bmkg-vs-open-meteo)
  - [11. Fitur Pengelolaan Internal (Verifikator & Operator)](#11-fitur-pengelolaan-internal-verifikator--operator)
  - [12. REST API Wilayah Administratif 4 Tingkat](#12-rest-api-wilayah-administratif-4-tingkat)
- [C. Data Sources and Transparency](#c-data-sources-and-transparency)
  - [Matriks Integrasi Sumber Data Eksternal](#matriks-integrasi-sumber-data-eksternal)
  - [Klasifikasi Tipe Data & Kejujuran Informasi](#klasifikasi-tipe-data--kejujuran-informasi)
- [D. Technology Stack](#d-technology-stack)
- [E. Architecture](#e-architecture)
  - [Struktur Komponen Sistem](#struktur-komponen-sistem)
  - [Pola Aliran Data & Pipeline Agregasi](#pola-aliran-data--pipeline-agregasi)
  - [Struktur Direktori Proyek](#struktur-direktori-proyek)
- [F. User Roles and Demo Accounts](#f-user-roles-and-demo-accounts)
  - [Tabel Peran & Hak Akses Pengguna Demo](#tabel-peran--hak-akses-pengguna-demo)
  - [Prosedur Pengujian Akun Demo](#prosedur-pengujian-akun-demo)
  - [Inisialisasi Akun Demo pada Database Baru](#inisialisasi-akun-demo-pada-database-baru)
- [G. Getting Started](#g-getting-started)
  - [Prasyarat Sistem](#prasyarat-sistem)
  - [Langkah Instalasi & Konfigurasi](#langkah-instalasi--konfigurasi)
  - [Menjalankan Aplikasi & Worker](#menjalankan-aplikasi--worker)
- [H. Routes and Navigation](#h-routes-and-navigation)
- [I. Testing and Quality Assurance](#i-testing-and-quality-assurance)
  - [Eksekusi Automated Tests](#eksekusi-automated-tests)
  - [Cakupan Pengujian Terverifikasi](#cakupan-pengujian-terverifikasi)
  - [Kompilasi Aset Frontend](#kompilasi-aset-frontend)
- [J. Development Progress Log](#j-development-progress-log)
  - [Tabel Kronologi Perkembangan Proyek](#tabel-kronologi-perkembangan-proyek)
  - [Latest Update Status](#latest-update-status)
- [K. Limitations and Future Development](#k-limitations-and-future-development)
  - [Keterbatasan Aktual Saat Ini](#keterbatasan-aktual-saat-ini)
  - [Rencana Pengembangan Masa Depan](#rencana-pengembangan-masa-depan)
- [L. Project Team](#l-project-team)

---

## A. Project Overview

**TanggapTerang** adalah platform observabilitas dan transparansi respons bencana berbasis web yang dirancang untuk menghadirkan kejelasan informasi di tengah situasi krisis. Mengusung prinsip *"Terlihat, Terlacak, dan Terukur"*, platform ini menghubungkan laporan kebutuhan masyarakat, catatan tindak lanjut relawan/lembaga, serta data agregasi intelijen terbuka dari berbagai penyedia resmi domestik maupun internasional.

### Latar Belakang & Masalah yang Diselesaikan
Dalam setiap peristiwa bencana darurat di Indonesia, kesenjangan dan kesimpangsiuran informasi kerap menjadi hambatan terbesar yang memperlambat penanganan korban:
1. **Asimetri Informasi & Misinformasi:** Masyarakat di lapangan kesulitan mengetahui apakah kondisi darurat mereka sudah dicatat, sedangkan publik di luar wilayah bencana dibanjiri berita tanpa kepastian keabsahan sumber.
2. **Penumpukan vs Kekurangan Logistik (*Supply Mismatch*):** Penyaluran bantuan cenderung terkonsentrasi pada posko-posko induk yang mudah diakses transportasi besar, sementara titik-titik pengungsian mandiri di pelosok desa tidak terdata dan luput dari pemenuhan bantuan.
3. **Ketiadaan Jejak Audit Publik (*Audit Trail*):** Laporan warga yang diajukan ke berbagai kanal sering hilang tanpa kejelasan status: apakah laporan ditolak, sedang diverifikasi, sedang dikirim bantuannya, atau sudah diterima langsung oleh warga.
4. **Ambiguitas Spasial Batas Administratif:** Koordinasi penanganan bencana sering terkendala ketidaksinkronan nama wilayah, kodifikasi ganda, atau ketidakjelasan batas wilayah antara desa/kelurahan definitif dan indikatif.
5. **Ketiadaan Komparasi Lintas Sumber:** Pemangku kepentingan dan analis jarang memiliki instrumen terpadu untuk membandingkan informasi cuaca atau peringatan dini dari sumber pemerintah (misal BMKG) dengan model numerik global (misal Open-Meteo atau GDACS).

### Tujuan & Nilai Utama Proyek
- **Transparansi Tanpa Hambatan:** Menyajikan status verifikasi laporan, riwayat tindak lanjut, dan asal-usul sumber data secara terbuka kepada publik tanpa mewajibkan masyarakat untuk mendaftar akun atau masuk log (*login*).
- **Akuntabilitas & Audit Trail Permanen:** Setiap perubahan status verifikasi, tindak lanjut, dan logistik dicatat dengan rekaman waktu dan peran petugas internal yang dapat ditelusuri.
- **Perlindungan Privasi Pelapor (Zero PII Leak):** Nama lengkap, nomor kontak, dan rincian identitas pribadi pelapor dilindungi secara ketat dan tidak pernah diekspos ke antarmuka publik atau payload API publik.
- **Kejujuran Provenance Sumber Data:** Sistem membedakan secara tegas antara data simulasi skenario demonstrasi, laporan warga langsung, peringatan dini otomatis, model prakiraan cuaca, dan data agregasi eksternal.

### Sasaran Pengguna & Manfaat Publik
- **Warga Terdampak & Saksi Mata:** Mengajukan laporan situasi dan permohonan bantuan secara mudah melalui ponsel tanpa registrasi, lalu memantau progres penanganan secara transparan menggunakan nomor tiket unik.
- **Relawan & Lembaga Kemanusiaan:** Mencatatkan komitmen dan realisasi penyaluran logistik, memantau titik-titik kebutuhan mendesak yang belum terpenuhi, serta menghindari tumpang-tindih bantuan di titik yang sama.
- **Tim Pengelola Respons (Verifikator, Operator, Administrator):** Memvalidasi laporan masuk dari lapangan, mengelompokkannya ke dalam kejadian bencana aktif, mengkoordinasikan tindak lanjut, dan memantau agregasi intelijen multi-sumber.
- **Publik, Juri Penilai, & Peneliti:** Memeriksa keterbukaan data melalui portal `/sumber-data`, mengamati linimasa perkembangan bencana secara interaktif via `/replay`, dan mengaudit integritas data penanganan secara objektif.

### Batasan Ruang Lingkup Sistem
> [!IMPORTANT]
> **Penegasan Ruang Lingkup Sistem:**
> TanggapTerang **BUKAN** platform komersial penyaluran donasi uang/finansial, **BUKAN** platform e-commerce pengiriman barang, dan **BUKAN** sistem komando darurat taktis lapangan (bukan pengganti panggilan darurat 112, Basarnas, atau SAR).
> TanggapTerang berfungsi sebagai **papan transparansi informasi, observabilitas publik, audit trail independen, dan instrumen analisis intelijen data bencana**.

---

## B. Core Features

TanggapTerang dibangun dengan arsitektur fitur terarah yang memisahkan akses publik yang transparan dengan kontrol operasional internal yang terotentikasi.

### 1. Dashboard Publik (Monitor Kejadian Bencana)
- **URL / Akses:** `/` | Publik (Tanpa Login).
- **Fungsi:** Menampilkan gambaran umum situasi kebencanaan aktif di seluruh Indonesia melalui peta spasial interaktif, ringkasan kartu metrik (*Total Kejadian Aktif, Laporan Masuk, Kebutuhan Mendesak, Bantuan Tersalurkan*), serta daftar insiden yang dapat difilter.
- **Alur Pengguna:** Pengguna membuka halaman beranda, memilih filter provinsi/kabupaten, kategori bencana (banjir, gempa bumi, tanah longsor, cuaca ekstrem), tingkat keparahan, atau mengetik kata kunci pencarian. Memilih kartu kejadian akan memusatkan tampilan peta ke koordinat lokasi kejadian.
- **Status Implementasi:** **Berfungsi Penuh & Teruji** (`PublicIncidentController@index`).

### 2. Peta Spasial & Informasi Kejadian
- **Komponen:** Peta interaktif Leaflet.js dengan layer OpenStreetMap.
- **Fungsi:** Visualisasi sebaran titik insiden menggunakan penanda (*marker*) berwarna dinamis sesuai tingkat keparahan (*Rendah, Sedang, Tinggi, Kritis*). Dilengkapi fitur interaksi *cross-highlighting* dua arah antara daftar kartu dan marker peta.
- **Pengguna:** Publik dan Staf Internal.
- **Status Implementasi:** **Berfungsi Penuh & Teruji** (`resources/js/interactions/map-crosshighlight.js`).

### 3. Detail Kejadian & Atribusi Sumber Informasi
- **URL / Akses:** `/incidents/{slug}` | Publik (Tanpa Login).
- **Fungsi:** Menampilkan profil lengkap sebuah kejadian bencana: deskripsi naratif, koordinat dan batas wilayah administratif, daftar kebutuhan logistik mendesak, atribusi sumber informasi pendukung (Laporan Masyarakat, Sumber Pemerintah, Media Massa, Media Sosial), linimasa audit pergerakan status, dan daftar respons bantuan relawan yang telah diverifikasi.
- **Alur Pengguna:** Pengguna mengklik tombol "Lihat Detail" pada kartu insiden di halaman monitor.
- **Status Implementasi:** **Berfungsi Penuh & Teruji** (`PublicIncidentController@show`).

### 4. Pelaporan Masyarakat Tanpa Login
- **URL / Akses:** `/reports/create` | Publik (Tanpa Login).
- **Fungsi:** Memungkinkan warga atau relawan melaporkan kejadian bencana atau kebutuhan mendesak secara langsung. Menggunakan dropdown wilayah bertingkat 4 level (Provinsi $\rightarrow$ Kabupaten $\rightarrow$ Kecamatan $\rightarrow$ Desa) yang terhubung ke REST API Wilayah internal.
- **Alur Pengguna:** Pengguna mengisi judul, jenis bencana, deskripsi kejadian, kebutuhan logistik, nama pelapor, kontak telepon, dan memilih lokasi administratif. Setelah formulir dikirim, sistem mengembalikan **Kode Tiket Laporan Unik Non-Sekuensial** (contoh: `REP-202611-9A4B2C`).
- **Jaminan Privasi:** Nama pelapor dan nomor telepon disimpan secara aman di database dan **tidak pernah dibocorkan** pada antarmuka publik mana pun.
- **Status Implementasi:** **Berfungsi Penuh & Teruji** (`PublicReportController@create` & `store`, rate-limited 15 req/menit).

### 5. Pelacakan Status Laporan (Tiket Anonim)
- **URL / Akses:** `/track` | Publik (Tanpa Login).
- **Fungsi:** Memfasilitasi pelapor memeriksa perkembangan tindak lanjut laporannya secara mandiri menggunakan kode tiket.
- **Alur Pengguna:** Pengguna memasukkan kode tiket pada kolom pencarian. Sistem menampilkan status verifikasi (*Menunggu Verifikasi, Terverifikasi, Ditolak, Duplikat*), status tindak lanjut, waktu laporan, serta riwayat audit tindak lanjut tanpa memaparkan data pribadi pelapor atau catatan rahasia verifikator internal.
- **Status Implementasi:** **Berfungsi Penuh & Teruji** (`PublicReportController@track`, rate-limited 20 req/menit).

### 6. Status Dokumentasi Respons & Penyaluran Logistik
- **URL / Akses:** `/aid-responses/create` | Publik (Tanpa Login).
- **Fungsi:** Lembaga swadaya masyarakat, komunitas relawan, atau donatur dapat mencatatkan komitmen dan penyaluran bantuan (contoh: beras, selimut, obat-obatan, tenda, armada evakuasi) untuk insiden tertentu.
- **Alur Pengguna:** Donatur/relawan memilih insiden bencana target, mengisi identitas organisasi, jenis barang, kuantitas, status penyaluran (*Dijanjikan, Dikirim, Diterima di Posko, Tersalurkan ke Warga*), dan tanggal serah terima. Catatan ini masuk antrean verifikasi internal sebelum tampil di linimasa publik.
- **Status Implementasi:** **Berfungsi Penuh & Teruji** (`PublicAidResponseController@create` & `store`).

### 7. Kronologi & Riwayat Informasi (Replay & Sitrep)
- **Replay Linimasa Bencana (`/replay`):**
  - Pemutar linimasa interaktif (Play, Pause, Step Next, Step Previous) untuk mengamati urutan peristiwa kebencanaan dari jam ke jam, termasuk perubahan status keparahan dan masuknya laporan masyarakat.
  - Status: **Berfungsi Penuh & Teruji** (`PublicReplayController@index`).
- **Laporan Situasi Terintegrasi (`/sitrep`):**
  - Halaman Situation Report (Sitrep) ringkas dan siap cetak yang menyajikan ringkasan statistik agregat bencana, persentase verifikasi laporan, proporsi tindak lanjut, dan rincian alokasi kebutuhan untuk pengambil keputusan.
  - Status: **Berfungsi Penuh & Teruji** (`PublicSitrepController@index`).

### 8. Transparansi Sumber Data Publik (`/sumber-data`)
- **URL / Akses:** `/sumber-data` dan `/sumber-data/records/{record}` | Publik (Tanpa Login).
- **Fungsi:** Halaman khusus transparansi yang membuktikan secara terbuka bahwa TanggapTerang benar-benar terintegrasi dengan penyedia data eksternal.
- **Fitur Tampilan:**
  - Kartu pemantauan untuk seluruh 8 provider data eksternal (status integrasi aktual, timestamp penarikan data terakhir, jumlah data diterima & tersimpan).
  - Filter kategori data (Gempa Bumi, Prakiraan Cuaca, Laporan Warga, Hidrologi, Peringatan Multi-Bahaya, Pemberitaan).
  - Tampilan 3 rekaman terbaru per provider beserta tautan sumber resmi eksternal.
  - Halaman detail rekaman publik yang aman (`/sumber-data/records/{record}`): menyajikan judul, ringkasan, waktu kejadian/publikasi, koordinat lokasi, dan identitas penyedia tanpa membocorkan raw JSON payload atau informasi teknis internal.
- **Status Implementasi:** **Berfungsi Penuh & Teruji** (`PublicDataSourceController@index` & `show`).

### 9. Pusat Intelijen Sumber Data untuk Administrator (`/internal/intelligence`)
- **URL / Akses:** `/internal/intelligence` | Internal Authenticated (**Khusus Role `admin`**).
- **Fungsi:** Ruang kendali intelijen data multi-sumber bagi administrator TanggapTerang.
- **Fitur Khusus Administrator:**
  - Pemantauan metrik agregasi: total provider aktif, jumlah total rekaman eksternal yang tersimpan, dan riwayat status koneksi.
  - Tombol **"Ambil Data Sekarang" (*Fetch Now*)**: Melakukan penarikan data live ke endpoint eksternal via HTTP POST (`/internal/intelligence/fetch/{provider}`).
  - Inspeksi Rekaman & Raw Payload (`/internal/intelligence/records/{record}`): Administrator dapat melihat payload JSON asli (*raw data*) dari pihak ketiga secara utuh beserta metadata parsing.
  - Audit Trail Penarikan Data (`/internal/intelligence/logs`): Riwayat log penarikan HTTP lengkap (durasi milidetik, kode status HTTP, waktu eksekusi, pesan galat jika terjadi kendala).
- **Status Implementasi:** **Berfungsi Penuh & Teruji** (`IntelligenceCenterController`).

### 10. Perbandingan Cuaca BMKG vs Open-Meteo
- **URL / Akses:** `/internal/intelligence/comparisons` | Internal Authenticated (**Khusus Role `admin`**).
- **Fungsi:** Membandingkan prakiraan cuaca resmi BMKG Indonesia dengan model prakiraan cuaca numerik global Open-Meteo Weather pada slot waktu yang sama (WIB / UTC) untuk wilayah observasi yang sama (misal Kabupaten Bogor).
- **Indikator yang Dibandingkan:** Suhu udara (°C), kondisi cuaca/awan, kelembapan udara (%), kecepatan angin (km/jam), dan potensi curah hujan (mm).
- **Ketahanan Render:** Dilengkapi logika penanganan aman (*defensive fallback*) terhadap slot waktu yang tidak sejajar, kunci data `time` yang hilang, atau payload kosong tanpa menyebabkan error 500.
- **Analisis Diskrepansi:** Administrator dapat menyimpan catatan analisis diskrepansi cuaca langsung ke database untuk evaluasi kesiapsiagaan bencana.
- **Status Implementasi:** **Berfungsi Penuh & Teruji** (`IntelligenceAggregatorService::getWeatherComparison`).

### 11. Fitur Pengelolaan Internal (Verifikator & Operator)
- **Verifikasi Laporan Masuk (`/internal/reports`):** Verifikator dan Administrator memeriksa keabsahan laporan warga, menandai sebagai *Terverifikasi*, *Ditolak*, atau *Duplikat* dengan alasan tertulis yang tersimpan di riwayat audit.
- **Pengelolaan Kejadian Bencana (`/internal/incidents`):** Operator dan Administrator memperbarui status tindak lanjut, menambahkan catatan kemajuan lapangan, menautkan laporan warga yang valid ke insiden tertentu, dan menambahkan sumber informasi baru.
- **Verifikasi Logistik Bantuan:** Verifikator memeriksa keabsahan klaim pengiriman bantuan dari lembaga donatur sebelum ditampilkan di publik.
- **Status Implementasi:** **Berfungsi Penuh & Teruji** (`InternalReportController` & `InternalIncidentController`).

### 12. REST API Wilayah Administratif 4 Tingkat
- **Prefix Endpoint:** `/api/wilayah` | Publik & Internal (Read-Only).
- **Fungsi:** Menyediakan akses hierarkis terhadap 91.363 master wilayah administratif Indonesia resmi (38 Provinsi, 514 Kabupaten/Kota, 7.282 Kecamatan, 83.529 Desa/Kelurahan) yang bebas dari orphan parent ID.
- **Status Implementasi:** **Berfungsi Penuh & Teruji** (`WilayahController`).

---

## C. Data Sources and Transparency

TanggapTerang menjunjung tinggi kejujuran intelektual dalam pengelolaan data. Sistem mengagregasi 8 penyedia data eksternal dengan status koneksi riil dan batasan teknis yang transparan.

### Matriks Integrasi Sumber Data Eksternal

| Penyedia Sumber | Kode Provider | Kategori | Jenis Data yang Dikelola | Status Integrasi Aktual | Keterangan & Keterbatasan Teknis |
|---|:---:|:---:|---|:---:|---|
| **BMKG TEWS Gempa** | `bmkg_gempa` | Dalam Negeri | Gempa bumi terkini M $\ge$ 5.0 & gempa dirasakan | **Terintegrasi Penuh (Aktif)** | Mengambil data live XML/JSON dari server BMKG (`autogempa.json` dan `gempaterkini.json`). Terbuka tanpa autentikasi. |
| **BMKG Cuaca Wilayah** | `bmkg_cuaca` | Dalam Negeri | Prakiraan cuaca tingkat kecamatan/desa | **Terintegrasi Penuh (Aktif)** | Mengambil data resmi dari API publik BMKG berdasarkan kode wilayah adm4 (misal: Bogor). Interval prakiraan per 3–6 jam. |
| **PetaBencana.id** | `petabencana` | Dalam Negeri | Laporan banjir, angin kencang, dan bencana berbasis warga | **Terintegrasi Penuh (Aktif)** | Mengambil laporan masyarakat terverifikasi melalui REST API PetaBencana (`https://data.petabencana.id/reports`). |
| **Open-Meteo Weather** | `open_meteo_weather` | Internasional | Model numerik cuaca global (suhu, hujan, angin, kelembapan) | **Terintegrasi Penuh (Aktif)** | Mengambil data prakiraan per jam via Open-Meteo Forecast API berdasarkan koordinat geografis wilayah terdampak. |
| **Open-Meteo Flood** | `open_meteo_flood` | Internasional | Model debit aliran sungai (*river discharge* $\text{m}^3/\text{s}$) | **Terintegrasi Penuh (Aktif)** | **PENTING:** Data ini berupa **indikator model numerik hidrologi (GloFAS)**, **BUKAN** bukti fisik kejadian banjir di daratan secara faktual. Digunakan sebagai indikator potensi risiko air. |
| **GDACS** | `gdacs` | Internasional | Peringatan multi-bahaya global (gempa, siklon, banjir, tsunami) | **Terintegrasi Penuh (Aktif)** | **PENTING:** GDACS mengelola peringatan skala internasional (*Green, Orange, Red Alert*). Peringatan pada GDACS **tidak otomatis** berarti bencana telah terjadi dan terkonfirmasi di tingkat desa Indonesia tanpa kroscek otoritas lokal. |
| **GDELT Project** | `gdelt` | Internasional | Pemindaian pemberitaan media global terkait bencana Indonesia | **Terhubung (Fluktuatif)** | Terhubung ke GDELT 2.1 Doc API. Upstream GDELT sewaktu-waktu dapat mengalami pembatasan kuota (*HTTP 429 Rate Limited*), tantangan Cloudflare, atau latensi tinggi; status kegagalan ditampilkan secara jujur di sistem. |
| **BNPB** | `bnpb` | Dalam Negeri | Data resmi kejadian bencana nasional | **Belum Terintegrasi (`not_integrated`)** | **KEJUJURAN IMPLEMENTASI:** BNPB belum menyediakan open REST API publik yang dapat diakses secara bebas tanpa kredensial/MOU institusional. Sistem secara jujur menandai provider ini sebagai `Belum Terintegrasi` tanpa membuat request simulasi palsu. |

### Klasifikasi Tipe Data & Kejujuran Informasi

Untuk mencegah kesalahpahaman informasi publik, seluruh tampilan TanggapTerang menyertakan lencana klasifikasi (*badge*) yang terstandarisasi:

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                          KLASIFIKASI INFORMASI TANGGAPTERANG                           │
├──────────────────────────────┬─────────────────────────────┬───────────────────────────┤
│ Laporan Warga                │ Laporan mandiri masyarakat  │ Perlu verifikasi tim      │
│ Sumber Pemerintah (Simulasi) │ Data skenario demonstrasi   │ Bertanda Simulation Banner│
│ Prakiraan & Model Numerik    │ Prediksi kalkulasi cuaca    │ Model matematis masa depan│
│ Peringatan Dini Multi-Bahaya │ Sensor & deteksi satelit    │ Indikator bahaya terbuka  │
│ Intelijen Terbuka Eksternal  │ Agregasi data API pihak ke-3│ Tidak diklaim 100% mutlak │
└──────────────────────────────┴─────────────────────────────┴───────────────────────────┘
```

1. **Laporan Masyarakat (*Citizen Reports*):** Bersumber dari masyarakat di lapangan. Sebelum diproses tim verifikator, laporan berstatus *Menunggu Verifikasi* dan belum menjadi fakta terverifikasi.
2. **Data Skenario Simulasi (*Simulation Data*):** Digunakan selama masa evaluasi kompetisi (misal: Skenario Banjir Bandang Tirta Jaya & Longsor Megamendung Bogor) agar juri dapat menguji alur kerja lengkap tanpa memicu kepanikan warga di dunia nyata. Dilengkapi banner kuning permanen: *"Data Simulasi - Digunakan untuk kebutuhan evaluasi fitur"*.
3. **Prakiraan & Indikator Numerik:** Merupakan estimasi model atmosfer dan debit air, bukan konfirmasi kejadian masa lampau.
4. **Peringatan Dini Internasional:** Informasi indikasi bahaya global dari instrumen GDACS/Open-Meteo yang memerlukan validasi instansi berwenang.

---

## D. Technology Stack

TanggapTerang dibangun menggunakan tumpukan teknologi modern, ringan, efisien, dan siap produksi:

| Lapisan Sistem | Teknologi | Versi | Referensi Konfigurasi |
|---|---|:---:|---|
| **Backend Framework** | Laravel | `^12.0` | [`composer.json`](composer.json) |
| **Runtime Bahasa** | PHP | `^8.2` | Kompatibel PHP 8.2 & PHP 8.3 |
| **Basis Data** | MySQL Server | `8.4 / 8.0` | InnoDB, Transaksi ACID, UTF8mb4 |
| **Frontend Templating** | Laravel Blade Engine | Native | Komponen Blade modular & aman |
| **CSS Styling Engine** | Tailwind CSS v4 | `^4.3.3` | [`@tailwindcss/vite`](package.json) |
| **Asset Bundler** | Vite | `^7.0.7` | [`vite.config.js`](vite.config.js) |
| **Client-side Scripting** | Vanilla JavaScript (ES6+) | Modular | `resources/js/interactions/*` |
| **Peta & Visualisasi Spasial** | Leaflet.js | `^1.9.4` | OpenStreetMap Tile Layer |
| **Testing Engine** | PHPUnit | `^11.5.50` | In-Memory SQLite untuk eksekusi cepat |
| **HTTP Client Provider** | Guzzle / Laravel Http Client | Native | Exponential timeout & payload normalizer |

---

## E. Architecture

TanggapTerang menggunakan arsitektur berlapis (*layered architecture*) yang memisahkan antarmuka pengguna, keamanan otorisasi peran, orkestrasi bisnis, dan integrasi API eksternal.

```
                    ┌────────────────────────────────────────────────────────┐
                    │               PENGGUNA & PERAMBAN WEB                  │
                    └───────────┬────────────────────────────────┬───────────┘
                                │                                │
                        (Rute Publik Bebas)             (Rute Internal /login)
                                │                                │
                                ▼                                ▼
                    ┌─────────────────────────┐      ┌───────────────────────┐
                    │  PUBLIC INTERFACE       │      │  INTERNAL INTERFACE   │
                    │  - Monitor Kejadian     │      │  - EnsureInternalRole │
                    │  - Buat Laporan Warga   │      │  - Verifikator Desk   │
                    │  - Lacak Tiket Laporan  │      │  - Operator Desk      │
                    │  - Transparansi Sumber  │      │  - Admin Intelligence │
                    └───────────┬─────────────┘      └───────────┬───────────┘
                                │                                │
                                └────────────────┬───────────────┘
                                                 │
                                                 ▼
                    ┌────────────────────────────────────────────────────────┐
                    │             CORE SERVICE & AGGREGATOR LAYER            │
                    │  - IntelligenceAggregatorService                       │
                    │  - Provider Services (BMKG, PetaBencana, GDACS, dll.)  │
                    │  - Defensive Fallback & Weather Comparison Engine      │
                    └────────────────────────────┬───────────────────────────┘
                                                 │
                                                 ▼
                    ┌────────────────────────────────────────────────────────┐
                    │             PERSISTENCE & AUDIT LOG LAYER              │
                    │  - Unique Index Deduplication (provider + external_id) │
                    │  - Atomic DB Transactions (beginTransaction / commit)  │
                    │  - Audit Logs (VerificationHistory, ExternalFetchLog)  │
                    │  - 91.363 Master Wilayah Administratif 4 Tingkat       │
                    └────────────────────────────────────────────────────────┘
```

### Struktur Komponen Sistem
1. **Public Interface (`app/Http/Controllers/Public*.php`):**
   Melayani interaksi masyarakat luas tanpa autentikasi. Seluruh input divalidasi ketat dan rate-limited menggunakan middleware pembatasan panggilan.
2. **Authentication & RBAC (`app/Http/Middleware/EnsureInternalRole.php`):**
   Menerapkan otorisasi berbasis peran menggunakan enum `UserRole` (`admin`, `verifikator`, `operator`). Pengguna tanpa peran yang sesuai akan menerima respons HTTP 403 Forbidden.
3. **Data Provider Services (`app/Services/Intelligence/`):**
   Setiap provider mengimplementasikan kontrak baku `DataProviderInterface`, mewarisi `AbstractHttpProvider` untuk standarisasi timeout HTTP (10 detik), penanganan error koneksi, dan pemetaan data ke format standar DTO `ProviderFetchResult`.
4. **Logging, Deduplication, & Persistence:**
   - **Deduplikasi Unik:** Tabel `external_source_records` memiliki indeks unik komposit `unique_provider_record` (`provider_code` + `external_id`) untuk mencegah data ganda dari fetch berulang.
   - **Transaksi Aman:** Penyimpanan batch menggunakan `DB::beginTransaction()` dan `DB::commit()`. Jika terjadi kegagalan basis data di tengah proses, seluruh rekaman di-rollback dan status log dicatat sebagai gagal.
   - **Audit Trail:** Riwayat penarikan data tercatat di `external_fetch_logs`, sedangkan riwayat penanganan laporan warga tercatat di `verification_histories` dan `follow_up_records`.

### Struktur Direktori Proyek
```
tanggap-terang/
├── app/
│   ├── Console/Commands/           # CLI Command (Import Wilayah, Test Live Fetch)
│   ├── Enums/                      # Enums (UserRole, VerificationStatus, SourceType, dll.)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/                # REST API Wilayah Administratif
│   │   │   ├── Auth/               # Autentikasi Staf Internal
│   │   │   ├── Internal/           # Dashboard, Verifikasi, Operator, Pusat Intelijen
│   │   │   └── Public*.php         # Controller Halaman Publik
│   │   └── Middleware/             # EnsureInternalRole Middleware
│   ├── Models/                     # Eloquent Models (Incident, Report, Provider, dll.)
│   └── Services/Intelligence/      # Aggregator, AbstractHttpProvider, 8 Provider Services
├── database/
│   ├── migrations/                 # 38 skrip migrasi database terstruktur
│   └── seeders/                    # DatabaseSeeder & SimulationDataSeeder
├── resources/
│   ├── css/app.css                 # Konfigurasi Tailwind CSS v4 & custom utility
│   ├── js/                         # Script modular (peta, modal, toast, tooltip)
│   └── views/                      # Template Blade (public, internal, auth, components)
├── routes/
│   ├── web.php                     # Rute utama aplikasi web
│   └── api.php                     # Rute REST API publik (/api/wilayah/...)
├── tests/
│   └── Feature/                    # 10 file automated test suite (79 test cases)
└── README.md                       # Dokumentasi resmi proyek TanggapTerang
```

---

## F. User Roles and Demo Accounts

Untuk memfasilitasi evaluasi oleh juri kompetisi WIA / MIA 2026, sistem TanggapTerang telah menyiapkan 3 akun demonstrasi yang merepresentasikan pembagian peran operasional secara nyata.

### Tabel Peran & Hak Akses Pengguna Demo

| Peran (*Role*) | Email Login Demo | Kata Sandi | Hak Akses Utama | Halaman Utama yang Dapat Diuji |
|---|---|:---:|---|---|
| **Administrator** | `admin@tanggap-terang.id` | `password123` | **Akses Penuh Seluruh Sistem.** Memiliki seluruh wewenang verifikator dan operator, ditambah hak khusus mengelola **Pusat Intelijen Sumber Data**, memicu penarikan data live API (*Fetch Now*), melihat raw JSON payload, membandingkan cuaca BMKG vs Open-Meteo, dan menginspeksi log audit penarikan HTTP. | `/internal/dashboard`<br>`/internal/reports`<br>`/internal/incidents`<br>`/internal/intelligence`<br>`/internal/intelligence/comparisons`<br>`/internal/intelligence/logs` |
| **Verifikator Informasi** | `verifikator@tanggap-terang.id` | `password123` | **Validasi Integritas Fakta.** Memeriksa laporan warga masuk, memverifikasi keabsahan laporan, menolak laporan hoaks/tidak valid, menandai laporan duplikat, dan memverifikasi komitmen bantuan relawan. Dibatasi dari mengedit operasional insiden dan diblokir dari Pusat Intelijen (HTTP 403). | `/internal/dashboard`<br>`/internal/reports`<br>`/internal/reports/{id}`<br>`/internal/incidents/{id}/edit` *(Tab Verifikasi)* |
| **Operator Tindak Lanjut** | `operator@tanggap-terang.id` | `password123` | **Koordinasi Respons Lapangan.** Mengubah status tindak lanjut kejadian, menambahkan catatan kronologi lapangan, menautkan laporan terverifikasi ke insiden, menambahkan sumber informasi baru, dan memperbarui penyaluran logistik. Dibatasi dari mengubah keputusan verifikasi laporan (HTTP 403). | `/internal/dashboard`<br>`/internal/reports`<br>`/internal/incidents`<br>`/internal/incidents/create`<br>`/internal/incidents/{id}/edit` *(Tab Tindak Lanjut)* |

### Prosedur Pengujian Akun Demo
1. Buka peramban dan akses alamat `/login` pada website demo atau server lokal.
2. Masukkan alamat surel demo sesuai peran yang ingin diuji (misal: `admin@tanggap-terang.id`).
3. Masukkan kata sandi demo: `password123`.

### Inisialisasi Akun Demo pada Database Baru
Jika Anda menguji pada instalasi database baru yang masih kosong, seluruh akun demo di atas dapat diinisialisasi secara otomatis melalui perintah seeder bawaan:
```bash
php artisan db:seed --class=SimulationDataSeeder
```
Atau jika Anda ingin menyetel kata sandi khusus sendiri pada lingkungan lokal tanpa memodifikasi file repositori, gunakan perintah CLI interaktif berikut:
```bash
php artisan tinker --execute="App\Models\User::updateOrCreate(['email' => 'admin@tanggap-terang.id'], ['name' => 'Admin TanggapTerang', 'password' => bcrypt('KATA_SANDI_PILIHAN_ANDA'), 'role' => App\Enums\UserRole::Admin, 'is_active' => true]);"
```

---

## G. Getting Started

### Prasyarat Sistem
Pastikan lingkungan pengembangan lokal Anda telah memenuhi spesifikasi minimum berikut:
- **PHP** $\ge$ `8.2` (dengan ekstensi wajib: `pdo`, `pdo_mysql`, `curl`, `mbstring`, `openssl`, `tokenizer`, `xml`, `bcmath`)
- **Composer** $\ge$ `2.x`
- **Node.js** $\ge$ `18.x` dan **npm** $\ge$ `9.x`
- **MySQL Server** $\ge$ `8.0` (disarankan MySQL 8.4)

### Langkah Instalasi & Konfigurasi

1. **Clone Repositori Proyek:**
   ```bash
   git clone https://github.com/nathavielathalitaaa/WIA_telkomsel.git
   cd WIA_telkomsel/tanggap-terang
   ```

2. **Pasang Dependensi Backend & Frontend:**
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Berkas Environment (`.env`):**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi Koneksi Database di `.env`:**
   Sesuaikan parameter kredensial basis data MySQL Anda:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=tanggap_terang
   DB_USERNAME=root
   DB_PASSWORD=kata_sandi_mysql_anda
   ```

5. **Jalankan Migrasi & Seeder Database:**
   Perintah ini akan membuat 38 tabel basis data, mengisi skenario simulasi kebencanaan, akun staf demo, master provider intelijen, dan data wilayah awal:
   ```bash
   php artisan migrate --seed
   ```

6. **Kompilasi Aset Frontend (Produksi):**
   ```bash
   npm run build
   ```

### Menjalankan Aplikasi & Worker
Jalankan server aplikasi lokal menggunakan perintah Artisan:
```bash
php artisan serve --host=127.0.0.1 --port=8000
```
Buka peramban pada alamat **`http://127.0.0.1:8000`** untuk menjelajahi portal publik TanggapTerang.

---

## H. Routes and Navigation

Daftar rute penting aplikasi berdasarkan [`routes/web.php`](routes/web.php) dan [`routes/api.php`](routes/api.php):

| URL | Nama Rute | Hak Akses | Deskripsi & Kegunaan Utama |
|---|---|:---:|---|
| `/` | `public.monitor` | Publik | **Dashboard Monitor Utama:** Peta spasial Leaflet, filter wilayah, feed insiden bencana aktif |
| `/incidents/{slug}` | `public.incidents.show` | Publik | **Detail Insiden Bencana:** Kebutuhan logistik, atribusi sumber, linimasa audit pergerakan status |
| `/reports/create` | `public.reports.create` | Publik | **Pelaporan Warga Mandiri:** Formulir pelaporan tanpa login dengan pemilih wilayah 4 tingkat |
| `/aid-responses/create` | `public.aid-responses.create` | Publik | **Pencatatan Bantuan:** Dokumentasi komitmen dan realisasi logistik dari relawan/lembaga |
| `/track` | `public.track` | Publik | **Pelacakan Tiket Laporan:** Cek status verifikasi laporan berdasarkan kode tiket anonim |
| `/replay` | `public.replay` | Publik | **Replay Linimasa Bencana:** Visualisasi interaktif pergerakan situasi bencana dari waktu ke waktu |
| `/sitrep` | `public.sitrep` | Publik | **Situation Report (Sitrep):** Ringkasan statistik bencana teragregasi dan tabulasi data siap cetak |
| `/sumber-data` | `public.sources.index` | Publik | **Transparansi Sumber Data:** Status aktual 8 provider eksternal, waktu fetch, jumlah data, 3 sampel record |
| `/sumber-data/records/{record}` | `public.sources.show` | Publik | **Detail Rekaman Publik:** Tinjauan aman rekaman eksternal tanpa mengekspos payload mentah |
| `/login` | `login` | Guest | **Halaman Masuk Staf Internal:** Autentikasi untuk Administrator, Verifikator, dan Operator |
| `/internal/dashboard` | `internal.dashboard` | Auth Internal | **Dashboard Pengelolaan Internal:** Ringkasan KPI operasional, antrean verifikasi, dan distribusi insiden |
| `/internal/reports` | `internal.reports.index` | Auth Internal | **Daftar Laporan Warga:** Antrean laporan masyarakat dari lapangan yang memerlukan tindakan |
| `/internal/reports/{report}` | `internal.reports.show` | Auth Internal | **Detail Laporan Warga:** Form verifikasi/tolak/duplikat (Verifikator) atau tautkan insiden (Operator) |
| `/internal/incidents` | `internal.incidents.index` | Auth Internal | **Tabel Insiden Bencana:** Pengelolaan status operasional seluruh peristiwa bencana |
| `/internal/incidents/create` | `internal.incidents.create` | Admin / Operator | **Inisiasi Insiden Baru:** Pencatatan peristiwa bencana baru ke dalam sistem |
| `/internal/incidents/{incident}/edit` | `internal.incidents.edit` | Auth Internal | **Manajemen Insiden:** Form tabulasi verifikasi, tindak lanjut, sumber informasi, dan log audit |
| `/internal/intelligence` | `internal.intelligence.index` | **Admin Saja** | **Pusat Intelijen Sumber Data:** Panel monitoring agregasi 8 provider, pemicu *Fetch Now* live |
| `/internal/intelligence/records/{record}` | `internal.intelligence.records.show` | **Admin Saja** | **Inspeksi Raw Payload:** Penampil data JSON mentah dari API penyedia eksternal |
| `/internal/intelligence/comparisons` | `internal.intelligence.comparisons` | **Admin Saja** | **Komparasi Cuaca BMKG vs Open-Meteo:** Analisis perbedaan prakiraan cuaca pada slot waktu yang sama |
| `/internal/intelligence/logs` | `internal.intelligence.logs` | **Admin Saja** | **Audit Log Penarikan Data:** Rekaman teknis latensi milidetik, kode HTTP, dan galat koneksi |
| `/api/wilayah/provinsi` | - | Publik API | REST API 38 provinsi resmi di Indonesia |
| `/api/wilayah/{id}/kabupaten` | - | Publik API | REST API kabupaten/kota berdasarkan ID provinsi induk |
| `/api/wilayah/{id}/kecamatan` | - | Publik API | REST API kecamatan berdasarkan ID kabupaten induk |
| `/api/wilayah/{id}/desa` | - | Publik API | REST API desa/kelurahan dengan paginasi berdasarkan ID kecamatan induk |

---

## I. Testing and Quality Assurance

Kualitas kode, keandalan alur data, dan proteksi hak akses TanggapTerang diverifikasi secara berkala melalui rangkaian *automated testing* menggunakan PHPUnit pada basis data SQLite In-Memory.

### Eksekusi Automated Tests
Jalankan seluruh pengujian otomatis melalui terminal proyek:
```bash
php artisan test
```

### Cakupan Pengujian Terverifikasi
Hasil pengujian menyeluruh terakhir membuktikan **100% kelulusan**:
- **Status:** **`79 Passed, 0 Failed`**
- **Jumlah Assertion:** **`746 Assertions`**
- **Durasi Eksekusi:** **`1.92 detik`**

```
   PASS  Tests\Unit\ExampleTest (1 test)
   PASS  Tests\Feature\ExampleTest (1 test)
   PASS  Tests\Feature\WilayahImportStagingTest (8 tests)
         - Staging resume preservation, failure tracking, orphan detection, idempotent promote, dry-run
   PASS  Tests\Feature\WilayahApiTest (10 tests)
         - Hierarki 4 level provinsi->kabupaten->kecamatan->desa, paginasi, validasi parent, enum status batas
   PASS  Tests\Feature\TanggapTerangSchemaTest (11 tests)
         - Relasi Eloquent, tiket non-sekuensial unik, isolasi skenario simulasi, proteksi privasi pelapor
   PASS  Tests\Feature\InternalAuthAndManagementTest (10 tests)
         - Otentikasi login/logout, penolakan user inaktif, penegakan RBAC role verifikator vs operator vs admin
   PASS  Tests\Feature\PublicFeaturesTest (7 tests)
         - Monitor kejadian, pelaporan warga mandiri tanpa login, pelacakan tiket anonim, proteksi PII
   PASS  Tests\Feature\ReplaySitrepAndEndToEndTest (7 tests)
         - Pemutaran replay riwayat bencana, agregasi laporan situasi sitrep, workflow end-to-end, empty states
   PASS  Tests\Feature\IntelligenceCenterTest (17 tests)
         - Proteksi akses admin-only, live fetch API eksternal, deduplikasi komposit, rollback transaksi atomik,
           pencegahan pemalsuan integrasi BNPB, penanganan aman cuaca BMKG vs Open-Meteo saat field time hilang
   PASS  Tests\Feature\PublicDataSourcesTest (7 tests)
         - Render data dinamis halaman /sumber-data, transparansi status BNPB & GDELT, isolasi raw payload dari publik
```

### Kompilasi Aset Frontend
Jalankan proses build produksi untuk memvalidasi bahwa seluruh modul styling dan JavaScript terintegrasi tanpa galat:
```bash
npm run build
```
*Hasil: Berhasil dikompilasi dalam waktu < 1 detik (`public/build/assets/app-*.css` dan `public/build/assets/app-*.js`).*

---

## J. Development Progress Log

*Bagian ini mendokumentasikan rekam jejak teknis pengembangan proyek TanggapTerang secara kronologis, faktual, dan dapat dibuktikan melalui riwayat Git commit, migrasi, dan pengujian aktual.*

### Tabel Kronologi Perkembangan Proyek

| Tanggal | Area Pengembangan | Perubahan yang Dilakukan | Komponen / File Utama | Hasil & Status Verifikasi |
|:---:|---|---|---|:---:|
| **2026-10-30** | Fondasi Proyek | Inisialisasi awal repositori proyek berbasis Laravel 12 dan PHP 8.2 | Kerangka dasar aplikasi, `composer.json`, `.gitignore` | `f7026c8` (Initial Laravel 12 project) |
| **2026-10-30** | Frontend Styling | Pemasangan dan konfigurasi Tailwind CSS v4 untuk antarmuka responsif | `package.json`, `@tailwindcss/vite`, `resources/css/app.css` | `9a2cf24` (Install and configure Tailwind CSS) |
| **2026-10-30** | Spasial & Pemetaan | Integrasi library Leaflet.js v1.9.4 untuk visualisasi peta bencana | `package.json`, `resources/js/app.js` | `7fe5e15` (Install and configure Leaflet) |
| **2026-10-30** | Database & Seeder | Perbaikan relasi seeder respons bantuan idempotent untuk pengujian | `database/seeders/ResponsBantuanSeeder.php` | `6dc14d4` (Perubahan progres) |
| **2026-10-30** | Skema Data Operasional | Finalisasi 8 entitas data kebencanaan dengan foreign key cascades | `database/migrations/2026_10_30_*`, Model relasional bencana | `503d589` (Finalize database structure) |
| **2026-10-31** | Pipeline Master Wilayah | Implementasi pipeline CLI import wilayah bertahap (*Fetch $\rightarrow$ Stage $\rightarrow$ Validate $\rightarrow$ Promote*) | `ImportWilayahAdministratif.php`, tabel `wilayah_staging`, tabel `wilayah_fetch_progress` | `4209e26` (91.363 entitas wilayah nasional resmi terintegrasi tanpa orphan parent) |
| **2026-11-01** | Layanan REST API | Pembuatan REST API hierarkis 4 tingkat wilayah (Provinsi, Kabupaten, Kecamatan, Desa) | `app/Http/Controllers/Api/WilayahController.php`, `routes/api.php`, automated tests (10 passed) | `46b9c34` & `49cc57a` (REST API wilayah beroperasi dengan paginasi desa) |
| **2026-11-07** | Alur Publik & Otorisasi RBAC | Pembangunan antarmuka monitor publik, pelaporan warga mandiri tanpa login, pelacakan tiket anonim, Replay, Sitrep, serta panel verifikasi berotentikasi peran | `PublicIncidentController`, `PublicReportController`, `PublicReplayController`, `PublicSitrepController`, `EnsureInternalRole`, views publik & internal | `ed869ff` (78 file terintegrasi, 49 tests passed) |
| **2026-11-08** | Verifikasi & Respons Bantuan | Peningkatan alur pelacakan tiket, formulir pencatatan bantuan relawan, workflow verifikasi respons, dan banner simulasi | `PublicAidResponseController`, `resources/views/public/track.blade.php`, migrasi verifikasi bantuan | `4d04dd3` (Pencatatan dan verifikasi bantuan logistik tuntas) |
| **2026-11-08** | Pusat Intelijen Sumber Data | Pengembangan modul intelijen agregasi 8 provider, CLI live fetch test, deduplikasi komposit unik, proteksi transaksi database, dan sanitasi payload | `IntelligenceAggregatorService`, `IntelligenceCenterController`, 8 Provider Services, tabel `external_*` | Integrasi 8 provider berfungsi, deduplikasi teruji aman |
| **2026-11-08** | Transparansi Publik `/sumber-data` | Pembuatan halaman publik baru untuk membuktikan status integrasi API eksternal dinamis secara terbuka bagi juri tanpa login | `PublicDataSourceController`, `resources/views/public/sources/*`, penambahan tautan footer & header nav | Rute `/sumber-data` aktif dengan filter 6 kategori dan detail aman |
| **2026-11-08** | Ketahanan Render Komparasi | Perbaikan galat `Undefined array key "time"` pada komparasi cuaca BMKG vs Open-Meteo dengan penanganan defensive fallback | `resources/views/internal/intelligence/index.blade.php`, `IntelligenceAggregatorService.php`, penambahan 3 skenario test | `26d034b` (79 passed, 746 assertions, zero 500 error) |
| **2026-11-08** | Finalisasi Git & Remote Push | Pembersihan repositori, audit keamanan rahasia (0 token bocor), kompilasi aset frontend, dan push cabang `main` ke GitHub | Repositori GitHub `origin/main` | Terverifikasi sinkron penuh pada commit `26d034b` |
| **2026-11-10** | Modern Minimalist Design System | Refactoring UI/UX menyeluruh: palet warna (#173B70, #315D91, #D94350), eliminasi border dekoratif berlebih, floating action lapor di mobile, dan perbaikan pemotongan tabel bantuan desktop | Seluruh file views publik & internal, komponen layout `app.blade.php`, `internal.blade.php` | `7ba3638`, `c3b75b3`, `96d9feb` |
| **2026-11-11** | Optimasi Responsif & Navbar Desktop | Konversi kartu status sitrep mobile menjadi grid 2-kolom tanpa geser, penghapusan tagline prototype header, dan pencegahan teks navbar bertumpuk via `whitespace-nowrap` | `resources/views/public/sitrep.blade.php`, `resources/views/layouts/app.blade.php`, `layouts/internal.blade.php` | `89ca05e`, `d9c1b51` |
| **2026-11-11** | Public-First Login & Pembersihan Sumber Data | Transformasi halaman login menjadi public-first landing experience dengan hero ajakan lapor dan form petugas di bawah, serta pembersihan elemen redundan sumber data | `resources/views/auth/login.blade.php`, `resources/views/public/sources/index.blade.php` | `6ce4e78`, `4f4b324` |
| **2026-11-11** | Master Design System Alignment (`/welcome`) | Penyatuan seluruh antarmuka publik ke Master Institutional Hero Card `/welcome` (Pantau, Lacak Tiket, SitRep, Replay, Form Pelaporan, dan Respons Bantuan) serta tombol contoh tiket monokromatik bersih | `resources/views/public/*.blade.php`, `master_design_system_alignment_report.md` | `74d7fb5`, `857e795`, `626fdac` |

### Latest Update Status
- **Commit Terakhir:** `626fdac`
- **Pesan Commit:** `fix(ui): unify example ticket buttons with clean monochromatic style without rainbow colors`
- **Branch Aktif:** `main` & `feat/modern-minimalist-overnight`
- **Status Sinkronisasi:** `working tree clean` dan sinkron penuh dengan remote `origin/main` (`https://github.com/nathavielathalitaaa/WIA_telkomsel.git`).
- **Live Deployment:** [https://wia-telkomsel.ethiodeploy.app/](https://wia-telkomsel.ethiodeploy.app/) (Tervalidasi aktif).

---

## K. Limitations and Future Development

### Keterbatasan Aktual Saat Ini
1. **Ketiadaan Open API Publik BNPB:** BNPB belum menyediakan API terbuka tanpa autentikasi khusus, sehingga status integrasinya tercatat secara jujur sebagai `Belum Terintegrasi` (`not_integrated`).
2. **Ketergantungan Rate Limit GDELT:** Layanan GDELT 2.1 Doc API sewaktu-waktu dapat mengembalikan galat HTTP 429 atau tantangan Cloudflare saat frekuensi request global meningkat.
3. **Karakteristik Model Numerik Open-Meteo Flood:** Data debit sungai merupakan estimasi model hidrologi numerik global (GloFAS), bukan laporan observasi banjir darat secara visual di lapangan.
4. **Peringatan Multi-Bahaya Global GDACS:** Berskala makro internasional dan memerlukan korelasi lebih lanjut dengan institusi kebencanaan tingkat lokal di Indonesia.
5. **Data Skenario Demonstrasi:** Demi keperluan uji fitur oleh dewan juri tanpa menimbulkan kepanikan publik, sistem menggunakan data skenario simulasi lokal yang diberi label penanda jelas.

### Rencana Pengembangan Masa Depan
1. **Notifikasi Status Laporan Otomatis (SMS / WhatsApp Gateway):** Mengirimkan pembaruan status tiket laporan langsung ke nomor ponsel pelapor ketika laporan diverifikasi atau ditindaklanjuti.
2. **Kemitraan Resmi BNPB & BPBD:** Menghubungkan TanggapTerang ke jalur API resmi BNPB / BPBD provinsi apabila nota kesepahaman institusional telah disepakati.
3. **Analisis Citra Satelit Penginderaan Jauh:** Integrasi data citra satelit multispektral (Sentinel-2 / Landsat) untuk menganalisis cakupan luas genangan banjir dan kerusakan infrastruktur secara otomatis.
4. **Offline-First PWA Form:** Menyediakan formulir pelaporan lapangan berbasis Progressive Web App (PWA) dengan sinkronisasi otomatis ketika koneksi internet pulih.

---

## L. Project Team

Proyek **TanggapTerang** dirancang dan dikembangkan sebagai karya inovasi teknologi informasi kebencanaan untuk inisiatif kompetisi **WIA (Women in AI) / MIA 2026**:

- **Pengembang / Peneliti:**
  **Nathaviela Thalita** ([@nathavielathalitaaa](https://github.com/nathavielathalitaaa))
  *SMK Telkom Sidoarjo*
  Surel: `nathaviela_thalita_ts7_24@student.smktelkom-sda.sch.id`

- **Repositori Resmi:**
  [https://github.com/nathavielathalitaaa/WIA_telkomsel.git](https://github.com/nathavielathalitaaa/WIA_telkomsel.git)

---
*Dokumentasi ini disusun secara objektif dan akurat berdasarkan implementasi kode aktual TanggapTerang.*

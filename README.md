# TanggapTerang

## Deskripsi
TanggapTerang adalah platform transparansi dan pemantauan respons bencana yang memungkinkan masyarakat melihat perkembangan penanganan kebutuhan, pihak yang merespons, dan kebutuhan yang belum terpenuhi. Platform ini bukan aplikasi penyalur bantuan maupun kanal pelaporan darurat, melainkan alat observasi dan evaluasi proses penanganan bencana.

## Tujuan & Permasalahan
- **Tujuan:** Menyajikan data yang dapat dipertanggungjawabkan tentang respons bencana, sehingga masyarakat dapat memantau, menelusuri, dan mengevaluasi tindakan yang diambil.
- **Permasalahan yang diangkat:** Kurangnya visibilitas terhadap proses penanganan kebutuhan bencana, sulitnya melacak bantuan yang dijanjikan, dikirim, dan diterima, serta kebutuhan akan data yang dapat dipertanggungjawabkan bagi semua pemangku kepentingan.

## Fitur Utama (Rencana)
- Pengumpulan laporan kondisi bencana oleh warga/relawan.
- Verifikasi laporan dan penggabungan laporan serupa.
- Pembuatan tiket kebutuhan berdasarkan laporan terverifikasi.
- Penugasan tiket kepada responden yang terverifikasi.
- Pencatatan bantuan yang dijanjikan, dikirim, dan dikonfirmasi.
- Konfirmasi penerimaan bantuan oleh warga atau koordinator.
- Riwayat publik status tiket dan bantuan.

> **Catatan:** Fitur-fitur di atas belum diimplementasikan secara penuh pada kode aplikasi; repository ini berisi fondasi database, model, migrasi, dan seeders.

## Teknologi yang Digunakan
- **Backend:** Laravel 12, PHP, MySQL, Eloquent ORM
- **Frontend (rencana):** Blade, Tailwind CSS v4, JavaScript, Vite
- **Peta & Lokasi:** Leaflet.js, OpenStreetMap

## Struktur Database (Entitas Utama)
- **KejadianBencana** – data kejadian bencana.
- **LaporanWarga** – laporan kondisi yang diajukan warga/relawan.
- **TiketKebutuhan** – kumpulan kebutuhan yang dihasilkan dari laporan terverifikasi.
- **TitikBantuan** – lokasi fisik tempat bantuan dapat disalurkan; relasi many‑to‑one ke `KejadianBencana`.
- **ResponsBantuan** – catatan bantuan yang dijanjikan/dikirim; dapat terkait ke `TitikBantuan` (nullable).
- **TiketLaporan** (pivot) – mengaitkan `TiketKebutuhan` dengan `LaporanWarga` (unique constraint pada kombinasi).
- **RiwayatStatusTiket** – riwayat perubahan status tiket.
- **KonfirmasiPenerimaan** – konfirmasi penerimaan bantuan oleh warga atau koordinator (hanya untuk tiket yang sudah memiliki bantuan terkirim).

## Struktur Direktori Penting
```
app/
  Models/                # Eloquent models dengan relasi

database/
  migrations/            # Semua migration, termasuk foreign key & unique constraint
  seeders/               # Seeders idempotent, berurutan sesuai dependensi
  report/                # Laporan audit refactor
resources/
  css/app.css            # Tailwind (tidak diubah dalam fase ini)
README.md                # Dokumentasi proyek (diupdate)
```

## Persyaratan & Instalasi
1. **Clone repository**
   ```bash
   git clone <repository-url>
   cd tanggap-terang
   ```
2. **Install dependencies**
   ```bash
   composer install
   npm install && npm run build
   ```
3. **Konfigurasi environment**
   - Salin file `.env.example` menjadi `.env` dan sesuaikan koneksi database MySQL.
   - Generate key:
     ```bash
     php artisan key:generate
     ```
4. **Migrasi & Seeding** (hanya saat siap, belum dijalankan otomatis)
   ```bash
   php artisan migrate
   php artisan db:seed --class=DatabaseSeeder
   ```
   Seeders menggunakan pendekatan **idempotent** (upsert / deterministic mapping) sehingga dapat dijalankan berulang kali tanpa menghasilkan duplikat.

## Status Pengembangan Saat Ini
- **Fondasi proyek** selesai: struktur basis data, model Eloquent, migration, dan seeders sudah final.
- **Logika aplikasi** (controller, route, UI) belum mulai dikerjakan.
- **Data simulasi** dalam seeders bersifat contoh untuk prototipe, bukan data nyata.

## Catatan Data Simulasi
Seeders menghasilkan contoh data:
- 10 tiket kebutuhan, 6 titik bantuan, 15 respons bantuan, dll.
- Semua relasi foreign key valid; `waktu_dikirim` hanya terisi untuk status `dikirim` atau `dikonfirmasi`.
- Konfirmasi penerimaan hanya dibuat untuk tiket yang memiliki bantuan terkirim atau terkonfirmasi.

---
*Dokumen ini dibuat untuk membantu kontributor memahami keadaan saat ini dan memulai pengembangan fitur selanjutnya.*

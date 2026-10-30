# Database Refactor Report

## Summary of Changes

| File | Change Description |
|------|--------------------|
| `database/seeders/DatabaseSeeder.php` | Menggunakan `User::firstOrCreate` untuk akun uji coba agar seeder aman dijalankan ulang, serta mendaftarkan `TitikBantuanSeeder` dalam urutan eksekusi dependensi yang benar. |
| `database/seeders/KejadianBencanaSeeder.php` | Menggunakan `upsert` berdasarkan primary key `id` agar aman dijalankan berulang kali tanpa konflik duplikasi. |
| `database/seeders/LaporanWargaSeeder.php` | Menggunakan `upsert` berdasarkan primary key `id` untuk memastikan data simulasi laporan warga bersifat idempotent. |
| `database/seeders/TiketKebutuhanSeeder.php` | Menggunakan `upsert` berdasarkan primary key `id` untuk menjaga integritas data tiket pada eksekusi berulang. |
| `database/seeders/RespondenSeeder.php` | Menggunakan `upsert` berdasarkan primary key `id` untuk data responden terverifikasi. |
| `database/seeders/TitikBantuanSeeder.php` | Membuat 6 posko titik bantuan terdistribusi pada kejadian bencana dengan `upsert` berbasis `id`. |
| `database/seeders/TiketLaporanSeeder.php` | Memfilter laporan dengan `status_verifikasi != 'ditolak'`, memetakan secara deterministik, dan menggunakan `upsert` pada kunci unik komposit `(tiket_kebutuhan_id, laporan_warga_id)`. |
| `database/seeders/ResponsBantuanSeeder.php` | Menentukan `waktu_dikirim` hanya saat status `dikirim` atau `dikonfirmasi` (null untuk `dijanjikan`), menghubungkan titik bantuan yang valid secara opsional, dan menggunakan `upsert` berbasis `id`. |
| `database/seeders/RiwayatStatusTiketSeeder.php` | Menyusun urutan riwayat status logis kronologis (`baru -> dalam_proses -> selesai`), menautkan ke ID user valid (`users`), dan menggunakan `upsert` berbasis `id`. |
| `database/seeders/KonfirmasiPenerimaanSeeder.php` | Hanya membuat konfirmasi penerimaan untuk tiket yang memiliki respons bantuan berstatus `dikirim` atau `dikonfirmasi`, dan menggunakan `upsert` berbasis `id`. |
| `app/Models/TitikBantuan.php` | Model baru dengan relasi `belongsTo` ke `KejadianBencana` dan `hasMany` ke `ResponsBantuan`. |
| `app/Models/ResponsBantuan.php` | Menambahkan `titik_bantuan_id` pada `$fillable` dan mendefinisikan relasi `titikBantuan()`. |
| `app/Models/KejadianBencana.php` | Menambahkan relasi `titikBantuan()`. |
| `database/migrations/2026_10_30_120008_create_titik_bantuan_table.php` | Migrasi pembuatan tabel `titik_bantuan` dengan foreign key `kejadian_bencana_id` cascade on delete. |
| `database/migrations/2026_10_30_120009_add_titik_bantuan_id_to_respons_bantuan_table.php` | Menambahkan kolom `titik_bantuan_id` nullable dengan foreign key `SET NULL` on delete pada tabel `respons_bantuan`. |
| `database/migrations/2026_10_30_120010_add_unique_constraint_to_tiket_laporan_table.php` | Menambahkan unique constraint pada kombinasi `(tiket_kebutuhan_id, laporan_warga_id)` di tabel pivot `tiket_laporan`. |

## Verification Checklist

- **Struktur Titik Bantuan**: Kolom `id`, `kejadian_bencana_id`, `nama`, `alamat`, `latitude`, `longitude`, `keterangan`, timestamps dengan foreign key cascade ke `kejadian_bencana.id`.
- **Kolom `respons_bantuan.titik_bantuan_id`**: Bersifat nullable dan foreign key dikonfigurasi `SET NULL` saat titik bantuan dihapus.
- **Pivot `tiket_laporan`**: Memiliki unique constraint pada pasangan `(tiket_kebutuhan_id, laporan_warga_id)`.
- **Idempotensi Seeder**: Seluruh seeder menggunakan pendekatan `upsert` / `firstOrCreate` sehingga aman dijalankan berulang kali tanpa kegagalan constraint duplikasi.
- **Aturan Bisnis & Integritas Relasi**:
  - Laporan berstatus `ditolak` tidak pernah dihubungkan ke tiket kebutuhan.
  - Nilai `waktu_dikirim` hanya terisi untuk respons yang statusnya `dikirim` atau `dikonfirmasi`.
  - Nilai `titik_bantuan_id` dapat bernilai null untuk penyaluran langsung atau terisi ID posko valid.
  - Konfirmasi penerimaan hanya dibuat untuk tiket yang terbukti memiliki respons bantuan yang dikirim/dikonfirmasi.
  - Foreign key `user_id` pada riwayat status hanya menunjuk ke pengguna yang benar-benar ada di tabel `users`.

## Catatan Eksekusi (Panduan bagi Pengguna)
> Catatan: Sesuai batasan tahap ini, migrasi dan seeder belum dieksekusi secara aktual pada database. Perintah berikut dapat dijalankan saat Anda siap:

```bash
# Menjalankan migrasi
php artisan migrate

# Menjalankan seeder
php artisan db:seed --class=DatabaseSeeder
```

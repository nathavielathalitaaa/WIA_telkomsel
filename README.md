# TanggapTerang

**Lihat siapa yang merespons, kapan, dan di mana bantuan belum sampai.**

TanggapTerang adalah platform transparansi dan koordinasi respons bencana yang membantu masyarakat memantau informasi kebutuhan, status penanganan, serta distribusi bantuan di wilayah terdampak. Platform ini dirancang agar proses respons bencana dapat diamati dan dievaluasi oleh masyarakat melalui informasi yang terstruktur, peta persebaran, dan linimasa penanganan.

> **Catatan:** TanggapTerang merupakan platform transparansi dan koordinasi informasi, bukan layanan pelaporan darurat maupun penyalur bantuan secara langsung.

## Teknologi yang Digunakan

| Teknologi | Kegunaan |
|---|---|
| Laravel 12 | Backend dan pengelolaan aplikasi |
| Blade | Template dan antarmuka web |
| Tailwind CSS v4 | Styling antarmuka |
| MySQL | Database |
| Leaflet.js | Menampilkan peta interaktif |
| OpenStreetMap | Sumber peta |
| JavaScript | Interaksi pada sisi pengguna |
| Vite | Pengelolaan dan build aset frontend |

## Instalasi dan Menjalankan Proyek

### 1. Clone Repository

```bash
git clone https://github.com/nathavielathalitaaa/WIA_telkomsel.git
```

Masuk ke direktori proyek:

```bash
cd WIA_telkomsel
```

### 2. Install Dependencies

Install dependencies PHP:

```bash
composer install
```

Install dependencies JavaScript:

```bash
npm install
```

### 3. Konfigurasi Environment

Salin file environment:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

### 4. Konfigurasi Database

Buat database MySQL dengan nama:

```sql
CREATE DATABASE tanggap_terang;
```

Kemudian sesuaikan konfigurasi database pada file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tanggap_terang
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Sesuaikan `DB_USERNAME` dan `DB_PASSWORD` dengan akun MySQL yang digunakan.

### 5. Jalankan Database Migration

```bash
php artisan migrate
```

### 6. Build Frontend Assets

```bash
npm run build
```

### 7. Jalankan Aplikasi

Jalankan server Laravel:

```bash
php artisan serve
```

Aplikasi dapat diakses melalui:

```text
http://127.0.0.1:8000
```

Untuk pengembangan frontend, jalankan Vite dalam terminal terpisah:

```bash
npm run dev
```

## Catatan Penggunaan

- Gunakan data simulasi untuk kebutuhan demonstrasi dan pengembangan prototipe.
- Informasi yang ditampilkan perlu memperhatikan privasi pelapor dan masyarakat terdampak.
- Status belum terdokumentasi tidak selalu berarti tidak ada tindakan di lapangan.
- Untuk kondisi darurat yang mengancam keselamatan, masyarakat harus menghubungi layanan darurat atau instansi berwenang.

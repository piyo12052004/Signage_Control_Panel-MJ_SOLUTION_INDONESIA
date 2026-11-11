# Backend Documentation

## 1. Teknologi yang Digunakan

Backend aplikasi ini menggunakan teknologi berikut:

| Teknologi | Versi / Keterangan |
| ----------|--------------------|
| PHP       |     8.4.25         |
| Laravel   | Laravel Framework  |
| Database  | PostgreSQL (pgsql) |
| WebSocket | Laravel Reverb     |
| Dependency Manager | Composer  |

## 2. Menjalankan Backend (Development)

Sebelum menjalankan backend, pastikan PHP, Composer, dan PostgreSQL sudah terinstal dan berjalan.

### 2.1 Masuk ke Direktori Backend

```bash
cd backend
```

### 2.2 Install Dependency

Jalankan perintah:

```bash
composer install
```
```bash
composer update
```

### 2.3 Konfigurasi Environment

Pastikan file `.env` sudah tersedia.

Jika belum, salin dari file `.env.example`:

```bash
cp .env.example .env
```

Buat application key:

```bash
php artisan key:generate
```

Atur konfigurasi database PostgreSQL pada file `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=nama_database
DB_USERNAME=postgres
DB_PASSWORD=password_database
```

Sesuaikan nama database, username, dan password dengan konfigurasi PostgreSQL yang digunakan.

### 2.4 Menjalankan Migration

Untuk membuat tabel database sesuai migration Laravel:

```bash
php artisan migrate
```

### 2.5 Menjalankan Laravel Backend

Jalankan perintah:

```bash
php artisan serve
```

```text
http://127.0.0.1:8000
```

## 3. Menjalankan WebSocket dengan Laravel Reverb

```bash
php artisan reverb:start
```

Secara default, Reverb berjalan pada port yang sudah dikonfigurasi, misalnya:

```text
http://127.0.0.1:8080
```

### Kenapa Menggunakan WebSocket?

WebSocket digunakan agar backend dapat mengirimkan informasi terbaru ke frontend secara real-time tanpa frontend harus terus-menerus melakukan request HTTP untuk memeriksa perubahan data.

Pada aplikasi Signage Control Panel, WebSocket digunakan untuk mengirimkan perubahan status device.

Contohnya:

1. Device mengirimkan heartbeat ke backend.
2. Backend menerima heartbeat dan memperbarui status device menjadi `online`.
3. Backend mengirimkan event `DeviceStatusUpdated` melalui Laravel Reverb.
4. Frontend menerima event melalui WebSocket.
5. Dashboard langsung memperbarui status device tanpa perlu melakukan refresh halaman.

Jika device tidak lagi mengirimkan heartbeat dan backend mendeteksi device tersebut offline, backend juga dapat mengirimkan event perubahan status melalui WebSocket.

### Keuntungan WebSocket

- **Real-time:** Perubahan status device dapat langsung ditampilkan pada dashboard.
- **Efisien:** Mengurangi kebutuhan frontend melakukan polling HTTP secara terus-menerus.
- **Komunikasi dua arah:** Koneksi WebSocket memungkinkan komunikasi antara client dan server.
- **Responsif:** Dashboard dapat menampilkan perubahan status device tanpa reload halaman.

## 4. Menjalankan Laravel Scheduler

Jalankan perintah berikut pada terminal baru:

```bash
php artisan schedule:work
```
**Penting:** Scheduler dan WebSocket memiliki fungsi yang berbeda:

- Laravel Scheduler bertanggung jawab menjalankan pemeriksaan dan tugas terjadwal.
- Laravel Reverb bertanggung jawab mengirimkan event perubahan data ke frontend.

Keduanya dibutuhkan agar proses deteksi status device dan pembaruan dashboard berjalan sesuai mekanisme aplikasi.

## 5. Menjalankan Seluruh Backend

Untuk development, jalankan ketiga perintah berikut pada terminal yang berbeda.

**Terminal 1 - Laravel Backend (API)**

```bash
php artisan serve
```

**Terminal 2 - WebSocket Server**

```bash
php artisan reverb:start
```

**Terminal 3 - Laravel Scheduler**

```bash
php artisan schedule:work
```

Ketiga proses tersebut harus tetap berjalan selama aplikasi sedang digunakan dalam lingkungan development.

## 6. Ringkasan Fungsi Perintah

| Perintah | Fungsi |
|---|---|
| `composer install` | Menginstal dependency aplikasi |
| `composer update` | Memperbarui dependency aplikasi |
| `php artisan key:generate` | Membuat application key |
| `php artisan migrate` | Menjalankan database migration |
| `php artisan serve` | Menjalankan HTTP server Laravel |
| `php artisan reverb:start` | Menjalankan server WebSocket |
| `php artisan schedule:work` | Menjalankan Laravel Scheduler |

---

**Kesimpulan**

Backend aplikasi Signage Control Panel menggunakan Laravel sebagai REST API, PostgreSQL sebagai database, dan Laravel Reverb sebagai server WebSocket untuk komunikasi real-time.

Laravel Scheduler digunakan untuk menjalankan tugas terjadwal, khususnya pemeriksaan status device berdasarkan heartbeat.

Dengan kombinasi ketiga proses tersebut, aplikasi dapat mengelola data device dan menampilkan perubahan status secara real-time pada dashboard.
# Frontend Documentation

## 1. Teknologi yang Digunakan

Frontend aplikasi Signage Control Panel menggunakan teknologi berikut:

| Teknologi | Keterangan |
|---|---|
| Nuxt.js | Framework frontend berbasis Vue.js |
| TypeScript | Bahasa pemrograman dengan static typing |
| Vue.js | Framework untuk membangun antarmuka pengguna |
| Nuxt UI | Component UI untuk membangun tampilan aplikasi |
| Bun | JavaScript runtime dan package manager |
| Laravel REST API | Komunikasi data dengan backend |
| Laravel Reverb | WebSocket untuk komunikasi real-time |

## 2. Menjalankan Frontend (Development)

### 2.1 Masuk ke Direktori Frontend

```bash
cd frontend
```

Sesuaikan nama direktori dengan struktur project yang digunakan.

### 2.2 Install Dependency

Aplikasi menggunakan Bun sebagai package manager.

Jalankan perintah:

```bash
bun install
```

Perintah ini digunakan untuk menginstal seluruh dependency yang dibutuhkan oleh aplikasi berdasarkan file `package.json` dan `bun.lock`.

### 2.3 Konfigurasi Environment (.env)

Frontend membutuhkan konfigurasi environment untuk menghubungkan aplikasi dengan backend Laravel dan server WebSocket Reverb.

Buat file `.env` pada direktori frontend.

### Contoh Konfigurasi .env

```env
# ============================================================
# API BACKEND
# ============================================================
NUXT_PUBLIC_API_BASE=http://127.0.0.1:8000/api/v1

# ============================================================
# LARAVEL REVERB - WEBSOCKET
# ============================================================
NUXT_PUBLIC_REVERB_APP_KEY=your_reverb_app_key
NUXT_PUBLIC_REVERB_HOST=127.0.0.1
NUXT_PUBLIC_REVERB_PORT=8080
NUXT_PUBLIC_REVERB_SCHEME=http
```

### Penjelasan Konfigurasi

#### A. API Backend

| Variabel | Fungsi |
|---|---|
| `NUXT_PUBLIC_API_BASE` | Alamat REST API Laravel yang digunakan frontend untuk mengambil dan mengirim data |

Contoh:

```env
NUXT_PUBLIC_API_BASE=http://127.0.0.1:8000/api/v1
```

Frontend menggunakan alamat tersebut untuk melakukan request ke endpoint backend, seperti autentikasi, pengelolaan device, dan pengambilan data konten.

#### B. WebSocket Reverb

| Variabel | Fungsi |
|---|---|
| `NUXT_PUBLIC_REVERB_APP_KEY` | Application key untuk koneksi Reverb |
| `NUXT_PUBLIC_REVERB_HOST` | Host server WebSocket |
| `NUXT_PUBLIC_REVERB_PORT` | Port server WebSocket |
| `NUXT_PUBLIC_REVERB_SCHEME` | Protokol koneksi WebSocket, HTTP atau HTTPS |

Konfigurasi ini digunakan agar frontend dapat terhubung dengan Laravel Reverb dan menerima event perubahan status device secara real-time.

**Catatan:** Application key boleh digunakan di frontend, tetapi `REVERB_APP_SECRET` dan `JWT_SECRET` tidak boleh dimasukkan ke dalam konfigurasi publik frontend.

## 3. Menjalankan Frontend

Setelah dependency selesai diinstal dan konfigurasi `.env` sudah dibuat, jalankan:

```bash
bun run dev
```

Secara default, Nuxt development server berjalan pada alamat:

```text
http://localhost:3000
```

Frontend dapat diakses melalui browser menggunakan alamat tersebut.

## 4. Integrasi Frontend dengan Backend

Frontend berkomunikasi dengan backend melalui dua mekanisme utama:

### A. REST API

REST API digunakan untuk komunikasi request dan response antara frontend dengan backend Laravel.

Contoh penggunaan:

- Login dan autentikasi pengguna.
- Mengambil daftar device.
- Menambahkan dan mengubah data device.
- Mengambil data konten.
- Mengelola informasi aplikasi.

Frontend mengirimkan HTTP request ke backend dan menerima response dalam format JSON.

### B. WebSocket (Laravel Reverb)

WebSocket digunakan untuk menerima perubahan data secara real-time dari backend.

Pada aplikasi Signage Control Panel, WebSocket digunakan untuk memantau perubahan status device.

Contoh alur komunikasi:

1. Device Client mengirimkan heartbeat ke backend.
2. Backend memperbarui status device pada database.
3. Backend mengirimkan event melalui Laravel Reverb.
4. Frontend menerima event melalui koneksi WebSocket.
5. Dashboard memperbarui status device secara otomatis tanpa reload halaman.

## 5. Menjalankan Seluruh Aplikasi (Development)

Untuk menjalankan aplikasi secara lokal, backend dan frontend harus dijalankan secara bersamaan.

### A. Backend

Jalankan pada terminal yang berbeda:

**Terminal 1 - Laravel API**

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

### B. Frontend

Jalankan pada terminal frontend:

```bash
bun install
bun run dev
```

### C. Alamat Aplikasi

| Layanan | Alamat |
|---|---|
| Frontend | http://localhost:3000 |
| Backend API | http://127.0.0.1:8000 |
| WebSocket | 127.0.0.1:8080 |

Pastikan seluruh proses backend dan frontend tetap berjalan selama development.

## 6. Build Frontend

Untuk membuat build aplikasi frontend, jalankan:

```bash
bun run build
```

Perintah ini digunakan untuk mengompilasi aplikasi Nuxt menjadi hasil build yang siap digunakan untuk deployment.

Untuk menjalankan hasil build secara lokal:

```bash
bun run preview
```

## 7. Ringkasan Perintah

| Perintah | Fungsi |
|---|---|
| `bun install` | Menginstal dependency frontend |
| `bun run dev` | Menjalankan development server |
| `bun run build` | Membuat production build |
| `bun run preview` | Menjalankan preview hasil build |

## 8. Alur Komunikasi Frontend

```text
             FRONTEND
           NUXT.JS + TS
                 |
         +-------+-------+
         |               |
         v               v
      REST API        WEBSOCKET
         |               |
         v               v
    LARAVEL API      LARAVEL REVERB
         |               ^
         v               |
     POSTGRESQL       BROADCAST EVENT
         ^
         |
     DEVICE CLIENT
      HEARTBEAT
```

Frontend menggunakan REST API untuk mengambil dan mengelola data aplikasi, sedangkan WebSocket digunakan untuk menerima perubahan status device secara real-time.

---

**Kesimpulan**

Frontend Signage Control Panel dibangun menggunakan Nuxt.js dan TypeScript dengan Bun sebagai package manager.

REST API Laravel digunakan untuk komunikasi data antara frontend dan backend, sedangkan Laravel Reverb digunakan untuk menerima event secara real-time, khususnya perubahan status device pada dashboard.
# QuickDine - Smart Self-Ordering & Kitchen Display System

**QuickDine** adalah sistem pemesanan makanan berbasis QR Code terpadu yang dirancang untuk modernisasi operasional restoran. Dibangun untuk Tugas Akhir/Capstone Project, aplikasi ini menghubungkan pelanggan, dapur (Kitchen Display System), dan manajemen admin dalam satu ekosistem _real-time_ yang efisien.

---

## Fitur Utama

### Customer Side (Frontend)

- **Pemesanan via QR Code:** Pelanggan cukup men-scan QR code di meja untuk langsung memesan tanpa perlu bantuan pelayan.
- **Menu Dinamis:** Kategori dan produk yang mudah ditelusuri.
- **Integrasi Midtrans:** Mendukung pembayaran digital instan (QRIS, GoPay, dll).
- **Panggil Pelayan (Call Waiter):** Tombol darurat persisten untuk meminta bantuan staf kapan saja.
- **Sistem Ulasan (Rating):** Pelanggan dapat memberikan masukan dan rating bintang setelah pesanan selesai.

### Kitchen Display System (KDS)

- **Real-time Sinkronisasi:** Menggunakan teknologi _WebSockets_ (Laravel Reverb) untuk memperbarui pesanan tanpa perlu me-_refresh_ halaman.
- **Manajemen Antrean Cerdas:** Panel terpisah untuk antrean baru, pesanan sedang diproses, dan pesanan selesai.
- **Notifikasi Panggil Pelayan:** Panel peringatan merah menyala berdenyut di bagian atas layar KDS saat pelanggan membutuhkan bantuan.
- **Kalkulator Kasir Tunai:** Fitur penghitung uang kembalian instan dengan saran nominal pembayaran.

### Admin Panel

- **Dashboard Analitik:** Grafik pendapatan interaktif (_Chart.js_) dan ringkasan metrik harian.
- **Manajemen Master Data:** Kelola data Meja, Menu, Kategori, dan Staf.
- **Manajemen QR Code:** Generator QR code bawaan (siap cetak fisik).
- **Laporan Otomatis:** Ekspor laporan penjualan berdasar periode tertentu ke dalam format **PDF**.
- **Monitoring Ulasan:** Pantau performa pelayanan melalui _feedback_ pelanggan secara langsung.

---

## Tech Stack

- **Backend:** Laravel 13 (PHP 8.3+)
- **Frontend:** Blade Templating Engine, Tailwind CSS v4 (Vite), Vanilla JavaScript
- **Real-Time Engine:** Laravel Reverb & Laravel Echo
- **Database:** MySQL
- **Visualisasi Data:** Chart.js
- **Laporan/Export:** Barryvdh/DomPDF (PDF) & Maatwebsite/Excel (Excel)
- **Payment Gateway:** Midtrans (Sandbox/Production)

---

## Panduan Instalasi (Development)

Untuk menjalankan proyek ini di komputer lokal Anda, ikuti langkah-langkah berikut:

### 1. Persiapan Awal

Pastikan Anda sudah menginstal:

- PHP (minimal versi 8.3)
- Composer
- Node.js & NPM
- MySQL/MariaDB (XAMPP/Laragon)

### 2. Kloning dan Instalasi Dependencies

```bash
git clone <repository_url> quickdine
cd quickdine
composer install
npm install
```

### 3. Konfigurasi Lingkungan (Environment)

Buat file konfigurasi environment Anda:

```bash
cp .env.example .env
php artisan key:generate
```

Buka file `.env` dan atur konfigurasi database serta API Midtrans Anda:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=quickdine
DB_USERNAME=root
DB_PASSWORD=

# Konfigurasi Midtrans
MIDTRANS_MERCHANT_ID=isi_dengan_merchant_id_anda
MIDTRANS_CLIENT_KEY=isi_dengan_client_key_anda
MIDTRANS_SERVER_KEY=isi_dengan_server_key_anda
MIDTRANS_IS_PRODUCTION=false

# Konfigurasi Reverb (Otomatis dari Laravel)
REVERB_APP_ID=...
REVERB_APP_KEY=...
```

### 4. Database Migration & Seeding

_(Catatan: Pastikan Anda sudah membuat database kosong bernama `quickdine` di phpMyAdmin)_

```bash
php artisan migrate:fresh --seed
```

_Perintah ini akan membuat semua struktur tabel dan mengisi data awal (dummy data) termasuk akun admin utama._

### 5. Menjalankan Aplikasi

Aplikasi ini membutuhkan **3 terminal (command prompt) terpisah** yang berjalan secara bersamaan agar fitur _real-time_ berfungsi:

**Terminal 1 (Menjalankan Web Server):**

```bash
php artisan serve --host=0.0.0.0
```

**Terminal 2 (Menjalankan WebSockets/Reverb):**

```bash
php artisan reverb:start
```

**Terminal 3 (Menjalankan Vite Asset Bundler):**

```bash
npm run dev
```

---

## Akun Default (Seeder)

Setelah Anda menjalankan `php artisan migrate --seed`, Anda dapat masuk menggunakan akun berikut:

- **Email:** `admin@quickdine.com`
- **Password:** `password123`

---

## Tips Testing QR Code di HP (Penting!)

Jika Anda ingin mengetes QR Code langsung menggunakan kamera HP:

1. Pastikan komputer dan HP Anda berada di jaringan Wi-Fi yang sama.
2. Saat menjalankan server, pastikan menggunakan perintah `php artisan serve --host=0.0.0.0`.
3. Akses admin panel menggunakan **IP Address komputer Anda** (contoh: `http://192.168.0.5:8000`), JANGAN gunakan `localhost` atau `127.0.0.1`.
4. Cetak QR code dari halaman tersebut, dan hasil QR akan bisa di-scan oleh HP Anda.

---


# 📝 TOEFL Prediction Test – Aplikasi Ujian Online

[![Laravel](https://img.shields.io/badge/Laravel-10.x-red.svg)](https://laravel.com)  
[![PHP](https://img.shields.io/badge/PHP-8.1+-blue.svg)](https://php.net)  
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-4.x-teal.svg)](https://tailwindcss.com)  
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)](https://mysql.com)

> Aplikasi prediksi ujian TOEFL berbasis web, dibangun menggunakan **Laravel 10** dan **TailwindCSS 4**, dilengkapi fitur **anti-kecurangan**, **manajemen soal paket**, dan **UI modern**.

---

## 🚀 Fitur Utama

### 👩‍💼 Panel Admin
- 📊 **Dashboard Real-Time**: Statistik peserta, batch, dan hasil
- 📦 **Manajemen Paket Soal**:
  - Target 75 soal (15 Listening + 30 Structure + 30 Reading)
  - Drag & drop interface builder
  - Indikator kelengkapan soal
- 🏦 **Bank Soal**: CRUD per section, dukung audio (Listening)
- 📆 **Manajemen Batch**: Jadwal ujian & assignment paket
- 🔑 **Kode Registrasi Unik**: Untuk tiap peserta
- 👥 **Manajemen Pengguna**: Reset password, status peserta
- 📄 **Laporan Hasil**: Export ke Excel/PDF

### 🧑‍🎓 Portal Peserta
- 🔐 **Registrasi Aman**: Menggunakan kode unik sekali pakai
- 🗓️ **Dashboard**: Informasi batch dan countdown ujian
- 📝 **Ujian Terstruktur**:
  - 3 Section: Listening → Structure → Reading
  - Timer server-side tiap section
  - Auto-save tiap 30 detik
  - Navigasi dalam section
  - Grid indikator soal

### 🛡️ Fitur Anti-Kecurangan
- 👁️ **Deteksi Tab Switch**: Maks 3x → auto-submit
- 🔈 **Audio Sekali Putar**: Listening clip hanya 1x
- 🧱 **Proteksi Shortcut**: F12, Ctrl+C/V, right-click
- 📱 **Concurrent Login Detection**
- 🕓 **Timer Sinkronisasi Server**
- 🔄 **Resume Ujian** saat koneksi terputus

### 🎨 Desain Modern & Responsif
- 📱 **Mobile First**: Fully responsive
- 🌙 **Dark / Light Mode**
- 💫 **Smooth Transitions** & animations
- ♿ **Aksesibilitas Tinggi**
- ⏳ **Skeleton Loader & Progress Indicator**

---

## 🧰 Stack Teknologi

| Layer | Teknologi |
|-------|-----------|
| **Backend** | Laravel 10 (PHP 8.1+) |
| **Frontend** | Vite 5 + TailwindCSS 4 + Alpine.js |
| **Database** | MySQL 8.0+ |
| **Authentication** | Laravel Sanctum |
| **Storage** | Local Disk |
| **Build Tool** | Vite (HMR) |

---

## 💻 Persyaratan Sistem

### Untuk Pengembangan
- PHP 8.1+
- MySQL 8.0+
- Composer
- Node.js 18+
- Ekstensi PHP: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML

### Untuk Produksi (Shared Hosting)
- PHP 8.1+ dengan ekstensi
- Composer (untuk dependency)
- Node.js (untuk build aset)
- Akses SSH/Terminal (jika tersedia)

---

## 🔧 Instalasi & Konfigurasi

### 1️⃣ Setup Local (WAMP/XAMPP)

```bash
# Buat database
http://localhost/phpmyadmin
Database: toefl_prediction_test
```

### 2️⃣ Instalasi Dependency

```bash
composer install
npm install
```

### 3️⃣ Konfigurasi .env

```bash
cp .env.example .env
php artisan key:generate
```

Edit bagian berikut:

```env
APP_NAME="TOEFL Prediction Test"
APP_URL=http://localhost:8000

DB_DATABASE=toefl_prediction_test
DB_USERNAME=root
DB_PASSWORD=
```

### 4️⃣ Migrasi & Seeder

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
```

### 5️⃣ Build Aset

```bash
# Development
npm run dev

# Production
npm run build
```

### 6️⃣ Menjalankan Server

```bash
php artisan serve
```

Akses: [http://localhost:8000](http://localhost:8000)

---

## 👥 Akun Demo

| Role | Email | Password | Kode Registrasi |
|------|-------|----------|-----------------|
| Admin | `admin@toefl.com` | `admin123` | - |
| Student | `student@toefl.com` | `student123` | `DEMO001` |

---

## 🌐 Deployment ke Shared Hosting (cPanel)

### Langkah Umum:

1. Build aset: `npm run build`
2. Upload file ke `public_html/`
3. Edit `.env` untuk `APP_URL`, `DB_*`, dll.
4. Jalankan command via terminal/SSH:
   ```bash
   composer install --optimize-autoloader --no-dev
   php artisan migrate --force
   php artisan db:seed --force
   php artisan storage:link
   ```
5. Set permission:
   ```bash
   chmod -R 755 storage/
   chmod -R 755 bootstrap/cache/
   ```

6. Konfigurasi `.htaccess` jika perlu.

---

## 🧪 Testing & QA

### ✅ Unit Testing

```bash
php artisan test
php artisan test --filter=ExamTest
php artisan test --coverage
```

### ✅ Manual Checklist

- [x] Login Admin & Student
- [x] Create Paket & Soal
- [x] Ujian 3 Section
- [x] Auto-save & Timer Sync
- [x] Anti-cheat bekerja

---

## 🛠 Monitoring & Maintenance

### Log
- Aplikasi: `storage/logs/laravel.log`
- Server: `/var/log/apache2/error.log`

### Performa
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Backup
```bash
mysqldump -u user -p toefl_prediction_test > backup.sql
mysql -u user -p toefl_prediction_test < backup.sql
```

---

## 🐛 Troubleshooting

| Masalah | Solusi |
|--------|--------|
| Composer Error | `php -d memory_limit=2G composer install` |
| Permission Denied | `chmod -R 755 storage/ bootstrap/cache/` |
| DB Connection | Cek `.env` dan status MySQL |
| Vite Build | Hapus `node_modules`, install ulang |
| Timer Bermasalah | Pastikan timezone `Asia/Jakarta` |

---

## 🤝 Kontribusi

```bash
# Fork repo
# Buat branch baru
git checkout -b feature/NamaFitur

# Commit
git commit -m "Menambahkan fitur baru"

# Push dan buat pull request
```

---

## 📄 Lisensi

MIT License – lihat [LICENSE](LICENSE) untuk detail.

---

## 🙏 Terima Kasih

- Laravel ❤️
- TailwindCSS 🌊
- Alpine.js ⚡
- Vite ⚙️
- Semua kontributor & tester

---

## 📬 Support

Jika mengalami kendala:

- Cek tab [Issues](https://github.com/namaprojek/issues)
- Buat issue baru disertai screenshot, versi PHP/MySQL, dan log error

Dibuat dengan ❤️ oleh tim pengembang TOEFL Prediction Test

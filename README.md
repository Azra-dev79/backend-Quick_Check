# QuickCheck — Presensi Mahasiswa Berbasis QR (Laravel 12)

Versi Laravel dari aplikasi QuickCheck (sebelumnya HTML + JavaScript + Google Sheets).
Data kini tersimpan di database, login memakai session Laravel, dan QR presensi
ditandatangani (HMAC) sehingga tidak bisa dipalsukan.

## Kebutuhan
- PHP 8.2 atau lebih baru (ekstensi: mbstring, openssl, fileinfo, pdo_sqlite / pdo_mysql)
- Composer 2

## Cara menjalankan (5 langkah)

```bash
composer install                 # 1. unduh library Laravel ke folder vendor/
copy .env.example .env           # 2. salin konfigurasi (Linux/Mac: cp .env.example .env)
php artisan key:generate         # 3. buat APP_KEY
php artisan migrate --seed       # 4. buat tabel + isi data awal (admin & contoh)
php artisan serve                # 5. jalankan di http://127.0.0.1:8000
```

Pintasan: `composer run setup` menjalankan langkah 1–4 sekaligus.

## Akun awal
| Peran     | Username | Password  |
|-----------|----------|-----------|
| Admin     | `admin`  | `admin123` (ubah di `.env` → `QC_ADMIN_PASSWORD` sebelum `migrate --seed`) |
| Mahasiswa | daftar sendiri di `/daftar` (nama, NIM, kelas, jurusan, password); datanya langsung masuk database. Atur `QC_REGISTER_OPEN=false` di `.env` bila NIM harus didaftarkan admin dulu |

## Fitur
- Mahasiswa: daftar/login, ambil mata kuliah, scan QR kelas, riwayat presensi, ganti password.
- Admin: kelola mahasiswa (termasuk impor CSV), mata kuliah, akun; rekap presensi + unduh CSV;
  generator QR kelas (berlaku per hari) dan QR pribadi; pencatatan manual via scan/NIM.

## Menjalankan test
```bash
php artisan test
```

## Catatan penting
- Kamera browser hanya berfungsi di **HTTPS** atau **localhost**.
- Sebelum dipakai sungguhan: ganti password admin, set `APP_DEBUG=false`, `APP_ENV=production`.
- Panduan lengkap penjelasan tiap langkah ada pada berkas Word "Panduan Laravel QuickCheck".

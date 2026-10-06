<?php

/*
|--------------------------------------------------------------------------
| Konfigurasi khusus aplikasi QuickCheck
|--------------------------------------------------------------------------
| ATURAN LARAVEL: env() hanya boleh dipanggil di dalam folder config/.
| Bagian lain aplikasi harus membaca nilainya lewat config('quickcheck.xxx'),
| supaya tetap berfungsi setelah `php artisan config:cache`.
*/

return [

    // Akun admin awal yang dibuat oleh AdminSeeder.
    'admin' => [
        'name' => env('QC_ADMIN_NAME', 'Administrator'),
        'username' => env('QC_ADMIN_USERNAME', 'admin'),
        'password' => env('QC_ADMIN_PASSWORD', 'admin123'),
    ],

    // Saran pilihan jurusan pada form.
    'jurusan' => [
        'Teknik Informatika',
        'Teknik Sipil',
        'Teknik Mesin',
        'Teknik Elektro',
        'Teknik Industri',
    ],

    // true  = mahasiswa boleh mendaftar sendiri dengan NIM apa saja; datanya otomatis
    //         masuk ke tabel mahasiswas + users (tidak perlu didaftarkan admin dulu).
    // false = NIM harus sudah ada di data mahasiswa (didaftarkan/diimpor admin).
    'register_open' => env('QC_REGISTER_OPEN', true),

    // Panjang minimal password akun mahasiswa.
    'min_password' => 6,

];

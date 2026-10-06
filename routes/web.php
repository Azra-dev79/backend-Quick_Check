<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\IzinController; // <-- Added
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes web QuickCheck
|--------------------------------------------------------------------------
| Urutan baca: URL -> (middleware) -> Controller@method -> View.
| Setiap route diberi ->name() agar di view cukup memakai route('nama').
*/

Route::redirect('/', '/login');

// ---------- Tamu (belum login) ----------
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.attempt');
    Route::get('/daftar', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [AuthController::class, 'register'])->middleware('throttle:10,1')->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ---------- Mahasiswa ----------
Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi');
    Route::post('/absensi/scan', [AbsensiController::class, 'scan'])->name('absensi.scan');

    // Route Kirim Izin/Sakit (Mahasiswa)
    Route::post('/izin', [IzinController::class, 'store'])->name('izin.store');

    Route::get('/profil', [ProfilController::class, 'show'])->name('profil');
    Route::put('/profil', [ProfilController::class, 'updateBiodata'])->name('profil.update');
    Route::put('/profil/password', [ProfilController::class, 'updatePassword'])->name('profil.password');
    Route::post('/profil/matkul', [ProfilController::class, 'ambilMatkul'])->name('profil.matkul.ambil');
    Route::delete('/profil/matkul/{mataKuliah}', [ProfilController::class, 'lepasMatkul'])->name('profil.matkul.lepas');
});

// ---------- Admin ----------
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::post('mahasiswa/import', [Admin\MahasiswaController::class, 'import'])->name('mahasiswa.import');
    Route::resource('mahasiswa', Admin\MahasiswaController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::resource('matkul', Admin\MataKuliahController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::get('users', [Admin\UserController::class, 'index'])->name('users.index');
    Route::delete('users/{user}', [Admin\UserController::class, 'destroy'])->name('users.destroy');
    Route::put('users/{user}/reset-password', [Admin\UserController::class, 'resetPassword'])->name('users.reset');

    Route::get('presensi', [Admin\PresensiController::class, 'index'])->name('presensi.index');
    Route::get('presensi/export', [Admin\PresensiController::class, 'export'])->name('presensi.export');
    Route::post('presensi', [Admin\PresensiController::class, 'store'])->name('presensi.store');
    Route::delete('presensi', [Admin\PresensiController::class, 'reset'])->name('presensi.reset');
    Route::delete('presensi/{presensi}', [Admin\PresensiController::class, 'destroy'])->name('presensi.destroy');

    // Route Update Status Izin (Admin Approve/Reject)
    Route::patch('izin/{izin}', [IzinController::class, 'updateStatus'])->name('izin.update');

    Route::get('qr', [Admin\QrController::class, 'index'])->name('qr.index');
    Route::get('qr/mahasiswa/{mahasiswa:nim}', [Admin\QrController::class, 'mahasiswa'])->name('qr.mahasiswa');
    Route::get('scan', [Admin\QrController::class, 'scan'])->name('scan');
});
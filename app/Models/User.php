<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_MAHASISWA = 'mahasiswa';

    /**
     * Mass assignment: hanya kolom di daftar ini yang boleh diisi lewat create()/update()/fill().
     * `role` SENGAJA tidak dimasukkan, agar pengguna tidak bisa menaikkan dirinya menjadi admin
     * lewat request. Role diisi secara eksplisit di kode (lihat AuthController & AdminSeeder).
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'nim',
        'kelas',
        'jurusan',
    ];

    /** @var list<string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'password' => 'hashed', // otomatis di-hash saat diisi
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isMahasiswa(): bool
    {
        return $this->role === self::ROLE_MAHASISWA;
    }

    /** Mata kuliah yang diambil mahasiswa ini (many-to-many). */
    public function mataKuliahs(): BelongsToMany
    {
        return $this->belongsToMany(MataKuliah::class, 'mata_kuliah_user')->withTimestamps();
    }

    /** Data induk mahasiswa, dihubungkan lewat kolom nim (bukan id). */
    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'nim', 'nim');
    }

    /** Riwayat presensi milik akun ini, dihubungkan lewat nim. */
    public function presensis(): HasMany
    {
        return $this->hasMany(Presensi::class, 'nim', 'nim');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presensi extends Model
{
    public const SUMBER_KELAS = 'kelas';
    public const SUMBER_ADMIN = 'admin';

    protected $table = 'presensis';

    protected $fillable = [
        'nim', 'nama', 'kelas', 'jurusan',
        'mata_kuliah_id', 'status', 'tanggal', 'waktu_hadir',
        'sumber', 'dicatat_oleh',
    ];

    /*
     * Catatan: kolom `tanggal` sengaja TIDAK di-cast ke date. Nilainya selalu berupa
     * string 'Y-m-d' sehingga perbandingan where('tanggal', '2026-09-19') konsisten
     * di SQLite maupun MySQL.
     */
    protected function casts(): array
    {
        return [
            'waktu_hadir' => 'datetime',
        ];
    }

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}

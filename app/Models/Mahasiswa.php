<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mahasiswa extends Model
{
    use HasFactory;

    // Nama tabel ditulis eksplisit karena Laravel menebak nama tabel dari bahasa Inggris.
    protected $table = 'mahasiswas';

    protected $fillable = ['nim', 'nama', 'kelas', 'jurusan'];

    public function presensis(): HasMany
    {
        return $this->hasMany(Presensi::class, 'nim', 'nim');
    }
}

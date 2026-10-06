<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MataKuliah extends Model
{
    use HasFactory;

    protected $table = 'mata_kuliahs';

    protected $fillable = ['kode', 'nama', 'sks', 'dosen', 'deskripsi'];

    /** Mahasiswa (akun) yang mengambil mata kuliah ini. */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'mata_kuliah_user')->withTimestamps();
    }

    public function presensis(): HasMany
    {
        return $this->hasMany(Presensi::class, 'mata_kuliah_id');
    }

    /** Accessor: $mk->label  =>  "RPL-II — Rekayasa Perangkat Lunak II" */
    protected function label(): Attribute
    {
        return Attribute::get(fn () => "{$this->kode} — {$this->nama}");
    }
}

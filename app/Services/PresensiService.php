<?php

namespace App\Services;

use App\Exceptions\PresensiException;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Presensi;
use App\Models\User;
use App\Support\QrToken;
use Illuminate\Database\UniqueConstraintViolationException;

/**
 * Seluruh aturan bisnis presensi ada di sini (bukan di controller),
 * supaya bisa dipakai ulang oleh halaman mahasiswa dan halaman admin.
 */
class PresensiService
{
    /** Mahasiswa memindai QR kelas yang ditampilkan dosen/admin. */
    public function dariQrKelas(User $user, string $payload): Presensi
    {
        $mhs = $user->nim ? Mahasiswa::where('nim', $user->nim)->first() : null;

        if (! $mhs) {
            throw new PresensiException('Akun kamu belum terhubung ke data mahasiswa. Hubungi admin.', 422);
        }

        $data = QrToken::decode($payload);

        if (! $data || ! QrToken::validKelas($data)) {
            throw new PresensiException('QR tidak valid. Pastikan kamu memindai QR presensi kelas.', 422);
        }

        if ($data['tgl'] !== today()->toDateString()) {
            throw new PresensiException('QR sudah kedaluwarsa. Minta QR terbaru kepada dosen/admin.', 422);
        }

        $mk = MataKuliah::find($data['mk']);

        if (! $mk) {
            throw new PresensiException('Mata kuliah pada QR ini tidak ditemukan.', 404);
        }

        if (! $user->mataKuliahs()->whereKey($mk->id)->exists()) {
            throw new PresensiException(
                "Kamu belum mengambil mata kuliah {$mk->nama}. Ambil dulu di halaman Profil.",
                403
            );
        }

        return $this->catat($mhs, $mk, Presensi::SUMBER_KELAS);
    }

    /** Admin mencatat presensi lewat QR pribadi mahasiswa, atau lewat input NIM manual. */
    public function dariAdmin(User $admin, MataKuliah $mk, ?string $payload, ?string $nim): Presensi
    {
        if ($payload) {
            $data = QrToken::decode($payload);

            if (! $data || ! QrToken::validMahasiswa($data)) {
                throw new PresensiException('QR mahasiswa tidak valid.', 422);
            }

            $nim = (string) $data['nim'];
        }

        $mhs = Mahasiswa::where('nim', $nim)->first();

        if (! $mhs) {
            throw new PresensiException("NIM {$nim} tidak terdaftar di data mahasiswa.", 404);
        }

        return $this->catat($mhs, $mk, Presensi::SUMBER_ADMIN, $admin->id);
    }

    /** Simpan satu catatan hadir; menolak jika sudah absen pada matkul & hari yang sama. */
    public function catat(Mahasiswa $mhs, MataKuliah $mk, string $sumber, ?int $dicatatOleh = null): Presensi
    {
        $tanggal = today()->toDateString();

        $sudah = Presensi::where('nim', $mhs->nim)
            ->where('mata_kuliah_id', $mk->id)
            ->where('tanggal', $tanggal)
            ->exists();

        if ($sudah) {
            throw new PresensiException("{$mhs->nama} sudah tercatat hadir di {$mk->nama} hari ini.", 409);
        }

        try {
            $presensi = Presensi::create([
                'nim' => $mhs->nim,
                'nama' => $mhs->nama,
                'kelas' => $mhs->kelas,
                'jurusan' => $mhs->jurusan,
                'mata_kuliah_id' => $mk->id,
                'status' => 'Hadir',
                'tanggal' => $tanggal,
                'waktu_hadir' => now(),
                'sumber' => $sumber,
                'dicatat_oleh' => $dicatatOleh,
            ]);
        } catch (UniqueConstraintViolationException) {
            // Jaring pengaman: dua request bersamaan lolos dari pengecekan di atas,
            // tetapi UNIQUE index di database tetap menolak yang kedua.
            throw new PresensiException("{$mhs->nama} sudah tercatat hadir di {$mk->nama} hari ini.", 409);
        }

        return $presensi->setRelation('mataKuliah', $mk);
    }
}

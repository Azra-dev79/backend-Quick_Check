<?php

namespace App\Support;

use App\Models\Mahasiswa;
use App\Models\MataKuliah;

/**
 * Membuat & memverifikasi isi (payload) QR Code.
 *
 * Setiap QR memuat tanda tangan HMAC yang dihitung dari APP_KEY, sehingga isi QR
 * tidak bisa dipalsukan tanpa mengetahui kunci aplikasi. QR kelas juga terikat
 * pada tanggal, jadi foto QR kemarin tidak bisa dipakai untuk absen hari ini.
 */
final class QrToken
{
    private const APP = 'quickcheck';

    /** Payload QR kelas: satu mata kuliah untuk satu tanggal. */
    public static function kelas(MataKuliah $mk, ?string $tanggal = null): string
    {
        $tanggal ??= today()->toDateString();

        return json_encode([
            'app' => self::APP,
            't' => 'kelas',
            'mk' => $mk->id,
            'tgl' => $tanggal,
            'k' => self::sign("kelas|{$mk->id}|{$tanggal}"),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /** Payload QR pribadi mahasiswa (dipindai oleh admin). */
    public static function mahasiswa(Mahasiswa $mhs): string
    {
        return json_encode([
            'app' => self::APP,
            't' => 'mhs',
            'nim' => $mhs->nim,
            'nama' => $mhs->nama,
            'k' => self::sign("mhs|{$mhs->nim}"),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /** Ubah teks hasil scan menjadi array; null bila bukan QR milik aplikasi ini. */
    public static function decode(string $payload): ?array
    {
        $data = json_decode($payload, true);

        if (! is_array($data) || ($data['app'] ?? null) !== self::APP) {
            return null;
        }

        return $data;
    }

    public static function validKelas(array $data): bool
    {
        if (($data['t'] ?? null) !== 'kelas' || ! self::skalar($data, ['mk', 'tgl', 'k'])) {
            return false;
        }

        return hash_equals(self::sign("kelas|{$data['mk']}|{$data['tgl']}"), (string) $data['k']);
    }

    public static function validMahasiswa(array $data): bool
    {
        if (($data['t'] ?? null) !== 'mhs' || ! self::skalar($data, ['nim', 'k'])) {
            return false;
        }

        return hash_equals(self::sign("mhs|{$data['nim']}"), (string) $data['k']);
    }

    /** Pastikan kunci-kunci yang dibutuhkan ada dan berupa nilai sederhana (bukan array/objek). */
    private static function skalar(array $data, array $keys): bool
    {
        foreach ($keys as $key) {
            if (! isset($data[$key]) || ! is_scalar($data[$key])) {
                return false;
            }
        }

        return true;
    }

    private static function sign(string $data): string
    {
        return substr(hash_hmac('sha256', $data, (string) config('app.key')), 0, 24);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use Illuminate\Database\Seeder;

/**
 * Data contoh agar aplikasi langsung bisa dicoba.
 * Hapus/ganti sesuai kebutuhan; data asli dapat diimpor dari CSV lewat menu Mahasiswa.
 */
class ContohDataSeeder extends Seeder
{
    public function run(): void
    {
        $mahasiswas = [
            ['nim' => '2024001', 'nama' => 'Budi Santoso', 'kelas' => '2A', 'jurusan' => 'Teknik Informatika'],
            ['nim' => '2024002', 'nama' => 'Siti Aisyah', 'kelas' => '2A', 'jurusan' => 'Teknik Informatika'],
            ['nim' => '2024003', 'nama' => 'Rizky Pratama', 'kelas' => '2B', 'jurusan' => 'Teknik Informatika'],
            ['nim' => '2024004', 'nama' => 'Dewi Lestari', 'kelas' => '2B', 'jurusan' => 'Teknik Sipil'],
            ['nim' => '2024005', 'nama' => 'Ahmad Fauzi', 'kelas' => '2A', 'jurusan' => 'Teknik Elektro'],
        ];

        foreach ($mahasiswas as $m) {
            Mahasiswa::updateOrCreate(['nim' => $m['nim']], $m);
        }

        $matkuls = [
            ['kode' => 'RPL-II', 'nama' => 'Rekayasa Perangkat Lunak II', 'sks' => 3, 'dosen' => null, 'deskripsi' => 'Mata kuliah lanjutan tentang rekayasa perangkat lunak'],
            ['kode' => 'IOT-01', 'nama' => 'Internet of Things', 'sks' => 3, 'dosen' => null, 'deskripsi' => null],
            ['kode' => 'BD-01', 'nama' => 'Basis Data', 'sks' => 3, 'dosen' => null, 'deskripsi' => null],
        ];

        foreach ($matkuls as $mk) {
            MataKuliah::updateOrCreate(['kode' => $mk['kode']], $mk);
        }
    }
}

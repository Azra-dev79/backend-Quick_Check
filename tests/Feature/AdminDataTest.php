<?php

namespace Tests\Feature;

use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dapat_menambah_dan_mengubah_mahasiswa(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.mahasiswa.store'), [
            'nim' => '2024099', 'nama' => 'Contoh Mahasiswa', 'kelas' => '2A', 'jurusan' => 'Teknik Informatika',
        ])->assertRedirect();

        $mhs = Mahasiswa::where('nim', '2024099')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.mahasiswa.update', $mhs), [
            'nama' => 'Nama Baru', 'kelas' => '2B', 'jurusan' => 'Teknik Informatika',
        ])->assertRedirect();

        $this->assertDatabaseHas('mahasiswas', ['nim' => '2024099', 'nama' => 'Nama Baru', 'kelas' => '2B']);
    }

    public function test_nim_ganda_ditolak(): void
    {
        $admin = User::factory()->admin()->create();
        Mahasiswa::factory()->create(['nim' => '2024099']);

        $this->actingAs($admin)->post(route('admin.mahasiswa.store'), [
            'nim' => '2024099', 'nama' => 'Ganda', 'kelas' => '2A', 'jurusan' => 'Teknik Informatika',
        ])->assertSessionHasErrors('nim');
    }

    public function test_admin_dapat_mengelola_mata_kuliah(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.matkul.store'), [
            'kode' => 'TST-01', 'nama' => 'Mata Kuliah Uji', 'sks' => 3,
        ])->assertRedirect();

        $mk = MataKuliah::where('kode', 'TST-01')->firstOrFail();

        // Mengubah dengan kode yang sama harus lolos (aturan unique mengecualikan dirinya sendiri).
        $this->actingAs($admin)->put(route('admin.matkul.update', $mk), [
            'kode' => 'TST-01', 'nama' => 'Nama Diubah', 'sks' => 2,
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('mata_kuliahs', ['kode' => 'TST-01', 'nama' => 'Nama Diubah', 'sks' => 2]);
    }

    public function test_impor_csv_mahasiswa(): void
    {
        $admin = User::factory()->admin()->create();

        $csv = "NIM;Nama;Kelas;Jurusan\n2024101;Ani Wijaya;1A;Teknik Sipil\n2024102;Beni Kurnia;1B;Teknik Mesin\n";
        $file = UploadedFile::fake()->createWithContent('mahasiswa.csv', $csv);

        $this->actingAs($admin)
            ->post(route('admin.mahasiswa.import'), ['file' => $file])
            ->assertRedirect();

        $this->assertDatabaseHas('mahasiswas', ['nim' => '2024101', 'nama' => 'Ani Wijaya', 'jurusan' => 'Teknik Sipil']);
        $this->assertDatabaseHas('mahasiswas', ['nim' => '2024102', 'nama' => 'Beni Kurnia']);
    }

    public function test_akun_admin_tidak_bisa_menghapus_dirinya_sendiri(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->delete(route('admin.users.destroy', $admin))->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}

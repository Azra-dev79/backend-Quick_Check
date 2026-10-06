<?php

namespace Tests\Feature;

use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_login_dapat_dibuka(): void
    {
        $this->get('/login')->assertOk()->assertSee('Masuk');
    }

    public function test_mahasiswa_dapat_login(): void
    {
        $user = User::factory()->mahasiswa('2024001')->create(['password' => 'rahasia1']);

        $this->post('/login', ['username' => '2024001', 'password' => 'rahasia1', 'role' => 'mahasiswa'])
            ->assertRedirect(route('absensi'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_password_salah_ditolak(): void
    {
        User::factory()->mahasiswa('2024001')->create(['password' => 'rahasia1']);

        $this->post('/login', ['username' => '2024001', 'password' => 'salah', 'role' => 'mahasiswa'])
            ->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_peran_yang_tidak_sesuai_ditolak(): void
    {
        User::factory()->mahasiswa('2024001')->create(['password' => 'rahasia1']);

        // Mahasiswa mencoba masuk lewat tab Admin.
        $this->post('/login', ['username' => '2024001', 'password' => 'rahasia1', 'role' => 'admin'])
            ->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_mahasiswa_baru_dapat_mendaftar_dan_langsung_masuk_database(): void
    {
        // NIM ini belum ada di data induk; mode terbuka membuatnya otomatis.
        $this->post('/daftar', [
            'nama' => 'Azratul Al Zahra',
            'nim' => '24105111093',
            'kelas' => '3A',
            'jurusan' => 'Teknik Informatika',
            'password' => 'rahasia1',
            'password_confirmation' => 'rahasia1',
        ])->assertRedirect(route('login'));

        $this->assertDatabaseHas('mahasiswas', ['nim' => '24105111093', 'nama' => 'Azratul Al Zahra', 'kelas' => '3A']);
        $this->assertDatabaseHas('users', ['username' => '24105111093', 'role' => 'mahasiswa', 'nim' => '24105111093']);
    }

    public function test_registrasi_dengan_nim_yang_sudah_ada_di_data_induk_dan_nama_cocok(): void
    {
        Mahasiswa::factory()->create(['nim' => '2024010', 'nama' => 'Budi Santoso']);

        $this->post('/daftar', [
            'nama' => 'budi santoso', // huruf besar/kecil diabaikan
            'nim' => '2024010',
            'kelas' => '2A',
            'jurusan' => 'Teknik Informatika',
            'password' => 'rahasia1',
            'password_confirmation' => 'rahasia1',
        ])->assertRedirect(route('login'));

        $this->assertDatabaseHas('users', ['username' => '2024010', 'role' => 'mahasiswa']);
        $this->assertDatabaseCount('mahasiswas', 1); // tidak membuat data induk ganda
    }

    public function test_registrasi_ditolak_bila_nama_tidak_cocok_dengan_data_induk(): void
    {
        Mahasiswa::factory()->create(['nim' => '2024010', 'nama' => 'Budi Santoso']);

        $this->post('/daftar', [
            'nama' => 'Orang Lain',
            'nim' => '2024010',
            'kelas' => '2A',
            'jurusan' => 'Teknik Informatika',
            'password' => 'rahasia1',
            'password_confirmation' => 'rahasia1',
        ])->assertSessionHasErrors('nama');

        $this->assertDatabaseMissing('users', ['username' => '2024010']);
    }

    public function test_nim_yang_sudah_punya_akun_ditolak(): void
    {
        User::factory()->mahasiswa('2024010')->create();

        $this->post('/daftar', [
            'nama' => 'Siapa Saja',
            'nim' => '2024010',
            'kelas' => '2A',
            'jurusan' => 'Teknik Informatika',
            'password' => 'rahasia1',
            'password_confirmation' => 'rahasia1',
        ])->assertSessionHasErrors('nim');
    }

    public function test_mode_tertutup_menolak_nim_yang_belum_terdaftar(): void
    {
        config(['quickcheck.register_open' => false]);

        $this->post('/daftar', [
            'nama' => 'Siapa Saja',
            'nim' => '9999999',
            'kelas' => '2A',
            'jurusan' => 'Teknik Informatika',
            'password' => 'rahasia1',
            'password_confirmation' => 'rahasia1',
        ])->assertSessionHasErrors('nim');

        $this->assertDatabaseMissing('mahasiswas', ['nim' => '9999999']);
    }
}

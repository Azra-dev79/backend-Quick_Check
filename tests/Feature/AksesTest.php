<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Menguji middleware auth + role: siapa boleh membuka halaman apa. */
class AksesTest extends TestCase
{
    use RefreshDatabase;

    public function test_tamu_diarahkan_ke_login(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
        $this->get('/absensi')->assertRedirect(route('login'));
    }

    public function test_mahasiswa_tidak_boleh_membuka_halaman_admin(): void
    {
        $mhs = User::factory()->mahasiswa('2024001')->create();

        $this->actingAs($mhs)->get('/admin')->assertForbidden();
        $this->actingAs($mhs)->get('/admin/mahasiswa')->assertForbidden();
    }

    public function test_admin_dapat_membuka_halaman_admin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->actingAs($admin)->get('/admin/mahasiswa')->assertOk();
        $this->actingAs($admin)->get('/admin/matkul')->assertOk();
        $this->actingAs($admin)->get('/admin/presensi')->assertOk();
        $this->actingAs($admin)->get('/admin/qr')->assertOk();
        $this->actingAs($admin)->get('/admin/scan')->assertOk();
        $this->actingAs($admin)->get('/admin/users')->assertOk();
    }

    public function test_admin_tidak_boleh_membuka_halaman_mahasiswa(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/absensi')->assertForbidden();
    }

    public function test_mahasiswa_dapat_membuka_absensi_dan_profil(): void
    {
        $mhs = User::factory()->mahasiswa('2024001')->create();

        $this->actingAs($mhs)->get('/absensi')->assertOk();
        $this->actingAs($mhs)->get('/profil')->assertOk();
    }
}

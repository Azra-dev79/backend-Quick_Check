<?php

namespace Tests\Feature;

use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\User;
use App\Support\QrToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PresensiTest extends TestCase
{
    use RefreshDatabase;

    /** @return array{0: User, 1: MataKuliah} */
    private function siapkan(bool $ambilMatkul = true): array
    {
        Mahasiswa::factory()->create(['nim' => '2024001', 'nama' => 'Budi Santoso']);
        $user = User::factory()->mahasiswa('2024001')->create();
        $mk = MataKuliah::factory()->create();

        if ($ambilMatkul) {
            $user->mataKuliahs()->attach($mk);
        }

        return [$user, $mk];
    }

    public function test_scan_qr_kelas_berhasil_mencatat_kehadiran(): void
    {
        [$user, $mk] = $this->siapkan();

        $this->actingAs($user)
            ->postJson(route('absensi.scan'), ['payload' => QrToken::kelas($mk)])
            ->assertCreated()
            ->assertJson(['ok' => true]);

        $this->assertDatabaseHas('presensis', [
            'nim' => '2024001',
            'mata_kuliah_id' => $mk->id,
            'tanggal' => today()->toDateString(),
            'sumber' => 'kelas',
        ]);
    }

    public function test_tidak_bisa_absen_dua_kali_di_hari_yang_sama(): void
    {
        [$user, $mk] = $this->siapkan();
        $payload = QrToken::kelas($mk);

        $this->actingAs($user)->postJson(route('absensi.scan'), ['payload' => $payload])->assertCreated();
        $this->actingAs($user)->postJson(route('absensi.scan'), ['payload' => $payload])->assertStatus(409);

        $this->assertDatabaseCount('presensis', 1);
    }

    public function test_qr_palsu_ditolak(): void
    {
        [$user, $mk] = $this->siapkan();

        $palsu = json_encode([
            'app' => 'quickcheck', 't' => 'kelas', 'mk' => $mk->id,
            'tgl' => today()->toDateString(), 'k' => 'tanda-tangan-palsu',
        ]);

        $this->actingAs($user)->postJson(route('absensi.scan'), ['payload' => $palsu])->assertStatus(422);
        $this->assertDatabaseCount('presensis', 0);
    }

    public function test_qr_kemarin_sudah_kedaluwarsa(): void
    {
        [$user, $mk] = $this->siapkan();

        $kemarin = QrToken::kelas($mk, today()->subDay()->toDateString());

        $this->actingAs($user)->postJson(route('absensi.scan'), ['payload' => $kemarin])->assertStatus(422);
        $this->assertDatabaseCount('presensis', 0);
    }

    public function test_harus_mengambil_matkul_terlebih_dahulu(): void
    {
        [$user, $mk] = $this->siapkan(ambilMatkul: false);

        $this->actingAs($user)
            ->postJson(route('absensi.scan'), ['payload' => QrToken::kelas($mk)])
            ->assertForbidden();
    }

    public function test_admin_dapat_mencatat_presensi_lewat_nim(): void
    {
        [, $mk] = $this->siapkan();
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->postJson(route('admin.presensi.store'), ['mata_kuliah_id' => $mk->id, 'nim' => '2024001'])
            ->assertCreated();

        $this->assertDatabaseHas('presensis', ['nim' => '2024001', 'sumber' => 'admin', 'dicatat_oleh' => $admin->id]);
    }

    public function test_admin_dapat_mencatat_presensi_lewat_qr_pribadi(): void
    {
        [, $mk] = $this->siapkan();
        $admin = User::factory()->admin()->create();
        $payload = QrToken::mahasiswa(Mahasiswa::where('nim', '2024001')->first());

        $this->actingAs($admin)
            ->postJson(route('admin.presensi.store'), ['mata_kuliah_id' => $mk->id, 'payload' => $payload])
            ->assertCreated();
    }

    public function test_mahasiswa_tidak_boleh_memakai_endpoint_admin(): void
    {
        [$user, $mk] = $this->siapkan();

        $this->actingAs($user)
            ->postJson(route('admin.presensi.store'), ['mata_kuliah_id' => $mk->id, 'nim' => '2024001'])
            ->assertForbidden();
    }
}

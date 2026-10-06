<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'email' => null,
            'password' => static::$password ??= Hash::make('password'),
            'role' => User::ROLE_MAHASISWA,
        ];
    }

    /** State: akun admin. Pemakaian: User::factory()->admin()->create() */
    public function admin(): static
    {
        return $this->state(fn () => ['role' => User::ROLE_ADMIN]);
    }

    /** State: akun mahasiswa dengan NIM tertentu (username = NIM). */
    public function mahasiswa(string $nim): static
    {
        return $this->state(fn () => [
            'role' => User::ROLE_MAHASISWA,
            'username' => $nim,
            'nim' => $nim,
            'kelas' => '2A',
            'jurusan' => 'Teknik Informatika',
        ]);
    }
}

<?php

namespace Database\Factories;

use App\Models\Mahasiswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mahasiswa>
 */
class MahasiswaFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'nim' => (string) fake()->unique()->numerify('24#########'),
            'nama' => fake()->name(),
            'kelas' => fake()->randomElement(['1A', '2A', '3B']),
            'jurusan' => 'Teknik Informatika',
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\MataKuliah;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MataKuliah>
 */
class MataKuliahFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'kode' => strtoupper(fake()->unique()->bothify('MK-###')),
            'nama' => 'Mata Kuliah '.fake()->unique()->word(),
            'sks' => 3,
            'dosen' => fake()->name(),
            'deskripsi' => null,
        ];
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /** Membuat (atau memperbarui) akun admin dari nilai di .env / config/quickcheck.php. */
    public function run(): void
    {
        $admin = User::firstOrNew(['username' => config('quickcheck.admin.username')]);

        // forceFill dipakai karena `role` sengaja tidak mass-assignable.
        $admin->forceFill([
            'name' => config('quickcheck.admin.name'),
            'password' => config('quickcheck.admin.password'),
            'role' => User::ROLE_ADMIN,
        ])->save();
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Membuat akun untuk setiap role.
     * Password semua akun demo: "password"
     */
    public function run(): void
    {
        $akun = [
            ['name' => 'davin', 'email' => 'warga@example.test', 'role' => User::ROLE_WARGA],
            ['name' => 'davin', 'email' => 'admin@example.test', 'role' => User::ROLE_ADMIN],
        ];

        foreach ($akun as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'role' => $data['role'],
                    'password' => 'password',
                    'email_verified_at' => now(),
                ],
            );
        }

        // Mahasiswa tambahan agar data pengaduan lebih variatif
        if (User::where('role', User::ROLE_WARGA)->count() < 7) {
            User::factory()
                ->count(6)
                ->create(['role' => User::ROLE_ADMIN]);
        }
    }
}

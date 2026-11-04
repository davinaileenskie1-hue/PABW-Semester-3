<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        User::factory()->create([
            'name' => 'Anggota Perpustakaan',
            'email' => 'user@example.com',
            'password' => 'password',
        ]);

        $this->call([
            \Modules\Buku\Database\Seeders\BukuDatabaseSeeder::class,
            \Modules\Peminjaman\Database\Seeders\PeminjamanDatabaseSeeder::class,
        ]);
    }
}

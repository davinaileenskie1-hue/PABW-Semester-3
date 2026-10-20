<?php

namespace Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PetugasSeeder extends Seeder
{
    /**
     * Membuat 5 akun petugas dummy dan 1 akun admin BPBD.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Lima akun petugas dummy dengan password "password".
        for ($i = 0; $i < 5; $i++) {
            DB::table('users')->insert([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Akun admin BPBD dengan email dan password tetap untuk pengujian.
        DB::table('users')->insert([
            'name' => 'Admin BPBD Kabupaten Bandung',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

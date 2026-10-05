<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        for ($i = 0; $i <20; $i++){
            DB::table('mahasiswa')->insert([
                'nama'=> $faker->name,
                'email'=> $faker->unique()->safeEmail,
                'jurusan'=> $faker->randomElement(['informatika', 'sistem informasi', 'teknik komputer']),
                'umur'=> $faker->numberBetween(18, 25),
                'created_at'=> now(),
                'updated_at'=> now()
            ]);
        }
    }
}

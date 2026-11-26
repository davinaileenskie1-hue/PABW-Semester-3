<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Akun demo untuk pengujian di Postman
        User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => Hash::make('praktikum123'),
        ]);

        User::create([
            'name' => 'Andi Pratama',
            'email' => 'andi@example.com',
            'password' => Hash::make('praktikum123'),
        ]);

        // Data buku contoh
        $books = [
            [
                'judul' => 'Pemrograman Web dengan Laravel',
                'penulis' => 'Davin Aileenskie Sofiandy',
                'penerbit' => 'Informatika',
                'tahun_terbit' => 2024,
                'stok' => 5,
            ],
            [
                'judul' => 'Basis Data Lanjutan',
                'penulis' => 'Abraham Silberschatz',
                'penerbit' => 'Andi Publisher',
                'tahun_terbit' => 2019,
                'stok' => 3,
            ],
            [
                'judul' => 'Algoritma dan Struktur Data',
                'penulis' => 'Rinaldi Munir',
                'penerbit' => 'Informatika',
                'tahun_terbit' => 2016,
                'stok' => 4,
            ],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }
    }
}

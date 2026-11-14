<?php

namespace Modules\Buku\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Buku\Models\Buku;

class BukuDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Buku::query()->exists()) {
            return;
        }

        $books = [
            ['judul' => 'Pemrograman Web dengan Laravel', 'penulis' => 'Andi Pratama', 'penerbit' => 'Informatika', 'tahun_terbit' => 2023, 'stok' => 12],
            ['judul' => 'Dasar-Dasar React dan Inertia', 'penulis' => 'Siti Rahmawati', 'penerbit' => 'Media Kreatif', 'tahun_terbit' => 2024, 'stok' => 8],
            ['judul' => 'Basis Data untuk Pemula', 'penulis' => 'Budi Santoso', 'penerbit' => 'Andi Offset', 'tahun_terbit' => 2021, 'stok' => 15],
            ['judul' => 'Algoritma dan Struktur Data', 'penulis' => 'Rina Kusuma', 'penerbit' => 'Erlangga', 'tahun_terbit' => 2020, 'stok' => 6],
            ['judul' => 'Arsitektur Perangkat Lunak Modern', 'penulis' => 'Dedi Kurniawan', 'penerbit' => 'Elex Media', 'tahun_terbit' => 2022, 'stok' => 9],
            ['judul' => 'Panduan Praktis Tailwind CSS', 'penulis' => 'Maya Lestari', 'penerbit' => 'Graha Ilmu', 'tahun_terbit' => 2025, 'stok' => 10],
        ];

        foreach ($books as $book) {
            Buku::create($book);
        }
    }
}

<?php

namespace Modules\Buku\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Buku\Models\Buku;

class BukuDatabaseSeeder extends Seeder
{
    /**
     * Seed contoh katalog buku perpustakaan.
     */
    public function run(): void
    {
        if (Buku::query()->exists()) {
            return;
        }

        $buku = [
            ['judul' => 'Algoritma dan Pemrograman', 'penulis' => 'Rinaldi Munir', 'penerbit' => 'Informatika', 'tahun_terbit' => 2016, 'stok' => 5],
            ['judul' => 'Pemrograman Web dengan PHP dan MySQL', 'penulis' => 'Budi Raharjo', 'penerbit' => 'Informatika', 'tahun_terbit' => 2018, 'stok' => 4],
            ['judul' => 'Clean Code', 'penulis' => 'Robert C. Martin', 'penerbit' => 'Prentice Hall', 'tahun_terbit' => 2008, 'stok' => 3],
            ['judul' => 'Basis Data Relasional', 'penulis' => 'Fathansyah', 'penerbit' => 'Informatika', 'tahun_terbit' => 2019, 'stok' => 6],
            ['judul' => 'Rekayasa Perangkat Lunak', 'penulis' => 'Rosa A. S.', 'penerbit' => 'Modula', 'tahun_terbit' => 2016, 'stok' => 2],
            ['judul' => 'Jaringan Komputer', 'penulis' => 'Andrew S. Tanenbaum', 'penerbit' => 'Pearson', 'tahun_terbit' => 2011, 'stok' => 3],
            ['judul' => 'Kecerdasan Buatan', 'penulis' => 'Suyanto', 'penerbit' => 'Informatika', 'tahun_terbit' => 2014, 'stok' => 2],
            ['judul' => 'Matematika Diskrit', 'penulis' => 'Rinaldi Munir', 'penerbit' => 'Informatika', 'tahun_terbit' => 2012, 'stok' => 7],
            ['judul' => 'Sistem Operasi', 'penulis' => 'Abraham Silberschatz', 'penerbit' => 'Wiley', 'tahun_terbit' => 2018, 'stok' => 4],
            ['judul' => 'Desain UI/UX untuk Pemula', 'penulis' => 'Tim Kreatif', 'penerbit' => 'Elex Media', 'tahun_terbit' => 2021, 'stok' => 5],
            ['judul' => 'Belajar Laravel dari Nol', 'penulis' => 'Davin Aileenskie', 'penerbit' => 'Media Belajar', 'tahun_terbit' => 2024, 'stok' => 3],
            ['judul' => 'Struktur Data', 'penulis' => 'Anita Desiani', 'penerbit' => 'Andi Offset', 'tahun_terbit' => 2017, 'stok' => 6],
        ];

        foreach ($buku as $data) {
            Buku::create($data);
        }
    }
}

<?php

namespace Modules\Peminjaman\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Peminjaman\Models\Peminjaman;

class PeminjamanDatabaseSeeder extends Seeder
{
    /**
     * Seed contoh transaksi peminjaman.
     */
    public function run(): void
    {
        if (Peminjaman::query()->exists()) {
            return;
        }

        $data = [
            ['nama_peminjam' => 'Budi Santoso', 'judul_buku' => 'Algoritma dan Pemrograman', 'tanggal_pinjam' => now()->subDays(9)->format('Y-m-d'), 'tanggal_kembali' => null, 'status' => 'Dipinjam'],
            ['nama_peminjam' => 'Rina Marlina', 'judul_buku' => 'Clean Code', 'tanggal_pinjam' => now()->subDays(20)->format('Y-m-d'), 'tanggal_kembali' => now()->subDays(12)->format('Y-m-d'), 'status' => 'Dikembalikan'],
            ['nama_peminjam' => 'Sinta Dewi', 'judul_buku' => 'Basis Data Relasional', 'tanggal_pinjam' => now()->subDays(4)->format('Y-m-d'), 'tanggal_kembali' => null, 'status' => 'Dipinjam'],
            ['nama_peminjam' => 'Agus Pratama', 'judul_buku' => 'Jaringan Komputer', 'tanggal_pinjam' => now()->subDays(30)->format('Y-m-d'), 'tanggal_kembali' => now()->subDays(21)->format('Y-m-d'), 'status' => 'Dikembalikan'],
            ['nama_peminjam' => 'Dewi Lestari', 'judul_buku' => 'Belajar Laravel dari Nol', 'tanggal_pinjam' => now()->subDays(2)->format('Y-m-d'), 'tanggal_kembali' => null, 'status' => 'Dipinjam'],
        ];

        foreach ($data as $item) {
            Peminjaman::create($item);
        }
    }
}

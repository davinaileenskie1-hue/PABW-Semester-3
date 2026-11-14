<?php

namespace Modules\Peminjaman\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Peminjaman\Models\Peminjaman;

class PeminjamanDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Peminjaman::query()->exists()) {
            return;
        }

        $records = [
            [
                'nama_peminjam' => 'Raka Pratama',
                'judul_buku' => 'Pemrograman Web dengan Laravel',
                'tanggal_pinjam' => now()->subDays(7)->toDateString(),
                'tanggal_kembali' => now()->addDays(7)->toDateString(),
                'status' => 'Dipinjam',
            ],
            [
                'nama_peminjam' => 'Nadia Putri',
                'judul_buku' => 'Dasar-Dasar React dan Inertia',
                'tanggal_pinjam' => now()->subDays(20)->toDateString(),
                'tanggal_kembali' => now()->subDays(6)->toDateString(),
                'status' => 'Dikembalikan',
            ],
            [
                'nama_peminjam' => 'Fajar Nugroho',
                'judul_buku' => 'Basis Data untuk Pemula',
                'tanggal_pinjam' => now()->subDays(3)->toDateString(),
                'tanggal_kembali' => now()->addDays(11)->toDateString(),
                'status' => 'Dipinjam',
            ],
        ];

        foreach ($records as $record) {
            Peminjaman::create($record);
        }
    }
}

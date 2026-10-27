<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    protected $table = 'laporan'; // Nama tabel yang dihubungkan

    // Kolom yang boleh diisi secara massal (mass assignment)
    protected $fillable = [
        'nama_pelapor',
        'no_hp',
        'lokasi',
        'kecamatan',
        'tinggi_air',
        'keterangan',
        'foto',
        'status',
    ];
}

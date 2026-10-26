<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kontak extends Model
{
    protected $table = 'kontaks';
    protected $fillable = [
    'nama',
    'alamat',
    'no_hp',
    'foto',
    ];
}

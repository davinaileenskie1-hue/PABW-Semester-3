<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $fillable = [
        'judul',
        'penulis',
        'penerbit',
        'tahun_terbit',
        'stok',
        'cover_path',
        'cover_name',
    ];

    // Lokasi file di server tidak perlu dikirim ke client
    protected $hidden = ['cover_path'];

    protected $casts = [
        'tahun_terbit' => 'integer',
        'stok' => 'integer',
    ];

    // Atribut tambahan yang ikut dikirim di JSON
    protected $appends = ['cover_url'];

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    // URL untuk membuka sampul, null jika tidak ada sampul
    protected function coverUrl(): Attribute
    {
        return Attribute::get(
            fn (): ?string => $this->cover_path
                ? url("/api/books/{$this->id}/cover")
                : null
        );
    }
}

<?php

namespace Modules\Peminjaman\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Peminjaman\Database\Factories\PeminjamanFactory;

class Peminjaman extends Model
{
    /** @use HasFactory<PeminjamanFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'peminjamans';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama_peminjam',
        'judul_buku',
        'tanggal_pinjam',
        'tanggal_kembali',
        'status',
        'bukti',
        'nama_bukti',
    ];

    /**
     * The accessors to append to the model's array and JSON form.
     *
     * @var list<string>
     */
    protected $appends = [
        'bukti_url',
    ];

    /**
     * Public URL of the uploaded proof, or null when there is no file.
     */
    protected function buktiUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->bukti
            ? asset('storage/'.$this->bukti)
            : null);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): PeminjamanFactory
    {
        return PeminjamanFactory::new();
    }
}

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
     * "Peminjaman" ditulis apa adanya, bukan bentuk jamak otomatis "peminjamen".
     *
     * @var string
     */
    protected $table = 'peminjamans';

    /**
     * Status peminjaman yang sah.
     *
     * @var list<string>
     */
    public const STATUSES = ['Dipinjam', 'Dikembalikan'];

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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_pinjam' => 'date:Y-m-d',
            'tanggal_kembali' => 'date:Y-m-d',
        ];
    }

    /**
     * Public URL of the uploaded proof file, or null when there is no file.
     */
    protected function buktiUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->bukti ? asset('storage/'.$this->bukti) : null);
    }

    protected static function newFactory(): PeminjamanFactory
    {
        return PeminjamanFactory::new();
    }
}

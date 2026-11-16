    <?php

    namespace Modules\TitikSampah\Models;

    use App\Models\User;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Support\Facades\Storage;



    class TitikSampah extends Model
    {
        use HasFactory;

        protected $table = 'pengaduan';

        public const STATUS = [
            'baru' => 'Baru',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
        ];

        protected $fillable = [
            'fasilitas_id',
            'user_id',
            'judul',
            'deskripsi',
            'tanggal_pengaduan',
            'status',
            'foto',
        ];

        protected $attributes = [
            'status' => 'baru',
        ];

        protected $casts = [
            'tanggal_pengaduan' => 'date',
        ];

        /* -----------------------------------------------------------------
        |  Relasi : pengaduan milik satu fasilitas & satu user
        | ----------------------------------------------------------------- */

        public function fasilitas(): BelongsTo
        {
            return $this->belongsTo(TitikSampah::class);
        }

        public function user(): BelongsTo
        {
            return $this->belongsTo(User::class);
        }

        /* -----------------------------------------------------------------
        |  Scope
        | ----------------------------------------------------------------- */

        public function scopeSearch(Builder $query, ?string $keyword): Builder
        {
            return $query->when($keyword, function (Builder $q) use ($keyword) {
                $q->where(function (Builder $inner) use ($keyword) {
                    $inner->where('judul', 'like', "%{$keyword}%")
                        ->orWhere('deskripsi', 'like', "%{$keyword}%")
                        ->orWhereHas('fasilitas', fn (Builder $f) => $f->where('nama', 'like', "%{$keyword}%"))
                        ->orWhereHas('user', fn (Builder $u) => $u->where('name', 'like', "%{$keyword}%"));
                });
            });
        }

        public function scopeStatus(Builder $query, ?string $status): Builder
        {
            return $query->when($status, fn (Builder $q) => $q->where('status', $status));
        }

        /* -----------------------------------------------------------------
        |  Accessor
        | ----------------------------------------------------------------- */

        public function getStatusLabelAttribute(): string
        {
            return self::STATUS[$this->status] ?? ucfirst($this->status);
        }

        public function getStatusBadgeAttribute(): string
        {
            $class = match ($this->status) {
                'baru' => 'danger',
                'diproses' => 'warning',
                'selesai' => 'success',
                default => 'secondary',
            };

            return '<span class="badge text-bg-' . $class . '">' . e($this->status_label) . '</span>';
        }


        public function getIsBaruAttribute(): bool
        {
            return $this->status === 'baru';
        }
    }

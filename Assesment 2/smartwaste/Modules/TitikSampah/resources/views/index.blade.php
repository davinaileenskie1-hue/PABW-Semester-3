@extends('layouts.app')

@section('title', 'Daftar TitikSampah')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h4 class="fw-bold mb-1">Data TitikSampah </h4>
            <p class="text-secondary mb-0 small">
                {{ $rekap['total'] }} TitikSampah &middot; {{ $rekap['baik'] }} baik &middot; {{ $rekap['rusak'] }} perlu pembuangan
            </p>
        </div>
        @if (auth()->user()->canManage())
            <a href="{{ route('fasilitas.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah TitikSampah
            </a>
        @endif
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            {{-- Form pencarian + filter kondisi --}}
            <form method="GET" action="{{ route('TitikSampah.index') }}" class="row g-2 mb-3 no-loader">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="q" value="{{ $keyword }}" class="form-control"
                               placeholder="Cari nama, lokasi, atau kategori...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="kondisi" class="form-select">
                        <option value="">Semua kondisi</option>
                        @foreach (\Modules\TitikSampah\Models\TitikSampah::KONDISI as $value => $label)
                            <option value="{{ $value }}" @selected($kondisi === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-primary" type="submit">Terapkan</button>
                    @if ($keyword !== '' || $kondisi)
                        <a href="{{ route('TitikSampah.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>

            @if ($keyword !== '')
                <div class="alert alert-info py-2 small">
                    Menampilkan hasil pencarian untuk <strong>"{{ $keyword }}"</strong>:
                    {{ $TitikSampah->total() }} data ditemukan.
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fasilitas</th>
                            <th>Lokasi</th>
                            <th>Kategori</th>
                            <th>Kondisi</th>
                            <th class="text-center">Pengaduan</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($TitikSampah as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="stat-icon text-bg-primary bg-opacity-10 text-primary" style="width:38px;height:38px;font-size:1rem;">
                                            <i class="bi {{ $item->kategori_icon }}"></i>
                                        </span>
                                        <div>
                                            <div class="fw-semibold">{{ $item->nama }}</div>
                                            <small class="text-secondary">{{ \Illuminate\Support\Str::limit($item->deskripsi, 60) ?: 'Tanpa deskripsi' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="small">{{ $item->lokasi }}</td>
                                <td><span class="badge text-bg-light border">{{ $item->kategori }}</span></td>
                                <td>{!! $item->kondisi_badge !!}</td>
                                <td class="text-center">
                                    <span class="badge text-bg-secondary">{{ $item->pengaduan_count }}</span>
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('fasilitas.show', $item) }}" class="btn btn-sm btn-outline-secondary" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if (auth()->user()->canManage())
                                        <a href="{{ route('fasilitas.edit', $item) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                data-delete-url="{{ route('fasilitas.destroy', $item) }}"
                                                data-delete-name="TitikSampah &quot;{{ $item->nama }}&quot;"
                                                title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-secondary py-5">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    Tidak ada data TitikSampah{{ $keyword !== '' ? ' untuk kata kunci "' . $keyword . '"' : '' }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
                <small class="text-secondary">
                    Menampilkan {{ $TitikSampah->firstItem() ?? 0 }}&ndash;{{ $TitikSampah->lastItem() ?? 0 }}
                    dari {{ $TitikSampah->total() }} fasilitas
                </small>
                {{ $TitikSampah->links() }}
            </div>
        </div>
    </div>
@endsection

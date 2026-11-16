@extends('layouts.app')

@section('title', 'Detail TitikSampah')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('TitikSampah.index') }}">TitikSampah</a></li>
            <li class="breadcrumb-item active">{{ $TitikSampah->nama }}</li>
        </ol>
    </nav>

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="stat-icon text-bg-primary bg-opacity-10 text-primary mx-auto mb-3" style="width:64px;height:64px;font-size:1.8rem;">
                        <i class="bi {{ $TitikSampah->kategori_icon }}"></i>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $TitikSampah->nama }}</h5>
                    <p class="text-secondary small mb-2">{{ $TitikSampah->lokasi }}</p>
                    <span class="badge text-bg-light border">{{ $TitikSampah->kategori }}</span>

                    <hr>

                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-secondary">Kondisi</span><span>{!! $TitikSampah->kondisi_badge !!}</span>
                    </div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-secondary">Total Pengaduan</span>
                        <span class="fw-semibold">{{ $totalPengaduan }}</span>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span class="text-secondary">Terdaftar</span>
                        <span>{{ $TitikSampah->created_at?->format('d M Y') }}</span>
                    </div>

                    <div class="text-start mt-3 small text-secondary">
                        {{ $TitikSampah->deskripsi ?: 'Belum ada deskripsi untuk fasilitas ini.' }}
                    </div>

                    @if (auth()->user()->canManage())
                        <div class="d-grid gap-2 mt-3">
                            <a href="{{ route('fasilitas.edit', $fasilitas) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-pencil me-1"></i>Edit Fasilitas
                            </a>
                            <button class="btn btn-outline-danger btn-sm"
                                    data-delete-url="{{ route('fasilitas.destroy', $fasilitas) }}"
                                    data-delete-name="fasilitas &quot;{{ $fasilitas->nama }}&quot;">
                                <i class="bi bi-trash me-1"></i>Hapus Fasilitas
                            </button>
                        </div>
                    @else
                        <div class="d-grid gap-2 mt-3">
                            <a href="{{ route('pengaduan.index') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-megaphone me-1"></i>Buat Pengaduan
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-semibold">
                    <i class="bi bi-clock-history me-1"></i>Riwayat Pengaduan
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Laporan</th>
                                <th>Pelapor</th>
                                <th>deskripsi</th>
                                <th>Kapasitas_kg</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($TitikSampah->Laporan as $Laporan)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $Laporan->judul }}</div>
                                        <small class="text-secondary">{{ \Illuminate\Support\Str::limit($Laporan->deskripsi, 70) }}</small>
                                    </td>
                                    <td class="small">{{ $Laporan->user->name ?? '-' }}</td>
                                    <td class="small">{{ $Laporan->kapasitas_kg?}}</td>
                                    <td>{!! $Laporan->status_badge !!}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-secondary py-4">
                                        titik sampah ini penuh.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

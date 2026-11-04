@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-4">
        <h4 class="mb-1">Halo, {{ auth()->user()->name }}</h4>
        <p class="text-muted mb-0">Anda login sebagai <strong>{{ auth()->user()->role->label() }}</strong>. Pilih menu di bawah untuk mulai bekerja.</p>
    </div>

    <div class="row g-3">
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="icon-box green"><i class="bi bi-journal-bookmark"></i></span>
                    <div class="flex-grow-1">
                        <div class="text-muted small">Total Judul Buku</div>
                        <div class="fs-4 fw-semibold">{{ $totalBuku }}</div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 pt-0">
                    <a href="{{ route('buku.index') }}" class="small text-decoration-none">
                        Lihat katalog buku <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="icon-box orange"><i class="bi bi-arrow-left-right"></i></span>
                    <div class="flex-grow-1">
                        <div class="text-muted small">Sedang Dipinjam</div>
                        <div class="fs-4 fw-semibold">{{ $totalDipinjam }}</div>
                        <div class="text-muted small">{{ $totalPeminjaman }} transaksi peminjaman</div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 pt-0">
                    <a href="{{ route('peminjaman.index') }}" class="small text-decoration-none">
                        Kelola peminjaman <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        @if (auth()->user()->isAdmin())
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <span class="icon-box blue"><i class="bi bi-people"></i></span>
                        <div class="flex-grow-1">
                            <div class="text-muted small">Total User</div>
                            <div class="fs-4 fw-semibold">{{ $totalUsers }}</div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0 pt-0">
                        <a href="{{ route('admin.users.index') }}" class="small text-decoration-none">
                            Kelola User <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

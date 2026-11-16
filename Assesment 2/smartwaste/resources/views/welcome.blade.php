<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SmartWaste &middot; Sistem Informasi SmartWaste</title>
    <script>
        document.documentElement.setAttribute('data-bs-theme', localStorage.getItem('campuscare-theme') || 'light');
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: radial-gradient(circle at top left, rgba(13,110,253,.15), transparent 45%),
                        radial-gradient(circle at bottom right, rgba(13,202,240,.18), transparent 45%),
                        var(--bs-body-bg);
        }
        .hero-badge {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .4rem .9rem; border-radius: 999px;
            background: rgba(13,110,253,.1); color: #0d6efd; font-weight: 600; font-size: .85rem;
        }
        .feature-icon {
            width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center;
            justify-content: center; font-size: 1.4rem; background: rgba(13,110,253,.1); color: #0d6efd;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div class="d-flex align-items-center gap-2 fw-bold fs-5">
                <span class="d-inline-flex align-items-center justify-content-center rounded-3 text-white"
                      style="width:38px;height:38px;background:linear-gradient(135deg,#0d6efd,#0dcaf0);">
                    <i class="bi bi-buildings"></i>
                </span>
                CampusCare
            </div>
            <div class="d-flex gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Daftar</a>
                @endauth
            </div>
        </div>

        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="hero-badge mb-3"><i class="bi bi-stars"></i>Laravel HMVC &middot; Blade &middot; jQuery AJAX</span>
                <h1 class="display-5 fw-bold mb-3">Kelola fasilitas kampus, tindak lanjuti pengaduan.</h1>
                <p class="text-secondary fs-5 mb-4">
                    SmartWaste membantu petugas memantau kondisi TPS dan memudahkan Warga
                    melaporkan secara cepat &mdash; tanpa perlu mengisi formulir kertas.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-4">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Mulai Demo
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-lg px-4">
                        Daftar Akun Warga
                    </a>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="feature-icon mb-3"><i class="bi bi-building-gear"></i></div>
                                <h6 class="fw-bold">Data Laporan</h6>
                                <p class="text-secondary small mb-0">CRUD Laporan.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="feature-icon mb-3"><i class="bi bi-megaphone"></i></div>
                                <h6 class="fw-bold">Pengaduan AJAX</h6>
                                <p class="text-secondary small mb-0">Kirim laporan kerusakan beserta foto tanpa reload halaman.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="feature-icon mb-3"><i class="bi bi-shield-check"></i></div>
                                <h6 class="fw-bold">Role &amp; Middleware</h6>
                                <p class="text-secondary small mb-0">Petugas dan Warga mendapat hak akses yang berbeda.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="feature-icon mb-3"><i class="bi bi-moon-stars"></i></div>
                                <h6 class="fw-bold">Responsif &amp; Dark Mode</h6>
                                <p class="text-secondary small mb-0">Tampilan nyaman di ponsel maupun desktop, tema tersimpan.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-5">
        <p class="text-center small text-secondary mb-0">
            &copy; {{ date('Y') }} CampusCare &mdash; Latihan Praktikum PABW 2.1 (Laravel HMVC, Blade, jQuery AJAX)
        </p>
    </div>
</body>
</html>

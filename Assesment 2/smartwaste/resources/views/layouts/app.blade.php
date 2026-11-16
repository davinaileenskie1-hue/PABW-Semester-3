<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') &middot; SmartWarte</title>

    {{-- Simpan tema sebelum render agar tidak berkedip --}}
    <script>
        (function () {
            var theme = localStorage.getItem('SmartWarte-theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', theme);
        })();
    </script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body { min-height: 100vh; }
        .navbar-brand-icon {
            width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center;
            border-radius: 10px; background: linear-gradient(135deg, #0d6efd, #0dcaf0); color: #fff;
        }
        .stat-card { border: 0; border-radius: 1rem; }
        .stat-icon {
            width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center;
            justify-content: center; font-size: 1.3rem;
        }
        .table > :not(caption) > * > * { padding: .85rem .75rem; }
        .foto-thumb { width: 64px; height: 48px; object-fit: cover; border-radius: 8px; }
        .global-loader {
            position: fixed; inset: 0; background: rgba(15, 23, 42, .35); z-index: 2000;
            display: flex; align-items: center; justify-content: center; backdrop-filter: blur(1px);
        }
        .nav-link.active { font-weight: 600; }
        .page-link { cursor: pointer; }
        [data-bs-theme="dark"] .card, [data-bs-theme="dark"] .modal-content { border-color: #2b3035; }
        .hero-gradient {
            background: linear-gradient(135deg, rgba(13,110,253,.10), rgba(13,202,240,.14));
            border-radius: 1.5rem;
        }
        .fade-in { animation: fadeIn .25s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: none; } }
        .kondisi-dot { width: 9px; height: 9px; border-radius: 50%; display: inline-block; margin-right: 6px; }
    </style>
    @stack('styles')
</head>
<body class="bg-body-tertiary">

<nav class="navbar navbar-expand-lg bg-body border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-semibold" href="{{ route('dashboard') }}">
            <span class="navbar-brand-icon"><i class="bi bi-buildings"></i></span>
            SmartWarte
            <span class="badge text-bg-light border align-middle d-none d-md-inline">HMVC</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Buka menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('titik TPS.*') ? 'active' : '' }}" href="{{ route('fasilitas.index') }}">
                        <i class="bi bi-building me-1"></i>titik TPS
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('pengaduan.*') ? 'active' : '' }}" href="{{ route('pengaduan.index') }}">
                        <i class="bi bi-megaphone me-1"></i>Pengaduan
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                {{-- Dark mode toggle --}}
                <button class="btn btn-outline-secondary btn-sm" id="theme-toggle" type="button" title="Mode terang / gelap">
                    <i class="bi bi-moon-stars" id="theme-icon"></i>
                </button>

                <div class="dropdown">
                    <button class="btn btn-light border btn-sm dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                        <img src="{{ auth()->user()->foto_url }}" alt="avatar" width="24" height="24" class="rounded-circle">
                        <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                        <span class="badge {{ auth()->user()->isPetugas() ? 'text-bg-warning' : 'text-bg-success' }}">
                            {{ auth()->user()->role_label }}
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li class="px-3 py-2 small text-secondary">
                            {{ auth()->user()->email }}
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="dropdown-item text-danger" type="submit">
                                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

{{-- Indikator loading global (dipakai juga oleh AJAX) --}}
<div class="global-loader d-none" id="global-loader">
    <div class="text-center text-white">
        <div class="spinner-border" role="status"></div>
        <div class="small mt-2">Memproses...</div>
    </div>
</div>

<main class="container py-4">
    @yield('content')
</main>

<footer class="border-top py-3 mt-4">
    <div class="container d-flex flex-wrap justify-content-between small text-secondary">
        <span>&copy; {{ date('Y') }} CampusCare &mdash; Sistem Informasi Fasilitas Kampus (Laravel HMVC + Blade + jQuery AJAX)</span>
        <span>Praktikum PABW &middot; Latihan 2.1</span>
    </div>
</footer>

{{-- Container toast notifikasi --}}
<div class="toast-container position-fixed bottom-0 end-0 p-3" id="toast-container" style="z-index: 2100;"></div>

{{-- Modal konfirmasi hapus (dipakai semua modul) --}}
<div class="modal fade" id="confirm-modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle text-danger me-1"></i>Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="confirm-modal-body">Yakin ingin menghapus data ini?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirm-modal-yes">
                    <i class="bi bi-trash me-1"></i>Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<form id="delete-form" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    /* =====================================================================
     | Helper global: toast, loading, konfirmasi hapus, dark mode
     |===================================================================== */
    const App = {
        toast(message, type = 'success') {
            const el = $(`
                <div class="toast align-items-center text-bg-${type} border-0 fade-in" role="alert">
                    <div class="d-flex">
                        <div class="toast-body"></div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>`);

            el.find('.toast-body').text(message);
            $('#toast-container').append(el);
            const toast = new bootstrap.Toast(el[0], { delay: 3500 });
            toast.show();
            el.on('hidden.bs.toast', () => el.remove());
        },

        loading(state) {
            $('#global-loader').toggleClass('d-none', !state);
        },

        confirmDelete(message, onConfirm) {
            $('#confirm-modal-body').text(message);
            const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('confirm-modal'));
            $('#confirm-modal-yes').off('click').on('click', function () {
                modal.hide();
                onConfirm();
            });
            modal.show();
        },

        clearErrors(selector) {
            const $form = $(selector);
            $form.find('.is-invalid').removeClass('is-invalid');
            $form.find('.invalid-feedback').text('');
        },

        showErrors(selector, errors) {
            App.clearErrors(selector);
            Object.keys(errors || {}).forEach(function (field) {
                const $input = $(selector).find('[name="' + field + '"]');
                $input.addClass('is-invalid');
                $input.siblings('.invalid-feedback').text(errors[field][0]);
            });
        }
    };

    // Tampilkan flash message sebagai toast
    @if (session('success'))
        App.toast(@json(session('success')), 'success');
    @endif
    @if (session('error'))
        App.toast(@json(session('error')), 'danger');
    @endif

    // Dark mode toggle
    (function () {
        const root = document.documentElement;
        const icon = document.getElementById('theme-icon');

        function syncIcon() {
            const dark = root.getAttribute('data-bs-theme') === 'dark';
            icon.className = dark ? 'bi bi-sun' : 'bi bi-moon-stars';
        }

        syncIcon();

        document.getElementById('theme-toggle').addEventListener('click', function () {
            const next = root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-bs-theme', next);
            localStorage.setItem('campuscare-theme', next);
            syncIcon();
        });
    })();

    // Tombol hapus generik: <button data-delete-url="..." data-delete-name="...">
    $(document).on('click', '[data-delete-url]', function () {
        const url = $(this).data('delete-url');
        const name = $(this).data('delete-name') || 'data ini';

        App.confirmDelete('Yakin ingin menghapus ' + name + '? Tindakan ini tidak bisa dibatalkan.', function () {
            App.loading(true);
            $('#delete-form').attr('action', url).trigger('submit');
        });
    });

    // Indikator loading untuk navigasi form biasa
    $(document).on('submit', 'form:not(#delete-form):not(.no-loader)', function () {
        App.loading(true);
    });
</script>
@stack('scripts')
</body>
</html>

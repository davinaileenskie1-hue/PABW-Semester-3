<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Masuk') &middot; SmartWarte</title>

    <script>
        (function () {
            var theme = localStorage.getItem('campuscare-theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', theme);
        })();
    </script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
            display: flex; align-items: center; justify-content: center;
        }
        .auth-card { width: 100%; max-width: 460px; border-radius: 1.25rem; }
        .auth-logo {
            width: 56px; height: 56px; border-radius: 16px;
            background: linear-gradient(135deg, #0d6efd, #0dcaf0);
            color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.6rem;
        }
    </style>
</head>
<body class="p-3">
    <div class="card auth-card shadow-lg border-0">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="auth-logo mx-auto mb-3"><i class="bi bi-buildings"></i></div>
                <h4 class="fw-bold mb-1">@yield('heading')</h4>
                <p class="text-secondary small mb-0">@yield('subheading')</p>
            </div>

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>

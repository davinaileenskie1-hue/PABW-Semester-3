<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LaporBanjir') - LaporBanjir BPBD Kab. Bandung</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f2f5f9;
            color: #333;
        }

        header {
            background-color: #0d6efd;
            color: #fff;
            padding: 20px 30px;
        }

        header h1 {
            margin: 0;
            font-size: 24px;
        }

        header p {
            margin: 5px 0 0;
            font-size: 14px;
            opacity: 0.9;
        }

        nav {
            background-color: #0b5ed7;
            padding: 0 30px;
        }

        nav a {
            display: inline-block;
            color: #fff;
            text-decoration: none;
            padding: 14px 18px;
            font-size: 15px;
        }

        nav a:hover {
            background-color: #0a53be;
        }

        main {
            max-width: 900px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .card {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        .card h2 {
            margin-top: 0;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-waspada {
            background-color: #fff3cd;
            color: #856404;
        }

        .badge-siaga {
            background-color: #ffe0b2;
            color: #b45309;
        }

        .badge-awas {
            background-color: #f8d7da;
            color: #b02a37;
        }

        .alert {
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 20px;
            background-color: #f8f9fa;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            margin-top: 14px;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        button {
            margin-top: 20px;
            background-color: #0d6efd;
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-size: 15px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0b5ed7;
        }

        .error-text {
            color: #b02a37;
            font-size: 13px;
            margin-top: 4px;
        }

        footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 13px;
            border-top: 1px solid #e2e6ea;
            margin-top: 40px;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
        }

        .detail-table th,
        .detail-table td {
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid #e2e6ea;
        }

        .detail-table th {
            width: 200px;
            color: #555;
        }
    </style>
</head>
<body>
    <header>
        <h1>LaporBanjir</h1>
        <p>Sistem Pelaporan Banjir - BPBD Kabupaten Bandung</p>
    </header>

    <nav>
        <a href="{{ route('laporan.index') }}">Daftar Laporan</a>
        <a href="{{ route('laporan.create') }}">Buat Laporan</a>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer>
        &copy; {{ date('Y') }} BPBD Kabupaten Bandung - Prototipe LaporBanjir
    </footer>
</body>
</html>

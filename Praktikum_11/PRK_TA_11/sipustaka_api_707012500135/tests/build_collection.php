<?php

/**
 * Membangun Postman Collection SiPustaka API + menjalankan seluruh request
 * sehingga setiap request memiliki saved example (response asli) siap screenshot.
 *
 * Jalankan: php tests/build_collection.php
 * Prasyarat: php artisan serve aktif di http://127.0.0.1:8000 dan database sudah di-seed.
 */

$base = 'http://127.0.0.1:8000/api';
$fixtures = 'postman/fixtures';
$root = dirname(__DIR__);

// ---------------------------------------------------------------------------
// Variabel runtime saat pengujian
// ---------------------------------------------------------------------------
$vars = [
    'base_url' => $base,
    'token' => '',
    'token_andi' => '',
    'book_id' => '1',
    'loan_id' => '1',
    'book_no_cover_id' => '1',
];// ---------------------------------------------------------------------------
// Definisi request (urutan = urutan folder di Postman)
// ---------------------------------------------------------------------------
$entries = [
    // ---------------------------- AUTH ----------------------------
    [
        'folder' => '1. Auth',
        'name' => 'Register Budi (201)',
        'desc' => 'Registrasi akun pertama. Password disimpan dalam bentuk hash.',
        'method' => 'POST',
        'path' => '/auth/register',
        'json' => ['name' => 'Budi Santoso', 'email' => 'budi@example.com', 'password' => 'praktikum123'],
        'expect' => 201,
        'save' => [],
    ],
    [
        'folder' => '1. Auth',
        'name' => 'Register Andi (201)',
        'desc' => 'Registrasi akun kedua, dipakai untuk menguji kepemilikan data (403).',
        'method' => 'POST',
        'path' => '/auth/register',
        'json' => ['name' => 'Andi Pratama', 'email' => 'andi@example.com', 'password' => 'praktikum123'],
        'expect' => 201,
        'save' => [],
    ],
    [
        'folder' => '1. Auth',
        'name' => 'Register email duplikat (409)',
        'desc' => 'Email yang sama tidak boleh didaftarkan dua kali.',
        'method' => 'POST',
        'path' => '/auth/register',
        'json' => ['name' => 'Budi Santoso', 'email' => 'budi@example.com', 'password' => 'praktikum123'],
        'expect' => 409,
        'save' => [],
    ],
    [
        'folder' => '1. Auth',
        'name' => 'Register validasi gagal (422)',
        'desc' => 'Nama kosong dan password kurang dari 8 karakter ditolak Form Request.',
        'method' => 'POST',
        'path' => '/auth/register',
        'json' => ['name' => '', 'email' => 'bukan-email', 'password' => '123'],
        'expect' => 422,
        'save' => [],
    ],
    [
        'folder' => '1. Auth',
        'name' => 'Login Budi (200) + simpan token',
        'desc' => 'Login berhasil mengembalikan access_token. Token disimpan otomatis ke variabel {{token}}.',
        'method' => 'POST',
        'path' => '/auth/login',
        'json' => ['email' => 'budi@example.com', 'password' => 'praktikum123'],
        'expect' => 200,
        'save' => ['token' => 'access_token'],
    ],
    [
        'folder' => '1. Auth',
        'name' => 'Login Andi (200) + simpan token',
        'desc' => 'Token akun kedua disimpan ke variabel {{token_andi}}.',
        'method' => 'POST',
        'path' => '/auth/login',
        'json' => ['email' => 'andi@example.com', 'password' => 'praktikum123'],
        'expect' => 200,
        'save' => ['token_andi' => 'access_token'],
    ],
    [
        'folder' => '1. Auth',
        'name' => 'Login password salah (401)',
        'desc' => 'Password salah menghasilkan 401 Unauthorized.',
        'method' => 'POST',
        'path' => '/auth/login',
        'json' => ['email' => 'budi@example.com', 'password' => 'salahbanget'],
        'expect' => 401,
        'save' => [],
    ],

    // ---------------------------- BUKU ----------------------------
    [
        'folder' => '2. Buku (Books)',
        'name' => 'Daftar buku tanpa token (200)',
        'desc' => 'Melihat daftar buku boleh tanpa login.',
        'method' => 'GET',
        'path' => '/books',
        'token' => null,
        'expect' => 200,
        'save' => [],
    ],
    [
        'folder' => '2. Buku (Books)',
        'name' => 'Tambah buku tanpa token (401)',
        'desc' => 'Menambah buku wajib membawa token Sanctum.',
        'method' => 'POST',
        'path' => '/books',
        'token' => null,
        'form' => [
            ['judul', 'Buku Tanpa Token', 'text'],
            ['penulis', 'Davin', 'text'],
            ['penerbit', 'Informatika', 'text'],
            ['tahun_terbit', '2023', 'text'],
            ['stok', '1', 'text'],
        ],
        'expect' => 401,
        'save' => [],
    ],
    [
        'folder' => '2. Buku (Books)',
        'name' => 'Tambah buku + sampul (201)',
        'desc' => "Format form-data: field teks + field File 'cover'. Sampul disimpan di storage/app/private/covers.",
        'method' => 'POST',
        'path' => '/books',
        'token' => '{{token}}',
        'form' => [
            ['judul', 'Basis Data Lanjutan', 'text'],
            ['penulis', 'Abraham Silberschatz', 'text'],
            ['penerbit', 'Andi Publisher', 'text'],
            ['tahun_terbit', '2019', 'text'],
            ['stok', '3', 'text'],
            ['cover', $fixtures.'/cover_buku.png', 'file'],
        ],
        'expect' => 201,
        'save' => ['book_id' => 'id'],
    ],
    [
        'folder' => '2. Buku (Books)',
        'name' => 'Tambah buku tanpa sampul (201)',
        'desc' => 'Sampul bersifat opsional. cover_url bernilai null bila tidak ada lampiran.',
        'method' => 'POST',
        'path' => '/books',
        'token' => '{{token}}',
        'form' => [
            ['judul', 'Algoritma dan Struktur Data', 'text'],
            ['penulis', 'Rinaldi Munir', 'text'],
            ['penerbit', 'Informatika', 'text'],
            ['tahun_terbit', '2016', 'text'],
            ['stok', '4', 'text'],
        ],
        'expect' => 201,
        'save' => ['book_no_cover_id' => 'id'],
    ],
    [
        'folder' => '2. Buku (Books)',
        'name' => 'Detail buku (200)',
        'desc' => 'Mengambil satu buku berdasarkan id. Memuat cover_url hasil accessor.',
        'method' => 'GET',
        'path' => '/books/{{book_id}}',
        'token' => null,
        'expect' => 200,
        'save' => [],
    ],
    [
        'folder' => '2. Buku (Books)',
        'name' => 'Validasi stok berisi huruf (422)',
        'desc' => 'Aturan integer menolak stok berupa huruf.',
        'method' => 'POST',
        'path' => '/books',
        'token' => '{{token}}',
        'form' => [
            ['judul', 'Buku Stok Salah', 'text'],
            ['penulis', 'Davin', 'text'],
            ['penerbit', 'Informatika', 'text'],
            ['tahun_terbit', '2021', 'text'],
            ['stok', 'abc', 'text'],
        ],
        'expect' => 422,
        'save' => [],
    ],
    [
        'folder' => '2. Buku (Books)',
        'name' => 'Validasi field kosong (422)',
        'desc' => 'Field wajib yang tidak dikirim ditolak dengan pesan per field.',
        'method' => 'POST',
        'path' => '/books',
        'token' => '{{token}}',
        'form' => [
            ['judul', 'Hanya Judul Saja', 'text'],
        ],
        'expect' => 422,
        'save' => [],
    ],
    [
        'folder' => '2. Buku (Books)',
        'name' => 'Format sampul salah (422)',
        'desc' => 'Berkas .ino tidak termasuk aturan mimes:jpg,jpeg,png.',
        'method' => 'POST',
        'path' => '/books',
        'token' => '{{token}}',
        'form' => [
            ['judul', 'Arduino', 'text'],
            ['penulis', 'Davin', 'text'],
            ['penerbit', 'Informatika', 'text'],
            ['tahun_terbit', '2021', 'text'],
            ['stok', '2', 'text'],
            ['cover', $fixtures.'/Arduino.ino', 'file'],
        ],
        'expect' => 422,
        'save' => [],
    ],
    [
        'folder' => '2. Buku (Books)',
        'name' => 'Sampul lebih dari 2 MB (422)',
        'desc' => 'Aturan max:2048 menolak berkas berukuran lebih dari 2 MB.',
        'method' => 'POST',
        'path' => '/books',
        'token' => '{{token}}',
        'form' => [
            ['judul', 'Sampul Kebesaran', 'text'],
            ['penulis', 'Davin', 'text'],
            ['penerbit', 'Informatika', 'text'],
            ['tahun_terbit', '2021', 'text'],
            ['stok', '2', 'text'],
            ['cover', $fixtures.'/cover_besar.png', 'file'],
        ],
        'expect' => 422,
        'save' => [],
    ],
    [
        'folder' => '2. Buku (Books)',
        'name' => 'Ubah buku + ganti sampul (200)',
        'desc' => 'Method spoofing: POST + _method=PATCH agar PHP membaca file. Sampul lama otomatis terhapus.',
        'method' => 'POST',
        'path' => '/books/{{book_id}}',
        'token' => '{{token}}',
        'form' => [
            ['_method', 'PATCH', 'text'],
            ['judul', 'Basis Data Lanjutan (Edisi 2)', 'text'],
            ['stok', '7', 'text'],
            ['cover', $fixtures.'/cover2.jpg', 'file'],
        ],
        'expect' => 200,
        'save' => [],
    ],
    [
        'folder' => '2. Buku (Books)',
        'name' => 'Ubah stok tanpa ganti sampul (200)',
        'desc' => 'PATCH JSON biasa, hanya mengubah stok. Sampul tetap.',
        'method' => 'PATCH',
        'path' => '/books/{{book_id}}',
        'token' => '{{token}}',
        'json' => ['stok' => 9],
        'expect' => 200,
        'save' => [],
    ],
    [
        'folder' => '2. Buku (Books)',
        'name' => 'Buka sampul buku (200)',
        'desc' => 'Endpoint publik, mengembalikan berkas gambar (inline). Jalankan ulang dengan tombol Send.',
        'method' => 'GET',
        'path' => '/books/{{book_id}}/cover',
        'token' => null,
        'expect' => 200,
        'save' => [],
        'noExample' => true,
    ],
    [
        'folder' => '2. Buku (Books)',
        'name' => 'Sampul buku tanpa lampiran (404)',
        'desc' => 'Buku yang tidak punya sampul mengembalikan 404.',
        'method' => 'GET',
        'path' => '/books/{{book_no_cover_id}}/cover',
        'token' => null,
        'expect' => 404,
        'save' => [],
    ],
    [
        'folder' => '2. Buku (Books)',
        'name' => 'Buku tidak ditemukan (404)',
        'desc' => 'findBook() memanggil abort(404) bila id tidak ada.',
        'method' => 'GET',
        'path' => '/books/9999',
        'token' => null,
        'expect' => 404,
        'save' => [],
    ],
    [
        'folder' => '2. Buku (Books)',
        'name' => 'Hapus sampul saja (200)',
        'desc' => 'Field remove_cover=true menghapus lampiran, cover_url menjadi null.',
        'method' => 'PATCH',
        'path' => '/books/{{book_id}}',
        'token' => '{{token}}',
        'json' => ['remove_cover' => true],
        'expect' => 200,
        'save' => [],
    ],
    [
        'folder' => '2. Buku (Books)',
        'name' => 'Hapus buku tanpa token (401)',
        'desc' => 'Menghapus buku juga wajib membawa token.',
        'method' => 'DELETE',
        'path' => '/books/{{book_no_cover_id}}',
        'token' => null,
        'expect' => 401,
        'save' => [],
    ],
    [
        'folder' => '2. Buku (Books)',
        'name' => 'Hapus buku (200)',
        'desc' => 'Buku dihapus bersama berkas sampulnya (kalau ada).',
        'method' => 'DELETE',
        'path' => '/books/{{book_no_cover_id}}',
        'token' => '{{token}}',
        'expect' => 200,
        'save' => [],
    ],

    // ---------------------------- PEMINJAMAN ----------------------------
    [
        'folder' => '3. Peminjaman (Loans)',
        'name' => 'Daftar peminjaman tanpa token (401)',
        'desc' => 'Seluruh endpoint peminjaman wajib login.',
        'method' => 'GET',
        'path' => '/loans',
        'token' => null,
        'expect' => 401,
        'save' => [],
    ],
    [
        'folder' => '3. Peminjaman (Loans)',
        'name' => 'Catat peminjaman (201)',
        'desc' => 'user_id terisi otomatis dari pengguna yang login; relasi book ikut dimuat.',
        'method' => 'POST',
        'path' => '/loans',
        'token' => '{{token}}',
        'json' => ['book_id' => '{{book_id}}', 'tanggal_pinjam' => '2026-11-25', 'status' => 'Dipinjam'],
        'expect' => 201,
        'save' => ['loan_id' => 'id'],
    ],
    [
        'folder' => '3. Peminjaman (Loans)',
        'name' => 'Validasi peminjaman kosong (422)',
        'desc' => 'book_id dan tanggal_pinjam wajib diisi.',
        'method' => 'POST',
        'path' => '/loans',
        'token' => '{{token}}',
        'json' => new stdClass,
        'expect' => 422,
        'save' => [],
    ],
    [
        'folder' => '3. Peminjaman (Loans)',
        'name' => 'Validasi book_id tidak ada (422)',
        'desc' => 'Aturan exists:books,id menolak id buku yang tidak terdaftar.',
        'method' => 'POST',
        'path' => '/loans',
        'token' => '{{token}}',
        'json' => ['book_id' => 999, 'tanggal_pinjam' => '2026-11-25'],
        'expect' => 422,
        'save' => [],
    ],
    [
        'folder' => '3. Peminjaman (Loans)',
        'name' => 'Andi ubah peminjaman Budi (403)',
        'desc' => 'Sudah login tetapi bukan pemilik data → 403 Forbidden.',
        'method' => 'PATCH',
        'path' => '/loans/{{loan_id}}',
        'token' => '{{token_andi}}',
        'json' => ['status' => 'Dikembalikan'],
        'expect' => 403,
        'save' => [],
    ],
    [
        'folder' => '3. Peminjaman (Loans)',
        'name' => 'Andi hapus peminjaman Budi (403)',
        'desc' => 'Penghapusan juga diperiksa kepemilikannya.',
        'method' => 'DELETE',
        'path' => '/loans/{{loan_id}}',
        'token' => '{{token_andi}}',
        'expect' => 403,
        'save' => [],
    ],
    [
        'folder' => '3. Peminjaman (Loans)',
        'name' => 'Budi ubah peminjaman sendiri (200)',
        'desc' => 'Pemilik boleh mengubah status dan tanggal kembali.',
        'method' => 'PATCH',
        'path' => '/loans/{{loan_id}}',
        'token' => '{{token}}',
        'json' => ['status' => 'Dikembalikan', 'tanggal_kembali' => '2026-12-01'],
        'expect' => 200,
        'save' => [],
    ],
    [
        'folder' => '3. Peminjaman (Loans)',
        'name' => 'Daftar peminjaman milik Budi (200)',
        'desc' => 'Hanya menampilkan peminjaman milik pengguna yang login.',
        'method' => 'GET',
        'path' => '/loans',
        'token' => '{{token}}',
        'expect' => 200,
        'save' => [],
    ],
    [
        'folder' => '3. Peminjaman (Loans)',
        'name' => 'Detail peminjaman (200)',
        'desc' => 'Detail satu peminjaman beserta data bukunya.',
        'method' => 'GET',
        'path' => '/loans/{{loan_id}}',
        'token' => '{{token}}',
        'expect' => 200,
        'save' => [],
    ],
    [
        'folder' => '3. Peminjaman (Loans)',
        'name' => 'Peminjaman tidak ditemukan (404)',
        'desc' => 'Id peminjaman yang tidak ada menghasilkan 404.',
        'method' => 'GET',
        'path' => '/loans/9999',
        'token' => '{{token}}',
        'expect' => 404,
        'save' => [],
    ],
    [
        'folder' => '3. Peminjaman (Loans)',
        'name' => 'Budi hapus peminjaman sendiri (200)',
        'desc' => 'Pemilik boleh menghapus catatan peminjamannya.',
        'method' => 'DELETE',
        'path' => '/loans/{{loan_id}}',
        'token' => '{{token}}',
        'expect' => 200,
        'save' => [],
    ],

    // ---------------------------- AKHIR ----------------------------
    [
        'folder' => '4. Akhir (Profil & Logout)',
        'name' => 'Profil pengguna login (200)',
        'desc' => 'GET /auth/me mengembalikan data pengguna pemilik token.',
        'method' => 'GET',
        'path' => '/auth/me',
        'token' => '{{token}}',
        'expect' => 200,
        'save' => [],
    ],
    [
        'folder' => '4. Akhir (Profil & Logout)',
        'name' => 'Logout (200)',
        'desc' => 'Menghapus token yang sedang dipakai.',
        'method' => 'POST',
        'path' => '/auth/logout',
        'token' => '{{token}}',
        'expect' => 200,
        'save' => [],
    ],
    [
        'folder' => '4. Akhir (Profil & Logout)',
        'name' => 'Profil setelah logout (401)',
        'desc' => 'Token yang sudah dihapus tidak berlaku lagi.',
        'method' => 'GET',
        'path' => '/auth/me',
        'token' => '{{token}}',
        'expect' => 401,
        'save' => [],
    ],
    [
        'folder' => '4. Akhir (Profil & Logout)',
        'name' => 'Login ulang Budi (200)',
        'desc' => 'Login kembali agar {{token}} bisa dipakai untuk pengujian berikutnya.',
        'method' => 'POST',
        'path' => '/auth/login',
        'json' => ['email' => 'budi@example.com', 'password' => 'praktikum123'],
        'expect' => 200,
        'save' => ['token' => 'access_token'],
    ],
];

// ---------------------------------------------------------------------------
// Helper
// ---------------------------------------------------------------------------
function resolve(string $value, array $vars): string
{
    return preg_replace_callback('/\{\{(\w+)\}\}/', fn ($m) => (string) ($vars[$m[1]] ?? $m[0]), $value);
}

function resolveDeep(mixed $value, array $vars): mixed
{
    if (is_string($value)) {
        return resolve($value, $vars);
    }
    if (is_array($value)) {
        foreach ($value as $k => $v) {
            $value[$k] = resolveDeep($v, $vars);
        }
    }

    return $value;
}

function send(array $e, array $vars): array
{
    $headers = ['Accept: application/json'];
    $url = $vars['base_url'].resolve($e['path'], $vars);
    $ch = curl_init($url);
    $payload = null;

    if (isset($e['form'])) {
        $payload = [];
        foreach ($e['form'] as [$key, $value, $type]) {
            if ($type === 'file') {
                $payload[$key] = new CURLFile($value);
            } else {
                $payload[$key] = resolve($value, $vars);
            }
        }
    } elseif (array_key_exists('json', $e)) {
        $headers[] = 'Content-Type: application/json';
        $payload = json_encode(resolveDeep($e['json'], $vars), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    $token = $e['token'] ?? null;
    if ($token) {
        $headers[] = 'Authorization: Bearer '.resolve($token, $vars);
    }

    if ($payload !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    }

    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => $e['method'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_HEADER => true,
    ]);

    $start = microtime(true);
    $raw = curl_exec($ch);
    $elapsed = (int) round((microtime(true) - $start) * 1000);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $head = substr($raw, 0, $headerSize);
    $body = substr($raw, $headerSize);
    curl_close($ch);

    return ['code' => $code, 'head' => $head, 'body' => $body, 'time' => $elapsed, 'url' => $url, 'sent' => $headers];
}

function statusText(int $code): string
{
    return [
        200 => 'OK', 201 => 'Created', 401 => 'Unauthorized', 403 => 'Forbidden',
        404 => 'Not Found', 409 => 'Conflict', 422 => 'Unprocessable Content', 500 => 'Internal Server Error',
    ][$code] ?? 'Unknown';
}

function responseHeader(string $head): array
{
    $out = [];
    foreach (explode("\r\n", trim($head)) as $line) {
        if (str_contains($line, ':')) {
            [$k, $v] = explode(':', $line, 2);
            $k = trim($k);
            if (in_array(strtolower($k), ['content-type', 'content-length', 'cache-control'])) {
                $out[] = ['key' => $k, 'value' => trim($v)];
            }
        }
    }

    return $out;
}

// ---------------------------------------------------------------------------
// Jalankan skenario + kumpulkan example
// ---------------------------------------------------------------------------
$examples = [];
$report = [];
$failed = 0;

foreach ($entries as $i => $e) {
    $res = send($e, $vars);
    $ok = $res['code'] === $e['expect'];
    $failed += $ok ? 0 : 1;

    $examples[$i] = $res;
    $report[] = sprintf('%s %-42s HTTP %d (harap %d) %dms', $ok ? '[OK  ]' : '[GAGAL]', $e['name'], $res['code'], $e['expect'], $res['time']);

    // Simpan variabel dari response (token, id)
    if (! empty($e['save'])) {
        $json = json_decode($res['body'], true);
        foreach ($e['save'] as $varName => $jsonKey) {
            if (isset($json[$jsonKey])) {
                $vars[$varName] = is_scalar($json[$jsonKey]) ? (string) $json[$jsonKey] : $vars[$varName];
            }
        }
    }
}

echo implode(PHP_EOL, $report).PHP_EOL.PHP_EOL;
echo $failed === 0 ? "SEMUA REQUEST BERHASIL ($failed gagal)".PHP_EOL : "ADA $failed REQUEST TIDAK SESUAI HARAPAN".PHP_EOL;

// ---------------------------------------------------------------------------
// Susun collection
// ---------------------------------------------------------------------------
function collectionRequest(array $e): array
{
    $headers = [['key' => 'Accept', 'value' => 'application/json']];

    if (isset($e['form'])) {
        $body = ['mode' => 'formdata', 'formdata' => []];
        foreach ($e['form'] as [$key, $value, $type]) {
            $item = ['key' => $key, 'type' => $type];
            if ($type === 'file') {
                $abs = realpath(dirname(__DIR__).'/'.$value);
                $item['src'] = $abs ? str_replace('\\', '/', $abs) : $value;
            } else {
                $item['value'] = $value;
            }
            $body['formdata'][] = $item;
        }
    } elseif (array_key_exists('json', $e)) {
        $headers[] = ['key' => 'Content-Type', 'value' => 'application/json'];
        $body = [
            'mode' => 'raw',
            'raw' => json_encode($e['json'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            'options' => ['raw' => ['language' => 'json']],
        ];
    } else {
        $body = null;
    }

    if (! empty($e['token'])) {
        $headers[] = ['key' => 'Authorization', 'value' => 'Bearer '.$e['token']];
    }

    $path = ltrim(resolve($e['path'], ['base_url' => '', 'book_id' => '{{book_id}}', 'loan_id' => '{{loan_id}}']), '/');
    $segments = explode('/', $path);

    $req = [
        'method' => $e['method'],
        'header' => $headers,
        'url' => [
            'raw' => '{{base_url}}/'.$path,
            'host' => ['{{base_url}}'],
            'path' => $segments,
        ],
        'description' => $e['desc'] ?? '',
    ];

    if ($body !== null) {
        $req['body'] = $body;
    }

    return $req;
}

$folders = [];
$folderIndex = [];

foreach ($entries as $i => $e) {
    $req = collectionRequest($e);
    $res = $examples[$i];

    $script = [
        "pm.test('status ".$e['expect']." ".statusText($e['expect'])."', function () {",
        "    pm.response.to.have.status(".$e['expect'].");",
        '});',
    ];

    // Simpan variabel supaya request berikutnya memakai token/id yang benar
    if (! empty($e['save'])) {
        $script[] = 'if (pm.response.code === '.$e['expect'].') {';
        $script[] = '    const data = pm.response.json();';
        foreach ($e['save'] as $varName => $jsonKey) {
            $script[] = "    pm.collectionVariables.set('$varName', String(data.$jsonKey));";
        }
        $script[] = '}';
    }

    $item = [
        'name' => $e['name'],
        'event' => [[
            'listen' => 'test',
            'script' => [
                'type' => 'text/javascript',
                'exec' => $script,
            ],
        ]],
        'request' => $req,
    ];

    if (empty($e['noExample'])) {
        $ct = 'application/json';
        $isJson = json_decode($res['body']) !== null || trim($res['body']) === '[]';
        $bodyText = $res['body'];

        $item['response'] = [[
            'name' => $e['name'],
            'originalRequest' => $req,
            'status' => statusText($res['code']),
            'code' => $res['code'],
            '_postman_previewlanguage' => $isJson ? 'json' : 'text',
            'header' => $isJson ? [['key' => 'Content-Type', 'value' => $ct]] : responseHeader($res['head']),
            'cookie' => [],
            'body' => $bodyText,
        ]];
    }

    if (! isset($folderIndex[$e['folder']])) {
        $folderIndex[$e['folder']] = count($folders);
        $folders[] = ['name' => $e['folder'], 'item' => []];
    }
    $folders[$folderIndex[$e['folder']]]['item'][] = $item;
}

$collection = [
    'info' => [
        '_postman_id' => 'b7c2a4d0-7070-1250-0135-sipustaka01',
        'name' => 'SiPustaka API - 707012500135',
        'description' => "Collection pengujian REST API SiPustaka (Tugas Praktikum 11 No. 1).\n\n"
            ."Urutan pakai: jalankan folder 1 (Auth) lebih dulu agar {{token}} dan {{token_andi}} terisi otomatis, "
            ."baru folder 2, 3, dan 4.\n\n"
            ."Setiap request sudah menyimpan contoh response hasil pengujian (klik nama request lalu pilih "
            ."'Example' di kanan atas untuk melihat status code dan body tanpa harus Send).",
        'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
    ],
    'variable' => [
        ['key' => 'base_url', 'value' => 'http://localhost:8000/api'],
        ['key' => 'token', 'value' => ''],
        ['key' => 'token_andi', 'value' => ''],
        ['key' => 'book_id', 'value' => (string) $vars['book_id']],
        ['key' => 'loan_id', 'value' => (string) $vars['loan_id']],
    ],
    'item' => $folders,
];

$out = $root.'/postman/SiPustaka_API_707012500135.postman_collection.json';
file_put_contents($out, json_encode($collection, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

echo "Collection ditulis: $out".PHP_EOL;
echo 'Jumlah request: '.count($entries).' ('.count($folders).' folder)'.PHP_EOL;

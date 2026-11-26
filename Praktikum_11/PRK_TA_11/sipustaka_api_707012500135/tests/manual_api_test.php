<?php

/**
 * Skrip pengujian manual endpoint SiPustaka API.
 * Jalankan: php tests/manual_api_test.php (server php artisan serve harus aktif)
 */
$base = 'http://127.0.0.1:8000/api';

function request(string $method, string $url, ?string $token, mixed $body, array $files = [], array $form = []): array
{
    $ch = curl_init($url);
    $headers = ['Accept: application/json'];

    if ($token) {
        $headers[] = 'Authorization: Bearer '.$token;
    }

    if ($files || $form) {
        $payload = $form;
        foreach ($files as $key => $path) {
            $payload[$key] = new CURLFile($path);
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    } elseif ($body !== null) {
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }

    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
    ]);

    $raw = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ['code' => $code, 'body' => $raw];
}

function show(string $label, array $res, int $expect): void
{
    $ok = $res['code'] === $expect ? 'OK  ' : 'GAGAL';
    echo "[$ok] $label -> HTTP {$res['code']} (harap $expect)\n";
    echo '        '.str_replace("\n", ' ', substr($res['body'], 0, 220))."\n\n";
}

$fixtures = __DIR__.'/../postman/fixtures';

// 1-2. Registrasi
show('Register Budi', request('POST', "$base/auth/register", null, [
    'name' => 'Budi Santoso', 'email' => 'budi@example.com', 'password' => 'praktikum123',
]), 201);

show('Register Andi', request('POST', "$base/auth/register", null, [
    'name' => 'Andi Pratama', 'email' => 'andi@example.com', 'password' => 'praktikum123',
]), 201);

// 3. Registrasi email duplikat
show('Register email duplikat', request('POST', "$base/auth/register", null, [
    'name' => 'Budi Santoso', 'email' => 'budi@example.com', 'password' => 'praktikum123',
]), 409);

// 4-5. Login dua akun
$login = request('POST', "$base/auth/login", null, ['email' => 'budi@example.com', 'password' => 'praktikum123']);
show('Login Budi', $login, 200);
$tokenBudi = json_decode($login['body'], true)['access_token'] ?? '';

$login2 = request('POST', "$base/auth/login", null, ['email' => 'andi@example.com', 'password' => 'praktikum123']);
show('Login Andi', $login2, 200);
$tokenAndi = json_decode($login2['body'], true)['access_token'] ?? '';

// 6. Login password salah
show('Login password salah', request('POST', "$base/auth/login", null, [
    'email' => 'budi@example.com', 'password' => 'salahbanget',
]), 401);

// 7. Tambah buku tanpa token
show('Tambah buku tanpa token', request('POST', "$base/books", null, null, [], [
    'judul' => 'Tanpa Token', 'penulis' => 'X', 'penerbit' => 'Y', 'tahun_terbit' => '2020', 'stok' => '1',
]), 401);

// 8. Tambah buku dengan JSON
show('Tambah buku (JSON)', request('POST', "$base/books", $tokenBudi, [
    'judul' => 'Algoritma dan Struktur Data', 'penulis' => 'Rinaldi Munir',
    'penerbit' => 'Informatika', 'tahun_terbit' => 2016, 'stok' => 4,
]), 201);

// 9. Validasi stok berisi huruf
show('Validasi stok huruf', request('POST', "$base/books", $tokenBudi, [
    'judul' => 'Stok Salah', 'penulis' => 'X', 'penerbit' => 'Y', 'tahun_terbit' => 2020, 'stok' => 'abc',
]), 422);

// 10. Validasi field kosong
show('Validasi field kosong', request('POST', "$base/books", $tokenBudi, ['judul' => 'Hanya Judul']), 422);

// 11. Buku tidak ditemukan
show('Buku tidak ditemukan', request('GET', "$base/books/9999", null, null), 404);

// 12. Tambah buku dengan sampul
$withCover = request('POST', "$base/books", $tokenBudi, null, [
    'cover' => "$fixtures/cover_buku.png",
], [
    'judul' => 'Basis Data Lanjutan', 'penulis' => 'Silberschatz',
    'penerbit' => 'Andi Publisher', 'tahun_terbit' => '2019', 'stok' => '3',
]);
show('Tambah buku + sampul', $withCover, 201);
$bookId = json_decode($withCover['body'], true)['id'] ?? 0;

// 13. Buka sampul (publik)
$cover = request('GET', "$base/books/$bookId/cover", null, null);
show('Buka sampul '.$bookId, $cover, 200);

// 13b. Sampul buku tanpa lampiran
show('Sampul buku tanpa lampiran', request('GET', "$base/books/1/cover", null, null), 404);

// 14. Ganti sampul via POST + _method=PATCH
show('Ganti sampul (_method=PATCH)', request('POST', "$base/books/$bookId", $tokenBudi, null, [
    'cover' => "$fixtures/cover2.jpg",
], [
    '_method' => 'PATCH', 'judul' => 'Basis Data Lanjutan (Edisi 2)', 'stok' => '7',
]), 200);

// 15. Format sampul salah
show('Format sampul .ino', request('POST', "$base/books", $tokenBudi, null, [
    'cover' => "$fixtures/Arduino.ino",
], [
    'judul' => 'Arduino', 'penulis' => 'Davin', 'penerbit' => 'Informatika',
    'tahun_terbit' => '2021', 'stok' => '1',
]), 422);

// 16. Sampul lebih dari 2 MB
show('Sampul > 2 MB', request('POST', "$base/books", $tokenBudi, null, [
    'cover' => "$fixtures/cover_besar.png",
], [
    'judul' => 'Sampul Kebesaran', 'penulis' => 'Davin', 'penerbit' => 'Informatika',
    'tahun_terbit' => '2021', 'stok' => '1',
]), 422);

// 17. Hapus sampul
show('Hapus sampul (remove_cover)', request('PATCH', "$base/books/$bookId", $tokenBudi, ['remove_cover' => true]), 200);

// 18. Catat peminjaman
$loan = request('POST', "$base/loans", $tokenBudi, [
    'book_id' => $bookId, 'tanggal_pinjam' => '2026-11-25', 'status' => 'Dipinjam',
]);
show('Catat peminjaman', $loan, 201);
$loanId = json_decode($loan['body'], true)['id'] ?? 0;

// 19. Peminjaman tanpa token
show('Daftar peminjaman tanpa token', request('GET', "$base/loans", null, null), 401);

// 20. Validasi peminjaman kosong
show('Validasi peminjaman kosong', request('POST', "$base/loans", $tokenBudi, []), 422);

// 21. Validasi book_id tidak ada
show('Validasi book_id tidak ada', request('POST', "$base/loans", $tokenBudi, [
    'book_id' => 999, 'tanggal_pinjam' => '2026-11-25',
]), 422);

// 22. Andi mengubah peminjaman Budi
show('Andi ubah peminjaman Budi', request('PATCH', "$base/loans/$loanId", $tokenAndi, ['status' => 'Dikembalikan']), 403);

// 23. Andi menghapus peminjaman Budi
show('Andi hapus peminjaman Budi', request('DELETE', "$base/loans/$loanId", $tokenAndi, null), 403);

// 24. Budi mengubah peminjamannya sendiri
show('Budi ubah peminjaman sendiri', request('PATCH', "$base/loans/$loanId", $tokenBudi, [
    'status' => 'Dikembalikan', 'tanggal_kembali' => '2026-12-01',
]), 200);

// 25. Daftar peminjaman milik sendiri
show('Daftar peminjaman Budi', request('GET', "$base/loans", $tokenBudi, null), 200);

// 26. Profil
show('Profil Budi', request('GET', "$base/auth/me", $tokenBudi, null), 200);

// 27. Hapus buku (sampul ikut terhapus)
show('Hapus buku', request('DELETE', "$base/books/$bookId", $tokenBudi, null), 200);

// 28. Logout lalu token tidak berlaku
show('Logout Budi', request('POST', "$base/auth/logout", $tokenBudi, null), 200);
show('Profil setelah logout', request('GET', "$base/auth/me", $tokenBudi, null), 401);

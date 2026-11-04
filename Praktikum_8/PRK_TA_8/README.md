# SiPustaka (PRK_TA_8)

Aplikasi perpustakaan kampus berbasis **Laravel 12** dengan arsitektur **HMVC**
(`nwidart/laravel-modules`), authorization berbasis role, dan CRUD AJAX.
Dibuat untuk Tugas Praktikum Modul 8 (nomor 1).

## Cara Menjalankan

```bash
composer install
copy .env.example .env        # Windows
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

Buka http://127.0.0.1:8000

## Akun Awal (dari seeder)

| Role  | Email               | Password |
|-------|---------------------|----------|
| Admin | admin@example.com   | password |
| User  | user@example.com    | password |

Akun hasil register selalu berperan **user**.

## Struktur

Bagian di luar HMVC:

- `app/Http/Controllers/Auth` — register, login, logout (manual)
- `app/Http/Middleware/EnsureUserHasRole.php` — middleware `role`, alias didaftarkan di `bootstrap/app.php`
- `app/Enums/UserRole.php` — daftar role (`admin`, `user`)

Modul HMVC (`Modules/`):

| Modul            | Jenis              | Akses        |
|------------------|--------------------|--------------|
| `Buku`           | CRUD synchronous   | Admin ubah, user lihat |
| `Peminjaman`     | CRUD AJAX + upload | Admin dan user |
| `UserManagement` | CRUD synchronous   | Admin        |

## Hak Akses

- **Tamu** → hanya halaman login/register.
- **User** → dashboard, katalog buku (read-only), peminjaman (CRUD AJAX).
- **Admin** → semua di atas + tambah/ubah/hapus buku + kelola user.

Membuka halaman khusus admin sebagai user menghasilkan **403 Forbidden**.

## Fitur per Modul

**Buku** — judul, penulis, penerbit, tahun_terbit, stok. Pencarian judul/penulis/penerbit
dan pagination 10 data per halaman.

**Peminjaman** — nama_peminjam, judul_buku, tanggal_pinjam, tanggal_kembali, status
(Dipinjam/Dikembalikan), plus bukti (jpg/png/pdf maks 2 MB). Semua operasi lewat AJAX
(tanpa refresh), pencarian di sisi browser, bukti dapat dibuka/diganti dan ikut terhapus
dari storage.

**UserManagement** — nama, email, role, password. Admin tidak dapat menghapus atau
menurunkan role akunnya sendiri.

## Pengujian

```bash
php artisan test
```

31 pengujian (Pest) mencakup authentication, pembatasan percobaan login, middleware role
(403), CRUD buku, CRUD user, CRUD AJAX peminjaman termasuk validasi 422, 404, 419 (CSRF),
penolakan format/ukuran berkas, penggantian dan penghapusan berkas.

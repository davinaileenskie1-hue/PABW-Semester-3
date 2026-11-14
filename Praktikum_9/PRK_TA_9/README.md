# SiPustaka (PRK_TA_9)

Aplikasi perpustakaan kampus berbasis **Laravel 12** dengan arsitektur **HMVC**
(`nwidart/laravel-modules`), authorization berbasis role, dan CRUD AJAX.
Dibuat untuk Tugas Praktikum Modul 9.

## Cara Menjalankan

```bash
composer install
copy .env.example .env        # Windows
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
npm install
npm run build                 # atau npm run dev
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

- `app/Http/Controllers/Auth` - register, login, logout
- `app/Http/Middleware/EnsureUserHasRole.php` - middleware `role`
- `app/Enums/UserRole.php` - daftar role (`admin`, `user`)

Modul HMVC (`Modules/`):

| Modul            | Jenis              | Akses        |
|------------------|--------------------|--------------|
| `Buku`           | CRUD synchronous   | Admin ubah, user lihat |
| `Peminjaman`     | CRUD AJAX + upload | Admin dan user |
| `UserManagement` | CRUD synchronous   | Admin        |

## Hak Akses

- **Tamu** - hanya halaman login/register.
- **User** - dashboard, katalog buku (read-only), peminjaman (CRUD AJAX).
- **Admin** - semua di atas + tambah/ubah/hapus buku + kelola user.

Membuka halaman khusus admin sebagai user menghasilkan **403 Forbidden**.

## Fitur per Modul

**Buku** - judul, penulis, penerbit, tahun_terbit, stok. Pencarian judul/penulis/penerbit
dan pagination 10 data per halaman.

**Peminjaman** - nama_peminjam, judul_buku, tanggal_pinjam, tanggal_kembali, status
(Dipinjam/Dikembalikan), plus bukti (jpg/png/pdf maks 2 MB). Semua operasi lewat AJAX
(tanpa refresh), pencarian di sisi browser, bukti dapat dibuka/diganti dan ikut terhapus
dari storage.

**UserManagement** - nama, email, role, password. Admin tidak dapat menghapus atau
menurunkan role akunnya sendiri.

## Pengujian

```bash
php artisan test
```

83 pengujian (Pest) lulus dengan 408 assertion, mencakup authentication, pembatasan
percobaan login, middleware role (403), CRUD buku, CRUD user, CRUD AJAX peminjaman
termasuk validasi 422, 404, 419 (CSRF), penolakan format/ukuran berkas, penggantian
dan penghapusan berkas.

## Laporan

- `707012500135_Davin Aileenskie Sofiandy_TA_modul9.docx`
- `707012500135_Davin Aileenskie Sofiandy_TA_modul9.pdf`
- Aset gambar laporan: `laporan-assets/`

Repositori: https://github.com/davinaileenskie1-hue/PABW-Semester-3

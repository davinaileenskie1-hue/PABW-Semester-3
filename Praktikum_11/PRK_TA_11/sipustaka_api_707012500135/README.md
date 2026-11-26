# SiPustaka API — Tugas Praktikum 11 No. 1

REST API perpustakaan kampus menggunakan Laravel 12 + Laravel Sanctum.
Dikerjakan mengikuti Modul 11 (Membuat REST API dengan Laravel) dengan studi kasus
**SiPustaka**: tabel `books` dan `loans` yang saling berelasi.

- Nama: Davin Aileenskie Sofiandy
- NIM: 707012500135
- Project: `sipustaka_api_707012500135`
- Database: `db_sipustaka`

## Cara Menjalankan

```bash
# 1. Buat database db_sipustaka di phpMyAdmin (collation utf8mb4_unicode_ci)
# 2. Sesuaikan koneksi database pada .env
composer install
php artisan key:generate

# 3. Jalankan migration
php artisan migrate

# 4. Jalankan server
php artisan serve
```

Base URL: `http://localhost:8000/api`

## Struktur Tabel

`books`

| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| id | bigint | primary key |
| judul | varchar(200) | judul buku |
| penulis | varchar(150) | nama penulis |
| penerbit | varchar(150) | nama penerbit |
| tahun_terbit | year | tahun terbit buku |
| stok | int unsigned | jumlah stok |
| cover_path | varchar | lokasi sampul di storage (nullable, disembunyikan dari JSON) |
| cover_name | varchar | nama asli sampul (nullable) |
| timestamps | | created_at, updated_at |

`loans`

| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| id | bigint | primary key |
| book_id | bigint | foreign key ke `books` |
| user_id | bigint | foreign key ke `users` |
| tanggal_pinjam | date | tanggal mulai pinjam |
| tanggal_kembali | date | tanggal kembali (nullable) |
| status | enum | `Dipinjam` / `Dikembalikan` |
| timestamps | | created_at, updated_at |

## Daftar Endpoint

### Publik (tanpa token)

| Method | Endpoint | Keterangan |
| --- | --- | --- |
| POST | `/api/auth/register` | registrasi pengguna |
| POST | `/api/auth/login` | login, mengembalikan token |
| GET | `/api/books` | daftar buku |
| GET | `/api/books/{id}` | detail buku |
| GET | `/api/books/{id}/cover` | membuka sampul buku |

### Wajib token (`Authorization: Bearer <token>`)

| Method | Endpoint | Keterangan |
| --- | --- | --- |
| GET | `/api/auth/me` | profil pengguna yang login |
| POST | `/api/auth/logout` | hapus token aktif |
| POST | `/api/books` | tambah buku (form-data bila membawa `cover`) |
| PATCH | `/api/books/{id}` | ubah buku |
| DELETE | `/api/books/{id}` | hapus buku beserta sampulnya |
| GET | `/api/loans` | daftar peminjaman milik sendiri |
| POST | `/api/loans` | catat peminjaman baru |
| GET | `/api/loans/{id}` | detail peminjaman milik sendiri |
| PATCH | `/api/loans/{id}` | ubah peminjaman milik sendiri |
| DELETE | `/api/loans/{id}` | hapus peminjaman milik sendiri |

## Catatan Uji

- Menambah/mengubah buku beserta sampul memakai `multipart/form-data`.
  Karena PHP tidak membaca file pada request PATCH, penggantian sampul dikirim dengan
  `POST /api/books/{id}` + field `_method=PATCH` (method spoofing).
- Sampul disimpan di `storage/app/private/covers` dan hanya bisa dibuka lewat
  `GET /api/books/{id}/cover`.
- Sampul lama otomatis dihapus saat diganti, saat `remove_cover=true`, atau saat buku dihapus.
- Validasi: sampul maksimal 2 MB (`max:2048`) dengan format `jpg, jpeg, png`.
  File lain atau file kebesaran ditolak dengan status 422.
- Kode status yang diuji: 200, 201, 401, 403, 404, 409, dan 422.

## Koleksi Postman

Berkas: `postman/SiPustaka_API_707012500135.postman_collection.json` (36 request, 4 folder).

**Cara import:** buka Postman, tekan `Ctrl + O` (Import), pilih berkas di atas.

**Urutan pengujian** (jalankan dari atas ke bawah, folder 1 sampai 4):

1. `1. Auth` - register, validasi, login; token otomatis tersimpan ke variabel
   `{{token}}` / `{{token_andi}}`.
2. `2. Buku (Books)` - daftar/detail buku, tambah/ubah/hapus, upload sampul,
   sekaligus kasus 401/422/404.
3. `3. Peminjaman (Loans)` - pencatatan pinjam, validasi, dan uji kepemilikan (403).
4. `4. Akhir (Profil & Logout)` - profil, logout, akses ulang (401), lalu login ulang.

Di dalam folder `2` ada request **Tambah buku + sampul** yang mengisi variabel
`{{book_id}}`, sehingga request sesudahnya otomatis memakai id buku yang baru dibuat.

Setiap request sudah menyimpan **contoh response hasil pengujian** (status code + body),
jadi bisa langsung dibuka tanpa menekan Send. Untuk hasil live, tekan **Send**
(atau `Ctrl + Enter`).

**Hasil pengujian otomatis** (Newman, mesin Postman yang sama):

```
requests: 36, failed: 0
test-scripts: 36, failed: 0
assertions: 36, failed: 0
```

Laporan lengkap: `postman/test-report.json`.

Script pembangun koleksi: `php tests/build_collection.php`
(menjalankan semua request lalu menulis ulang berkas JSON beserta contoh response).

# LaporBanjir

Aplikasi pelaporan banjir untuk Tugas Akhir Modul 7 Praktikum Pengembangan Aplikasi Berbasis Web. Warga dapat mengirim laporan dari form publik tanpa akun, sedangkan petugas mengelola laporan setelah login.

## Fitur

- Form laporan publik: nama pelapor, nomor HP, lokasi, kecamatan, tinggi air, keterangan, dan foto bukti.
- CRUD laporan untuk petugas: daftar, detail, edit, dan hapus.
- Upload foto ke disk `public` dengan validasi `jpg`, `jpeg`, `png`, maksimal 2 MB.
- Foto lama dihapus saat diganti, dan file foto ikut dihapus saat laporan dihapus.
- Thumbnail, status penanganan, pencarian data, dan pagination lima baris per halaman.
- Autentikasi petugas memakai Laravel Breeze.
- Form warga dapat diakses tanpa login, halaman pengelolaan dilindungi middleware `auth`.

## Teknologi

- Laravel 12.70.1 dan Laravel Breeze 2.4.2 (Blade)
- PHP 8.2
- MySQL atau MariaDB
- Tailwind CSS dan Vite

## Menjalankan Proyek

1. Clone repositori dan masuk ke folder proyek.

   ```bash
   git clone https://github.com/davinaileenskie1-hue/PABW-Semester-3.git
   cd PABW-Semester-3/Praktikum_7/PRK_TA_7
   ```

2. Pasang dependensi PHP dan JavaScript.

   ```bash
   composer install
   npm install
   ```

3. Salin konfigurasi contoh, lalu buat application key.

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Buat database `db_laporbanjir_modul7` di MySQL atau phpMyAdmin. Konfigurasi bawaan memakai host `127.0.0.1`, port `3306`, user `root`, dan password kosong. Sesuaikan bagian `DB_*` di `.env` jika berbeda.

5. Jalankan migrasi dan seeder.

   ```bash
   php artisan migrate --seed
   ```

6. Buat symbolic link storage, lalu build aset frontend.

   ```bash
   php artisan storage:link
   npm run build
   ```

7. Jalankan server.

   ```bash
   php artisan serve
   ```

   Buka `http://127.0.0.1:8000`.

## Akun Demo

Setelah `php artisan migrate --seed`, akun bawaan seeder adalah:

- Email: `test@example.com`
- Password: `password`

Akun yang dipakai saat pengujian lokal:

- Email: `petugas@laporbanjir.test`
- Password: `password123`

Akun kedua hanya ada jika database lokal dari proses pengujian masih dipakai. Pada instalasi baru, daftarkan akun petugas melalui halaman `/register`.

## Halaman Utama

- `/laporan/create`: form laporan warga, dapat dibuka tanpa login.
- `/laporan`: daftar laporan untuk petugas, memerlukan login.
- `/laporan/{id}`: detail laporan.
- `/laporan/{id}/edit`: form edit laporan.
- `/dashboard`: halaman setelah login.

## Struktur Data

Tabel `laporans` berisi kolom:

- `nama_pelapor`
- `no_hp`
- `lokasi`
- `kecamatan`
- `tinggi_air` (cm)
- `keterangan` (opsional)
- `foto` (path file di disk `public`)
- `status` (default `Menunggu`)
- `timestamps`

Foto disimpan di `storage/app/public/laporan` dan diakses melalui `public/storage`.

## Pengujian

```bash
php artisan test
```

Proyek ini lulus 25 tes bawaan Laravel Breeze. Pengujian alur laporan dilakukan secara manual: kirim laporan, validasi jenis dan ukuran file, pagination, detail, edit status, ganti foto, dan hapus laporan.

## Laporan

- [Laporan Praktikum (DOCX)](<707012500135_Davin Aileenskie Sofiandy_TA_modul7.docx>)
- [Laporan Praktikum (PDF)](<707012500135_Davin Aileenskie Sofiandy_TA_modul7.pdf>)

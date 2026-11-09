"""Builds the Modul 9 lab report from the Modul 8 report template.

The Modul 8 file supplies the cover and page setup; its body paragraphs are
removed and replaced with the new content. The source file is never modified.

Usage: py laporan-tools\build_laporan.py
"""

import re
from pathlib import Path

from docx import Document
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.shared import Emu, Pt

ROOT = Path(__file__).resolve().parent.parent
SRC = Path(r"D:\xampp\htdocs\PABW\Praktikum_8\PRK_8\707012500135_Davin Aileenskie Sofiandy_MODUL_08.docx")
OUT = ROOT / "707012500135_Davin Aileenskie Sofiandy_MODUL_09.docx"
RAY = ROOT / "laporan-assets" / "ray"
SHOTS = ROOT / "laporan-assets" / "dokumentasi"

# 5,486,400 EMU is the content width used by the Modul 8 report.
IMG_W = 5486400
# Very long code files would overflow the page at full width, so they are
# scaled down to keep the code readable without a two-page stretch.
IMG_W_TALL = 4419600

doc = Document(SRC)

# The cover is inherited from Modul 8, so only its subject line changes.
doc.paragraphs[1].runs[0].text = "Modul 9: HMVC, Otorisasi, dan AJAX pada React Starter Kit"

body_start = 14
for paragraph in doc.paragraphs[body_start:]:
    paragraph._element.getparent().remove(paragraph._element)

# Remove images that lived in the removed body paragraphs.
for rel_id, rel in list(doc.part.rels.items()):
    if rel.reltype.endswith("/image") and rel_id not in doc.part.package.image_parts:
        pass


def head(text: str) -> None:
    paragraph = doc.add_paragraph()
    run = paragraph.add_run(text)
    run.bold = True


def para(text: str) -> None:
    doc.add_paragraph(text)


def bullet(text: str) -> None:
    doc.add_paragraph(text)


def pic(path: Path, wide: bool = False) -> None:
    doc.add_picture(str(path), width=Emu(IMG_W if wide else IMG_W))


def code_pic(name: str, tall: bool = False) -> None:
    # Snippet files are stored without their source extension (11-user-form.png).
    path = RAY / re.sub(r"\.(php|json|tsx?|js)$", "", name)
    path = path.with_suffix(".png")
    if not path.exists():
        raise FileNotFoundError(path)
    width = IMG_W_TALL if tall else IMG_W
    doc.add_picture(str(path), width=Emu(width))


def shot(name: str) -> None:
    path = SHOTS / name
    if not path.exists():
        raise FileNotFoundError(path)
    doc.add_picture(str(path), width=Emu(IMG_W))


def caption(text: str) -> None:
    paragraph = doc.add_paragraph(text)
    paragraph.alignment = WD_ALIGN_PARAGRAPH.CENTER


# 1. Tujuan Praktikum
head("1. Tujuan Praktikum")
para(
    "Praktikum Modul 9 ini saya kerjakan untuk membangun aplikasi Laravel dengan struktur HMVC, "
    "pembatasan akses berdasarkan role, dan CRUD berbasis AJAX di atas Laravel React Starter Kit. "
    "Materi modul memakai aplikasi perpustakaan, sedangkan project yang saya pakai adalah "
    "pertemuan9_hmvc_react dengan database hmvc_react. Starter kit sudah membawa Inertia, React, "
    "TypeScript, Tailwind, dan shadcn/ui, jadi hampir semua tampilan ditulis sebagai komponen React, "
    "bukan Blade. Tujuan yang ingin saya capai adalah:"
)
for item in [
    "1. Memasang nwidart/laravel-modules pada starter kit React dan membuat dua modul, yaitu UserManagement dan Pendaftaran.",
    "2. Menambahkan kolom role pada tabel users, membuat enum UserRole, dan memasang middleware role pada route.",
    "3. Memakai autentikasi bawaan starter kit (register, login, logout) yang berada di luar modul, lalu memastikan akun baru selalu beroleh role user.",
    "4. Membuat modul UserManagement dengan CRUD akun yang hanya bisa dibuka admin, lengkap dengan pencarian dan filter role.",
    "5. Membuat modul Pendaftaran dengan CRUD berbasis AJAX, respons JSON, dan unggah berkas lewat FormData.",
    "6. Menampilkan pesan berhasil dan pesan error per input, baik dari flash session maupun dari respons 422 JSON.",
    "7. Menulis pengujian otomatis dengan Pest untuk role, middleware, CRUD kedua modul, dan seeder.",
]:
    bullet(item)

# 2. Alat dan Bahan
head("2. Alat dan Bahan")
para(
    "Praktikum ini saya kerjakan pada laptop pribadi dengan Windows 11 Pro, prosesor Intel Core "
    "i7-13650HX, dan RAM 16 GB. Berkas project disimpan di D:\\xampp\\htdocs\\PABW\\Praktikum_9\\pertemuan9_hmvc_react "
    "dan dijalankan lewat XAMPP (Apache dan MySQL) serta php artisan serve."
)
head("2.1 Perangkat Lunak")
para(
    "Versi perangkat lunak di bawah ini saya catat langsung dari mesin yang dipakai, bukan dari "
    "dokumentasi. PHP 8.2.12, Composer 2.10.3, Node.js v25.6.0, dan npm 11.8.0. Laravel yang "
    "terpasang adalah laravel/framework v12.71.0 dengan inertiajs/inertia-laravel v2.0.28. Paket "
    "modularnya nwidart/laravel-modules v12.0.5. Pengujian memakai pestphp/pest v3.8.7 dan "
    "pest-plugin-laravel v3.2.0. Formatter kode memakai laravel/pint v1.30.4."
)
para(
    "Untuk tampilan, starter kit memakai React 19 dengan TypeScript, Vite, Tailwind CSS, dan "
    "shadcn/ui. Ikon diambil dari lucide-react dan helper route dari Ziggy. Tidak ada jQuery atau "
    "Bootstrap seperti pada modul versi Blade, karena semua halaman dirender oleh React melalui Inertia."
)
head("2.2 Bahan")
para(
    "Bahannya adalah Laravel React Starter Kit yang sudah terpasang beserta autentikasinya. Route "
    "login, register, logout, verifikasi email, dan reset password ada di routes/auth.php dengan "
    "controller di app/Http/Controllers/Auth. Layout aplikasi, sidebar, dan komponen shadcn/ui "
    "sudah tersedia, jadi yang saya tambahkan adalah kolom role, middleware, dua modul, dan halaman "
    "React untuk tiap modul. Database hmvc_react dibuat lewat phpMyAdmin dengan pengguna root tanpa "
    "password mengikuti konfigurasi XAMPP."
)

# 3. Langkah-Langkah Praktikum
head("3. Langkah-Langkah Praktikum")
para(
    "Urutan pengerjaan saya mulai dari database dan konfigurasi, lalu role, lalu dua modul. Modul "
    "UserManagement saya kerjakan lebih dulu karena halaman adminnya memakai cara Inertia biasa. "
    "Setelah itu modul Pendaftaran yang seluruh operasinya lewat AJAX, dan terakhir pengujian otomatis."
)

head("3.1 Menyiapkan Database dan Memasang Paket Modul")
para(
    "Isi .env saya sesuaikan dengan database lokal: DB_CONNECTION=mysql, DB_HOST=127.0.0.1, "
    "DB_PORT=3306, DB_DATABASE=hmvc_react, DB_USERNAME=root, dan DB_PASSWORD kosong. Karena memakai "
    "starter kit React, perintah php artisan migrate dijalankan setelah npm install dan npm run build, "
    "supaya aset Vite tersedia. Paket modular dipasang dengan composer require nwidart/laravel-modules "
    "lalu php artisan vendor:publish --provider=\"Nwidart\\Modules\\LaravelModulesServiceProvider\"."
)
para(
    "Dua modul dibuat dengan php artisan module:make UserManagement dan php artisan module:make "
    "Pendaftaran. Hasilnya berupa folder Modules/UserManagement dan Modules/Pendaftaran yang masing-masing "
    "punya app, routes, resources, dan database sendiri. Status modul dicatat di modules_statuses.json."
)
code_pic("06-modules-statuses.png")

head("3.2 Menambahkan Kolom Role dan Enum UserRole")
para(
    "Kolom role saya tambahkan lewat migration baru, bukan dengan mengubah migration users bawaan, "
    "supaya riwayat migration tetap rapi. Kolomnya berupa string dengan nilai bawaan user, jadi akun "
    "lama otomatis menjadi user biasa. Method down menghapus kolom itu kembali."
)
code_pic("02-migration-role.png")
para(
    "Nilai role tidak saya tulis sebagai string bebas di banyak tempat. Saya membuat enum UserRole di "
    "app/Enums/UserRole.php dengan dua case, Admin dan User, serta method label() untuk teks yang "
    "ditampilkan di tampilan. Backed enum seperti ini bisa di-cast langsung oleh Eloquent dan divalidasi "
    "dengan Rule::enum()."
)
code_pic("01-UserRole.png")

head("3.3 Model User")
para(
    "Model User saya ubah di tiga tempat. Kolom role masuk ke $fillable, nilai bawaannya diatur user "
    "lewat $attributes, dan $casts mengubahnya menjadi enum UserRole. Saya juga menambahkan dua method: "
    "isAdmin() untuk mengecek role admin, dan hasRole() yang menerima enum maupun string sehingga bisa "
    "dipakai middleware dengan parameter role:admin,user."
)
code_pic("03-User.png", tall=True)
para(
    "Selama pengerjaan, starter kit ini sempat membawa kolom two_factor_secret dan two_factor_recovery_codes "
    "di model dan factory, padahal migration users tidak punya kolom tersebut. Karena fitur dua faktor tidak "
    "dipakai di modul ini, bagian itu saya hapus dari model, factory, dan trait TwoFactorAuthenticatable. "
    "Setelah dihapus, migrate:fresh --seed berjalan tanpa error. Factory User saya beri state admin() supaya "
    "seeder dan pengujian lebih mudah menulis data admin."
)

head("3.4 Middleware EnsureUserHasRole")
para(
    "Pembatasan akses saya taruh di satu middleware bernama EnsureUserHasRole. Middleware ini menerima "
    "daftar role lewat parameter variadic, jadi penulisannya bisa role:admin atau role:admin,user. Kalau "
    "pengguna belum login atau role-nya tidak cocok, request dihentikan dengan 403 beserta pesan singkat "
    "berbahasa Indonesia."
)
code_pic("04-EnsureUserHasRole.png")
para(
    "Middleware didaftarkan di bootstrap/app.php dengan alias role. Di file yang sama, HandleInertiaRequests "
    "sudah dipasang pada grup web bawaan starter kit, jadi data pengguna dan flash message bisa dibagikan ke "
    "semua halaman React."
)
code_pic("05-bootstrap-app.png")

head("3.5 Controller Modul UserManagement")
para(
    "Modul UserManagement dipakai admin untuk mengelola akun. Method index membaca parameter search dan role, "
    "lalu menyusun query dengan when() supaya filter hanya aktif kalau diisi. Hasilnya dipaginasi sepuluh "
    "baris dan diubah lewat through() menjadi array kecil yang aman dikirim ke React, lengkap dengan "
    "role_label dan created_at dalam bentuk diffForHumans()."
)
code_pic("08-UserManagementController.png")
para(
    "Method update hanya mengubah password kalau kolomnya diisi, karena nilai password kosong dibuang dari "
    "data tervalidasi. Method destroy menolak penghapusan akun sendiri supaya admin tidak mengunci dirinya "
    "keluar, dan mengembalikan pesan error lewat flash. Daftar pilihan role untuk select di React diambil "
    "dari UserRole::cases() lewat roleOptions(), jadi menambah role baru cukup dilakukan di enum."
)

head("3.6 Route dan Validasi Form User")
para(
    "Route modul UserManagement memakai Route::resource users dengan prefix admin dan nama admin., dibungkus "
    "middleware auth serta role:admin. Method show tidak dipakai sehingga dikecualikan. Karena semuanya berada "
    "di grup itu, URL /admin/users langsung mengembalikan 403 bagi user biasa."
)
code_pic("07-UserManagement-routes.png")
para(
    "Validasi form tambah user ada di StoreUserRequest. Name dan email wajib, email harus unik di tabel users, "
    "role wajib sesuai enum, dan password wajib dikonfirmasi serta mengikuti aturan Password::defaults(). "
    "Authorize() hanya mengizinkan pengguna dengan role admin, jadi request langsung ditolak walaupun route "
    "terlewat."
)
code_pic("09-StoreUserRequest.png")
para(
    "UpdateUserRequest hampir sama, tetapi email diabaikan untuk akun yang sedang diedit dan password boleh "
    "kosong. Ada tambahan Rule::when() yang memaksa role tetap admin kalau yang diedit adalah akun sendiri, "
    "dengan pesan Anda tidak dapat menurunkan role akun Anda sendiri."
)
code_pic("10-UpdateUserRequest.png")

head("3.7 Tampilan React Modul UserManagement")
para(
    "Form tambah dan edit sama-sama memakai komponen user-form.tsx. Komponen ini menerima data, errors, "
    "processing, roles, dan setData dari useForm() Inertia, sehingga tidak ada logika form yang terduplikasi "
    "di dua halaman. Pesan error per input ditampilkan lewat komponen InputError yang bawaan starter kit."
)
code_pic("11-user-form.tsx")
para(
    "Halaman index menampilkan tabel akun, kotak pencarian, dan select filter role. Pencarian dan filter "
    "dikirim ulang lewat router.get() dengan preserveState dan replace, jadi halaman tidak dimuat ulang penuh. "
    "Tombol hapus membuka ConfirmDialog sebelum router.delete() dijalankan, dan akun yang sedang login tidak "
    "menampilkan tombol hapus."
)
code_pic("12-UserManagement-index.tsx")
para(
    "Halaman create memakai useForm() dengan nilai awal role user. Saat form dikirim, post() mengarah ke "
    "/admin/users, lalu kalau berhasil Inertia mengikuti redirect dari controller menuju halaman daftar beserta "
    "flash success."
)
code_pic("13-UserManagement-create.tsx")

head("3.8 Controller dan Route Modul Pendaftaran")
para(
    "Modul Pendaftaran dibungkus middleware auth dan role:admin,user, jadi admin maupun user biasa bisa "
    "mengaksesnya. Rutenya dipisah antara menampilkan halaman dan mengambil data, agar AJAX bisa meminta data "
    "tanpa memuat komponen React lagi. Update memakai PUT dengan parameter {daftar} yang di-resolve otomatis "
    "menjadi model Daftar."
)
code_pic("14-Pendaftaran-routes.png")
para(
    "PendaftaranController mengembalikan halaman React pada index, sedangkan getData() mengirim seluruh data "
    "sebagai JSON. Method store menyimpan berkas dengan store('berkas_pendaftaran', 'public') dan mencatat nama "
    "asli berkas untuk ditampilkan di tabel. Method update hanya mengganti berkas kalau ada yang diunggah, dan "
    "berkas lama di disk public dihapus sebelum berkas pengganti disimpan."
)
code_pic("15-PendaftaranController.png", tall=True)

head("3.9 Model Daftar dan Validasi Form Pendaftaran")
para(
    "Model Daftar mendaftarkan semua kolom ke $fillable. Ada accessor berkas_url yang ikut di JSON lewat "
    "$appends, sehingga React cukup memakai URL siap pakai tanpa menyusun path sendiri. Factory Daftar "
    "didaftarkan lewat newFactory() supaya pengujian bisa memakai Daftar::factory()."
)
code_pic("17-Daftar.png")
para(
    "Validasi form pendaftaran ada di DaftarRequest. Semua isian teks wajib, email unik di tabel daftars, "
    "no_hp harus angka dengan panjang 8 sampai 20, dan berkas wajib saat menambah data tetapi boleh kosong "
    "saat mengedit. Jenis berkas dibatasi jpg, jpeg, png, atau pdf dengan ukuran maksimal 2 MB."
)
code_pic("16-DaftarRequest.png")
para(
    "Pesan validasi saya tulis sendiri dalam bahasa Indonesia, misalnya No HP hanya boleh berisi angka "
    "(8-20 digit) dan Berkas wajib diunggah. Saya juga menambahkan lang/id/validation.php berisi terjemahan "
    "aturan bawaan Laravel beserta daftar nama atribut, karena sebelumnya pesan yang muncul masih berbahasa "
    "Inggris walaupun APP_LOCALE sudah diisi id."
)

head("3.10 Klien AJAX dan Halaman Pendaftaran")
para(
    "Untuk AJAX saya membuat helper http() di resources/js/lib/http.ts. Helper ini mengirim fetch dengan "
    "header Accept JSON, X-Requested-With, dan X-XSRF-TOKEN yang dibaca dari cookie XSRF-TOKEN. Respons yang "
    "tidak OK dilempar sebagai HttpError yang membawa status dan isi body, jadi halaman bisa membedakan "
    "validasi 422 dari error lain."
)
code_pic("18-http.ts")
para(
    "Halaman index Pendaftaran menyimpan data di state records dan memuatnya sekali lewat useEffect(). "
    "Pencarian dilakukan di browser dengan useMemo, jadi tidak ada request baru setiap kali mengetik. Form "
    "tambah dan edit memakai Dialog shadcn/ui dengan field yang dibangun dari array fields agar konsisten."
)
code_pic("19-Pendaftaran-index.tsx", tall=True)
para(
    "Saat form dikirim, semua isian dimasukkan ke FormData, berkas ditambahkan lewat formData.append('berkas', file), "
    "dan mode edit menambahkan _method=PUT. Kalau server menjawab 422, isi errors disimpan ke state dan "
    "ditampilkan per input lewat InputError. Kalau berhasil, dialog ditutup, pesan sukses muncul, dan data "
    "dimuat ulang tanpa berpindah halaman."
)

head("3.11 Komponen Pesan")
para(
    "Pesan berhasil dan error memakai satu komponen AlertMessage dengan dua varian warna. Varian untuk flash "
    "session ditangani FlashMessage, yang membaca shared prop flash dari Inertia dan menutup sendiri setelah "
    "empat detik. Halaman Pendaftaran memakai AlertMessage langsung karena pesannya berasal dari respons AJAX, "
    "bukan dari flash session."
)
code_pic("20-alert-message.tsx")

head("3.12 Sidebar dan Resolusi Halaman Modul")
para(
    "Menu sidebar menampilkan Dashboard dan Pendaftaran untuk semua pengguna yang login, sedangkan Kelola User "
    "hanya muncul untuk admin. Data role dibaca dari shared prop auth.user lewat hook useSharedProps()."
)
code_pic("21-app-sidebar.tsx")
para(
    "Halaman modul dirender dengan nama UserManagement::index atau Pendaftaran::index. Nama tersebut belum "
    "dikenali oleh import.meta.glob bawaan starter kit, jadi saya menambahkan glob kedua untuk folder "
    "Modules/*/resources/js/pages dan memecah nama halaman pada tanda :: sebelum memanggil resolvePageComponent()."
)
code_pic("22-app.tsx")

head("3.13 Data Bersama dan Dashboard")
para(
    "HandleInertiaRequests membagikan auth.user dan flash success/error ke setiap halaman. Karena objek User "
    "sudah di-cast, role ikut terkirim sebagai string admin atau user, sehingga React tidak perlu menebak."
)
code_pic("23-HandleInertiaRequests.php")
para(
    "Hook useSharedProps() merangkum auth.user, isAdmin, dan flash supaya komponen tidak menulis ulang tipe "
    "yang sama. DashboardController menghitung jumlah pendaftar untuk semua pengguna, sedangkan total user hanya "
    "dikirim ke admin. Kartu jumlah user tidak tampil bagi user biasa, dan route-nya tetap aman karena "
    "perlindungan sebenarnya berada di middleware dan Form Request."
)
code_pic("24-use-shared-props.ts")
code_pic("25-DashboardController.php")

head("3.14 Seeder")
para(
    "DatabaseSeeder mengisi dua akun awal: Administrator dengan email admin@example.com berrole admin, dan User "
    "Biasa dengan email user@example.com berrole user. Keduanya memakai password yang sama untuk keperluan "
    "praktikum. Data pendaftar tidak di-seed karena diisi lewat halaman AJAX."
)
code_pic("26-DatabaseSeeder.php")

head("3.15 Menjalankan Aplikasi dan Pengujian Otomatis")
para(
    "Aplikasi dijalankan dengan npm run dev di terminal pertama dan php artisan serve di terminal kedua, lalu "
    "dibuka di http://127.0.0.1:8000. Untuk lingkungan pengujian, phpunit.xml memakai SQLite :memory:, jadi tes "
    "tidak menyentuh database hmvc_react. Berkas tes yang saya tambahkan adalah tests/Feature/RoleTest.php, "
    "tests/Feature/UserManagementTest.php, tests/Feature/PendaftaranTest.php, tests/Feature/DashboardTest.php, "
    "dan tests/Feature/DatabaseSeederTest.php. Seluruh tes dijalankan dengan php artisan test."
)
para(
    "Tes modul Pendaftaran memakai Storage::fake('public') dan UploadedFile::fake(), sehingga berkas tidak "
    "benar-benar ditulis ke storage. Tes route modul memakai ->component('Modul::page', shouldExist: false) "
    "karena view finder Inertia menyelesaikan nama ber-namespace ke folder pages biasa. Setiap tes yang butuh "
    "pengguna login memakai $this->actingAs() langsung, karena helper yang memanggil test()->actingAs() di dalam "
    "closure ternyata tidak mengautentikasi request yang sedang diuji."
)

# 4. Hasil dan Dokumentasi Program
head("4. Hasil dan Dokumentasi Program")
para(
    "Semua bagian sudah berjalan. Tabel users memiliki kolom role, middleware role terpasang pada route kedua "
    "modul, dan halaman kelola user maupun pendaftaran sudah tampil sebagai komponen React. Pengujian otomatis "
    "menghasilkan 75 tes lulus dengan 338 assertion dalam waktu sekitar lima detik."
)
para(
    "Gambar di bawah ini saya ambil dari aplikasi yang benar-benar berjalan di http://127.0.0.1:8123 dengan data "
    "seeder dan beberapa data pendaftar. Tampilan memakai tema gelap bawaan starter kit."
)
shot("01-beranda.png")
caption("Gambar 1. Halaman beranda starter kit sebelum login.")
shot("02-login.png")
caption("Gambar 2. Halaman login bawaan starter kit.")
shot("03-dashboard-admin.png")
caption("Gambar 3. Dashboard setelah login sebagai admin, dengan menu Kelola User dan kartu jumlah user.")
shot("09-dashboard-user.png")
caption("Gambar 4. Dashboard sebagai user biasa. Menu Kelola User dan kartu jumlah user tidak tampil.")
shot("05-kelola-user-admin.png")
caption("Gambar 5. Halaman Kelola User dengan pencarian, filter role, dan pagination.")
shot("06-tambah-user-baru.png")
caption("Gambar 6. Form tambah user dengan pilihan role dari enum UserRole.")
shot("08-validasi-user.png")
caption("Gambar 7. Pesan validasi per input pada form tambah user, tampil dalam bahasa Indonesia.")
shot("07-edit-user.png")
caption("Gambar 8. Form edit user. Kolom password boleh kosong bila tidak ingin diganti.")
shot("10-user-403.png")
caption("Gambar 9. Halaman 403 saat user biasa membuka /admin/users.")
shot("12-pendaftaran-user.png")
caption("Gambar 10. Halaman Pendaftaran ketika dibuka oleh user biasa. Menu Kelola User tidak ada di sidebar.")
shot("04-pendaftaran.png")
caption("Gambar 11. Halaman Pendaftaran yang memuat data lewat AJAX.")
shot("11-dialog-tambah-pendaftar.png")
caption("Gambar 12. Dialog tambah pendaftar beserta kolom unggah berkas.")
shot("13-validasi-ajax-pendaftaran.png")
caption("Gambar 13. Respons 422 dari AJAX ditampilkan sebagai pesan error di bawah tiap field.")
shot("14-sukses-tambah-pendaftar.png")
caption("Gambar 14. Data baru muncul di tabel tanpa memuat ulang halaman, disertai pesan berhasil.")
shot("15-hasil-pengujian.png")
caption("Gambar 15. Hasil php artisan test, seluruh berkas tes lulus.")

# 5. Analisis dan Pembahasan
head("5. Analisis dan Pembahasan")
para("1. Pemetaan HMVC pada project ini.")
para(
    "HMVC di sini bukan berarti setiap modul berjalan sebagai aplikasi terpisah. Semua modul masih memakai satu "
    "database, satu konfigurasi, dan satu proses Laravel. Yang dipisahkan adalah kode: controller, request, "
    "model, route, dan halaman React milik UserManagement dan Pendaftaran berada di dalam folder Modules, "
    "sementara autentikasi, layout, dan komponen UI tetap di app dan resources. Dependency antarmodul nyaris "
    "tidak ada karena keduanya hanya berbagi model User dan komponen tampilan starter kit."
)
para("2. Autentikasi di luar modul, otorisasi di dalam route.")
para(
    "Autentikasi tetap memakai controller bawaan starter kit pada routes/auth.php. Saya menambahkan require "
    "__DIR__.'/auth.php'; di routes/web.php karena tanpanya route login tidak terdaftar dan halaman beranda "
    "mengarah ke 404. Otorisasi baru muncul setelah pengguna dikenali, yaitu lewat middleware role pada grup route "
    "modul. Pemisahan ini membuat aturan akses mudah dibaca: cukup melihat deklarasi grup route untuk tahu siapa "
    "yang boleh masuk."
)
para("3. Pembatasan akses berlapis.")
para(
    "Middleware role:admin adalah penjaga pertama. Authorize() pada StoreUserRequest dan UpdateUserRequest adalah "
    "lapisan kedua untuk operasi tulis, sedangkan lapisan ketiga ada di tampilan: menu Kelola User hanya dirender "
    "untuk admin dan tombol hapus disembunyikan pada baris akun sendiri. Lapisan tampilan bukan pengaman utama, "
    "tetapi mengurangi peluang pengguna menekan aksi yang pasti ditolak server."
)
para("4. Pengecekan role yang aman terhadap nilai tidak dikenal.")
para(
    "Filter role di halaman kelola user memakai UserRole::tryFrom(). Nilai yang tidak dikenal akan menjadi null "
    "dan filter dilewati, bukan menghasilkan error. Ada tes khusus yang mengirim role=superadmin dan memastikan "
    "halaman tetap terbuka dengan seluruh data. Ini penting karena parameter filter datang dari URL yang bisa "
    "diubah siapa saja."
)
para("5. Perbandingan CRUD Inertia dan CRUD AJAX.")
para(
    "CRUD user memakai Inertia seperti halaman lain di starter kit: form mengirim request, controller "
    "mengembalikan redirect, dan Inertia menukar komponen tanpa memuat ulang dokumen. Halaman Pendaftaran memakai "
    "cara yang lebih manual: fetch() ke endpoint JSON, state React diperbarui, dan tabel digambar ulang. Cara "
    "kedua lebih banyak menulis kode, tetapi jelas memperlihatkan alur request dan respons yang dimaksud modul."
)
para("6. Token CSRF pada permintaan AJAX.")
para(
    "fetch() tidak otomatis mengirim token CSRF seperti form Laravel. Helper http() membaca cookie XSRF-TOKEN "
    "yang dibuat Laravel dan mengirimkannya sebagai header X-XSRF-TOKEN. Saat menguji dengan skrip terpisah, "
    "permintaan tanpa header ini langsung dijawab 419, jadi jelas bahwa token memang diperlukan. Hal yang sama "
    "berlaku untuk update: metode PUT dikirim lewat POST dengan _method=PUT karena FormData tidak mendukung PUT "
    "secara langsung."
)
para("7. Pengelolaan berkas pendaftaran.")
para(
    "Berkas disimpan di storage/app/public/berkas_pendaftaran dengan nama acak dari store(), sedangkan nama asli "
    "disimpan di kolom nama_berkas. Tautan berkas dibentuk oleh accessor berkas_url memakai asset('storage/...'), "
    "sehingga butuh php artisan storage:link agar bisa dibuka. Saat berkas diganti, berkas lama dihapus lalu berkas "
    "baru disimpan dengan nama acak. Saat data dihapus, berkasnya ikut dihapus supaya storage tidak menumpuk file yatim."
)
para("8. Kendala dan penyelesaian.")
para(
    "Kendala pertama muncul saat menjalankan pengujian: tes verifikasi email bawaan starter kit gagal karena "
    "redirect-nya mengarah ke /home, bukan /dashboard. Setelah saya telusuri, rute verify-email milik Fortify "
    "ternyata terdaftar lebih dulu dan menimpa rute starter kit, sehingga response Fortify dengan konfigurasi "
    "home=/home yang dipakai. Perbaikannya adalah memanggil Fortify::ignoreRoutes() di AppServiceProvider dan "
    "menambahkan config/fortify.php berisi home=/dashboard. Setelah itu seluruh tes lulus."
)
para(
    "Kendala kedua ada di tampilan. Halaman modul mengembalikan error Unable to locate file in Vite manifest "
    "karena resources/views/app.blade.php memuat @vite untuk halaman per halaman, padahal hanya bundle app.tsx yang "
    "dibangun. Saya menggantinya dengan satu @vite(['resources/js/app.tsx']) dan mengandalkan Inertia untuk merender "
    "komponen. Kendala ketiga, pesan validasi masih berbahasa Inggris walaupun APP_LOCALE=id, karena lang/id hanya "
    "berisi auth.php. Solusinya menambahkan lang/id/validation.php beserta nama atribut."
)
para(
    "Kendala keempat saya temukan saat mengambil tangkapan layar. Submit dengan fetch() biasa ternyata tidak "
    "menampilkan pesan validasi di halaman, karena respons error tidak diteruskan ke komponen. Setelah form diisi "
    "dan dikirim lewat tombol di dalam halaman, pesan per input muncul seperti pada Gambar 7 dan Gambar 13. Ini "
    "sekaligus memastikan alur yang dipakai pengguna benar-benar bekerja."
)
para("9. Perbedaan dengan modul.")
para(
    "Perbedaan paling besar ada pada basis aplikasinya. Modul memakai Laravel dengan Blade, Bootstrap, dan jQuery, "
    "sedangkan saya memakai Laravel React Starter Kit dengan Inertia, React, dan shadcn/ui. Akibatnya semua bagian "
    "tampilan ditulis sebagai komponen TypeScript, modal memakai Dialog shadcn/ui, dan pesan error memakai komponen "
    "InputError. Di sisi server, pola yang dipakai tetap sama seperti modul: migration terpisah, enum role, "
    "middleware dengan parameter, Form Request, serta controller yang memisahkan halaman dan endpoint JSON. "
    "Perbedaan lain, project ini tidak memakai fitur verifikasi email pada model User karena baris MustVerifyEmail "
    "di model memang dikomentari secara bawaan starter kit."
)

# 6. Kesimpulan
head("6. Kesimpulan")
para(
    "Struktur modular berhasil diterapkan pada Laravel React Starter Kit dengan paket nwidart/laravel-modules. "
    "Modul UserManagement dan Pendaftaran berdiri sebagai folder terpisah dengan route, controller, request, model, "
    "dan halaman React sendiri, sementara autentikasi dan layout tetap berada di luar modul."
)
para(
    "Otorisasi berbasis role berjalan di dua tingkat. Middleware role:admin dan role:admin,user menjaga route, "
    "sedangkan Form Request menjaga operasi tulis. Pengujian membuktikan pengunjung diarahkan ke halaman login, "
    "user biasa menerima 403 saat membuka /admin/users, dan admin tidak bisa menurunkan role atau menghapus akunnya "
    "sendiri."
)
para(
    "CRUD AJAX di modul Pendaftaran berjalan tanpa memuat ulang halaman, termasuk unggah dan penggantian berkas. "
    "Respons 422 dari server tampil sebagai pesan error tepat di bawah field yang bermasalah, dan respons berhasil "
    "tampil sebagai alert di atas tabel. Pesan validasi sudah berbahasa Indonesia setelah lang/id/validation.php "
    "ditambahkan."
)
para(
    "Pengujian otomatis dengan Pest mencakup enum dan middleware role, CRUD kedua modul, unggah berkas, seeder, "
    "dashboard, serta route bawaan starter kit. Hasil akhirnya 75 tes lulus dengan 338 assertion. Perintah "
    "php artisan test bisa dijalankan ulang kapan saja tanpa menyentuh database hmvc_react karena tes memakai SQLite "
    "di memori."
)

# 7. Link Repository
head("7. Link Repository Github")
para("https://github.com/davinaileenskie1-hue/PABW-Semester-3")
para(
    "Project berada di folder Praktikum_9/pertemuan9_hmvc_react. Perubahan pada modul ini belum saya commit ke "
    "repository saat laporan ini disusun."
)

doc.save(OUT)
print("saved", OUT)
print("paragraphs:", len(doc.paragraphs))
print("inline shapes:", len(doc.inline_shapes))

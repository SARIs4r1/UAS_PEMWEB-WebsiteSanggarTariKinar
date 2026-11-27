# Website Company Profile Sanggar Tari Kinar

UAS Pemrograman Web: Sari (D1041241061)

Sanggar Tari Kinar adalah sanggar seni tari yang berpusat di Kecamatan Tekarang, Kabupaten Sambas, Kalimantan Barat, dengan pusat latihan di SMAN 1 Tekarang. Website ini adalah **company profile** sanggar. Penyewaan kostum dan properti tari hanya layanan pelengkap.

## Fitur

- Halaman publik: beranda, profil (visi, misi, kontak), tari, kegiatan, galeri foto (dengan lightbox), katalog sewa kostum
- Autentikasi: register, login, logout (password di-hash, CSRF token, session aman)
- Role dan access control: **admin** (kelola seluruh isi situs) dan **pelanggan** (ajukan dan pantau sewa)
- CRUD admin: kegiatan, galeri foto, tari, katalog sewa (semua dengan upload foto), pengaturan teks situs, persetujuan permintaan sewa

## Tech stack

PHP native, MySQL (PDO), HTML, CSS, dan JavaScript tanpa framework.

## Struktur folder

```text
├── admin/              -> halaman khusus role admin (dashboard, kegiatan, galeri, tari, katalog, sewa, pengaturan)
├── assets/
│   ├── css/            -> kinar.css (stylesheet utama)
│   └── js/             -> main.js (interaksi navbar, toast, lightbox galeri)
├── database/           -> db_sanggar.sql (skema database + data awal)
├── includes/           -> bootstrap, db, auth (role), helpers (CSRF, upload, setting), header, footer, admin_header, admin_footer
├── uploads/            -> foto yang diunggah admin (tidak di-commit)
├── index.php           -> beranda company profile
├── profil.php          -> profil sanggar (visi, misi, pusat latihan & kontak)
├── tari.php            -> daftar tarian yang dibawakan sanggar
├── kegiatan.php        -> agenda & dokumentasi kegiatan sanggar
├── galeri.php          -> galeri foto sanggar
├── katalog.php         -> katalog sewa kostum & properti tari (layanan pelengkap)
├── katalog_detail.php  -> detail item sewa
├── register.php / login.php / logout.php
├── sewa_ajukan.php     -> form pengajuan sewa (pelanggan)
└── sewa_saya.php       -> riwayat & status sewa pelanggan
```

## Menjalankan di lokal

1. Import `database/db_sanggar.sql` (phpMyAdmin atau `mysql -u root -p < database/db_sanggar.sql`).
2. Sesuaikan kredensial di `includes/db.php` bila perlu.
3. Dari folder project: `php -S localhost:8000` (atau buka `http://localhost/UAS_PEMWEB` lewat Laragon).
4. Buka `http://localhost:8000` di browser.

Akun admin: `admin@sanggar.test` / `admin123` (ganti setelah deploy). Pelanggan mendaftar lewat halaman Daftar.

## Mengisi foto dan konten

Masuk sebagai admin, lalu di Panel Admin:

- **Pengaturan situs**: foto latar beranda, foto bagian tentang, teks profil, visi, misi, jadwal latihan, WhatsApp, email, Instagram
- **Galeri foto**: unggah banyak foto sekaligus
- **Kegiatan, Tari, Katalog sewa**: tambah, ubah, hapus beserta fotonya

Data tari dan katalog bawaan hanyalah contoh, silakan ubah atau hapus.

## Deploy

1. Buat database di hosting, import `database/db_sanggar.sql` (hapus dua baris `CREATE DATABASE` dan `USE` bila hosting sudah menyediakan database).
2. Isi kredensial hosting di `includes/db.php`.
3. Upload semua file lewat FTP/File Manager dan pastikan folder `uploads/` bisa ditulis (izin 755 atau 775).

## Progres

### Tahap 1 (20 November 2026)
- Analisis kebutuhan sistem: fitur utama (company profile, autentikasi, CRUD, role admin dan pelanggan)
- Perancangan basis data (ERD) dan wireframe halaman utama
- Setup repository Git, struktur folder, dan scaffold awal (auth, CRUD, role-based access control)

### Tahap 2 (27 November 2026)
- Pembaruan skema database `db_sanggar.sql` dengan penambahan tabel `settings`, `tari`, `kegiatan`, dan `galeri`
- Penambahan fitur CRUD admin untuk mengelola tarian, kegiatan sanggar, galeri foto, serta pengaturan profil situs
- Perombakan tampilan beranda dan halaman publik agar berfokus pada company profile Sanggar Tari Kinar (Tekarang, Sambas)
- Penambahan proteksi CSRF dan fitur unggah foto pada formulir

## Catatan pengembangan lanjutan

- Belum ada payment gateway untuk sewa kostum (bisa jadi pengembangan KP).
- Ketersediaan stok per tanggal belum dihitung; saat ini stok hanya batas jumlah pengajuan.
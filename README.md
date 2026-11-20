# Website Company Profile dan Katalog Penyewaan Kostum serta Properti Tari

UAS Pemrograman Web — Sari (D1041241061)

## Deskripsi

Website company profile dan katalog penyewaan kostum serta properti tari, dengan fitur:

- Company profile (beranda & profil usaha)
- Katalog kostum & properti tari (publik, bisa difilter per kategori)
- Autentikasi (register & login) dengan hash password
- CRUD katalog (Create, Read, Update, Delete) khusus role **admin**
- Role-based access control: **admin** (kelola katalog & permintaan sewa) vs **pelanggan** (lihat katalog & ajukan sewa)
- Pengelolaan permintaan sewa (pelanggan mengajukan, admin menyetujui/menolak)

## Tech Stack

- PHP native (tanpa framework)
- MySQL (PDO)
- HTML/CSS native

## Struktur Folder

```
├── admin/              -> halaman khusus role admin (CRUD katalog, kelola sewa)
├── assets/css/         -> stylesheet
├── database/schema.sql -> skema database + data awal
├── includes/           -> koneksi DB, auth/access control, header & footer
├── index.php           -> beranda
├── profil.php          -> profil usaha
├── katalog.php         -> katalog publik
├── katalog_detail.php  -> detail item
├── register.php / login.php / logout.php
├── sewa_ajukan.php     -> form pengajuan sewa (pelanggan)
└── sewa_saya.php       -> riwayat sewa pelanggan
```

## Cara Menjalankan (Local)

1. Import `database/schema.sql` ke MySQL (lewat phpMyAdmin atau `mysql -u root -p < database/schema.sql`).
2. Sesuaikan kredensial database di `includes/db.php` kalau perlu.
3. Jalankan dengan PHP built-in server dari root project:
   ```
   php -S localhost:8000
   ```
4. Buka `http://localhost:8000` di browser.

**Akun demo:**
- Admin: `admin@sanggar.test` / `admin123`
- Pelanggan: daftar sendiri lewat halaman Register.

## Progres

### Tahap 1 — 20 November 2026
- Analisis kebutuhan sistem: menentukan fitur utama (company profile, katalog kostum & properti tari, autentikasi, CRUD data katalog, role admin & pelanggan)
- Perancangan basis data (ERD) untuk entitas `users`, `items`, dan `rental_requests`
- Perancangan wireframe untuk halaman beranda/profil usaha, katalog, detail item, dan dashboard admin
- Setup repository Git, struktur folder project, dan scaffold awal (auth, CRUD katalog, role-based access control)

## Catatan Pengembangan Lanjutan

- Belum ada payment gateway (di luar scope UAS, bisa jadi pengembangan KP lanjutan).
- Upload foto item belum diimplementasikan (kolom `foto` sudah disiapkan di skema).
- `katalog_delete.php` memakai GET + konfirmasi JS untuk kesederhanaan; untuk produksi sebaiknya diubah ke POST.

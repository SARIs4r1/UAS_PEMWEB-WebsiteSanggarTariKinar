CREATE DATABASE IF NOT EXISTS db_sanggar CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_sanggar;

-- Pengguna + role (access control): admin | pelanggan
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','pelanggan') NOT NULL DEFAULT 'pelanggan',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Pengaturan isi situs (teks profil, kontak, foto hero) - diubah admin
CREATE TABLE IF NOT EXISTS settings (
    k VARCHAR(50) PRIMARY KEY,
    v TEXT NOT NULL
);

-- Tari yang dibawakan sanggar
CREATE TABLE IF NOT EXISTS tari (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(150) NOT NULL,
    asal VARCHAR(100) DEFAULT NULL,
    deskripsi TEXT,
    foto VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Kegiatan / agenda / berita sanggar
CREATE TABLE IF NOT EXISTS kegiatan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    tanggal DATE NOT NULL,
    lokasi VARCHAR(150) DEFAULT NULL,
    ringkasan VARCHAR(300) DEFAULT NULL,
    isi TEXT,
    foto VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Galeri foto
CREATE TABLE IF NOT EXISTS galeri (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(150) DEFAULT NULL,
    foto VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Layanan pelengkap: katalog sewa kostum & properti
CREATE TABLE IF NOT EXISTS items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(150) NOT NULL,
    kategori ENUM('Kostum Tari','Properti Tari') NOT NULL,
    deskripsi TEXT,
    harga_sewa_per_hari DECIMAL(10,2) NOT NULL DEFAULT 0,
    stok INT NOT NULL DEFAULT 0,
    foto VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS rental_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    item_id INT NOT NULL,
    tanggal_mulai DATE NOT NULL,
    tanggal_selesai DATE NOT NULL,
    jumlah INT NOT NULL DEFAULT 1,
    catatan TEXT,
    status ENUM('menunggu','disetujui','ditolak') NOT NULL DEFAULT 'menunggu',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE
);

-- Akun admin: admin@sanggar.test / admin123 (password sudah di-hash bcrypt, GANTI setelah deploy)
INSERT IGNORE INTO users (id, name, email, password, role) VALUES
(1, 'Admin Sanggar', 'admin@sanggar.test', '$2y$12$wWhYd.BZPQcmaehsyYlSqOr9NABUG0TAot5mH9F5Do3NmN1.Orpay', 'admin');

-- Isi awal situs (semua bisa diubah di Panel Admin > Pengaturan situs)
INSERT IGNORE INTO settings (k, v) VALUES
('nama_sanggar', 'Sanggar Tari Kinar'),
('tagline', 'Sanggar seni tari dari Kecamatan Tekarang, Kabupaten Sambas, Kalimantan Barat.'),
('tentang', 'Sanggar Tari Kinar adalah sanggar seni tari yang berpusat di Kecamatan Tekarang, Kabupaten Sambas, Kalimantan Barat. Pusat latihannya berada di SMAN 1 Tekarang.\n\nDi sini pelajar dan generasi muda belajar menari, mengenal tari tradisional daerah, dan tampil di berbagai acara sekolah, festival, serta kegiatan budaya.'),
('visi', 'Menjadi sanggar tari yang melestarikan seni budaya daerah dan membanggakan masyarakat Tekarang dan Kabupaten Sambas.'),
('misi', 'Menyelenggarakan latihan tari secara rutin dan terarah di SMAN 1 Tekarang.\nMelestarikan tari tradisional daerah lewat pementasan dan festival budaya.\nMembina generasi muda agar mencintai dan percaya diri membawakan seni tari.\nMenyediakan sewa kostum dan properti tari untuk sekolah dan masyarakat.'),
('jadwal_latihan', 'Hubungi kami untuk jadwal latihan terbaru.'),
('alamat', 'SMAN 1 Tekarang, Kec. Tekarang, Kab. Sambas, Kalimantan Barat'),
('whatsapp', ''),
('email', ''),
('instagram', ''),
('hero_foto', ''),
('about_foto', '');

-- Contoh data. Ubah/hapus lewat Panel Admin sesuai kenyataan di sanggar.
INSERT INTO tari (nama, asal, deskripsi)
SELECT * FROM (
    SELECT 'Tari Jepin' AS nama, 'Melayu Sambas' AS asal, 'Tari pergaulan Melayu yang dibawakan berpasangan dengan gerak kaki lincah dan diiringi musik khas.' AS deskripsi
    UNION ALL SELECT 'Tari Tandak Sambas', 'Melayu Sambas', 'Tari tradisional Melayu Sambas yang sering tampil dalam acara adat dan penyambutan tamu.'
    UNION ALL SELECT 'Tari Kreasi', 'Karya sanggar', 'Garapan baru yang memadukan gerak tradisi dengan penataan panggung masa kini untuk festival dan lomba.'
) AS t WHERE NOT EXISTS (SELECT 1 FROM tari);

INSERT INTO items (nama, kategori, deskripsi, harga_sewa_per_hari, stok)
SELECT * FROM (
    SELECT 'Baju Kurung Melayu (Putri)' AS nama, 'Kostum Tari' AS kategori, 'Baju kurung lengkap untuk penari putri, tersedia beberapa ukuran.' AS deskripsi, 75000 AS harga, 5 AS stok
    UNION ALL SELECT 'Baju Teluk Belanga (Putra)', 'Kostum Tari', 'Setelan teluk belanga untuk penari putra.', 70000, 5
    UNION ALL SELECT 'Selendang Songket', 'Properti Tari', 'Selendang bermotif songket untuk pelengkap busana tari.', 25000, 10
    UNION ALL SELECT 'Kipas Tari', 'Properti Tari', 'Kipas tari untuk pementasan tari kreasi.', 15000, 15
) AS t WHERE NOT EXISTS (SELECT 1 FROM items);

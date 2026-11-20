-- Skema database: Website Company Profile & Katalog Penyewaan Kostum serta Properti Tari
-- Jalankan file ini di phpMyAdmin / mysql client sebelum menjalankan aplikasi.

CREATE DATABASE IF NOT EXISTS db_kostum_tari CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_kostum_tari;

-- Tabel pengguna + role (role-based access control)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','pelanggan') NOT NULL DEFAULT 'pelanggan',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel katalog kostum & properti tari (CRUD oleh admin)
CREATE TABLE items (
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

-- Tabel permintaan sewa dari pelanggan
CREATE TABLE rental_requests (
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

-- Akun admin default -> email: admin@sanggar.test | password: admin123
-- (hash di bawah ini dibuat dengan PHP password_hash(), BUKAN hash sembarangan)
INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@sanggar.test', '$2y$10$nXzw0e0zHHkb94B/Af6raeeZdShNV0.eDcHuPrev2NHoG7WtGoUz.', 'admin');

-- Contoh data katalog awal
INSERT INTO items (nama, kategori, deskripsi, harga_sewa_per_hari, stok) VALUES
('Kostum Tari Jaipong', 'Kostum Tari', 'Kostum tari Jaipong lengkap dengan aksesoris kepala.', 75000, 5),
('Kostum Tari Saman', 'Kostum Tari', 'Kostum tari Saman warna merah-hitam, ukuran dewasa.', 70000, 8),
('Properti Payung Tari', 'Properti Tari', 'Payung hias untuk tarian tradisional.', 25000, 10),
('Properti Kipas Tari', 'Properti Tari', 'Kipas tari motif batik.', 15000, 15);

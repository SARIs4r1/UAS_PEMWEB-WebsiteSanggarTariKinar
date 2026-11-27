-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 27, 2026 at 01:14 PM
-- Server version: 8.0.45
-- PHP Version: 8.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_sanggar`
--

-- --------------------------------------------------------

--
-- Table structure for table `galeri`
--

CREATE TABLE `galeri` (
  `id` int NOT NULL,
  `judul` varchar(150) DEFAULT NULL,
  `foto` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int NOT NULL,
  `nama` varchar(150) NOT NULL,
  `kategori` enum('Kostum Tari','Properti Tari') NOT NULL,
  `deskripsi` text,
  `harga_sewa_per_hari` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stok` int NOT NULL DEFAULT '0',
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `nama`, `kategori`, `deskripsi`, `harga_sewa_per_hari`, `stok`, `foto`, `created_at`, `updated_at`) VALUES
(1, 'Baju Kurung Melayu (Putri)', 'Kostum Tari', 'Baju kurung lengkap untuk penari putri, tersedia beberapa ukuran.', 75000.00, 5, NULL, '2026-11-27 08:45:49', '2026-11-27 08:45:49'),
(2, 'Baju Teluk Belanga (Putra)', 'Kostum Tari', 'Setelan teluk belanga untuk penari putra.', 70000.00, 5, NULL, '2026-11-27 08:45:49', '2026-11-27 08:45:49'),
(3, 'Selendang Songket', 'Properti Tari', 'Selendang bermotif songket untuk pelengkap busana tari.', 25000.00, 10, NULL, '2026-11-27 08:45:49', '2026-11-27 08:45:49'),
(4, 'Kipas Tari', 'Properti Tari', 'Kipas tari untuk pementasan tari kreasi.', 15000.00, 15, NULL, '2026-11-27 08:45:49', '2026-11-27 08:45:49'),
(8, 'Baju Dayak (Putri)', 'Kostum Tari', '', 90000.00, 9, NULL, '2026-11-27 11:16:13', '2026-11-27 11:16:31');

-- --------------------------------------------------------

--
-- Table structure for table `kegiatan`
--

CREATE TABLE `kegiatan` (
  `id` int NOT NULL,
  `judul` varchar(200) NOT NULL,
  `tanggal` date NOT NULL,
  `lokasi` varchar(150) DEFAULT NULL,
  `ringkasan` varchar(300) DEFAULT NULL,
  `isi` text,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rental_requests`
--

CREATE TABLE `rental_requests` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `item_id` int NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `jumlah` int NOT NULL DEFAULT '1',
  `catatan` text,
  `status` enum('menunggu','disetujui','ditolak') NOT NULL DEFAULT 'menunggu',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `k` varchar(50) NOT NULL,
  `v` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`k`, `v`) VALUES
('about_foto', 'uploads/c18749fdfdb2f7f19be2.jpg'),
('alamat', 'SMAN 1 Tekarang, Kec. Tekarang, Kab. Sambas, Kalimantan Barat'),
('email', ''),
('hero_foto', 'uploads/05384914a856bcb3184d.jpg'),
('instagram', ''),
('jadwal_latihan', 'Setiap Sabtu, 13.00 WIB.'),
('latihan_foto', 'uploads/59bb77dc8dd8dfa5bfcc.jpg'),
('misi', 'Menyelenggarakan latihan tari secara rutin dan terarah di SMAN 1 Tekarang.\r\nMelestarikan seni tari tradisional Melayu Sambas serta mengembangkan tari kreasi daerah.\r\nBerpartisipasi aktif dalam berbagai festival, perlombaan, dan ajang gelar seni tari tingkat daerah maupun provinsi.\r\nMenyediakan jasa pementasan tari untuk berbagai acara adat, seremonial, penyambutan tamu, dan kegiatan masyarakat.\r\nMenyediakan layanan penyewaan kostum dan properti tari sebagai pendukung kebutuhan pentas seni.'),
('nama_sanggar', 'Sanggar Tari Kinar'),
('tagline', 'Sanggar seni tari dari Kecamatan Tekarang, Kabupaten Sambas, Kalimantan Barat.'),
('tentang', 'Sanggar Tari Kinar adalah sanggar seni tari yang berpusat di Kecamatan Tekarang, Kabupaten Sambas, Kalimantan Barat, dengan pusat latihan di SMAN 1 Tekarang.\r\n\r\nSanggar ini menjadi wadah pembinaan dan pelestarian seni tari daerah bagi pelajar serta generasi muda. Selain aktif mengikuti berbagai kompetisi dan festival seni tari, Sanggar Tari Kinar juga menerima permintaan pementasan tari untuk memeriahkan acara penyambutan tamu, pernikahan, peresmian, maupun kegiatan budaya lainnya, serta menyediakan layanan penyewaan kostum dan properti tari.'),
('visi', 'Menjadi sanggar tari yang melestarikan seni budaya daerah dan membanggakan masyarakat Tekarang dan Kabupaten Sambas.'),
('whatsapp', '083198067803');

-- --------------------------------------------------------

--
-- Table structure for table `tari`
--

CREATE TABLE `tari` (
  `id` int NOT NULL,
  `nama` varchar(150) NOT NULL,
  `asal` varchar(100) DEFAULT NULL,
  `deskripsi` text,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tari`
--

INSERT INTO `tari` (`id`, `nama`, `asal`, `deskripsi`, `foto`, `created_at`) VALUES
(1, 'Tari Jepin Lembut', 'Melayu Sambas', 'Tari pergaulan Melayu yang dibawakan berpasangan dengan gerak kaki lincah dan diiringi musik khas.', NULL, '2026-11-27 08:45:49'),
(2, 'Tari Tandak Sambas', 'Melayu Sambas', 'Tari tradisional Melayu Sambas yang sering tampil dalam acara adat dan penyambutan tamu.', NULL, '2026-11-27 08:45:49'),
(3, 'Tari Kreasi', 'Karya sanggar', 'Garapan baru yang memadukan gerak tradisi dengan penataan panggung masa kini untuk festival dan lomba.', NULL, '2026-11-27 08:45:49');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','pelanggan') NOT NULL DEFAULT 'pelanggan',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Admin Sanggar', 'admin@sanggar.test', '$2y$12$wWhYd.BZPQcmaehsyYlSqOr9NABUG0TAot5mH9F5Do3NmN1.Orpay', 'admin', '2026-11-27 08:45:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `galeri`
--
ALTER TABLE `galeri`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kegiatan`
--
ALTER TABLE `kegiatan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rental_requests`
--
ALTER TABLE `rental_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`k`);

--
-- Indexes for table `tari`
--
ALTER TABLE `tari`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `galeri`
--
ALTER TABLE `galeri`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `kegiatan`
--
ALTER TABLE `kegiatan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rental_requests`
--
ALTER TABLE `rental_requests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tari`
--
ALTER TABLE `tari`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `rental_requests`
--
ALTER TABLE `rental_requests`
  ADD CONSTRAINT `rental_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rental_requests_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

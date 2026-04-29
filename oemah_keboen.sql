-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 29, 2026 at 12:46 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `oemah_keboen`
--

-- --------------------------------------------------------

--
-- Table structure for table `fasilitas`
--

CREATE TABLE `fasilitas` (
  `id` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `deskripsi` text,
  `harga` int NOT NULL,
  `status` enum('Tersedia','Perbaikan','Dihapus') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'Tersedia',
  `image` varchar(255) DEFAULT '../../assets/img/default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `fasilitas`
--

INSERT INTO `fasilitas` (`id`, `nama`, `deskripsi`, `harga`, `status`, `image`) VALUES
(6, 'graaaah', 'yaaaaaaaaa', 10000, 'Dihapus', 'https://res.cloudinary.com/dbalv2ih1/image/upload/v1777433241/oemahkeboen/facilities/r5ps7zhu4megumyq5plm.jpg'),
(8, 'aaaa', 'aaaaaaaaaaaaa', 10000, 'Tersedia', 'https://res.cloudinary.com/dbalv2ih1/image/upload/v1777457560/oemahkeboen/facilities/qigzljghi3mxcxiekbjo.jpg'),
(9, 'aaaaa', 'aaaaaaaaaaaaa', 10000, 'Tersedia', 'https://res.cloudinary.com/dbalv2ih1/image/upload/v1777457575/oemahkeboen/facilities/pyt0hopjunkdxvsfyoxv.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int NOT NULL,
  `nama` varchar(150) NOT NULL,
  `kategori` enum('Buah','Minuman','Paket Edukasi') NOT NULL,
  `harga` int NOT NULL,
  `status` enum('Tersedia','Habis') DEFAULT 'Tersedia',
  `deskripsi` text,
  `image` varchar(255) DEFAULT '../../assets/img/default.png',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `nama`, `kategori`, `harga`, `status`, `deskripsi`, `image`, `created_at`) VALUES
(4, 'Jus Jambu Kristal', 'Minuman', 20000, 'Tersedia', 'Jus segar asli tanpa gula tambahan dan pengawet.', '../../assets/img/products/prod_1775721828.jpg', '2026-04-09 08:03:48'),
(5, 'gygg', 'Buah', 111111, 'Habis', '', '', '2026-04-10 11:28:16'),
(9, 'aaaa', 'Buah', 1000, 'Tersedia', '', 'https://res.cloudinary.com/dbalv2ih1/image/upload/v1777457644/oemahkeboen/products/zqh3y3evrn1piisubdsz.jpg', '2026-04-29 10:14:05'),
(10, 'aaaa', 'Buah', 1000, 'Tersedia', '', 'https://res.cloudinary.com/dbalv2ih1/image/upload/v1777457667/oemahkeboen/products/e986ojmqrvtpkqmvr1gn.jpg', '2026-04-29 10:14:28');

-- --------------------------------------------------------

--
-- Table structure for table `reservasi`
--

CREATE TABLE `reservasi` (
  `id` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `fasilitas_id` int NOT NULL,
  `tanggal` date NOT NULL,
  `jumlah_orang` int NOT NULL,
  `catatan` text,
  `total_harga` int NOT NULL,
  `status` enum('Menunggu Review','Lunas','Dibatalkan') DEFAULT 'Menunggu Review',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `reservasi`
--

INSERT INTO `reservasi` (`id`, `nama`, `no_hp`, `fasilitas_id`, `tanggal`, `jumlah_orang`, `catatan`, `total_harga`, `status`, `created_at`) VALUES
(15, 'sadadada', '08211231231231', 9, '2026-04-29', 1, '', 15000, 'Lunas', '2026-04-29 10:17:27'),
(16, 'asdada', '085753556422', 8, '2026-04-30', 1, '', 15000, 'Lunas', '2026-04-29 10:45:14');

-- --------------------------------------------------------

--
-- Table structure for table `status_panen`
--

CREATE TABLE `status_panen` (
  `id` int NOT NULL,
  `is_panen` tinyint(1) DEFAULT '1',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `status_panen`
--

INSERT INTO `status_panen` (`id`, `is_panen`, `updated_at`) VALUES
(1, 0, '2026-04-29 04:10:19');

-- --------------------------------------------------------

--
-- Table structure for table `ulasan`
--

CREATE TABLE `ulasan` (
  `id` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `rating` int NOT NULL,
  `komentar` text NOT NULL,
  `status` enum('Pending','Approved') DEFAULT 'Pending',
  `tanggal` date NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ulasan`
--

INSERT INTO `ulasan` (`id`, `nama`, `rating`, `komentar`, `status`, `tanggal`, `created_at`) VALUES
(1, 'Nurul Ulfa', 5, 'Soulful, tempatnya teduh, jauh dari kota jadi tenang banget, dan terawat!\r\n\r\nAda kolam ikan, kebun Jambu, pendopo, toilet, musholla, dapur, sawah, & warung. Lumayan enak buat kumpul keluarga karena ada beberapa gazebo dan pendopo yang bisa dipakai.\r\n\r\nAkun sosial medianya juga aktif. Rekomen buat yang cari tempat menyepi di pinggir Samarinda!', 'Approved', '2026-04-09', '2026-04-09 10:31:10'),
(2, 'Radit', 5, 'Tempatnya asri dan nyaman banget untuk kumpul bareng keluarga, teman, maupun sahabat. Pokoknya seru deh, apalagi dapat metik jambu sendiri secara langsung', 'Approved', '2026-04-09', '2026-04-09 10:31:51'),
(3, 'dgdgdgd', 5, 'fgdgfdgdfgdf', 'Approved', '2026-04-10', '2026-04-10 11:23:41'),
(4, 'satria rajadwali', 5, 'bagis temapat', 'Approved', '2026-04-17', '2026-04-17 08:42:28'),
(5, 'Jeje', 5, 'seru dan asik banget', 'Pending', '2026-04-28', '2026-04-27 23:50:12'),
(6, 'gagagaga', 5, '123131231123131', 'Pending', '2026-04-29', '2026-04-29 04:16:13'),
(7, 'dasdasdasdasda', 5, 'sdasdasdasdasdasdasdada', 'Pending', '2026-04-29', '2026-04-29 04:16:37'),
(8, 'dadasdasdadasda', 5, 'sadasdadadasadasdada', 'Approved', '2026-04-29', '2026-04-29 04:16:47'),
(12, 'dsadasdasdasdsa', 5, 'Sadasdadadaasda', 'Approved', '2026-04-29', '2026-04-29 09:59:42');

-- --------------------------------------------------------

--
-- Table structure for table `ulasan_foto`
--

CREATE TABLE `ulasan_foto` (
  `id` int NOT NULL,
  `ulasan_id` int NOT NULL,
  `foto_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ulasan_foto`
--

INSERT INTO `ulasan_foto` (`id`, `ulasan_id`, `foto_url`) VALUES
(1, 1, '../../assets/img/reviews/rev_1775730670_808_0.jpg'),
(2, 1, '../../assets/img/reviews/rev_1775730670_268_1.jpg'),
(3, 1, '../../assets/img/reviews/rev_1775730670_374_2.jpg'),
(4, 3, '../../assets/img/reviews/rev_1775820221_993_0.jpg'),
(5, 3, '../../assets/img/reviews/rev_1775820221_104_1.jpg'),
(6, 4, '../../assets/img/reviews/rev_1776415348_352_0.jpg'),
(7, 4, '../../assets/img/reviews/rev_1776415348_806_1.jpg'),
(8, 5, 'https://res.cloudinary.com/dbalv2ih1/image/upload/v1777333809/oemahkeboen/reviews/hmdtvv7uxcpxvsrxctlj.jpg'),
(9, 5, 'https://res.cloudinary.com/dbalv2ih1/image/upload/v1777333812/oemahkeboen/reviews/fypyq9ieqojba12wqwbt.jpg'),
(10, 7, 'https://res.cloudinary.com/dbalv2ih1/image/upload/v1777436196/oemahkeboen/reviews/fxlvhtzj5lxejxkkaczx.jpg'),
(12, 12, 'https://res.cloudinary.com/dbalv2ih1/image/upload/v1777456776/oemahkeboen/reviews/yz0al7n763oxzyv3snvi.jpg'),
(13, 12, 'https://res.cloudinary.com/dbalv2ih1/image/upload/v1777456781/oemahkeboen/reviews/n1riivdnitzxzl3g0eeu.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `role` enum('Admin','Pegawai') DEFAULT 'Pegawai',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `password`, `no_hp`, `role`, `is_active`, `created_at`) VALUES
(1, 'Dewi', 'admin', '$2y$10$mHV9AZCWKaEWgmY4qMEBCu85mfP44KYlk4hPzcKBOUfTV88YUPHC2', '081234567890', 'Admin', 1, '2026-04-08 23:58:51'),
(10, 'Budi Setiawan', 'budi_setiawan', '$2y$10$/cOp/T.UM2jydON38oKlg.c9zunV5GUfPI3yHUkSP7UXXNvBg2ld.', '081234567890', 'Pegawai', 0, '2026-04-09 04:02:45'),
(11, 'Andi Pratama', 'andi_p', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567890', 'Pegawai', 0, '2026-04-09 04:09:08'),
(12, 'Siti Aminah', 'siti_a', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567891', 'Pegawai', 0, '2026-04-09 04:09:08'),
(13, 'Budi Santoso', 'budi_s', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567892', 'Pegawai', 0, '2026-04-09 04:09:08'),
(14, 'Dewi Lestari', 'dewi_l', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567893', 'Pegawai', 0, '2026-04-09 04:09:08'),
(16, 'Fitri Handayani', 'fitri_h', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567895', 'Pegawai', 1, '2026-04-09 04:09:08'),
(17, 'Gilang Ramadhan', 'gilang_r', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567896', 'Pegawai', 1, '2026-04-09 04:09:08'),
(18, 'Hana Pertiwi', 'hana_p', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567897', 'Pegawai', 1, '2026-04-09 04:09:08'),
(19, 'Indra Kusuma', 'indra_k', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567898', 'Pegawai', 1, '2026-04-09 04:09:08'),
(39, 'Andi Wijaya', 'dsadada', '$2y$10$IkMy5Y5EBweY5T7jk6ba6Oq9yM5sr0k0oOAstWmw7IjhSBccmCNwe', '085753556422', 'Pegawai', 1, '2026-04-29 10:27:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fasilitas`
--
ALTER TABLE `fasilitas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reservasi`
--
ALTER TABLE `reservasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fasilitas_id` (`fasilitas_id`);

--
-- Indexes for table `status_panen`
--
ALTER TABLE `status_panen`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ulasan`
--
ALTER TABLE `ulasan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ulasan_foto`
--
ALTER TABLE `ulasan_foto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ulasan_id` (`ulasan_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `fasilitas`
--
ALTER TABLE `fasilitas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `reservasi`
--
ALTER TABLE `reservasi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `status_panen`
--
ALTER TABLE `status_panen`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ulasan`
--
ALTER TABLE `ulasan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `ulasan_foto`
--
ALTER TABLE `ulasan_foto`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reservasi`
--
ALTER TABLE `reservasi`
  ADD CONSTRAINT `reservasi_ibfk_1` FOREIGN KEY (`fasilitas_id`) REFERENCES `fasilitas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ulasan_foto`
--
ALTER TABLE `ulasan_foto`
  ADD CONSTRAINT `ulasan_foto_ibfk_1` FOREIGN KEY (`ulasan_id`) REFERENCES `ulasan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

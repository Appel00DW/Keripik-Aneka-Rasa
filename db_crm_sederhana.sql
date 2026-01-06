-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 02, 2026 at 08:58 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_crm_sederhana`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail_pesanan`
--

CREATE TABLE `detail_pesanan` (
  `id_detail` int(11) NOT NULL,
  `id_pesanan` int(11) DEFAULT NULL,
  `id_produk` int(11) DEFAULT NULL,
  `jumlah` int(11) NOT NULL,
  `harga_saat_pesan` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_pesanan`
--

INSERT INTO `detail_pesanan` (`id_detail`, `id_pesanan`, `id_produk`, `jumlah`, `harga_saat_pesan`) VALUES
(1, 1, 2, 1, 99900000.00),
(2, 7, 4, 1, 100000.00),
(3, 8, 5, 1, 2500000.00),
(4, 9, 5, 51, 2500000.00),
(5, 10, 4, 1, 100000.00),
(6, 10, 5, 1, 2500000.00),
(7, 11, 4, 60, 100000.00),
(8, 12, 4, 1, 100000.00),
(9, 13, 4, 7, 100000.00),
(10, 14, 4, 2, 100000.00),
(11, 15, 4, 1, 100000.00),
(12, 15, 5, 3, 2500000.00),
(13, 16, 5, 1, 2500000.00),
(14, 17, 4, 1, 100000.00),
(15, 18, 4, 1, 100000.00),
(16, 19, 5, 1, 2500000.00),
(17, 20, 4, 1, 100000.00);

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id_pengguna` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `alamat` text DEFAULT NULL,
  `no_telp` varchar(15) DEFAULT NULL,
  `role` enum('pelanggan','admin') NOT NULL DEFAULT 'pelanggan',
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id_pengguna`, `nama_lengkap`, `email`, `password`, `alamat`, `no_telp`, `role`, `dibuat_pada`) VALUES
(1, 'Admin Utama', 'admin@toko.com', '$2y$10$A09.mM7BsUuEr7XlrcEDJulmVcRSnM3aBcEkWR2ZkDjklMJY4sfrW', NULL, NULL, 'admin', '2025-10-16 16:46:40'),
(2, 'Dimas', 'dimas@gmail.com', '$2y$10$VwDBh7AkS35F21LJlULsK.mjEgXRgdMfEkexVOqJtyu.Y6a/fCIOO', 'antara', '08237238178', 'pelanggan', '2025-10-17 15:59:30'),
(3, 'anjay', 'anjay@gmail.com', '$2y$10$m2CR0eQIupG/RpM/LhEiwOQmiC9QkCSwyWe/53KEWAlXH/O4BaVb.', 'antara', '08237238178', 'pelanggan', '2026-01-02 07:48:54');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` int(11) NOT NULL,
  `id_pengguna` int(11) DEFAULT NULL,
  `total_harga` decimal(10,2) NOT NULL,
  `alamat_pengiriman` text NOT NULL,
  `status_pesanan` enum('pending','diproses','dikirim','selesai') NOT NULL DEFAULT 'pending',
  `tanggal_pesanan` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `id_pengguna`, `total_harga`, `alamat_pengiriman`, `status_pesanan`, `tanggal_pesanan`) VALUES
(1, 2, 99900000.00, 'antara', 'dikirim', '2025-10-17 16:00:05'),
(7, 1, 100000.00, 'kisaran', 'pending', '2025-10-17 17:03:54'),
(8, 1, 2500000.00, 'kisaran', 'pending', '2025-10-17 17:06:35'),
(9, 1, 99999999.99, 'kisaran', 'pending', '2025-10-17 17:18:11'),
(10, 1, 2600000.00, 'kisaran', 'pending', '2025-10-18 13:03:26'),
(11, 1, 6000000.00, 'antara\\r\\n', 'pending', '2025-10-18 13:24:33'),
(12, 1, 100000.00, 'kisaran', 'pending', '2025-10-18 13:26:47'),
(13, 1, 700000.00, 'kisaran', 'pending', '2025-10-18 14:06:12'),
(14, 1, 200000.00, 'antara', 'pending', '2025-10-18 14:07:01'),
(15, 1, 6080000.00, 'antara', 'pending', '2025-10-18 14:32:45'),
(16, 1, 2500000.00, 'kisaran', 'pending', '2025-10-18 14:59:56'),
(17, 2, 100000.00, 'antara', 'selesai', '2025-10-19 14:34:37'),
(18, 1, 100000.00, 'jakarta', 'selesai', '2025-10-19 14:36:26'),
(19, 2, 2500000.00, 'antara', 'selesai', '2025-10-19 14:59:30'),
(20, 3, 100000.00, 'antara', 'pending', '2026-01-02 07:50:27');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id_produk` int(11) NOT NULL,
  `nama_produk` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL,
  `gambar_produk` varchar(255) DEFAULT NULL,
  `status_produk` enum('aktif','diarsip') NOT NULL DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id_produk`, `nama_produk`, `deskripsi`, `harga`, `gambar_produk`, `status_produk`) VALUES
(2, 'iphone', 'cakep', 99900000.00, '20251016200242_iphone.jpg', 'aktif'),
(4, 'iphone 12', 'mewah', 100000.00, '20251017182540_iphone.jpg', 'aktif'),
(5, 'iphone 11', 'mewahhh', 2500000.00, '20251017183846_iphone.jpg', 'aktif');

-- --------------------------------------------------------

--
-- Table structure for table `produk_variasi`
--

CREATE TABLE `produk_variasi` (
  `id_variasi` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `warna` varchar(50) NOT NULL,
  `stok` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk_variasi`
--

INSERT INTO `produk_variasi` (`id_variasi`, `id_produk`, `warna`, `stok`) VALUES
(3, 4, 'ijo', 26),
(6, 5, 'biru', 44);

-- --------------------------------------------------------

--
-- Table structure for table `ulasan`
--

CREATE TABLE `ulasan` (
  `id_ulasan` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL COMMENT 'Rating dari 1 sampai 5',
  `ulasan` text DEFAULT NULL,
  `tanggal_ulasan` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ulasan`
--

INSERT INTO `ulasan` (`id_ulasan`, `id_produk`, `id_pengguna`, `rating`, `ulasan`, `tanggal_ulasan`) VALUES
(5, 4, 1, 4, 'waw\r\n', '2025-10-19 15:00:19');

-- --------------------------------------------------------

--
-- Table structure for table `voucher`
--

CREATE TABLE `voucher` (
  `id_voucher` int(11) NOT NULL,
  `kode_voucher` varchar(50) NOT NULL,
  `jenis` enum('persen','nominal') NOT NULL,
  `nilai` decimal(10,2) NOT NULL,
  `minimal_belanja` decimal(10,2) DEFAULT 0.00,
  `kuota` int(11) DEFAULT NULL,
  `berlaku_hingga` date DEFAULT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `voucher`
--

INSERT INTO `voucher` (`id_voucher`, `kode_voucher`, `jenis`, `nilai`, `minimal_belanja`, `kuota`, `berlaku_hingga`, `status`) VALUES
(3, 'B003', 'nominal', 100000.00, 100000.00, NULL, NULL, 'nonaktif'),
(4, 'B005', 'persen', 20.00, 100000.00, NULL, NULL, 'nonaktif');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_pesanan` (`id_pesanan`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_pengguna`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`),
  ADD KEY `id_pengguna` (`id_pengguna`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`);

--
-- Indexes for table `produk_variasi`
--
ALTER TABLE `produk_variasi`
  ADD PRIMARY KEY (`id_variasi`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indexes for table `ulasan`
--
ALTER TABLE `ulasan`
  ADD PRIMARY KEY (`id_ulasan`),
  ADD KEY `id_produk` (`id_produk`),
  ADD KEY `id_pengguna` (`id_pengguna`);

--
-- Indexes for table `voucher`
--
ALTER TABLE `voucher`
  ADD PRIMARY KEY (`id_voucher`),
  ADD UNIQUE KEY `kode_voucher` (`kode_voucher`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `produk_variasi`
--
ALTER TABLE `produk_variasi`
  MODIFY `id_variasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `ulasan`
--
ALTER TABLE `ulasan`
  MODIFY `id_ulasan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `voucher`
--
ALTER TABLE `voucher`
  MODIFY `id_voucher` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD CONSTRAINT `detail_pesanan_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`),
  ADD CONSTRAINT `detail_pesanan_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`);

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`);

--
-- Constraints for table `produk_variasi`
--
ALTER TABLE `produk_variasi`
  ADD CONSTRAINT `produk_variasi_ibfk_1` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE;

--
-- Constraints for table `ulasan`
--
ALTER TABLE `ulasan`
  ADD CONSTRAINT `ulasan_ibfk_1` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE,
  ADD CONSTRAINT `ulasan_ibfk_2` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

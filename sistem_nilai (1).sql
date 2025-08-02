-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 29, 2025 at 05:37 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sistem_nilai`
--

-- --------------------------------------------------------

--
-- Table structure for table `galeri`
--

CREATE TABLE `galeri` (
  `id` int(11) NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `tanggal_upload` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `galeri`
--

INSERT INTO `galeri` (`id`, `nama_file`, `keterangan`, `deskripsi`, `tanggal_upload`) VALUES
(2, '68882a0c3c46d.jpg', 'koding hari ini', '', '2025-07-29 08:55:24'),
(3, '68882a1cde0d0.jpeg', 'latihan input data', '', '2025-07-29 08:55:40'),
(4, '68882a28b9e62.jpeg', 'sosialisasi koding', '', '2025-07-29 08:55:52'),
(5, '68882a3e580f1.jpg', 'front end trainning', '', '2025-07-29 08:56:14'),
(6, '68882a4dead41.jpg', 'pelatihan koding', '', '2025-07-29 08:56:29'),
(7, '68882a60ceae7.jpg', 'input data manual', '', '2025-07-29 08:56:48'),
(8, '688840c3d49a5.jpg', 'hih', '', '2025-07-29 10:32:19');

-- --------------------------------------------------------

--
-- Table structure for table `guru`
--

CREATE TABLE `guru` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `id_mapel` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('guru','walikelas') DEFAULT 'guru'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guru`
--

INSERT INTO `guru` (`id`, `username`, `password`, `nama`, `id_mapel`, `created_at`, `role`) VALUES
(1, 'guru_inggris', 'inggris2025', 'Guru Bahasa Inggris', 1, '2025-07-15 01:08:35', 'guru'),
(2, 'guru_indo', 'indo2025', 'Guru Bahasa Indonesia', 2, '2025-07-15 01:08:35', 'guru'),
(3, 'guru_matematika', 'math2025', 'Guru Matematika', 3, '2025-07-15 01:08:35', 'guru'),
(4, 'walikelas1', 'wali2025', 'Wali Kelas 1', NULL, '2025-07-29 01:11:24', 'walikelas');

-- --------------------------------------------------------

--
-- Table structure for table `mata_pelajaran`
--

CREATE TABLE `mata_pelajaran` (
  `id` int(11) NOT NULL,
  `nama_mapel` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mata_pelajaran`
--

INSERT INTO `mata_pelajaran` (`id`, `nama_mapel`) VALUES
(1, 'Bahasa Inggris'),
(2, 'Bahasa Indonesia'),
(3, 'Matematika');

-- --------------------------------------------------------

--
-- Table structure for table `nilai`
--

CREATE TABLE `nilai` (
  `id` int(11) NOT NULL,
  `nis` varchar(10) DEFAULT NULL,
  `id_mapel` int(11) DEFAULT NULL,
  `uts` decimal(5,2) DEFAULT NULL,
  `uas` decimal(5,2) DEFAULT NULL,
  `tugas` decimal(5,2) DEFAULT NULL,
  `na` decimal(5,2) DEFAULT NULL,
  `grade` char(1) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nilai`
--

INSERT INTO `nilai` (`id`, `nis`, `id_mapel`, `uts`, `uas`, `tugas`, `na`, `grade`, `created_at`) VALUES
(1, '17299', 1, 80.00, 78.00, 90.00, 82.67, 'B', '2025-07-15 01:21:28'),
(2, '17299', 2, 95.00, 88.00, 67.00, 83.33, 'B', '2025-07-15 01:23:58'),
(3, '17301', 1, 90.00, 90.00, 90.00, 90.00, 'A', '2025-07-15 01:39:09'),
(4, '17301', 2, 77.00, 78.00, 80.00, 78.33, 'C', '2025-07-15 02:54:48'),
(5, NULL, 2, 90.00, 90.00, 90.00, 90.00, 'A', '2025-07-15 03:43:51'),
(6, '17300', 2, 66.00, 89.00, 78.00, 77.67, 'C', '2025-07-15 03:50:33'),
(7, '17304', 3, 90.00, 90.00, 90.00, 90.00, 'A', '2025-07-22 06:36:08'),
(8, '17300', 1, 10.00, 30.00, 20.00, 20.00, 'E', '2025-07-29 00:58:52'),
(9, '17302', 2, 80.00, 34.00, 56.00, 56.67, 'D', '2025-07-29 03:27:44'),
(10, '17302', 1, 70.00, 67.00, 56.00, 64.33, 'D', '2025-07-29 03:28:24'),
(11, '17302', 3, 67.00, 89.00, 45.00, 67.00, 'D', '2025-07-29 03:28:59');

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `nis` varchar(10) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jenis_kelamin` char(1) NOT NULL,
  `kelas` varchar(20) NOT NULL DEFAULT 'XII PPLG 1',
  `tahun_ajaran` varchar(20) NOT NULL DEFAULT '2054/2026'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`nis`, `nama`, `jenis_kelamin`, `kelas`, `tahun_ajaran`) VALUES
('17299', 'ABIMANYU ADENA MAULANA AKBAR', 'L', 'XII PPLG 1', '2054/2026'),
('17300', 'ABIMANYU BAGASKARA PUTRA PURNAMA', 'L', 'XII PPLG 1', '2054/2026'),
('17301', 'AILA RAHMA', 'P', 'XII PPLG 1', '2054/2026'),
('17302', 'ALDIANO VRYZAS SUDARSONO', 'L', 'XII PPLG 1', '2054/2026'),
('17303', 'ALSYA KANAYA DZIKRA', 'P', 'XII PPLG 1', '2054/2026'),
('17304', 'ANINDYA NUR RAHMA', 'P', 'XII PPLG 1', '2054/2026'),
('17305', 'ARIEF ARDIANTO NUGROHO', 'L', 'XII PPLG 1', '2054/2026'),
('17306', 'CIKA OKTI RAMDHANI', 'P', 'XII PPLG 1', '2054/2026'),
('17307', 'DENNIS ABHIESTA ARCHIEYASA', 'L', 'XII PPLG 1', '2054/2026'),
('17309', 'DEVITA WAHYU WULANDARI', 'P', 'XII PPLG 1', '2054/2026'),
('17310', 'ELFREDA FAIZ RADITYA PRATAMA', 'L', 'XII PPLG 1', '2054/2026'),
('17311', 'FENNY PRAVITA NURAINI', 'P', 'XII PPLG 1', '2054/2026'),
('17312', 'FINA ROHMATUL UMAH', 'P', 'XII PPLG 1', '2054/2026'),
('17313', 'GAVIN TYAGA DANISWARA', 'L', 'XII PPLG 1', '2054/2026'),
('17314', 'HAIDAR AGAM AFFANDANI', 'L', 'XII PPLG 1', '2054/2026'),
('17315', 'HELMI AQSHA FIRDAUSSI', 'L', 'XII PPLG 1', '2054/2026'),
('17316', 'IRFAN ARIF FADHILLAH', 'L', 'XII PPLG 1', '2054/2026'),
('17317', 'IRMA ALIFIYATUS ZAHWA', 'P', 'XII PPLG 1', '2054/2026'),
('17318', 'IZZATUN NISSA', 'P', 'XII PPLG 1', '2054/2026'),
('17319', 'JASON GERARD ATMAJA', 'L', 'XII PPLG 1', '2054/2026'),
('17320', 'KANAKA RACHEL NASHITA', 'P', 'XII PPLG 1', '2054/2026'),
('17321', 'KHAYLILLA RYANA AGUSTIN', 'P', 'XII PPLG 1', '2054/2026'),
('17322', 'KURNIA AZ ZAHRA ILMI SYADZA', 'P', 'XII PPLG 1', '2054/2026'),
('17323', 'MADA ALVINO MAULANA RUKY', 'L', 'XII PPLG 1', '2054/2026'),
('17324', 'MIRZA ZAKY QASTHALANY', 'L', 'XII PPLG 1', '2054/2026'),
('17325', 'MOHAMAD SINATRYA AL WARID', 'L', 'XII PPLG 1', '2054/2026'),
('17326', 'MUHAMMAD IQBAAL TAQI TSAAQIF', 'L', 'XII PPLG 1', '2054/2026'),
('17327', 'NAURRA CITRA RAHMAWATI', 'P', 'XII PPLG 1', '2054/2026'),
('17328', 'PUTRI ARUMARISTIANA AMALIA HIDAYATI', 'P', 'XII PPLG 1', '2054/2026'),
('17329', 'RAIHAN BAGUS SAPUTRA', 'L', 'XII PPLG 1', '2054/2026'),
('17330', 'RASYA NELFI FABRIELE DELVINO', 'L', 'XII PPLG 1', '2054/2026'),
('17331', 'RAZINDRA ZAHYALWAAN BAADHILAH', 'L', 'XII PPLG 1', '2054/2026'),
('17332', 'SODIQ RAHMADTULLAH', 'L', 'XII PPLG 1', '2054/2026'),
('17333', 'WASFA NUR\'AINI', 'P', 'XII PPLG 1', '2054/2026'),
('17334', 'ZAHRA AULIA DESINTA', 'P', 'XII PPLG 1', '2054/2026');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `galeri`
--
ALTER TABLE `galeri`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guru`
--
ALTER TABLE `guru`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `mata_pelajaran`
--
ALTER TABLE `mata_pelajaran`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nilai`
--
ALTER TABLE `nilai`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nis` (`nis`),
  ADD KEY `id_mapel` (`id_mapel`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`nis`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `galeri`
--
ALTER TABLE `galeri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `guru`
--
ALTER TABLE `guru`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `mata_pelajaran`
--
ALTER TABLE `mata_pelajaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `nilai`
--
ALTER TABLE `nilai`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `nilai`
--
ALTER TABLE `nilai`
  ADD CONSTRAINT `nilai_ibfk_1` FOREIGN KEY (`nis`) REFERENCES `siswa` (`nis`),
  ADD CONSTRAINT `nilai_ibfk_2` FOREIGN KEY (`id_mapel`) REFERENCES `mata_pelajaran` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

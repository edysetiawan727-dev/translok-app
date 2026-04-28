-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 17 Mar 2026 pada 02.52
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `translok_app`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `jenis_kegiatan`
--

CREATE TABLE `jenis_kegiatan` (
  `id` int(11) NOT NULL,
  `nama_kegiatan` varchar(100) NOT NULL,
  `warna_label` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jenis_kegiatan`
--

INSERT INTO `jenis_kegiatan` (`id`, `nama_kegiatan`, `warna_label`) VALUES
(1, 'Translok', '#ffc107'),
(3, 'Rapat Dinas', '#198754'),
(4, 'Dinas Luar', '#6c757d'),
(5, 'Innas/Inda Mengajar', '#0db5fd');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kegiatan`
--

CREATE TABLE `kegiatan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jenis` varchar(100) DEFAULT NULL,
  `keterangan` text NOT NULL,
  `status` enum('LOCKED','FINAL') DEFAULT 'LOCKED',
  `update_count` tinyint(4) DEFAULT 0,
  `alasan_update` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `history_log` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kegiatan`
--

INSERT INTO `kegiatan` (`id`, `user_id`, `tanggal`, `jenis`, `keterangan`, `status`, `update_count`, `alasan_update`, `created_at`, `history_log`) VALUES
(1, 2, '2026-01-26', 'Translok', 'Translok Seruti TW 1', 'FINAL', 2, 'Salah kegiatan survei', '2026-01-26 09:06:26', '\n[Admin Reset - 28-01-2026 10:10] Admin membuka kunci tanggal ini.\n[Update ke-2 - 28-01-2026 10:11]\n- Dari: Translok Susenas\n- Jadi: Translok Seruti TW 1\n- Alasan: Salah kegiatan survei'),
(2, 2, '2026-01-23', 'Translok', 'Translok Sakernas Kec Pakisaji', 'FINAL', 1, 'Salah Kecamatan', '2026-01-26 09:21:10', NULL),
(3, 3, '2026-02-02', 'Translok', 'Translok Kegiatan Susenas di Kec Kepanjen', 'FINAL', 1, 'Ganti Kegiatan Survey', '2026-02-10 03:19:36', '[Input Awal - 10-02-2026 04:19] Input pertama kali.\n[Update ke-1 - 10-02-2026 04:43]\n- Jenis: Translok -> Translok\n- Ket: Translok Kegiatan Sakernas di Kec Kepanjen -> Translok Kegiatan Susenas di Kec Kepanjen\n- Alasan: Ganti Kegiatan Survey'),
(4, 3, '2026-02-03', 'Innas/Inda Mengajar', 'Pengajar Kegiatan Pelatihan Survei SUSENAS Mar 2026', 'FINAL', 1, 'Ganti Bulan', '2026-02-10 03:30:30', '[Input Awal - 10-02-2026 04:30] Input pertama kali.\n[Update ke-1 - 10-02-2026 04:31]\n- Jenis: Innas/Inda Mengajar -> Innas/Inda Mengajar\n- Ket: Pengajar Kegiatan Pelatihan Survei SUSENAS Maret 2026 -> Pengajar Kegiatan Pelatihan Survei SUSENAS Mar 2026\n- Alasan: Ganti Bulan'),
(5, 3, '2026-02-04', 'Innas/Inda Mengajar', 'Mengajar di Kantor', 'LOCKED', 0, NULL, '2026-02-10 03:31:56', '[Input Awal - 10-02-2026 04:31] Input pertama kali.'),
(6, 3, '2026-02-05', 'Innas/Inda Mengajar', 'Inda Mengajar KSA Padi', 'LOCKED', 0, NULL, '2026-02-10 03:44:57', '[Input Awal - 10-02-2026 04:44] Input pertama kali.'),
(7, 2, '2026-02-09', 'Innas/Inda Mengajar', 'Mengajar KSA Jagung', 'FINAL', 1, 'Salah redaksi', '2026-02-10 06:58:01', '[Input Awal - 10-02-2026 07:58] Input pertama kali.\n[Update ke-1 - 10-02-2026 07:58]\n- Jenis: Innas/Inda Mengajar -> Innas/Inda Mengajar\n- Ket: Pengajar KSA Jagung -> Mengajar KSA Jagung\n- Alasan: Salah redaksi'),
(8, 2, '2026-02-10', 'Innas/Inda Mengajar', 'Inda Mengajar SHP', 'LOCKED', 0, NULL, '2026-02-10 06:59:07', '[Input Awal - 10-02-2026 07:59] Input pertama kali.'),
(9, 2, '2026-02-05', 'Rapat Dinas', 'Rapat Dinas di Provinsi', 'LOCKED', 0, NULL, '2026-02-10 07:25:44', '[Input Awal - 10-02-2026 08:25] Input pertama kali.'),
(10, 2, '2026-03-13', 'Innas/Inda Mengajar', 'Mengajar Pelatihan Petugas INPEK Tahun 2026', 'FINAL', 1, 'Salah Memasukan Jenis Kegiatan', '2026-03-13 05:51:33', '[Input Awal - 13-03-2026 06:51] Input pertama kali.\n[Update ke-1 - 13-03-2026 06:52]\n- Jenis: Translok -> Innas/Inda Mengajar\n- Ket: Mengajar Pelatihan Petugas INPEK Tahun 2026 -> Mengajar Pelatihan Petugas INPEK Tahun 2026\n- Alasan: Salah Memasukan Jenis Kegiatan'),
(11, 2, '2026-03-12', 'Translok', 'Transport Lokal GC PLN di Kecamatan Kepanjen', 'FINAL', 2, 'Salah input keterangan kegiatan', '2026-03-13 06:32:33', '[Input Awal - 13-03-2026 07:32] Input pertama kali.\n[Update ke-1 - 13-03-2026 07:33]\n- Jenis: Innas/Inda Mengajar -> Translok\n- Ket: Transport Lokal GC PBI di Kecamatan Kepanjen -> Transport Lokal GC PBI di Kecamatan Kepanjen\n- Alasan: Salah Input Jenis Kegiatan\n[Admin Reset - 13-03-2026 09:11] Admin membuka kunci tanggal ini.\n[Update ke-2 - 13-03-2026 09:15]\n- Jenis: Translok -> Translok\n- Ket: Transport Lokal GC PBI di Kecamatan Kepanjen -> Transport Lokal GC PLN di Kecamatan Kepanjen\n- Alasan: Salah input keterangan kegiatan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nip` varchar(30) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('pegawai','admin') DEFAULT 'pegawai'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nip`, `nama`, `password`, `role`) VALUES
(1, 'admin123', 'Super Admin', '0192023a7bbd73250516f069df18b500', 'admin'),
(2, '19900101', 'Pacar Edy', 'e10adc3949ba59abbe56e057f20f883e', 'pegawai'),
(3, '12345', 'Teman Edy', '202cb962ac59075b964b07152d234b70', 'pegawai');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `jenis_kegiatan`
--
ALTER TABLE `jenis_kegiatan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kegiatan`
--
ALTER TABLE `kegiatan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unik_tanggal_pegawai` (`user_id`,`tanggal`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nip` (`nip`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `jenis_kegiatan`
--
ALTER TABLE `jenis_kegiatan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `kegiatan`
--
ALTER TABLE `kegiatan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `kegiatan`
--
ALTER TABLE `kegiatan`
  ADD CONSTRAINT `kegiatan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

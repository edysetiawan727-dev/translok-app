<?php
session_start();
include '../config/database.php';

// Cek Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    exit("Akses Ditolak");
}

$id_kegiatan = $_GET['id'];
$tgl_sekarang = date('d-m-Y H:i');

// Ambil data lama dulu untuk log
$cek = mysqli_query($koneksi, "SELECT history_log FROM kegiatan WHERE id='$id_kegiatan'");
$data = mysqli_fetch_assoc($cek);
$log_lama = $data['history_log'];

// Tambahkan catatan baru
$log_baru = $log_lama . "\n[Admin Reset - $tgl_sekarang] Admin membuka kunci tanggal ini.";

// Update Database
// Status jadi LOCKED, tapi update_count biarkan saja (atau tambah jika mau)
$query = "UPDATE kegiatan SET 
          status = 'LOCKED', 
          history_log = '$log_baru' 
          WHERE id='$id_kegiatan'";

if (mysqli_query($koneksi, $query)) {
    // Kembali ke dashboard dengan pesan sukses
    echo "<script>alert('Berhasil dibuka! Pegawai bisa mengeditnya lagi.'); window.location='dashboard.php';</script>";
} else {
    echo "Gagal: " . mysqli_error($koneksi);
}
?>
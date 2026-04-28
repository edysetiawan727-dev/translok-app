<?php
session_start();
include '../config/database.php';

$user_id    = $_SESSION['id'];
$mode       = $_POST['mode'];
$tanggal    = $_POST['tanggal'];
$jenis      = $_POST['jenis']; // <-- Kuncinya dibuka, ambil dari input user lagi
$keterangan = $_POST['keterangan'];
$tgl_jam    = date('d-m-Y H:i');

if ($mode == 'baru') {
    // --- INPUT BARU ---
    // Log awal
    $log_awal = "[Input Awal - $tgl_jam] Input pertama kali.";
    
    $query = "INSERT INTO kegiatan (user_id, tanggal, jenis, keterangan, status, update_count, history_log) 
              VALUES ('$user_id', '$tanggal', '$jenis', '$keterangan', 'LOCKED', 0, '$log_awal')";
    
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Berhasil disimpan!'); window.location='index.php';</script>";
    } else {
        echo "Gagal: " . mysqli_error($koneksi);
    }

} elseif ($mode == 'update') {
    // --- UPDATE ---
    $id_kegiatan   = $_POST['id_kegiatan'];
    $alasan_update = $_POST['alasan_update'];

    // 1. Ambil data lama
    $ambil_lama = mysqli_query($koneksi, "SELECT jenis, keterangan, history_log, update_count FROM kegiatan WHERE id='$id_kegiatan'");
    $dt_lama    = mysqli_fetch_assoc($ambil_lama);
    
    $jenis_lama = $dt_lama['jenis']; // Ambil jenis lama
    $ket_lama   = $dt_lama['keterangan'];
    $log_lama   = $dt_lama['history_log'];
    $count_lama = $dt_lama['update_count'];

    // 2. Susun Log Baru
    $count_baru = $count_lama + 1;
    // Log sekarang mencatat perubahan JENIS juga
    $log_tambahan = "\n[Update ke-$count_baru - $tgl_jam]\n- Jenis: $jenis_lama -> $jenis\n- Ket: $ket_lama -> $keterangan\n- Alasan: $alasan_update";
    
    $log_final = $log_lama . $log_tambahan;

    // 3. Simpan
    $query = "UPDATE kegiatan SET 
              jenis = '$jenis',
              keterangan = '$keterangan',
              alasan_update = '$alasan_update',
              status = 'FINAL',
              update_count = $count_baru,
              history_log = '$log_final'
              WHERE id = '$id_kegiatan' AND user_id = '$user_id'";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data berhasil diupdate dan tersimpan di riwayat.'); window.location='index.php';</script>";
    } else {
        echo "Gagal Update: " . mysqli_error($koneksi);
    }
}
?>
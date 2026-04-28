<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

$aksi = isset($_POST['aksi']) ? $_POST['aksi'] : $_GET['aksi'];

// --- PROSES TAMBAH ---
if ($aksi == 'tambah') {
    $nip      = $_POST['nip'];
    $nama     = $_POST['nama'];
    $password = md5($_POST['password']); // Enkripsi MD5
    $role     = $_POST['role'];

    // Cek NIP kembar
    $cek = mysqli_query($koneksi, "SELECT * FROM users WHERE nip='$nip'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Gagal! NIP sudah terdaftar.'); window.history.back();</script>";
        exit;
    }

    $query = "INSERT INTO users (nip, nama, password, role) VALUES ('$nip', '$nama', '$password', '$role')";
    
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('User berhasil ditambahkan!'); window.location='users.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}

// --- PROSES EDIT ---
elseif ($aksi == 'edit') {
    $id       = $_POST['id'];
    $nip      = $_POST['nip'];
    $nama     = $_POST['nama'];
    $role     = $_POST['role'];
    $pass_raw = $_POST['password'];

    // Logika update password:
    // Jika kolom password diisi, update password baru.
    // Jika kosong, pakai password lama (jangan di-update).
    if (!empty($pass_raw)) {
        $password = md5($pass_raw);
        $query = "UPDATE users SET nip='$nip', nama='$nama', password='$password', role='$role' WHERE id='$id'";
    } else {
        $query = "UPDATE users SET nip='$nip', nama='$nama', role='$role' WHERE id='$id'";
    }

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data user berhasil diupdate!'); window.location='users.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}

// --- PROSES HAPUS ---
elseif ($aksi == 'hapus') {
    $id = $_GET['id'];
    
    // Hapus dulu kegiatan milik user ini (karena ada Foreign Key)
    mysqli_query($koneksi, "DELETE FROM kegiatan WHERE user_id='$id'");
    
    // Baru hapus usernya
    $query = "DELETE FROM users WHERE id='$id'";
    
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('User berhasil dihapus!'); window.location='users.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>
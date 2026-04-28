<?php
session_start();
include 'config/database.php'; // Panggil koneksi database

// Ambil data dari form
$nip = $_POST['nip'];
$password = md5($_POST['password']); // Ubah password jadi MD5 biar cocok sama database

// Cek ke database
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE nip='$nip' AND password='$password'");
$cek = mysqli_num_rows($query);

if ($cek > 0) {
    $data = mysqli_fetch_assoc($query);

    // Simpan data user ke SESSION (biar diingat terus sama browser)
    $_SESSION['id'] = $data['id'];
    $_SESSION['nama'] = $data['nama'];
    $_SESSION['role'] = $data['role'];

    // Arahkan ke halaman masing-masing
    if ($data['role'] == "admin") {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: pegawai/index.php");
    }
} else {
    // Jika salah
    echo "<script>
            alert('NIP atau Password salah!');
            window.location='index.php';
          </script>";
}
?>
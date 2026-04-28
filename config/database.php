<?php
// config/database.php

$host = "localhost";
$user = "root";       // User default XAMPP
$pass = "";           // Password default XAMPP (kosong)
$db   = "translok_app"; // Nama database yang barusan kita buat

// Melakukan koneksi
$koneksi = mysqli_connect($host, $user, $pass, $db);

// Cek jika koneksi gagal
if (!$koneksi) {
    die("Gagal terhubung ke database: " . mysqli_connect_error());
}
?>
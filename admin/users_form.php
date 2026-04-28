<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

// Cek Mode: Tambah atau Edit?
$aksi = $_GET['aksi'];
$id   = isset($_GET['id']) ? $_GET['id'] : '';

$nip = ""; $nama = ""; $role = "pegawai"; // Default value

if ($aksi == 'edit' && !empty($id)) {
    // Ambil data lama
    $q = mysqli_query($koneksi, "SELECT * FROM users WHERE id='$id'");
    $d = mysqli_fetch_assoc($q);
    $nip = $d['nip'];
    $nama = $d['nama'];
    $role = $d['role'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">
                            <?= ($aksi == 'tambah') ? '➕ Tambah User Baru' : '✏️ Edit User' ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="users_proses.php" method="POST">
                            <input type="hidden" name="aksi" value="<?= $aksi ?>">
                            <input type="hidden" name="id" value="<?= $id ?>">

                            <div class="mb-3">
                                <label class="form-label fw-bold">NIP (Username)</label>
                                <input type="text" name="nip" class="form-control" value="<?= $nip ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" value="<?= $nama ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="<?= ($aksi == 'edit') ? '(Kosongkan jika tidak ingin mengubah password)' : 'Wajib diisi' ?>" <?= ($aksi == 'tambah') ? 'required' : '' ?>>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Role (Hak Akses)</label>
                                <select name="role" class="form-select">
                                    <option value="pegawai" <?= ($role == 'pegawai') ? 'selected' : '' ?>>Pegawai Biasa</option>
                                    <option value="admin" <?= ($role == 'admin') ? 'selected' : '' ?>>Administrator</option>
                                </select>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="users.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary fw-bold">Simpan Data</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
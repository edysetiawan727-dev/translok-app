<?php
session_start();
include '../config/database.php';

// Cek Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

// Ambil data users (kecuali akun yang sedang login, biar gak hapus diri sendiri)
$id_saya = $_SESSION['id'];
$query = mysqli_query($koneksi, "SELECT * FROM users ORDER BY role ASC, nama ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand p-0" href="dashboard.php">
                <img src="../assets/img/logo_dampit.png" alt="DaMPiT" 
                     style="height: 55px; background: white; padding: 5px 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
            </a>
            
            <div class="navbar-nav me-auto ms-4">
                <a class="nav-link text-white opacity-75 hover-opacity-100" href="dashboard.php">Dashboard</a>
                <a class="nav-link text-white opacity-75 hover-opacity-100" href="cetak_rekap.php">🖨️ Cetak Rekap</a>
                <a class="nav-link text-white fw-bold active" href="users.php">👥 Kelola User</a>
                <a class="nav-link text-white opacity-75 hover-opacity-100" href="kegiatan_master.php">⚙️ Master Kegiatan</a>
            </div>

            <div class="d-flex text-white align-items-center">
                <span class="me-3">Halo, Admin</span>
                <a href="../logout.php" class="btn btn-sm btn-outline-light fw-bold">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="card shadow border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Daftar Pengguna</h5>
                <a href="users_form.php?aksi=tambah" class="btn btn-primary btn-sm fw-bold">
                    <i class="bi bi-person-plus"></i> Tambah Baru
                </a>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="px-4">No</th>
                            <th>NIP (Username)</th>
                            <th>Nama Lengkap</th>
                            <th>Role</th>
                            <th class="text-end px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while($row = mysqli_fetch_assoc($query)): 
                        ?>
                        <tr>
                            <td class="px-4"><?= $no++ ?></td>
                            <td><?= $row['nip'] ?></td>
                            <td><?= $row['nama'] ?></td>
                            <td>
                                <?php if($row['role'] == 'admin'): ?>
                                    <span class="badge bg-danger">Admin</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Pegawai</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end px-4">
                                <a href="users_form.php?aksi=edit&id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                
                                <?php if($row['id'] != $id_saya): ?>
                                <a href="users_proses.php?aksi=hapus&id=<?= $row['id'] ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Yakin ingin menghapus user ini? Data kegiatan mereka juga akan hilang!')">
                                    <i class="bi bi-trash"></i>
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>
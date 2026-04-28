<?php
session_start();
include '../config/database.php';

// Cek Login Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

// PROSES TAMBAH KEGIATAN BARU
if (isset($_POST['simpan'])) {
    $nama  = $_POST['nama'];
    $warna = $_POST['warna'];
    // Simpan ke database
    mysqli_query($koneksi, "INSERT INTO jenis_kegiatan (nama_kegiatan, warna_label) VALUES ('$nama', '$warna')");
    header("Location: kegiatan_master.php");
}

// PROSES HAPUS KEGIATAN
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM jenis_kegiatan WHERE id='$id'");
    header("Location: kegiatan_master.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Master Kegiatan - DaMPiT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .card { border: none; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand p-0" href="dashboard.php">
                <img src="../assets/img/logo_dampit.png" alt="DaMPiT" 
                     style="height: 55px; background: white; padding: 5px 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
            </a>
            <div class="navbar-nav me-auto ms-4">
                <a class="nav-link text-white opacity-75 hover-opacity-100" href="dashboard.php">Dashboard</a>
                <a class="nav-link text-white opacity-75 hover-opacity-100" href="cetak_rekap.php">🖨️ Cetak Rekap</a>
                <a class="nav-link text-white opacity-75 hover-opacity-100" href="users.php">👥 Kelola User</a>
                <a class="nav-link text-white fw-bold active" href="kegiatan_master.php">⚙️ Master Kegiatan</a>
            </div>
            <div class="d-flex text-white align-items-center">
                <span class="me-3">Halo, Admin</span>
                <a href="../logout.php" class="btn btn-sm btn-outline-light fw-bold">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white fw-bold">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Jenis Kegiatan
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Nama Kegiatan</label>
                                <input type="text" name="nama" class="form-control" placeholder="Contoh: Rapat Internal" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Pilih Warna Label</label>
                                <input type="color" name="warna" class="form-control form-control-color w-100" value="#0d6efd" title="Pilih warna">
                            </div>
                            <button type="submit" name="simpan" class="btn btn-primary w-100 fw-bold">Simpan Baru</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-white fw-bold border-bottom">
                        <i class="bi bi-list-ul me-2"></i>Daftar Master Kegiatan
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Nama Kegiatan</th>
                                    <th>Warna</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $q = mysqli_query($koneksi, "SELECT * FROM jenis_kegiatan ORDER BY id ASC");
                                while($row = mysqli_fetch_assoc($q)): 
                                ?>
                                <tr>
                                    <td class="ps-4 fw-bold"><?php echo htmlspecialchars($row['nama_kegiatan']); ?></td>
                                    <td>
                                        <span class="badge rounded-pill px-3 py-2" style="background-color: <?php echo $row['warna_label']; ?>; color: #fff; text-shadow: 0 0 2px black; border: 1px solid rgba(0,0,0,0.1);">
                                            <?php echo $row['warna_label']; ?>
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="?hapus=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus jenis kegiatan ini?')">
                                            <i class="bi bi-trash"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
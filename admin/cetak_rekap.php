<?php
session_start();
// Cek Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekapitulasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                <a class="nav-link text-white fw-bold active" href="cetak_rekap.php">🖨️ Cetak Rekap</a>
                <a class="nav-link text-white opacity-75 hover-opacity-100" href="users.php">👥 Kelola User</a>
                <a class="nav-link text-white opacity-75 hover-opacity-100" href="kegiatan_master.php">⚙️ Master Kegiatan</a>
            </div>

            <div class="d-flex text-white align-items-center">
                <span class="me-3">Halo, Admin</span>
                <a href="../logout.php" class="btn btn-sm btn-outline-light fw-bold">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow border-0">
                    <div class="card-header bg-secondary text-white text-center py-3">
                        <h4 class="mb-0 fw-bold">🖨️ Rekapitulasi Seluruh Pegawai</h4>
                    </div>
                    <div class="card-body p-4">
                        <form action="cetak_rekap_print.php" method="GET" target="_blank">
                            
                            <div class="alert alert-info small">
                                <i class="bi bi-info-circle"></i> Laporan ini akan mencetak seluruh kegiatan (Translok & Innas/Inda) dari semua pegawai dalam rentang tanggal yang dipilih.
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Dari Tanggal</label>
                                <input type="date" name="tgl_awal" class="form-control" required value="<?= date('Y-m-01') ?>">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Sampai Tanggal</label>
                                <input type="date" name="tgl_akhir" class="form-control" required value="<?= date('Y-m-t') ?>">
                            </div>

                            <button type="submit" class="btn btn-dark w-100 py-2 fw-bold">
                                CETAK DATA
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
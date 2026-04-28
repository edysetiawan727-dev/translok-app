<?php
session_start();
include '../config/database.php';

// Cek Login
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pegawai') {
    header("Location: ../index.php");
    exit;
}

$tanggal = $_GET['tanggal'];
$mode    = $_GET['mode']; 
$data    = [];

// Jika mode UPDATE, ambil data lama
if ($mode == 'update') {
    $id_user = $_SESSION['id'];
    $query   = mysqli_query($koneksi, "SELECT * FROM kegiatan WHERE user_id='$id_user' AND tanggal='$tanggal'");
    $data    = mysqli_fetch_assoc($query);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Kegiatan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <?= ($mode == 'baru') ? '📝 Input Kegiatan Baru' : '✏️ Update Kegiatan' ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        
                        <form action="proses_simpan.php" method="POST">
                            <input type="hidden" name="mode" value="<?= $mode ?>">
                            <input type="hidden" name="id_kegiatan" value="<?= isset($data['id']) ? $data['id'] : '' ?>">
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tanggal</label>
                                <input type="text" name="tanggal" class="form-control bg-light" value="<?= $tanggal ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Jenis Kegiatan</label>
                                <select name="jenis" class="form-select" required>
                                    <option value="">-- Pilih Jenis Kegiatan --</option>
                                    <?php
                                    // AMBIL DATA DARI MASTER KEGIATAN
                                    $q_jenis = mysqli_query($koneksi, "SELECT * FROM jenis_kegiatan ORDER BY nama_kegiatan ASC");
                                    while ($j = mysqli_fetch_assoc($q_jenis)) {
                                        // Jika sedang mode edit, cek apakah ini yang dipilih sebelumnya
                                        $selected = ($mode == 'update' && $data_edit['jenis'] == $j['nama_kegiatan']) ? 'selected' : '';
                                        echo "<option value='" . $j['nama_kegiatan'] . "' $selected>" . $j['nama_kegiatan'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Keterangan Kegiatan</label>
                                <textarea name="keterangan" class="form-control" rows="3" placeholder="Contoh: Kegiatan di Kecamatan X..." required><?= isset($data['keterangan']) ? $data['keterangan'] : '' ?></textarea>
                            </div>

                            <?php if ($mode == 'update') : ?>
                            <div class="alert alert-warning">
                                <strong>Perhatian!</strong> Anda hanya bisa update 1 kali. Setelah ini data akan berstatus <strong>FINAL</strong>.
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-danger">Alasan Update (Wajib)</label>
                                <textarea name="alasan_update" class="form-control border-danger" rows="2" placeholder="Kenapa data diubah?" required></textarea>
                            </div>
                            <?php endif; ?>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="index.php" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan Data</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
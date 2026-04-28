<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pegawai') {
    exit("Akses Ditolak");
}

$id_user   = $_SESSION['id'];
$tgl_awal  = $_GET['tgl_awal'];
$tgl_akhir = $_GET['tgl_akhir'];

// Ambil data sesuai rentang tanggal
$query = mysqli_query($koneksi, "SELECT * FROM kegiatan 
                                 WHERE user_id='$id_user' 
                                 AND tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir' 
                                 ORDER BY tanggal ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Translok</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12pt; margin: 40px; }
        h2, h4 { text-align: center; margin: 0; }
        .garis { border-bottom: 2px solid black; margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        
        /* Hilangkan elemen browser saat print */
        @media print {
            @page { margin: 2cm; }
        }
    </style>
</head>
<body onload="window.print()">

    <h2>LAPORAN KEGIATAN TRANSLOK</h2>
    <h4>Periode: <?= date('d-m-Y', strtotime($tgl_awal)) ?> s/d <?= date('d-m-Y', strtotime($tgl_akhir)) ?></h4>
    <div class="garis"></div>

    <p>
        <strong>Nama Pegawai :</strong> <?= $_SESSION['nama'] ?><br>
        <strong>NIP :</strong> (NIP Anda di Database)
    </p>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 20%;">Tanggal</th>
                <th style="width: 15%;">Jenis</th>
                <th>Keterangan Kegiatan</th>
                <th style="width: 15%;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            if (mysqli_num_rows($query) > 0) {
                while ($row = mysqli_fetch_assoc($query)) { 
                    $tgl_indo = date('d-m-Y', strtotime($row['tanggal']));
                    $status_text = ($row['status'] == 'FINAL') ? 'FINAL' : 'TERKUNCI';
            ?>
                <tr>
                    <td style="text-align: center;"><?= $no++ ?></td>
                    <td><?= $tgl_indo ?></td>
                    <td><?= $row['jenis'] ?></td>
                    <td><?= $row['keterangan'] ?></td>
                    <td style="text-align: center;"><?= $status_text ?></td>
                </tr>
            <?php 
                } 
            } else {
                echo "<tr><td colspan='5' style='text-align:center;'>Tidak ada data pada periode ini.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <br><br>
    <div style="float: right; text-align: center; width: 200px;">
        Kepanjen, <?= date('d-m-Y') ?><br>
        Pegawai Yang Bersangkutan,<br><br><br><br>
        <strong><?= $_SESSION['nama'] ?></strong>
    </div>

</body>
</html>
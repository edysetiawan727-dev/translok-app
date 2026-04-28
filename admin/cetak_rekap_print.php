<?php
session_start();
include '../config/database.php';

// Cek Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    exit("Akses Ditolak");
}

$tgl_awal  = $_GET['tgl_awal'];
$tgl_akhir = $_GET['tgl_akhir'];

// QUERY PENTING:
// Kita ambil data kegiatan, TAPI kita gabungkan (JOIN) dengan tabel users
// Supaya kita tahu ID kegiatan ini milik Siapa (Namanya siapa, NIP-nya berapa)
$query = mysqli_query($koneksi, "SELECT kegiatan.*, users.nama, users.nip 
                                 FROM kegiatan 
                                 JOIN users ON kegiatan.user_id = users.id
                                 WHERE kegiatan.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'
                                 ORDER BY kegiatan.tanggal ASC, users.nama ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Kegiatan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11pt; margin: 30px; }
        h2, h4 { text-align: center; margin: 0; }
        .garis { border-bottom: 2px solid black; margin: 15px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; vertical-align: top;}
        th { background-color: #f2f2f2; text-align: center; }
        
        @media print {
            @page { margin: 1.5cm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <h2>REKAPITULASI KEGIATAN PEGAWAI</h2>
    <h4>Periode: <?= date('d-m-Y', strtotime($tgl_awal)) ?> s/d <?= date('d-m-Y', strtotime($tgl_akhir)) ?></h4>
    <div class="garis"></div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 20%;">Nama Pegawai / NIP</th>
                <th style="width: 20%;">Jenis Kegiatan</th>
                <th>Keterangan</th>
                <th style="width: 10%;">Status</th>
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
                    <td style="text-align: center;"><?= $tgl_indo ?></td>
                    <td>
                        <strong><?= $row['nama'] ?></strong><br>
                        <small>NIP: <?= $row['nip'] ?></small>
                    </td>
                    <td><?= $row['jenis'] ?></td>
                    <td><?= $row['keterangan'] ?></td>
                    <td style="text-align: center; font-size: 0.9em;"><?= $status_text ?></td>
                </tr>
            <?php 
                } 
            } else {
                echo "<tr><td colspan='6' style='text-align:center; padding: 20px;'>Tidak ada kegiatan pegawai pada periode ini.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div style="margin-top: 30px; float: right; text-align: center; width: 250px;">
        Dicetak oleh Admin,<br>
        Pada: <?= date('d-m-Y H:i') ?><br><br><br>
        (........................................)
    </div>

</body>
</html>
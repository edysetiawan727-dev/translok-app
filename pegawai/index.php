<?php
session_start();
include '../config/database.php';

// 1. CEK LOGIN
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pegawai') {
    header("Location: ../index.php");
    exit;
}

// 2. AMBIL MASTER WARNA (LOGIKA BARU: EXACT MATCH)
$master_warna = [];
$q_master = mysqli_query($koneksi, "SELECT * FROM jenis_kegiatan");
while ($m = mysqli_fetch_assoc($q_master)) {
    // Key: Nama Kegiatan (Lower case & Trim), Value: Kode Warna
    $master_warna[strtolower(trim($m['nama_kegiatan']))] = $m['warna_label'];
}

// FUNGSI PENCARI WARNA BERDASARKAN JENIS (BUKAN TEBAK KATA LAGI)
function getWarnaByJenis($jenis, $master) {
    $kunci = strtolower(trim($jenis));
    if (isset($master[$kunci])) {
        return $master[$kunci];
    }
    return "#6c757d"; // Default Abu
}

// 3. LOGIKA TANGGAL & QUERY
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$id_user = $_SESSION['id'];

// Query Data Kegiatan Kalender
$query_kegiatan = mysqli_query($koneksi, "SELECT * FROM kegiatan WHERE user_id='$id_user' AND MONTH(tanggal)='$bulan' AND YEAR(tanggal)='$tahun'");
$data_kegiatan = [];
while ($row = mysqli_fetch_assoc($query_kegiatan)) {
    $data_kegiatan[$row['tanggal']] = $row;
}

// Query Sidebar
$query_list = mysqli_query($koneksi, "SELECT * FROM kegiatan WHERE user_id='$id_user' AND MONTH(tanggal)='$bulan' AND YEAR(tanggal)='$tahun' ORDER BY tanggal DESC");
$total_item = mysqli_num_rows($query_list);

// Data Kalender Dasar
$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
$tgl_pertama = "$tahun-$bulan-01";
$hari_pertama = date('w', strtotime($tgl_pertama)); 

$nama_bulan = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pegawai - DaMPiT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .welcome-box { background: linear-gradient(135deg, #0d6efd, #0043a8); color: white; border-radius: 15px; padding: 30px; box-shadow: 0 10px 20px rgba(13, 110, 253, 0.2); margin-bottom: 25px; }
        .custom-card { background: white; border: none; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); overflow: hidden; height: 100%; }
        
        .kalender-grid { display: flex; flex-wrap: wrap; border-top: 1px solid #eee; border-left: 1px solid #eee; }
        .kalender-col { width: 14.28%; border-right: 1px solid #eee; border-bottom: 1px solid #eee; min-height: 110px; position: relative; background-color: white; padding: 8px; cursor: pointer; transition: 0.2s; }
        .kalender-col:hover { background-color: #f9f9f9; z-index: 5; box-shadow: inset 0 0 10px rgba(0,0,0,0.05); }
        .header-hari { width: 14.28%; text-align: center; font-weight: bold; background-color: #fff; padding: 15px 0; color: #8898aa; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; border-bottom: 2px solid #eee; }

        .tgl-kiri { background-color: #fafafa; cursor: default; }
        .weekend { background-color: #fff5f5; } 
        .text-tgl { font-weight: bold; font-size: 1rem; display: block; margin-bottom: 5px; color: #525f7f; }
        .text-danger-custom { color: #dc3545 !important; }
        
        .status-badge { display: inline-block; margin-top: 5px; font-size: 0.7rem; padding: 3px 6px; border-radius: 4px; font-weight: bold; color: white; text-shadow: 0 0 1px rgba(0,0,0,0.3); }
        .text-keterangan { font-size: 0.75rem; color: #555; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

        .list-group-item { border: none; border-bottom: 1px solid #f0f0f0; padding: 15px; }
        .list-group-item:last-child { border-bottom: none; }
        .icon-box { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; font-weight: bold; color: white; text-shadow: 0 0 2px rgba(0,0,0,0.2); }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm sticky-top mb-4">
        <div class="container">
            <a class="navbar-brand p-0" href="index.php">
                <img src="../assets/img/logo_dampit.png" alt="DaMPiT" style="height: 50px; background: white; padding: 5px 12px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
            </a>
            <div class="navbar-nav me-auto ms-4">
                <a class="nav-link text-white fw-bold active" href="index.php">Kalender</a>
                <a class="nav-link text-white opacity-75 hover-opacity-100" href="cetak.php">🖨️ Cetak Laporan</a>
            </div>
            <div class="d-flex text-white align-items-center">
                <span class="me-3 d-none d-md-block">Halo, <?php echo $_SESSION['nama']; ?></span>
                <a href="../logout.php" class="btn btn-sm btn-light text-primary fw-bold">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="welcome-box d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div><h2 class="fw-bold mb-1">Dashboard Kinerja 🚀</h2><p class="mb-0 opacity-75">Kelola kegiatan Anda di sini.</p></div>
            <div><a href="input_kegiatan.php?tanggal=<?php echo date('Y-m-d'); ?>&mode=baru" class="btn btn-light text-primary fw-bold shadow-sm px-4 py-2"><i class="bi bi-plus-lg me-2"></i> Input Hari Ini</a></div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6 mb-2">
                <div class="custom-card p-3 d-flex align-items-center">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary me-3" style="color: var(--bs-primary) !important;"><i class="bi bi-journal-text"></i></div>
                    <div><small class="text-muted fw-bold text-uppercase">Total Kegiatan</small><h4 class="mb-0 fw-bold"><?php echo $total_item; ?> <span class="fs-6 text-muted fw-normal">Item</span></h4></div>
                </div>
            </div>
            <div class="col-md-6 mb-2">
                <div class="custom-card p-3 d-flex align-items-center">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning me-3" style="color: var(--bs-warning) !important;"><i class="bi bi-calendar-event"></i></div>
                    <div><small class="text-muted fw-bold text-uppercase">Periode Laporan</small><h4 class="mb-0 fw-bold"><?php echo $nama_bulan[$bulan]; ?> <span class="fs-6 text-muted fw-normal"><?php echo $tahun; ?></span></h4></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="custom-card">
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white">
                        <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-calendar3 me-2"></i>Kalender Kegiatan</h5>
                        <div class="btn-group shadow-sm">
                            <a href="?bulan=<?= date('m', mktime(0,0,0,$bulan-1,1,$tahun)) ?>&tahun=<?= date('Y', mktime(0,0,0,$bulan-1,1,$tahun)) ?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-chevron-left"></i></a>
                            <a href="#" class="btn btn-primary btn-sm px-3 fw-bold"><?= $nama_bulan[$bulan] ?></a>
                            <a href="?bulan=<?= date('m', mktime(0,0,0,$bulan+1,1,$tahun)) ?>&tahun=<?= date('Y', mktime(0,0,0,$bulan+1,1,$tahun)) ?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="d-flex text-center">
                        <div class="header-hari text-danger-custom">MGG</div><div class="header-hari">SEN</div><div class="header-hari">SEL</div><div class="header-hari">RAB</div><div class="header-hari">KAM</div><div class="header-hari">JUM</div><div class="header-hari text-danger-custom">SAB</div>
                    </div>
                    <div class="kalender-grid">
                        <?php
                        for ($i = 0; $i < $hari_pertama; $i++) { echo '<div class="kalender-col tgl-kiri"></div>'; }

                        for ($tgl = 1; $tgl <= $jumlah_hari; $tgl++) {
                            $tgl_full = sprintf("%04d-%02d-%02d", $tahun, $bulan, $tgl);
                            $hari_angka = date('w', strtotime($tgl_full));
                            $bg_dasar = ($hari_angka == 0 || $hari_angka == 6) ? 'weekend' : '';
                            $teks_merah = ($hari_angka == 0 || $hari_angka == 6) ? 'text-danger-custom' : '';

                            $konten = ""; $status_db = ""; $keterangan_full = ""; $alasan_full = ""; $jenis_label = "";

                            if (isset($data_kegiatan[$tgl_full])) {
                                $row = $data_kegiatan[$tgl_full];
                                $status_db = $row['status'];
                                $keterangan_full = htmlspecialchars($row['keterangan']);
                                $alasan_full = isset($row['alasan_update']) ? htmlspecialchars($row['alasan_update']) : '-';
                                $jenis_db = isset($row['jenis']) ? $row['jenis'] : ''; // AMBIL KOLOM JENIS
                                
                                // --- LOGIKA WARNA (CEK LANGSUNG KE MASTER) ---
                                $warna = getWarnaByJenis($jenis_db, $master_warna);
                                $style_bg = "background-color:".$warna."20; border-left: 4px solid ".$warna; // bg transparan
                                
                                $icon_status = "";
                                if ($status_db == 'LOCKED') $icon_status = "<span class='badge bg-secondary ms-1'><i class='bi bi-lock-fill'></i></span>";
                                if ($status_db == 'FINAL') $icon_status = "<span class='badge bg-danger ms-1'><i class='bi bi-x-circle-fill'></i></span>";

                                $label_tampil = !empty($jenis_db) ? $jenis_db : 'Kegiatan';

                                $konten = "<div class='h-100 w-100 p-1 rounded' style='$style_bg'>";
                                $konten .= "<span class='text-tgl $teks_merah'>$tgl</span>";
                                $konten .= "<div class='text-keterangan'>$keterangan_full</div>";
                                $konten .= "<div class='mt-1'><span class='status-badge' style='background-color:$warna;'>$label_tampil</span>$icon_status</div>";
                                $konten .= "</div>";
                            } else {
                                $konten = "<span class='text-tgl $teks_merah'>$tgl</span>";
                            }
                        ?>
                            <div class="kalender-col <?php echo $bg_dasar; ?>" 
                                 onclick="klikTanggal('<?= $tgl_full ?>', '<?= $status_db ?>', '<?= $keterangan_full ?>', '<?= $alasan_full ?>', '<?= $jenis_label ?? '' ?>')">
                                <?= $konten ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="custom-card h-100">
                    <div class="p-3 bg-light border-bottom"><h6 class="fw-bold mb-0 text-dark"><i class="bi bi-list-task me-2"></i>Riwayat Bulan Ini</h6></div>
                    <div class="list-group list-group-flush overflow-auto" style="max-height: 500px;">
                        <?php if(mysqli_num_rows($query_list) > 0) { ?>
                            <?php while($list = mysqli_fetch_assoc($query_list)) { 
                                $jenis_side = isset($list['jenis']) ? $list['jenis'] : '';
                                $warna_side = getWarnaByJenis($jenis_side, $master_warna);
                                $label_side = !empty($jenis_side) ? $jenis_side : 'Kegiatan';
                            ?>
                            <div class="list-group-item d-flex gap-3 align-items-start">
                                <div class="icon-box flex-shrink-0" style="background-color: <?php echo $warna_side; ?>;">
                                    <?php echo date('d', strtotime($list['tanggal'])); ?>
                                </div>
                                <div class="w-100">
                                    <div class="d-flex justify-content-between w-100 mb-1">
                                        <small class="fw-bold text-dark"><?php echo date('d M Y', strtotime($list['tanggal'])); ?></small>
                                        <small class="badge" style="background-color: <?php echo $warna_side; ?>; opacity: 0.8;"><?php echo $label_side; ?></small>
                                    </div>
                                    <p class="mb-0 small text-muted text-truncate" style="max-width: 200px;"><?php echo $list['keterangan']; ?></p>
                                </div>
                            </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="text-center p-5 text-muted"><i class="bi bi-clipboard-x display-4 opacity-25"></i><p class="small mt-2">Belum ada kegiatan bulan ini.</p></div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-2 d-flex flex-wrap gap-3 small text-muted">
            <?php 
            // Reset pointer
            mysqli_data_seek($q_master, 0); 
            while($m = mysqli_fetch_assoc($q_master)): ?>
                <div class="d-flex align-items-center"><span class="d-inline-block rounded me-1" style="width:15px;height:15px;background:<?php echo $m['warna_label']; ?>;"></span> <?php echo $m['nama_kegiatan']; ?></div>
            <?php endwhile; ?>
            <div class="d-flex align-items-center ms-auto"><span class="badge bg-danger bg-opacity-10 text-danger border border-danger me-2"> </span> Hari Libur</div>
        </div>
    </div>

    <div class="modal fade" id="modalRingkasan" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white" id="modalHeader">
                    <h5 class="modal-title" id="modalTitle">📄 Detail Kegiatan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2"><strong>Tanggal:</strong> <span id="viewTanggal"></span></div>
                    <div class="mb-3"><strong>Status:</strong> <span id="viewStatusBadge"></span></div>
                    <div class="mb-3"><label class="fw-bold text-muted small">Keterangan:</label><div class="p-3 bg-light border rounded" id="viewKeterangan"></div></div>
                    <div id="blokAlasan" class="mb-3" style="display: none;"><label class="fw-bold text-danger small">Alasan Update:</label><div class="p-3 bg-danger bg-opacity-10 border border-danger rounded text-danger" id="viewAlasan"></div></div>
                    <div id="pesanKesempatan" class="mt-3 text-muted small"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
                    <a href="#" id="btnUpdate" class="btn btn-warning">✏️ Update Data</a>
                    <a href="#" target="_blank" id="btnAdmin" class="btn btn-danger" style="display: none;"><i class="bi bi-whatsapp"></i> Ajukan ke Admin</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const modalRingkasan = new bootstrap.Modal(document.getElementById('modalRingkasan'));
        function klikTanggal(tanggal, status, keterangan, alasan, jenis) {
            if (!status && !keterangan) { 
                let konfirmasi = confirm("Input kegiatan tanggal " + tanggal + "?");
                if(konfirmasi) window.location.href = "input_kegiatan.php?tanggal=" + tanggal + "&mode=baru";
                return;
            }

            document.getElementById('viewTanggal').innerText = tanggal;
            document.getElementById('viewKeterangan').innerText = keterangan;
            document.getElementById('blokAlasan').style.display = 'none';
            document.getElementById('btnUpdate').style.display = 'none';
            document.getElementById('btnAdmin').style.display = 'none';
            
            const badge = document.getElementById('viewStatusBadge');
            const pesan = document.getElementById('pesanKesempatan');
            const header = document.getElementById('modalHeader');
            const title = document.getElementById('modalTitle');

            if (status === 'LOCKED') {
                badge.className = 'badge bg-warning text-dark'; badge.innerText = 'LOCKED (Terkunci)'; pesan.innerText = '* Kesempatan update 1x lagi.';
                header.className = 'modal-header bg-warning text-dark'; title.innerText = '🔒 Kegiatan Terkunci';
                document.getElementById('btnUpdate').style.display = 'block';
                document.getElementById('btnUpdate').href = "input_kegiatan.php?tanggal=" + tanggal + "&mode=update";
                modalRingkasan.show();
            } 
            else if (status === 'FINAL') {
                badge.className = 'badge bg-danger'; badge.innerText = 'FINAL (Tidak Bisa Diedit)'; pesan.innerText = '* Data sudah final.';
                header.className = 'modal-header bg-danger text-white'; title.innerText = '🚫 Status Final';
                document.getElementById('blokAlasan').style.display = 'block'; document.getElementById('viewAlasan').innerText = alasan;
                document.getElementById('btnAdmin').style.display = 'block';
                document.getElementById('btnAdmin').href = "https://wa.me/6282268097901?text=Halo Admin, mohon buka kunci tanggal " + tanggal;
                modalRingkasan.show();
            } 
            else {
                let konfirmasi = confirm("Edit kegiatan tanggal " + tanggal + "?");
                if(konfirmasi) window.location.href = "input_kegiatan.php?tanggal=" + tanggal + "&mode=baru"; 
            }
        }
    </script>
</body>
</html>
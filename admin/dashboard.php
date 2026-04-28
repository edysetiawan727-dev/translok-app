<?php
session_start();
include '../config/database.php';

// Cek Login Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

// -- 1. AMBIL MASTER WARNA --
$master_warna = [];
$q_master = mysqli_query($koneksi, "SELECT * FROM jenis_kegiatan");
while ($m = mysqli_fetch_assoc($q_master)) {
    $master_warna[strtolower(trim($m['nama_kegiatan']))] = $m['warna_label'];
}

// -- 2. STATISTIK --
$bulan_ini = date('m');
$tahun_ini = date('Y');

$q_user = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM users WHERE role='pegawai'");
$d_user = mysqli_fetch_assoc($q_user);
$total_pegawai = $d_user['total'];

$q_kegiatan = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kegiatan WHERE MONTH(tanggal)='$bulan_ini' AND YEAR(tanggal)='$tahun_ini'");
$d_kegiatan = mysqli_fetch_assoc($q_kegiatan);
$total_kegiatan = $d_kegiatan['total'];

$rata_rata = ($total_pegawai > 0) ? round($total_kegiatan / $total_pegawai, 1) : 0;

// -- LOGIKA MATRIX & CHART & DROPDOWN --
$bln_matrix = isset($_GET['bln_matrix']) ? $_GET['bln_matrix'] : date('m');
$thn_matrix = isset($_GET['thn_matrix']) ? $_GET['thn_matrix'] : date('Y');
$jml_hari_matrix = cal_days_in_month(CAL_GREGORIAN, $bln_matrix, $thn_matrix);

$q_peg_all = mysqli_query($koneksi, "SELECT id, nama FROM users WHERE role='pegawai' ORDER BY nama ASC");
$list_pegawai = [];
while ($p = mysqli_fetch_assoc($q_peg_all)) $list_pegawai[$p['id']] = $p['nama'];

$q_matrix = mysqli_query($koneksi, "SELECT * FROM kegiatan WHERE MONTH(tanggal)='$bln_matrix' AND YEAR(tanggal)='$thn_matrix'");
$matrix = [];
while ($km = mysqli_fetch_assoc($q_matrix)) $matrix[$km['user_id']][(int)date('d', strtotime($km['tanggal']))] = $km;

// -- DATA DETAIL --
$id_pegawai_pilih = isset($_GET['id_pegawai']) ? $_GET['id_pegawai'] : '';
$bulan_pilih = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun_pilih = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$data_kegiatan_detail = []; $nama_pegawai_terpilih = "";

if (!empty($id_pegawai_pilih)) {
    $nama_pegawai_terpilih = isset($list_pegawai[$id_pegawai_pilih]) ? $list_pegawai[$id_pegawai_pilih] : "Pegawai";
    $q_detail = mysqli_query($koneksi, "SELECT * FROM kegiatan WHERE user_id='$id_pegawai_pilih' AND MONTH(tanggal)='$bulan_pilih' AND YEAR(tanggal)='$tahun_pilih'");
    while ($row = mysqli_fetch_assoc($q_detail)) $data_kegiatan_detail[$row['tanggal']] = $row;
}
$jumlah_hari_detail = cal_days_in_month(CAL_GREGORIAN, $bulan_pilih, $tahun_pilih);
$tgl_pertama = "$tahun_pilih-$bulan_pilih-01";
$hari_pertama = date('w', strtotime($tgl_pertama));
$nama_bulan_indo = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];

// -- CHART DATA --
$grafik_data = []; $grafik_label = []; $q_top = false;
if (empty($id_pegawai_pilih)) {
    for ($i = 1; $i <= date('t'); $i++) {
        $tgl_cek = sprintf("%04d-%02d-%02d", $tahun_ini, $bulan_ini, $i);
        $d_harian = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kegiatan WHERE tanggal='$tgl_cek'"));
        $grafik_data[] = $d_harian['total']; $grafik_label[] = $i;
    }
    $q_top = mysqli_query($koneksi, "SELECT u.nama, COUNT(k.id) as jumlah FROM users u LEFT JOIN kegiatan k ON u.id = k.user_id AND MONTH(k.tanggal)='$bulan_ini' AND YEAR(k.tanggal)='$tahun_ini' WHERE u.role='pegawai' GROUP BY u.id ORDER BY jumlah DESC LIMIT 5");
}

function getWarnaByJenis($jenis, $master) {
    $kunci = strtolower(trim($jenis));
    return isset($master[$kunci]) ? $master[$kunci] : "#6c757d";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - DaMPiT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        
        /* --- STYLE KARTU STATISTIK (INTERAKTIF) --- */
        .stat-card {
            border: none; border-radius: 15px; color: white; 
            position: relative; overflow: hidden; 
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            cursor: default;
        }
        .stat-card:hover {
            transform: translateY(-5px) scale(1.02); /* Efek Mengembang & Naik */
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
            z-index: 10;
        }
        .stat-icon {
            font-size: 4rem; opacity: 0.2; 
            position: absolute; right: 10px; bottom: -10px;
            transform: rotate(-15deg);
            transition: all 0.4s ease;
        }
        .stat-card:hover .stat-icon {
            transform: rotate(0deg) scale(1.2); /* Ikon berputar & membesar saat hover */
            opacity: 0.4;
            right: 20px; bottom: 10px;
        }
        
        /* Gradients */
        .bg-gradient-primary { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); }
        .bg-gradient-success { background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%); }
        .bg-gradient-info { background: linear-gradient(135deg, #36b9cc 0%, #258391 100%); }
        .bg-gradient-warning { background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%); }

        /* Lainnya */
        .matrix-container { overflow-x: auto; background: white; border-radius: 12px; position: relative; }
        .table-matrix { margin-bottom: 0; font-size: 0.8rem; white-space: nowrap; }
        .col-sticky { position: sticky; left: 0; background-color: #fff; z-index: 5; border-right: 2px solid #eee; min-width: 180px; max-width: 180px; overflow: hidden; text-overflow: ellipsis; }
        .th-weekend { background-color: #fff5f5 !important; color: #dc3545; }
        .cell-activity { height: 30px; width: 100%; border-radius: 4px; cursor: pointer; transition: 0.2s; position: relative; }
        .cell-activity:hover { transform: scale(1.2); box-shadow: 0 0 8px rgba(0,0,0,0.3); z-index: 100; border: 1px solid #fff; }
        .cell-empty { background-color: #f8f9fa; }
        .status-dot { font-size: 9px; color: white; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-shadow: 0 0 2px black;}
        
        .kalender-grid { display: flex; flex-wrap: wrap; border-top: 1px solid #eee; border-left: 1px solid #eee; }
        .kalender-col { width: 14.28%; border-right: 1px solid #eee; border-bottom: 1px solid #eee; min-height: 120px; position: relative; background-color: white; padding: 8px; cursor: pointer; transition: 0.2s; }
        .kalender-col:hover { background-color: #f9f9f9; z-index: 5; box-shadow: inset 0 0 10px rgba(0,0,0,0.05); }
        .header-hari { width: 14.28%; text-align: center; font-weight: bold; background-color: #fff; padding: 15px 0; color: #8898aa; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; border-bottom: 2px solid #eee; }
        .tgl-kiri { background-color: #fafafa; cursor: default; }
        .weekend { background-color: #fff5f5; }
        .text-tgl { font-weight: bold; font-size: 1rem; display: block; margin-bottom: 5px; color: #525f7f; }
        .text-danger-custom { color: #dc3545 !important; }
        .status-badge { display: inline-block; margin-top: 5px; font-size: 0.7rem; padding: 3px 6px; border-radius: 4px; font-weight: bold; color: white; text-shadow: 0 0 1px rgba(0,0,0,0.3); }
        .text-ket { font-size: 0.75rem; color: #555; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand p-0" href="dashboard.php"><img src="../assets/img/logo_dampit.png" alt="DaMPiT" style="height: 55px; background: white; padding: 5px 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);"></a>
            <div class="navbar-nav me-auto ms-4">
                <a class="nav-link text-white fw-bold active" href="dashboard.php">Dashboard</a>
                <a class="nav-link text-white opacity-75 hover-opacity-100" href="cetak_rekap.php">🖨️ Cetak Rekap</a>
                <a class="nav-link text-white opacity-75 hover-opacity-100" href="users.php">👥 Kelola User</a>
                <a class="nav-link text-white opacity-75 hover-opacity-100" href="kegiatan_master.php">⚙️ Master Kegiatan</a>
            </div>
            <div class="d-flex text-white align-items-center"><span class="me-3">Halo, Admin</span><a href="../logout.php" class="btn btn-sm btn-outline-light fw-bold">Logout</a></div>
        </div>
    </nav>

    <div class="container pb-5">
        
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card stat-card bg-gradient-primary h-100">
                    <div class="card-body p-4">
                        <h6 class="text-uppercase small fw-bold opacity-75">Total Pegawai</h6>
                        <h2 class="mb-0 fw-bold display-5"><?php echo $total_pegawai; ?></h2>
                        <i class="bi bi-people-fill stat-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card bg-gradient-success h-100">
                    <div class="card-body p-4">
                        <h6 class="text-uppercase small fw-bold opacity-75">Total Kegiatan</h6>
                        <h2 class="mb-0 fw-bold display-5"><?php echo $total_kegiatan; ?></h2>
                        <i class="bi bi-check-circle-fill stat-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card bg-gradient-info h-100">
                    <div class="card-body p-4">
                        <h6 class="text-uppercase small fw-bold opacity-75">Rata-rata</h6>
                        <h2 class="mb-0 fw-bold display-5"><?php echo $rata_rata; ?></h2>
                        <i class="bi bi-bar-chart-fill stat-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card bg-gradient-warning h-100">
                    <div class="card-body p-4 text-white">
                        <h6 class="text-uppercase small fw-bold opacity-75">Periode</h6>
                        <h2 class="mb-0 fw-bold display-6"><?php echo date('M Y'); ?></h2>
                        <i class="bi bi-calendar-check-fill stat-icon"></i>
                    </div>
                </div>
            </div>
        </div>

        <?php if (empty($id_pegawai_pilih)): ?>
            <div class="row mb-4">
                <div class="col-lg-8 mb-4">
                    <div class="card card-custom h-100">
                        <div class="card-header bg-white py-3"><h6 class="m-0 fw-bold text-primary">📈 Tren Harian</h6></div>
                        <div class="card-body"><canvas id="grafikAktivitas" style="max-height: 250px;"></canvas></div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card card-custom h-100">
                        <div class="card-header bg-white py-3"><h6 class="m-0 fw-bold text-success">🏆 Top 5 Pegawai</h6></div>
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0 small">
                                <thead class="table-light"><tr><th class="ps-4">Nama</th><th class="text-center">Jml</th></tr></thead>
                                <tbody>
                                    <?php while($top = mysqli_fetch_assoc($q_top)): ?>
                                    <tr><td class="ps-4 fw-bold"><?php echo $top['nama']; ?></td><td class="text-center"><span class="badge bg-success rounded-pill px-3"><?php echo $top['jumlah']; ?></span></td></tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-custom mb-4 border-start border-4 border-primary">
                <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div><h5 class="fw-bold text-primary mb-1"><i class="bi bi-search me-2"></i>Monitoring Detail</h5><p class="text-muted mb-0 small">Pilih pegawai untuk melihat kalender personal.</p></div>
                    <form action="" method="GET" class="d-flex gap-2">
                        <input type="hidden" name="bulan" value="<?= $bulan_pilih ?>"><input type="hidden" name="tahun" value="<?= $tahun_pilih ?>">
                        <select name="id_pegawai" class="form-select shadow-sm" style="min-width: 300px;" onchange="this.form.submit()">
                            <option value="">-- Pilih Nama Pegawai --</option>
                            <?php foreach($list_pegawai as $uid => $unama) { echo "<option value='$uid'>$unama</option>"; } ?>
                        </select>
                    </form>
                </div>
            </div>

            <div class="card card-custom shadow-sm mb-5">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <div><h5 class="m-0 fw-bold text-primary"><i class="bi bi-grid-3x3-gap me-2"></i>Matrix Aktivitas</h5></div>
                    <form action="" method="GET" class="d-flex gap-2">
                        <select name="bln_matrix" class="form-select form-select-sm w-auto"><?php foreach($nama_bulan_indo as $k=>$v) { $sel=($k==$bln_matrix)?'selected':''; echo "<option value='$k' $sel>$v</option>"; } ?></select>
                        <select name="thn_matrix" class="form-select form-select-sm w-auto"><?php for($t=2024;$t<=date('Y')+1;$t++) { $sel=($t==$thn_matrix)?'selected':''; echo "<option value='$t' $sel>$t</option>"; } ?></select>
                        <button type="submit" class="btn btn-sm btn-outline-primary">Lihat</button>
                    </form>
                </div>
                <div class="matrix-container">
                    <table class="table table-bordered table-matrix table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="col-sticky py-3 ps-3" style="z-index:10;">NAMA PEGAWAI</th>
                                <?php for($i=1; $i<=$jml_hari_matrix; $i++): $tgl_cek = sprintf("%04d-%02d-%02d", $thn_matrix, $bln_matrix, $i); $hari = date('w', strtotime($tgl_cek)); $cls_weekend = ($hari==0 || $hari==6) ? 'th-weekend' : ''; ?>
                                    <th class="text-center <?php echo $cls_weekend; ?>" style="min-width:35px;"><?php echo $i; ?></th>
                                <?php endfor; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($list_pegawai as $uid => $unama): ?>
                            <tr>
                                <td class="col-sticky py-2 ps-3 fw-bold text-dark bg-white"><?php echo $unama; ?></td>
                                <?php for($d=1; $d<=$jml_hari_matrix; $d++): 
                                    $data = isset($matrix[$uid][$d]) ? $matrix[$uid][$d] : null;
                                    $bg_weekend = (date('w', strtotime(sprintf("%04d-%02d-%02d", $thn_matrix, $bln_matrix, $d)))==0 || date('w', strtotime(sprintf("%04d-%02d-%02d", $thn_matrix, $bln_matrix, $d)))==6) ? 'background-color:#fff5f5;' : '';
                                ?>
                                    <td class="p-1 text-center" style="<?php echo $bg_weekend; ?>">
                                        <?php if($data): 
                                            $jenis_keg = isset($data['jenis']) ? $data['jenis'] : '';
                                            $warna = getWarnaByJenis($jenis_keg, $master_warna);
                                            $icon = "";
                                            if($data['status']=='LOCKED') $icon = "<i class='bi bi-lock-fill status-dot'></i>";
                                            if($data['status']=='FINAL') $icon = "<i class='bi bi-check-all status-dot'></i>";
                                            $tooltip = date('d M', strtotime($data['tanggal'])) . " : " . $data['keterangan'];
                                        ?>
                                            <div class="cell-activity" style="background-color: <?php echo $warna; ?>;" data-bs-toggle="tooltip" title="<?php echo $tooltip; ?>"><?php echo $icon; ?></div>
                                        <?php else: ?><div class="cell-activity cell-empty"></div><?php endif; ?>
                                    </td>
                                <?php endfor; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white d-flex gap-3 small text-muted flex-wrap">
                    <?php mysqli_data_seek($q_master, 0); while($m = mysqli_fetch_assoc($q_master)): ?>
                        <div class="d-flex align-items-center"><span class="d-inline-block rounded me-1" style="width:12px;height:12px;background:<?php echo $m['warna_label']; ?>;"></span> <?php echo $m['nama_kegiatan']; ?></div>
                    <?php endwhile; ?>
                </div>
            </div>
            <script>
                const ctx = document.getElementById('grafikAktivitas').getContext('2d');
                new Chart(ctx, { type: 'line', data: { labels: <?php echo json_encode($grafik_label); ?>, datasets: [{ label: 'Kegiatan', data: <?php echo json_encode($grafik_data); ?>, borderColor: '#4e73df', backgroundColor: 'rgba(78, 115, 223, 0.05)', fill: true, tension: 0.3 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false } } } } });
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]')); var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) { return new bootstrap.Tooltip(tooltipTriggerEl) });
            </script>
        <?php endif; ?>

        <?php if (!empty($id_pegawai_pilih)): ?>
            <div class="card card-custom mb-4 border-start border-4 border-primary">
                <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div><h5 class="fw-bold text-primary mb-1"><i class="bi bi-person-check me-2"></i><?php echo $nama_pegawai_terpilih; ?></h5></div>
                    <form action="" method="GET" class="d-flex gap-2">
                        <select name="id_pegawai" class="form-select shadow-sm" style="min-width: 300px;" onchange="this.form.submit()">
                            <?php foreach($list_pegawai as $uid => $unama) { $selected = ($id_pegawai_pilih == $uid) ? 'selected' : ''; echo "<option value='$uid' $selected>$unama</option>"; } ?>
                        </select>
                         <a href="dashboard.php" class="btn btn-outline-danger">x Tutup</a>
                    </form>
                </div>
            </div>

            <div class="card card-custom shadow-sm mb-5">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                         <a href="?id_pegawai=<?= $id_pegawai_pilih ?>&bulan=<?= date('m', mktime(0,0,0,$bulan_pilih-1,1,$tahun_pilih)) ?>&tahun=<?= date('Y', mktime(0,0,0,$bulan_pilih-1,1,$tahun_pilih)) ?>" class="btn btn-outline-secondary btn-sm me-2"><i class="bi bi-chevron-left"></i></a>
                         <h5 class="m-0 fw-bold text-dark"><?= $nama_bulan_indo[$bulan_pilih] ?> <?= $tahun_pilih ?></h5>
                         <a href="?id_pegawai=<?= $id_pegawai_pilih ?>&bulan=<?= date('m', mktime(0,0,0,$bulan_pilih+1,1,$tahun_pilih)) ?>&tahun=<?= date('Y', mktime(0,0,0,$bulan_pilih+1,1,$tahun_pilih)) ?>" class="btn btn-outline-secondary btn-sm ms-2"><i class="bi bi-chevron-right"></i></a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="d-flex text-center bg-light border-bottom">
                        <div class="header-hari text-danger-custom">MGG</div><div class="header-hari">SEN</div><div class="header-hari">SEL</div><div class="header-hari">RAB</div><div class="header-hari">KAM</div><div class="header-hari">JUM</div><div class="header-hari text-danger-custom">SAB</div>
                    </div>
                    <div class="kalender-grid">
                        <?php
                        for ($i = 0; $i < $hari_pertama; $i++) echo '<div class="kalender-col tgl-kiri"></div>';
                        for ($tgl = 1; $tgl <= $jumlah_hari_detail; $tgl++) {
                            $tgl_full = sprintf("%04d-%02d-%02d", $tahun_pilih, $bulan_pilih, $tgl);
                            $hari_angka = date('w', strtotime($tgl_full));
                            $bg_dasar = ($hari_angka == 0 || $hari_angka == 6) ? 'weekend' : '';
                            $teks_merah = ($hari_angka == 0 || $hari_angka == 6) ? 'text-danger-custom' : '';
                            $konten = ""; $status = ""; $ket = ""; $alasan = ""; $history = ""; 

                            if (isset($data_kegiatan_detail[$tgl_full])) {
                                $row = $data_kegiatan_detail[$tgl_full];
                                $status = $row['status']; $id_kegiatan = $row['id'];
                                $ket = htmlspecialchars($row['keterangan']);
                                $alasan = isset($row['alasan_update']) ? htmlspecialchars($row['alasan_update']) : '-';
                                $history = isset($row['history_log']) ? str_replace("\n", "<br>", htmlspecialchars($row['history_log'])) : '';
                                
                                $jenis_keg = isset($row['jenis']) ? $row['jenis'] : '';
                                $kode_warna = getWarnaByJenis($jenis_keg, $master_warna);
                                
                                $status_icon = "";
                                if ($status == 'LOCKED') $status_icon = "<span class='badge bg-secondary ms-1'><i class='bi bi-lock-fill'></i></span>"; 
                                if ($status == 'FINAL') $status_icon = "<span class='badge bg-danger ms-1'><i class='bi bi-x-circle-fill'></i></span>"; 

                                $label_tampil = !empty($jenis_keg) ? $jenis_keg : 'Kegiatan';

                                $konten = "<div class='text-ket'>$ket</div><div class='mt-1'><span class='status-badge' style='background-color:$kode_warna; border:1px solid rgba(0,0,0,0.2);'>$label_tampil</span>$status_icon</div>";
                            }
                        ?>
                            <div class="kalender-col <?php echo $bg_dasar; ?>" style="<?php echo ($konten!='') ? 'background-color:#fff;' : ''; ?>" 
                                 onclick="modalAdmin('<?= $tgl_full ?>', '<?= $status ?>', '<?= $id_kegiatan ?? '' ?>', '<?= $ket ?>', '<?= $alasan ?>', '<?= $history ?>')">
                                <div class="h-100 w-100 p-1 rounded" style="<?php if($konten!='') echo "background-color:".$kode_warna."20; border-left: 4px solid ".$kode_warna; ?>">
                                    <span class="text-tgl <?= $teks_merah ?>"><?= $tgl ?></span>
                                    <?= $konten ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="modal fade" id="modalAdmin" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white"><h5 class="modal-title">🛡️ Kontrol Admin</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <p><strong>Tanggal:</strong> <span id="admTgl"></span></p><p><strong>Keterangan:</strong> <span id="admKet" class="fw-bold"></span></p>
                    <div id="blokAlasan" style="display:none;" class="alert alert-danger py-2"><small class="fw-bold">Alasan Update Pegawai:</small><br><span id="admAlasan"></span></div>
                    <div class="mt-3"><small class="fw-bold text-muted">📜 Riwayat Log:</small><div class="bg-light border rounded p-2 small" style="max-height: 150px; overflow-y: auto;"><span id="admHistory"></span></div></div>
                    <hr><a href="#" id="btnReset" class="btn btn-warning w-100 fw-bold mb-2">🔓 BUKA KUNCI (RESET)</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const modal = new bootstrap.Modal(document.getElementById('modalAdmin'));
        function modalAdmin(tgl, status, id, ket, alasan, history) {
            if (status === '') return;
            document.getElementById('admTgl').innerText = tgl; document.getElementById('admKet').innerText = ket; document.getElementById('admAlasan').innerText = alasan; document.getElementById('admHistory').innerHTML = history;
            document.getElementById('blokAlasan').style.display = (alasan && alasan !== '-') ? 'block' : 'none';
            document.getElementById('btnReset').href = "proses_reset.php?id=" + id;
            modal.show();
        }
    </script>
</body>
</html>
<?php
session_start();
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: pegawai/index.php");
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DaMPiT BPS Kab. Malang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* SETTING BACKGROUND GAMBAR KANTOR */
        body { 
            /* Gambar dilapis warna biru tua transparan (0.85) agar tulisan terbaca & tidak ngejreng */
            background: linear-gradient(to bottom, rgba(0, 43, 92, 0.85), rgba(0, 43, 92, 0.7)), 
                        url('assets/img/bg_login.jpeg');
            
            background-size: cover;       /* Gambar memenuhi layar */
            background-position: center;  /* Gambar di tengah */
            background-attachment: fixed; /* Gambar diam saat discroll */
            background-repeat: no-repeat;
            min-height: 100vh;            /* Tinggi minimal setinggi layar */
        }

        .login-card { 
            max-width: 420px; 
            margin: 60px auto; 
            border-radius: 15px; 
            /* Tambahkan bayangan lebih kuat biar kartu menonjol dari background */
            box-shadow: 0 15px 30px rgba(0,0,0,0.3); 
            overflow: hidden; 
        }
        
        .card-header { 
            background-color: #002B5C; 
            padding: 40px 20px; 
            text-align: center; 
            border-bottom: 5px solid #ff9f00; 
        }
        
        .logo-bps { 
            height: 65px; 
            width: auto;
            margin-bottom: 30px; 
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .box-dampit {
            background-color: white;
            border-radius: 12px;
            display: inline-block;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            padding: 5px 15px; 
            margin-bottom: 10px;
        }

        .logo-dampit {
            height: 85px; 
            width: auto;
            display: block;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="card login-card border-0">
            <div class="card-header">
                
                <img src="assets/img/logo_bps.png" alt="Logo BPS" class="logo-bps">
                
                <div class="box-dampit">
                    <img src="assets/img/logo_dampit.png" alt="Logo DaMPiT" class="logo-dampit">
                </div>

                <div class="text-white-50 small mt-3 fw-bold">
                    Dashboard Monitoring Kegiatan Pegawai Terpadu
                </div>
            </div>

            <div class="card-body p-4 pt-4">
                <form action="cek_login.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">NIP</label>
                        <input type="text" name="nip" class="form-control form-control-lg" placeholder="Masukkan NIP Anda" required autofocus>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Password</label>
                        <input type="password" name="password" class="form-control form-control-lg" placeholder="Masukkan Password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 btn-lg fw-bold shadow-sm" style="background-color: #002B5C; border-color: #002B5C;">MASUK APLIKASI</button>
                </form>
            </div>
            <div class="card-footer text-center text-muted bg-white py-3 border-0 small">
                &copy; 2026 Badan Pusat Statistik Kabupaten Malang
            </div>
        </div>
    </div>

</body>
</html>
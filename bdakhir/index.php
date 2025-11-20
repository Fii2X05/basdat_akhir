<?php
require_once 'config/database.php';

try {
    $total_mhs = $pdo->query("SELECT COUNT(*) FROM mahasiswa")->fetchColumn();
    $total_dosen = $pdo->query("SELECT COUNT(*) FROM dosen")->fetchColumn();
    $total_matkul = $pdo->query("SELECT COUNT(*) FROM mata_kuliah")->fetchColumn();
    $total_kelas = $pdo->query("SELECT COUNT(*) FROM kelas")->fetchColumn();

    $sql_view = "SELECT m.kode_matkul, m.nama_matkul, m.sks, j.nama_jurusan 
                 FROM mata_kuliah m 
                 JOIN jurusan j ON m.id_jurusan = j.id_jurusan 
                 ORDER BY m.created_at DESC LIMIT 5";
    $stmt_recent = $pdo->query($sql_view);

} catch (PDOException $e) {
    $error = "Gagal memuat data dashboard: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Sistem Manajemen Kampus</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
            border-top: 4px solid var(--accent);
        }
        .card h3 { margin: 0; font-size: 2.5em; color: var(--primary); }
        .card p { color: #666; margin-top: 5px; font-weight: bold; }
        
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        .menu-item {
            background: var(--primary);
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 6px;
            text-decoration: none;
            transition: background 0.3s;
        }
        .menu-item:hover { background: var(--accent); }
        .menu-item.secondary { background: #7f8c8d; }
        
        .section-title { border-bottom: 2px solid #eee; padding-bottom: 10px; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <header style="margin-bottom: 30px; text-align: center;">
            <h1>Sistem Akademik Kampus</h1>
            <p>Dashboard Administrator & Manajemen Data</p>
        </header>

        <?php if(isset($error)): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>

        <div class="dashboard-grid">
            <div class="card">
                <h3><?= $total_mhs ?></h3>
                <p>Mahasiswa Aktif</p>
            </div>
            <div class="card">
                <h3><?= $total_dosen ?></h3>
                <p>Dosen Pengajar</p>
            </div>
            <div class="card">
                <h3><?= $total_matkul ?></h3>
                <p>Mata Kuliah</p>
            </div>
            <div class="card">
                <h3><?= $total_kelas ?></h3>
                <p>Kelas Terdaftar</p>
            </div>
        </div>

        <h2 class="section-title">Menu Utama</h2>
        <div class="menu-grid">
            <a href="mahasiswa.php" class="menu-item">
                <strong>👥 Data Mahasiswa</strong><br><small>CRUD & Pencarian</small>
            </a>
            <a href="input_nilai.php" class="menu-item">
                <strong>📝 Input Nilai</strong><br><small>Transaksi & Validasi</small>
            </a>
            <a href="#" class="menu-item secondary">
                <strong>📚 Katalog Matkul</strong><br><small>Manajemen Kurikulum</small>
            </a>
            <a href="#" class="menu-item secondary">
                <strong>🎓 Transkrip</strong><br><small>Cetak Laporan</small>
            </a>
        </div>

        <h2 class="section-title">Fitur Teknis Database</h2>
        <div class="menu-grid">
            <a href="laporan_view.php" class="menu-item secondary">
                <strong>📊 Laporan Views</strong><br><small>Complex Joins & Materialized View</small>
            </a>
            <a href="demo_indexing.php" class="menu-item secondary">
                <strong>🚀 Demo Indexing</strong><br><small>Analisa Performa Query</small>
            </a>
        </div>

        <h2 class="section-title">Mata Kuliah Terbaru (Read-Only View)</h2>
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Mata Kuliah</th>
                    <th>SKS</th>
                    <th>Jurusan</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $stmt_recent->fetch()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['kode_matkul']) ?></td>
                    <td><?= htmlspecialchars($row['nama_matkul']) ?></td>
                    <td><?= htmlspecialchars($row['sks']) ?></td>
                    <td><?= htmlspecialchars($row['nama_jurusan']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
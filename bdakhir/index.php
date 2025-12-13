<?php
session_start();

// 1. Koneksi Database
$dbConfigPath = __DIR__ . '/config/database.php';
if (!file_exists($dbConfigPath)) { die("Error: config/database.php missing."); }
require_once $dbConfigPath;

if (!isset($pdo) || !$pdo) { die("Error: Database connection failed."); }

// 2. Load Semua Controller
require_once __DIR__ . '/controllers/JurusanController.php';
require_once __DIR__ . '/controllers/KelasController.php';
require_once __DIR__ . '/controllers/MahasiswaController.php';
require_once __DIR__ . '/controllers/DosenController.php';
require_once __DIR__ . '/controllers/MatakuliahController.php';
require_once __DIR__ . '/controllers/NilaiController.php';
require_once __DIR__ . '/controllers/JadwalController.php';
require_once __DIR__ . '/controllers/TranskripController.php';
require_once __DIR__ . '/controllers/LaporanController.php';
require_once __DIR__ . '/controllers/TransactionController.php'; // Pastikan ini ada

// 3. Ambil Parameter URL
$page = $_GET['page'] ?? 'dashboard';
$act = $_GET['act'] ?? 'list';
$id = $_GET['id'] ?? null;

// 4. ROUTING LOGIC

// A. Handle Request POST (Simpan / Update data)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($page) {
        case 'jurusan':
            $ctrl = new JurusanController($pdo);
            if ($act === 'store') $ctrl->store();
            if ($act === 'update') $ctrl->update();
            break;
        case 'kelas':
            $ctrl = new KelasController($pdo);
            if ($act === 'store') $ctrl->store();
            if ($act === 'update') $ctrl->update();
            break;
        case 'mahasiswa':
            $ctrl = new MahasiswaController($pdo);
            if ($act === 'store') $ctrl->store();
            if ($act === 'update') $ctrl->update();
            break;
        case 'dosen':
            $ctrl = new DosenController($pdo);
            if ($act === 'store') $ctrl->store();
            if ($act === 'update') $ctrl->update();
            break;
        case 'matakuliah':
            $ctrl = new MatakuliahController($pdo);
            if ($act === 'store') $ctrl->store();
            if ($act === 'update') $ctrl->update();
            break;
        case 'nilai':
            $ctrl = new NilaiController($pdo);
            if ($act === 'store') $ctrl->store();
            if ($act === 'update') $ctrl->update();
            break;
        case 'jadwal':
            $ctrl = new JadwalController($pdo);
            if ($act === 'store') $ctrl->store();
            if ($act === 'update') $ctrl->update();
            break;
        case 'transkrip':
            $ctrl = new TranskripController($pdo);
            break;
    }
}
// B. Handle Request GET khusus Action (Delete)
elseif ($act === 'delete' && $id) {
    switch ($page) {
        case 'jurusan': (new JurusanController($pdo))->delete($id); break;
        case 'kelas': (new KelasController($pdo))->delete($id); break;
        case 'mahasiswa': (new MahasiswaController($pdo))->delete($id); break;
        case 'dosen': (new DosenController($pdo))->delete($id); break;
        case 'matakuliah': (new MatakuliahController($pdo))->delete($id); break;
        case 'nilai': (new NilaiController($pdo))->delete($id); break;
        case 'jadwal': (new JadwalController($pdo))->delete($id); break;
    }
}
// C. Handle Request GET khusus Action (Refresh MV)
elseif ($act === 'refresh') {
    switch ($page) {
        case 'mahasiswa': (new MahasiswaController($pdo))->refresh(); break;
        case 'dosen': (new DosenController($pdo))->refresh(); break;
        case 'jurusan': (new JurusanController($pdo))->refresh(); break;
        case 'kelas': (new KelasController($pdo))->refresh(); break;
        case 'matakuliah': (new MatakuliahController($pdo))->refresh(); break;
        case 'jadwal': (new JadwalController($pdo))->refresh(); break;
        case 'nilai': (new NilaiController($pdo))->refresh(); break;
        case 'laporan': (new LaporanController($pdo))->refresh(); break;
    }
}
// D. [BARU] Handle Transaction Demo
elseif ($page === 'transaction_test') {
    $ctrl = new TransactionController($pdo);
    if ($act === 'success') $ctrl->demoSuccess();
    if ($act === 'fail') $ctrl->demoFail();
}

// 5. HTML VIEW RENDERING
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIAKAD - Sistem Informasi Akademik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background-color: #f8f9fa; overflow-x: hidden; }
        .sidebar { position: fixed; top: 0; bottom: 0; left: 0; z-index: 100; width: 250px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding-top: 20px; overflow-y: auto; }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding: 12px 20px; margin: 5px 15px; border-radius: 8px; transition: all 0.3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background-color: rgba(255,255,255,0.2); }
        .sidebar .nav-link i { margin-right: 10px; width: 20px; }
        .content-area { margin-left: 250px; padding: 30px; min-height: 100vh; }
        .card-custom { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .navbar-brand { color: white !important; font-weight: bold; font-size: 1.4rem; }
        @media (max-width: 768px) {
            .sidebar { position: relative; width: 100%; height: auto; }
            .content-area { margin-left: 0; width: 100%; }
        }
        @media print { .sidebar { display: none; } .content-area { margin-left: 0; padding: 0; } }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar d-print-none">
                <div class="text-center mb-4">
                    <h4 class="navbar-brand">SIAKAD</h4>
                    <small class="text-white-50">Sistem Akademik</small>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link <?= ($page === 'dashboard') ? 'active' : '' ?>" href="?page=dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a>
                    <a class="nav-link <?= ($page === 'mahasiswa') ? 'active' : '' ?>" href="?page=mahasiswa"><i class="bi bi-people"></i> Mahasiswa</a>
                    <a class="nav-link <?= ($page === 'dosen') ? 'active' : '' ?>" href="?page=dosen"><i class="bi bi-person-badge"></i> Dosen</a>
                    <a class="nav-link <?= ($page === 'jurusan') ? 'active' : '' ?>" href="?page=jurusan"><i class="bi bi-diagram-3"></i> Jurusan</a>
                    <a class="nav-link <?= ($page === 'kelas') ? 'active' : '' ?>" href="?page=kelas"><i class="bi bi-building"></i> Kelas</a>
                    <a class="nav-link <?= ($page === 'matakuliah') ? 'active' : '' ?>" href="?page=matakuliah"><i class="bi bi-journal-text"></i> Mata Kuliah</a>
                    <a class="nav-link <?= ($page === 'jadwal') ? 'active' : '' ?>" href="?page=jadwal"><i class="bi bi-calendar-week"></i> Jadwal Kuliah</a>
                    <a class="nav-link <?= ($page === 'nilai') ? 'active' : '' ?>" href="?page=nilai"><i class="bi bi-bar-chart"></i> Nilai</a>
                    <hr style="border-color: rgba(255,255,255,0.3);">
                    <a class="nav-link <?= ($page === 'transkrip') ? 'active' : '' ?>" href="?page=transkrip"><i class="bi bi-file-earmark-text"></i> Transkrip Nilai</a>
                    <a class="nav-link <?= ($page === 'laporan') ? 'active' : '' ?>" href="?page=laporan"><i class="bi bi-graph-up"></i> Laporan Akademik</a>
                    <a class="nav-link <?= ($page === 'transaction_test') ? 'active' : '' ?>" href="?page=transaction_test"><i class="bi bi-shield-check"></i> Uji Transaksi</a>
                </nav>
            </div>
            
            <div class="col-md-10 content-area">
                <?php
                $viewFile = __DIR__ . "/views/{$page}.php";
                if (file_exists($viewFile)) {
                    include $viewFile;
                } else {
                    echo "<div class='alert alert-danger'><h5>Halaman tidak ditemukan!</h5><p>File <code>views/{$page}.php</code> tidak ada.</p></div>";
                }
                ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
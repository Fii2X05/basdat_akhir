<?php
session_start();

$dbConfigPath = __DIR__ . '/config/database.php';
if (!file_exists($dbConfigPath)) { die("Error: config/database.php missing."); }
require_once $dbConfigPath;

if (!isset($pdo) || !$pdo) { die("Error: Database connection failed."); }

require_once __DIR__ . '/controllers/JurusanController.php';
require_once __DIR__ . '/controllers/KelasController.php';
require_once __DIR__ . '/controllers/MahasiswaController.php';
require_once __DIR__ . '/controllers/DosenController.php';
require_once __DIR__ . '/controllers/MatakuliahController.php';
require_once __DIR__ . '/controllers/NilaiController.php';

$page = $_GET['page'] ?? 'dashboard';
$act = $_GET['act'] ?? 'list';
$id = $_GET['id'] ?? null;

// 4. Controller Logic Dispatcher
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
        case 'jadwal': // <--- BARU
            $ctrl = new JadwalController($pdo);
            if ($act === 'store') $ctrl->store();
            if ($act === 'update') $ctrl->update();
            break;
    }
} elseif ($act === 'delete' && $id) {
    switch ($page) {
        case 'jurusan':
            (new JurusanController($pdo))->delete($id);
            break;
        case 'kelas':
            (new KelasController($pdo))->delete($id);
            break;
        case 'mahasiswa':
            (new MahasiswaController($pdo))->delete($id);
            break;
        case 'dosen':
            (new DosenController($pdo))->delete($id);
            break;
        case 'matakuliah':
            (new MatakuliahController($pdo))->delete($id);
            break;
        case 'nilai':
            (new NilaiController($pdo))->delete($id);
            break;
    }
}

// 5. HTML View Rendering
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

<link rel="stylesheet" href="assets/css/style.css">

    <style>
        body { 
            background-color: #f8f9fa; 
            overflow-x: hidden;
        }
        
        .sidebar { 
            position: fixed; 
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            width: 16.666667%; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            padding-top: 20px; 
            overflow-y: auto; 
        }

        .sidebar .nav-link { 
            color: rgba(255,255,255,0.8); 
            padding: 12px 20px; 
            margin: 5px 15px; 
            border-radius: 8px; 
            transition: all 0.3s; 
        }
        
        .sidebar .nav-link:hover, .sidebar .nav-link.active { 
            color: #fff; 
            background-color: rgba(255,255,255,0.2); 
        }
        
        .sidebar .nav-link i { 
            margin-right: 10px; 
            width: 20px; 
        }

        .content-area { 
            margin-left: 16.666667%; 
            width: 83.333333%; 
            padding: 30px; 
            min-height: 100vh;
        }

        .card-custom { 
            background: white; 
            border-radius: 10px; 
            padding: 20px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
        }
        
        .navbar-brand { 
            color: white !important; 
            font-weight: bold; 
            font-size: 1.4rem; 
        }

        @media (max-width: 768px) {
            .sidebar {
                position: relative; 
                width: 100%;
                height: auto;
                min-height: auto;
            }
            .content-area {
                margin-left: 0; 
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar">
                <div class="text-center mb-4">
                    <h4 class="navbar-brand">SIAKAD</h4>
                    <small class="text-white-50">Sistem Akademik</small>
                </div>
                
                <nav class="nav flex-column">
                    <a class="nav-link <?= ($page === 'dashboard') ? 'active' : '' ?>" href="?page=dashboard">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a class="nav-link <?= ($page === 'mahasiswa') ? 'active' : '' ?>" href="?page=mahasiswa">
                        <i class="bi bi-people"></i> Mahasiswa
                    </a>
                    <a class="nav-link <?= ($page === 'dosen') ? 'active' : '' ?>" href="?page=dosen">
                        <i class="bi bi-person-badge"></i> Dosen
                    </a>
                    <a class="nav-link <?= ($page === 'jurusan') ? 'active' : '' ?>" href="?page=jurusan">
                        <i class="bi bi-diagram-3"></i> Jurusan
                    </a>
                    <a class="nav-link <?= ($page === 'kelas') ? 'active' : '' ?>" href="?page=kelas">
                        <i class="bi bi-building"></i> Kelas
                    </a>
                    <a class="nav-link <?= ($page === 'matakuliah') ? 'active' : '' ?>" href="?page=matakuliah">
                        <i class="bi bi-journal-text"></i> Mata Kuliah
                    </a>
                    <a class="nav-link <?= ($page === 'jadwal') ? 'active' : '' ?>" href="?page=jadwal">
                        <i class="bi bi-calendar-week"></i> Jadwal Kuliah</a> 
                    <a class="nav-link <?= ($page === 'nilai') ? 'active' : '' ?>" href="?page=nilai">
                        <i class="bi bi-bar-chart"></i> Nilai
                    </a>
                    <hr style="border-color: rgba(255,255,255,0.3);">
                    <a class="nav-link <?= ($page === 'transkrip') ? 'active' : '' ?>" href="?page=transkrip">
                        <i class="bi bi-file-earmark-text"></i> Transkrip Nilai
                    </a>
                    <a class="nav-link <?= ($page === 'laporan') ? 'active' : '' ?>" href="?page=laporan">
                        <i class="bi bi-graph-up"></i> Laporan Akademik
                    </a>
                </nav>
            </div>
            
            <div class="col-md-10 content-area">
                <?php
                // Load View
                $viewFile = __DIR__ . "/views/{$page}.php";
                
                if (file_exists($viewFile)) {
                    include $viewFile;
                } else {
                    echo "<div class='alert alert-danger'>
                        <h5>Halaman tidak ditemukan!</h5>
                        <p>File <code>views/{$page}.php</code> tidak ditemukan.</p>
                        <a href='?page=dashboard' class='btn btn-primary btn-sm'>Kembali ke Dashboard</a>
                    </div>";
                }
                ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
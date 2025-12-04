<?php
// index.php - Router sederhana

// default page
$page = $_GET['page'] ?? 'dashboard';

// mapping page -> view file & title
switch ($page) {
    case 'mahasiswa':
        $title = "Data Mahasiswa";
        $viewFile = __DIR__ . "/views/mahasiswa_list.php";
        break;

    case 'dosen':
        $title = "Data Dosen";
        $viewFile = __DIR__ . "/views/dosen_list.php";
        break;

    case 'jurusan':
        $title = "Data Jurusan";
        $viewFile = __DIR__ . "/views/jurusan_list.php";
        break;

    case 'kelas':
        $title = "Data Kelas";
        $viewFile = __DIR__ . "/views/kelas_list.php";
        break;

    case 'matakuliah':
        $title = "Data Matakuliah";
        $viewFile = __DIR__ . "/views/matakuliah_list.php";
        break;

    case 'jadwal':
        $title = "Data Jadwal";
        $viewFile = __DIR__ . "/views/jadwal_list.php";
        break;

    case 'nilai':
        $title = "Data Nilai";
        $viewFile = __DIR__ . "/views/nilai_list.php";
        break;

    default:
        $title = "Dashboard";
        $viewFile = __DIR__ . "/views/dashboard.php";
        break;
}

// pastikan view ada sebelum load layout
if (!isset($viewFile) || !is_file($viewFile)) {
    // fallback ke dashboard jika file view tidak ditemukan
    $title = "Dashboard";
    $viewFile = __DIR__ . "/views/dashboard.php";
}

// load layout yang akan include $viewFile secara aman
include __DIR__ . "/views/headerfooter.php";

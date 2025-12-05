<?php
// index.php - Router

$page = $_GET['page'] ?? 'dashboard';

switch ($page) {

    // LIST
    case 'mahasiswa':
        $title = "Data Mahasiswa";
        $viewFile = __DIR__ . "/views/list/mahasiswa_list.php";
        break;

    case 'dosen':
        $title = "Data Dosen";
        $viewFile = __DIR__ . "/views/list/dosen_list.php";
        break;

    case 'jurusan':
        $title = "Data Jurusan";
        $viewFile = __DIR__ . "/views/list/jurusan_list.php";
        break;

    case 'kelas':
        $title = "Data Kelas";
        $viewFile = __DIR__ . "/views/list/kelas_list.php";
        break;

    case 'matakuliah':
        $title = "Data Mata Kuliah";
        $viewFile = __DIR__ . "/views/list/matakuliah_list.php";
        break;

    case 'jadwal':
        $title = "Data Jadwal";
        $viewFile = __DIR__ . "/views/list/jadwal_list.php";
        break;

    case 'nilai':
        $title = "Data Nilai";
        $viewFile = __DIR__ . "/views/list/nilai_list.php";
        break;


    // CREATE
    case 'mahasiswa_create':
        $title = "Tambah Mahasiswa";
        $viewFile = __DIR__ . "/views/create/mahasiswa_create.php";
        break;

    case 'dosen_create':
        $title = "Tambah Dosen";
        $viewFile = __DIR__ . "/views/create/dosen_create.php";
        break;

    case 'jurusan_create':
        $title = "Tambah Jurusan";
        $viewFile = __DIR__ . "/views/create/jurusan_create.php";
        break;

    case 'kelas_create':
        $title = "Tambah Kelas";
        $viewFile = __DIR__ . "/views/create/kelas_create.php";
        break;

    case 'matakuliah_create':
        $title = "Tambah Matakuliah";
        $viewFile = __DIR__ . "/views/create/matakuliah_create.php";
        break;

    case 'jadwal_create':
        $title = "Tambah Jadwal";
        $viewFile = __DIR__ . "/views/create/jadwal_create.php";
        break;

    case 'nilai_create':
        $title = "Tambah Nilai";
        $viewFile = __DIR__ . "/views/create/nilai_create.php";
        break;


    // EDIT
    case 'mahasiswa_edit':
        $title = "Edit Mahasiswa";
        $viewFile = __DIR__ . "/views/edit/mahasiswa_edit.php";
        break;

    case 'dosen_edit':
        $title = "Edit Dosen";
        $viewFile = __DIR__ . "/views/edit/dosen_edit.php";
        break;

    case 'jurusan_edit':
        $title = "Edit Jurusan";
        $viewFile = __DIR__ . "/views/edit/jurusan_edit.php";
        break;

    case 'kelas_edit':
        $title = "Edit Kelas";
        $viewFile = __DIR__ . "/views/edit/kelas_edit.php";
        break;

    case 'matakuliah_edit':
        $title = "Edit Mata Kuliah";
        $viewFile = __DIR__ . "/views/edit/matakuliah_edit.php";
        break;

    case 'jadwal_edit':
        $title = "Edit Jadwal";
        $viewFile = __DIR__ . "/views/edit/jadwal_edit.php";
        break;

    case 'nilai_edit':
        $title = "Edit Nilai";
        $viewFile = __DIR__ . "/views/edit/nilai_edit.php";
        break;


    // DETAIL
    case 'mahasiswa_detail':
        $title = "Detail Mahasiswa";
        $viewFile = __DIR__ . "/views/detail/mahasiswa_detail.php";
        break;

    case 'dosen_detail':
        $title = "Detail Dosen";
        $viewFile = __DIR__ . "/views/detail/dosen_detail.php";
        break;

    case 'jurusan_detail':
        $title = "Detail Jurusan";
        $viewFile = __DIR__ . "/views/detail/jurusan_detail.php";
        break;

    case 'kelas_detail':
        $title = "Detail Kelas";
        $viewFile = __DIR__ . "/views/detail/kelas_detail.php";
        break;

    case 'matakuliah_detail':
        $title = "Detail Mata Kuliah";
        $viewFile = __DIR__ . "/views/detail/matakuliah_detail.php";
        break;

    case 'jadwal_detail':
        $title = "Detail Jadwal";
        $viewFile = __DIR__ . "/views/detail/jadwal_detail.php";
        break;

    case 'nilai_detail':
        $title = "Detail Nilai";
        $viewFile = __DIR__ . "/views/detail/nilai_detail.php";
        break;


    // DEFAULT
    default:
        $title = "Dashboard";
        $viewFile = __DIR__ . "/views/dashboard.php";
        break;
}

include __DIR__ . "/views/headerfooter.php";

<?php
// Pastikan $viewFile selalu ada sebelum include
if (!isset($viewFile) || $viewFile == "") {
    $viewFile = "dashboard.php";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SI Akademik Kampus</title>

    <!-- BOOTSTRAP -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <!-- ICONS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            background: #eef1f7;
        }

        /* SIDEBAR */
        .sidebar {
            width: 240px;
            height: 100vh;
            background: #0b1a39;
            position: fixed;
            left: 0;
            top: 0;
            color: white;
            padding: 25px 20px;
        }

        .sidebar .title {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 30px;
            line-height: 1.2;
        }

        .sidebar a {
            display: block;
            padding: 10px 12px;
            margin-bottom: 8px;
            color: #cbd3f5;
            border-radius: 8px;
            font-size: 15px;
            text-decoration: none;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #1a2e63;
            color: white;
        }

        /* CONTENT */
        .content-area {
            margin-left: 240px;
            padding: 35px;
        }

        .top-header {
            background: white;
            padding: 18px 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-size: 20px;
            font-weight: 600;
        }

        .card-custom {
            background: white;
            border-radius: 12px;
            padding: 25px;
            border: 1px solid #e2e5ec;
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="title">SIAKAD<br><small>Sistem Akademik Kampus</small></div>

        <a href="?page=dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="?page=mahasiswa"><i class="bi bi-people"></i> Data Mahasiswa</a>
        <a href="?page=dosen"><i class="bi bi-person-badge"></i> Data Dosen</a>
        <a href="?page=jurusan"><i class="bi bi-diagram-3"></i> Data Jurusan</a>
        <a href="?page=kelas"><i class="bi bi-building"></i> Data Kelas</a>
        <a href="?page=matakuliah"><i class="bi bi-journal-text"></i> Data Matakuliah</a>
        <a href="?page=jadwal"><i class="bi bi-calendar-week"></i> Data Jadwal</a>
        <a href="?page=nilai"><i class="bi bi-bar-chart"></i> Data Nilai</a>
    </div>

    <!-- CONTENT -->
    <div class="content-area">

        <div class="top-header">
            <?= $title ?? "Dashboard"; ?>
        </div>

        <?php include $viewFile; ?>

    </div>

</body>
</html>

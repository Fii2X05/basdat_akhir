<?php
require '../config.php';

$id = $_GET['id'];

$q = "
SELECT j.*, mk.nama AS matakuliah, d.nama AS dosen, k.nama AS kelas
FROM jadwal j
JOIN mata_kuliah mk ON j.matakuliah_id = mk.id
JOIN dosen d ON j.dosen_id = d.id
JOIN kelas k ON j.kelas_id = k.id
WHERE j.id = $id
";

$r = mysqli_fetch_assoc(mysqli_query($conn, $q));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Detail Jadwal</title>
</head>
<body>
    <h1>Detail Jadwal</h1>

    <p><strong>ID:</strong> <?= $r['id'] ?></p>
    <p><strong>Mata Kuliah:</strong> <?= $r['matakuliah'] ?></p>
    <p><strong>Dosen:</strong> <?= $r['dosen'] ?></p>
    <p><strong>Kelas:</strong> <?= $r['kelas'] ?></p>
    <p><strong>Hari:</strong> <?= $r['hari'] ?></p>
    <p><strong>Jam:</strong> <?= $r['jam'] ?></p>

    <br>
    <a href="jadwal_edit.php?id=<?= $r['id'] ?>">Edit</a> |
    <a href="jadwal_list.php">Kembali</a>
</body>
</html>

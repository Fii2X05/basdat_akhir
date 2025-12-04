<?php
require '../config.php';

$id = $_GET['id'];

$q = "
SELECT n.*, m.nama AS mahasiswa, mk.nama AS matakuliah
FROM nilai n
JOIN mahasiswa m ON n.mahasiswa_id = m.id
JOIN mata_kuliah mk ON n.mata_kuliah_id = mk.id
WHERE n.id = $id
";

$r = mysqli_fetch_assoc(mysqli_query($conn, $q));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Detail Nilai</title>
</head>
<body>
    <h1>Detail Nilai</h1>

    <p><strong>ID:</strong> <?= $r['id'] ?></p>
    <p><strong>Mahasiswa:</strong> <?= $r['mahasiswa'] ?></p>
    <p><strong>Mata Kuliah:</strong> <?= $r['matakuliah'] ?></p>
    <p><strong>Nilai:</strong> <?= $r['nilai'] ?></p>

    <br>
    <a href="nilai_edit.php?id=<?= $r['id'] ?>">Edit</a> |
    <a href="nilai_list.php">Kembali</a>
</body>
</html>

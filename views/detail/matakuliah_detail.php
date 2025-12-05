<?php
require '../config.php';

$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM mata_kuliah WHERE id = $id");
$data = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Detail Mata Kuliah</title>
</head>
<body>
    <h1>Detail Mata Kuliah</h1>

    <p><strong>ID:</strong> <?= $data['id'] ?></p>
    <p><strong>Kode:</strong> <?= $data['kode'] ?></p>
    <p><strong>Nama Mata Kuliah:</strong> <?= $data['nama'] ?></p>
    <p><strong>SKS:</strong> <?= $data['sks'] ?></p>

    <br>
    <a href="matakuliah_edit.php?id=<?= $data['id'] ?>">Edit</a> |
    <a href="matakuliah_list.php">Kembali ke List</a>
</body>
</html>

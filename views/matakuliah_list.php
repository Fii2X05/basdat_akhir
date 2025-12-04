<?php
require __DIR__ . '/../config/database.php';

$result = mysqli_query($conn, "SELECT * FROM mata_kuliah ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List Mata Kuliah</title>
</head>
<body>
    <h1>List Mata Kuliah</h1>
    <a href="matakuliah_create.php">+ Tambah Mata Kuliah</a>
    <br><br>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Kode</th>
            <th>Nama Mata Kuliah</th>
            <th>SKS</th>
            <th>Aksi</th>
        </tr>

        <?php while($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['kode'] ?></td>
            <td><?= $row['nama'] ?></td>
            <td><?= $row['sks'] ?></td>
            <td>
                <a href="matakuliah_detail.php?id=<?= $row['id'] ?>">Detail</a> |
                <a href="matakuliah_edit.php?id=<?= $row['id'] ?>">Edit</a> |
                <a href="matakuliah_delete.php?id=<?= $row['id'] ?>"
                   onclick="return confirm('Yakin hapus?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

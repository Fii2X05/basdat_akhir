<?php
require dirname(__DIR__, 2) . '/config/database.php';

$result = pg_query($conn, "SELECT * FROM mata_kuliah ORDER BY id DESC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List Mata Kuliah</title>
</head>
<body>
    <h1>List Mata Kuliah</h1>
    <a href="?page=create_matakuliah">+ Tambah Mata Kuliah</a>
    <br><br>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Kode</th>
            <th>Nama Mata Kuliah</th>
            <th>SKS</th>
            <th>Aksi</th>
        </tr>

        <?php while($row = pg_fetch_assoc($result)): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['kode'] ?></td>
            <td><?= $row['nama'] ?></td>
            <td><?= $row['sks'] ?></td>
            <td>
                <a href="?page=detail_matakuliah&id=<?= $row['id'] ?>">Detail</a> |
                <a href="?page=edit_matakuliah&id=<?= $row['id'] ?>">Edit</a> |
                <a href="?page=delete_matakuliah&id=<?= $row['id'] ?>" 
                   onclick="return confirm('Yakin hapus?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

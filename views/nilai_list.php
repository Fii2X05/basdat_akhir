<?php
require '../config.php';

$q = "
SELECT n.id, m.nama AS mahasiswa, mk.nama AS matakuliah, n.nilai
FROM nilai n
JOIN mahasiswa m ON n.mahasiswa_id = m.id
JOIN mata_kuliah mk ON n.mata_kuliah_id = mk.id
ORDER BY n.id DESC
";
$result = mysqli_query($conn, $q);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List Nilai</title>
</head>
<body>
    <h1>List Nilai</h1>
    <a href="nilai_create.php">+ Tambah Nilai</a>
    <br><br>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Mahasiswa</th>
            <th>Mata Kuliah</th>
            <th>Nilai</th>
            <th>Aksi</th>
        </tr>

        <?php while($r = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= $r['id'] ?></td>
            <td><?= $r['mahasiswa'] ?></td>
            <td><?= $r['matakuliah'] ?></td>
            <td><?= $r['nilai'] ?></td>
            <td>
                <a href="nilai_detail.php?id=<?= $r['id'] ?>">Detail</a> |
                <a href="nilai_edit.php?id=<?= $r['id'] ?>">Edit</a> |
                <a href="nilai_delete.php?id=<?= $r['id'] ?>" onclick="return confirm('Hapus nilai ini?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

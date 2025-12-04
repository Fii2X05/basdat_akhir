<?php
require '../config.php';

$id = $_GET['id'];

$q = "SELECT * FROM nilai WHERE id=$id";
$d = mysqli_fetch_assoc(mysqli_query($conn, $q));

// ambil list
$mhs = mysqli_query($conn, "SELECT id, nama FROM mahasiswa");
$mk = mysqli_query($conn, "SELECT id, nama FROM mata_kuliah");

if (isset($_POST['submit'])) {
    $mahasiswa_id = $_POST['mahasiswa_id'];
    $matakuliah_id = $_POST['matakuliah_id'];
    $nilai = $_POST['nilai'];

    $update = "
        UPDATE nilai SET
        mahasiswa_id='$mahasiswa_id',
        mata_kuliah_id='$matakuliah_id',
        nilai='$nilai'
        WHERE id=$id
    ";
    mysqli_query($conn, $update);

    header("Location: nilai_list.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Nilai</title>
</head>
<body>
    <h1>Edit Nilai</h1>

    <form method="POST">
        <label>Mahasiswa:</label><br>
        <select name="mahasiswa_id">
            <?php while($row = mysqli_fetch_assoc($mhs)): ?>
                <option value="<?= $row['id'] ?>"
                    <?= $row['id'] == $d['mahasiswa_id'] ? 'selected' : '' ?>>
                    <?= $row['nama'] ?>
                </option>
            <?php endwhile; ?>
        </select>
        <br><br>

        <label>Mata Kuliah:</label><br>
        <select name="matakuliah_id">
            <?php while($row = mysqli_fetch_assoc($mk)): ?>
                <option value="<?= $row['id'] ?>"
                    <?= $row['id'] == $d['mata_kuliah_id'] ? 'selected' : '' ?>>
                    <?= $row['nama'] ?>
                </option>
            <?php endwhile; ?>
        </select>
        <br><br>

        <label>Nilai:</label><br>
        <input type="text" name="nilai" value="<?= $d['nilai'] ?>">
        <br><br>

        <button type="submit" name="submit">Update</button>
    </form>

    <br>
    <a href="nilai_list.php">← Kembali</a>
</body>
</html>

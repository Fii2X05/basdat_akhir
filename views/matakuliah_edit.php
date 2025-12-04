<?php
require '../config.php';

$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM mata_kuliah WHERE id = $id");
$data = mysqli_fetch_assoc($result);

if (isset($_POST['submit'])) {
    $kode = $_POST['kode'];
    $nama = $_POST['nama'];
    $sks = $_POST['sks'];

    $q = "UPDATE mata_kuliah SET kode='$kode', nama='$nama', sks='$sks' WHERE id=$id";
    mysqli_query($conn, $q);

    header("Location: matakuliah_list.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Mata Kuliah</title>
</head>
<body>
    <h1>Edit Mata Kuliah</h1>

    <form method="POST">
        <label>Kode:</label><br>
        <input type="text" name="kode" value="<?= $data['kode'] ?>" required><br><br>

        <label>Nama Mata Kuliah:</label><br>
        <input type="text" name="nama" value="<?= $data['nama'] ?>" required><br><br>

        <label>SKS:</label><br>
        <input type="number" name="sks" value="<?= $data['sks'] ?>" required><br><br>

        <button type="submit" name="submit">Update</button>
    </form>

    <br>
    <a href="matakuliah_list.php">← Kembali</a>
</body>
</html>

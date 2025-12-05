<?php
require '../config.php';

if (isset($_POST['submit'])) {
    $kode = $_POST['kode'];
    $nama = $_POST['nama'];
    $sks = $_POST['sks'];

    $q = "INSERT INTO mata_kuliah(kode, nama, sks) VALUES('$kode', '$nama', '$sks')";
    mysqli_query($conn, $q);

    header("Location: matakuliah_list.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Mata Kuliah</title>
</head>
<body>
    <h1>Tambah Mata Kuliah</h1>

    <form method="POST">
        <label>Kode:</label><br>
        <input type="text" name="kode" required><br><br>

        <label>Nama Mata Kuliah:</label><br>
        <input type="text" name="nama" required><br><br>

        <label>SKS:</label><br>
        <input type="number" name="sks" required><br><br>

        <button type="submit" name="submit">Simpan</button>
    </form>

    <br>
    <a href="matakuliah_list.php">← Kembali</a>
</body>
</html>

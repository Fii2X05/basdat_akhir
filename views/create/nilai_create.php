<?php
require '../config.php';

// Ambil data mahasiswa & mata kuliah
$mhs = mysqli_query($conn, "SELECT id, nama FROM mahasiswa");
$mk = mysqli_query($conn, "SELECT id, nama FROM mata_kuliah");

if (isset($_POST['submit'])) {
    $mahasiswa_id = $_POST['mahasiswa_id'];
    $matakuliah_id = $_POST['matakuliah_id'];
    $nilai = $_POST['nilai'];

    $q = "INSERT INTO nilai(mahasiswa_id, mata_kuliah_id, nilai)
          VALUES('$mahasiswa_id', '$matakuliah_id', '$nilai')";
    mysqli_query($conn, $q);

    header("Location: nilai_list.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Nilai</title>
</head>
<body>
    <h1>Tambah Nilai</h1>

    <form method="POST">

        <label>Mahasiswa:</label><br>
        <select name="mahasiswa_id" required>
            <option value="">-- pilih --</option>
            <?php while($row = mysqli_fetch_assoc($mhs)): ?>
                <option value="<?= $row['id'] ?>"><?= $row['nama'] ?></option>
            <?php endwhile; ?>
        </select>
        <br><br>

        <label>Mata Kuliah:</label><br>
        <select name="matakuliah_id" required>
            <option value="">-- pilih --</option>
            <?php while($row = mysqli_fetch_assoc($mk)): ?>
                <option value="<?= $row['id'] ?>"><?= $row['nama'] ?></option>
            <?php endwhile; ?>
        </select>
        <br><br>

        <label>Nilai:</label><br>
        <input type="text" name="nilai" required>
        <br><br>

        <button type="submit" name="submit">Simpan</button>
    </form>

    <br>
    <a href="nilai_list.php">← Kembali</a>
</body>
</html>

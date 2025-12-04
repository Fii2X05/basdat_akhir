<?php
require '../config.php';

$mk = mysqli_query($conn, "SELECT id, nama FROM mata_kuliah");
$dosen = mysqli_query($conn, "SELECT id, nama FROM dosen");
$kelas = mysqli_query($conn, "SELECT id, nama FROM kelas");

if (isset($_POST['submit'])) {
    $mk_id = $_POST['matakuliah_id'];
    $dosen_id = $_POST['dosen_id'];
    $kelas_id = $_POST['kelas_id'];
    $hari = $_POST['hari'];
    $jam = $_POST['jam'];

    $q = "
    INSERT INTO jadwal(matakuliah_id, dosen_id, kelas_id, hari, jam)
    VALUES('$mk_id', '$dosen_id', '$kelas_id', '$hari', '$jam')
    ";
    mysqli_query($conn, $q);

    header("Location: jadwal_list.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Jadwal</title>
</head>
<body>
    <h1>Tambah Jadwal</h1>

    <form method="POST">
        <label>Mata Kuliah:</label><br>
        <select name="matakuliah_id" required>
            <option value="">-- pilih --</option>
            <?php while ($row = mysqli_fetch_assoc($mk)): ?>
                <option value="<?= $row['id'] ?>"><?= $row['nama'] ?></option>
            <?php endwhile; ?>
        </select>
        <br><br>

        <label>Dosen:</label><br>
        <select name="dosen_id" required>
            <option value="">-- pilih --</option>
            <?php while ($row = mysqli_fetch_assoc($dosen)): ?>
                <option value="<?= $row['id'] ?>"><?= $row['nama'] ?></option>
            <?php endwhile; ?>
        </select>
        <br><br>

        <label>Kelas:</label><br>
        <select name="kelas_id" required>
            <option value="">-- pilih --</option>
            <?php while ($row = mysqli_fetch_assoc($kelas)): ?>
                <option value="<?= $row['id'] ?>"><?= $row['nama'] ?></option>
            <?php endwhile; ?>
        </select>
        <br><br>

        <label>Hari:</label><br>
        <select name="hari" required>
            <option value="">-- pilih hari --</option>
            <option>Senin</option>
            <option>Selasa</option>
            <option>Rabu</option>
            <option>Kamis</option>
            <option>Jumat</option>
            <option>Sabtu</option>
        </select>
        <br><br>

        <label>Jam:</label><br>
        <input type="time" name="jam" required>
        <br><br>

        <button type="submit" name="submit">Simpan</button>
    </form>

    <br>
    <a href="jadwal_list.php">← Kembali</a>
</body>
</html>

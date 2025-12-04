<?php
require '../config.php';

$id = $_GET['id'];

$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM jadwal WHERE id=$id"));

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
    UPDATE jadwal SET
        matakuliah_id='$mk_id',
        dosen_id='$dosen_id',
        kelas_id='$kelas_id',
        hari='$hari',
        jam='$jam'
    WHERE id=$id
    ";

    mysqli_query($conn, $q);
    header("Location: jadwal_list.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Jadwal</title>
</head>
<body>

    <h1>Edit Jadwal</h1>

    <form method="POST">

        <label>Mata Kuliah:</label><br>
        <select name="matakuliah_id">
            <?php while ($row = mysqli_fetch_assoc($mk)): ?>
                <option value="<?= $row['id'] ?>"
                    <?= $row['id'] == $data['matakuliah_id'] ? 'selected' : '' ?>>
                    <?= $row['nama'] ?>
                </option>
            <?php endwhile; ?>
        </select>
        <br><br>

        <label>Dosen:</label><br>
        <select name="dosen_id">
            <?php while ($row = mysqli_fetch_assoc($dosen)): ?>
                <option value="<?= $row['id'] ?>"
                    <?= $row['id'] == $data['dosen_id'] ? 'selected' : '' ?>>
                    <?= $row['nama'] ?>
                </option>
            <?php endwhile; ?>
        </select>
        <br><br>

        <label>Kelas:</label><br>
        <select name="kelas_id">
            <?php while ($row = mysqli_fetch_assoc($kelas)): ?>
                <option value="<?= $row['id'] ?>"
                    <?= $row['id'] == $data['kelas_id'] ? 'selected' : '' ?>>
                    <?= $row['nama'] ?>
                </option>
            <?php endwhile; ?>
        </select>
        <br><br>

        <label>Hari:</label><br>
        <select name="hari">
            <?php
            $hariList = ["Senin","Selasa","Rabu","Kamis","Jumat","Sabtu"];
            foreach ($hariList as $h):
            ?>
                <option <?= $data['hari'] == $h ? 'selected' : '' ?>><?= $h ?></option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label>Jam:</label><br>
        <input type="time" name="jam" value="<?= $data['jam'] ?>">
        <br><br>

        <button type="submit" name="submit">Update</button>
    </form>

    <br>
    <a href="jadwal_list.php">← Kembali</a>

</body>
</html>

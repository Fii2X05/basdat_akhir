<?php
require __DIR__ . '/../../config.php'; // SESUAIKAN LOKASI CONFIG

// Ambil data dropdown
$mk    = pg_query($conn, "SELECT id, nama FROM mata_kuliah ORDER BY nama");
$dosen = pg_query($conn, "SELECT id, nama FROM dosen ORDER BY nama");
$kelas = pg_query($conn, "SELECT id, nama FROM kelas ORDER BY nama");

if (isset($_POST['submit'])) {

    $mk_id     = $_POST['matakuliah_id'];
    $dosen_id  = $_POST['dosen_id'];
    $kelas_id  = $_POST['kelas_id'];
    $hari      = $_POST['hari'];
    $jam       = $_POST['jam'];

    // Insert ke PostgreSQL
    $q = "
        INSERT INTO jadwal (matakuliah_id, dosen_id, kelas_id, hari, jam)
        VALUES ($1, $2, $3, $4, $5)
    ";

    $result = pg_query_params($conn, $q, [$mk_id, $dosen_id, $kelas_id, $hari, $jam]);

    if ($result) {
        // KEMBALI KE ROUTER
        header("Location: ../../index.php?page=jadwal");
        exit;
    } else {
        echo "<p style='color:red'>Gagal menyimpan data!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah Jadwal</title>
</head>
<body>
    <h1>Tambah Jadwal</h1>

    <form method="POST">
        <label>Mata Kuliah:</label><br>
        <select name="matakuliah_id" required>
            <option value="">-- pilih --</option>
            <?php while ($row = pg_fetch_assoc($mk)): ?>
                <option value="<?= $row['id'] ?>"><?= $row['nama'] ?></option>
            <?php endwhile; ?>
        </select>
        <br><br>

        <label>Dosen:</label><br>
        <select name="dosen_id" required>
            <option value="">-- pilih --</option>
            <?php while ($row = pg_fetch_assoc($dosen)): ?>
                <option value="<?= $row['id'] ?>"><?= $row['nama'] ?></option>
            <?php endwhile; ?>
        </select>
        <br><br>

        <label>Kelas:</label><br>
        <select name="kelas_id" required>
            <option value="">-- pilih --</option>
            <?php while ($row = pg_fetch_assoc($kelas)): ?>
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
    <a href="../../index.php?page=jadwal">← Kembali</a>
</body>
</html>

<?php
// PERBAIKAN: Mengganti '/../config.php' menjadi '/../config/database.php' 
// agar sesuai dengan struktur folder Anda.
require __DIR__ . '/../config/database.php';


// PERBAIKAN: Menambahkan pengecekan apakah koneksi $conn sudah tersedia
if (!isset($conn) || $conn === false) {
    die("Koneksi database gagal dimuat. Pastikan database.php menginisialisasi \$conn.");
}

$q = "
SELECT j.id, mk.nama AS matakuliah, d.nama AS dosen, k.nama AS kelas, j.hari, j.jam
FROM jadwal j
JOIN mata_kuliah mk ON j.matakuliah_id = mk.id
JOIN dosen d ON j.dosen_id = d.id
JOIN kelas k ON j.kelas_id = k.id
ORDER BY j.id DESC
";
$result = mysqli_query($conn, $q);

// PERBAIKAN: Menambahkan pengecekan jika query gagal (misal tabel tidak ditemukan)
if (!$result) {
    // Tampilkan error database jika terjadi, sangat berguna saat debugging
    die("Query Gagal: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List Jadwal</title>
</head>
<body>
    <h1>Data Jadwal</h1>
    <a href="jadwal_create.php">+ Tambah Jadwal</a>
    <br><br>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Mata Kuliah</th>
            <th>Dosen</th>
            <th>Kelas</th>
            <th>Hari</th>
            <th>Jam</th>
            <th>Aksi</th>
        </tr>

        <?php while($r = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= htmlspecialchars($r['id']) ?></td>
            <td><?= htmlspecialchars($r['matakuliah']) ?></td>
            <td><?= htmlspecialchars($r['dosen']) ?></td>
            <td><?= htmlspecialchars($r['kelas']) ?></td>
            <td><?= htmlspecialchars($r['hari']) ?></td>
            <td><?= htmlspecialchars($r['jam']) ?></td>
            <td>
                <a href="jadwal_detail.php?id=<?= $r['id'] ?>">Detail</a> |
                <a href="jadwal_edit.php?id=<?= $r['id'] ?>">Edit</a> |
                <a href="jadwal_delete.php?id=<?= $r['id'] ?>" onclick="return confirm('Hapus jadwal ini?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
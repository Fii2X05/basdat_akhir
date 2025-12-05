<?php
// Load database dengan path yang benar
require __DIR__ . '/../../config/database.php';

// Cek koneksi
if (!isset($conn) || $conn === false) {
    die("Koneksi database gagal dimuat. Pastikan database.php menginisialisasi \$conn.");
}

// Query jadwal
$q = "
SELECT j.id, mk.nama AS matakuliah, d.nama AS dosen, k.nama AS kelas, j.hari, j.jam
FROM jadwal j
JOIN mata_kuliah mk ON j.matakuliah_id = mk.id
JOIN dosen d ON j.dosen_id = d.id
JOIN kelas k ON j.kelas_id = k.id
ORDER BY j.id DESC
";

$result = mysqli_query($conn, $q);

if (!$result) {
    die("Query Gagal: " . mysqli_error($conn));
}
?>

<div class="card-custom mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Data Jadwal</h5>

        <!-- Button create menggunakan router -->
        <a href="?page=jadwal_create" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> Tambah Jadwal
        </a>
    </div>
</div>

<div class="card-custom">
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Mata Kuliah</th>
                <th>Dosen</th>
                <th>Kelas</th>
                <th>Hari</th>
                <th>Jam</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
        <?php while ($r = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $r['id'] ?></td>
                <td><?= $r['matakuliah'] ?></td>
                <td><?= $r['dosen'] ?></td>
                <td><?= $r['kelas'] ?></td>
                <td><?= $r['hari'] ?></td>
                <td><?= $r['jam'] ?></td>

                <td>
                    <a href="?page=jadwal_detail&id=<?= $r['id'] ?>" class="btn btn-info btn-sm">
                        Detail
                    </a>

                    <a href="?page=jadwal_edit&id=<?= $r['id'] ?>" class="btn btn-warning btn-sm">
                        Edit
                    </a>

                    <a href="?page=jadwal_delete&id=<?= $r['id'] ?>"
                       onclick="return confirm('Yakin ingin menghapus?');"
                       class="btn btn-danger btn-sm">
                        Hapus
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

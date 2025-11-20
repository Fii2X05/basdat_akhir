<?php
require_once 'config/database.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; 
$offset = ($page - 1) * $limit;
$search = isset($_GET['q']) ? $_GET['q'] : '';

$sql = "SELECT m.*, j.nama_jurusan, k.nama_kelas 
        FROM mahasiswa m
        LEFT JOIN jurusan j ON m.id_jurusan = j.id_jurusan
        LEFT JOIN kelas k ON m.id_kelas = k.id_kelas
        WHERE (m.nama_mahasiswa ILIKE :search OR m.nim ILIKE :search)";

$stmtCount = $pdo->prepare("SELECT COUNT(*) FROM ($sql) as count_table");
$stmtCount->execute(['search' => "%$search%"]);
$total_records = $stmtCount->fetchColumn();
$total_pages = ceil($total_records / $limit);

$sql .= " ORDER BY m.nim ASC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':search', "%$search%");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$mahasiswa = $stmt->fetchAll();

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $delStmt = $pdo->prepare("DELETE FROM mahasiswa WHERE id_mahasiswa = ?");
    $delStmt->execute([$_GET['id']]);
    header("Location: mahasiswa.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Mahasiswa - Sistem Kampus</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2>Data Mahasiswa</h2>
        <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
            <a href="tambah_mahasiswa.php" class="btn btn-success">+ Tambah Mahasiswa</a>
            <input type="text" placeholder="Cari Nama/NIM..." 
                   value="<?= e($search) ?>" 
                   onkeyup="liveSearch(this.value)" 
                   style="width: 300px;">
        </div>

        <table>
            <thead>
                <tr>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Gender</th>
                    <th>Jurusan</th>
                    <th>Kelas</th>
                    <th>Angkatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mahasiswa as $m): ?>
                <tr>
                    <td><?= e($m['nim']) ?></td>
                    <td><?= e($m['nama_mahasiswa']) ?></td>
                    <td><?= e($m['jenis_kelamin']) ?></td>
                    <td><?= e($m['nama_jurusan']) ?></td>
                    <td><?= e($m['nama_kelas']) ?></td>
                    <td><?= e($m['angkatan']) ?></td>
                    <td>
                        <a href="edit_mahasiswa.php?id=<?= $m['id_mahasiswa'] ?>" class="btn">Edit</a>
                        <button onclick="confirmDelete(<?= $m['id_mahasiswa'] ?>)" class="btn btn-danger">Hapus</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i ?>&q=<?= e($search) ?>" 
                   class="<?= $page == $i ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    </div>
    <script src="js/script.js"></script>
</body>
</html>
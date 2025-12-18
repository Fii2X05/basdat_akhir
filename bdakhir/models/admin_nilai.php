<?php
require_once __DIR__ . '/../config/database.php';

// 2. LOGIC HANDLER
$act = $_GET['act'] ?? 'list';
$id_nilai = $_GET['id'] ?? null;

// Helper: Fungsi Menghitung Nilai Huruf
function hitungNilaiHuruf($angka) {
    if ($angka >= 85) return 'A';
    if ($angka >= 75) return 'B';
    if ($angka >= 65) return 'C';
    if ($angka >= 55) return 'D';
    return 'E';
}

// Ambil data Mahasiswa & Matkul untuk Dropdown (Dipakai di Create & Edit)
if ($act === 'create' || $act === 'edit') {
    // Ambil Mahasiswa
    $stmtMhs = $pdo->query("SELECT id_mahasiswa, nim, nama_mahasiswa FROM mahasiswa ORDER BY nama_mahasiswa ASC");
    $optMhs = $stmtMhs->fetchAll(PDO::FETCH_ASSOC);

    // Ambil Mata Kuliah
    $stmtMk = $pdo->query("SELECT id_matkul, kode_matkul, nama_matkul FROM mata_kuliah ORDER BY nama_matkul ASC");
    $optMk = $stmtMk->fetchAll(PDO::FETCH_ASSOC);
}

// Handle Create (Store)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $act === 'store') {
    $id_mhs = $_POST['id_mahasiswa'];
    $id_mk = $_POST['id_matkul'];
    $angka = $_POST['nilai_angka'];
    $huruf = hitungNilaiHuruf($angka); // Hitung otomatis

    try {
        $sql = "INSERT INTO nilai (id_mahasiswa, id_matkul, nilai_angka, nilai_huruf) 
                VALUES (:mhs, :mk, :angka, :huruf)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'mhs' => $id_mhs, 'mk' => $id_mk, 
            'angka' => $angka, 'huruf' => $huruf
        ]);
        header("Location: ?page=nilai");
        exit;
    } catch (PDOException $e) {
        // Menangani error jika mahasiswa sudah punya nilai di matkul tsb (Unique Constraint)
        echo "<script>alert('Gagal! Mahasiswa ini sudah memiliki nilai untuk mata kuliah tersebut.'); window.history.back();</script>";
        exit;
    }
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $act === 'update') {
    $id = $_POST['id_nilai'];
    $id_mhs = $_POST['id_mahasiswa'];
    $id_mk = $_POST['id_matkul'];
    $angka = $_POST['nilai_angka'];
    $huruf = hitungNilaiHuruf($angka); // Hitung ulang huruf

    try {
        $sql = "UPDATE nilai SET 
                id_mahasiswa = :mhs, id_matkul = :mk, 
                nilai_angka = :angka, nilai_huruf = :huruf
                WHERE id_nilai = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'mhs' => $id_mhs, 'mk' => $id_mk, 
            'angka' => $angka, 'huruf' => $huruf, 
            'id' => $id
        ]);
        header("Location: ?page=nilai");
        exit;
    } catch (PDOException $e) {
        echo "<script>alert('Gagal update! Kombinasi Mahasiswa dan Mata Kuliah sudah ada.'); window.history.back();</script>";
        exit;
    }
}

// Handle Delete
if ($act === 'delete' && $id_nilai) {
    $sql = "DELETE FROM nilai WHERE id_nilai = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id_nilai]);

    header("Location: ?page=nilai");
    exit;
}
?>

<?php if ($act === 'list'): ?>
    <?php
    $sql = "SELECT n.*, m.nama_mahasiswa, m.nim, mk.nama_matkul, mk.kode_matkul 
            FROM nilai n 
            JOIN mahasiswa m ON n.id_mahasiswa = m.id_mahasiswa 
            JOIN mata_kuliah mk ON n.id_matkul = mk.id_matkul 
            ORDER BY n.id_nilai DESC";
    $stmt = $pdo->query($sql);
    $dataNilai = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="card-custom mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Data Nilai Mahasiswa</h5>
            <a href="?page=nilai&act=create" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle"></i> Tambah Nilai
            </a>
        </div>
    </div>

    <div class="card-custom">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Mahasiswa</th>
                    <th>Mata Kuliah</th>
                    <th width="100">Angka</th>
                    <th width="80">Huruf</th>
                    <th width="200">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($dataNilai)): ?>
                    <?php foreach ($dataNilai as $index => $row): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td>
                                <strong><?= htmlspecialchars($row['nim']) ?></strong><br>
                                <?= htmlspecialchars($row['nama_mahasiswa']) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($row['nama_matkul']) ?><br>
                                <small class="text-muted"><?= htmlspecialchars($row['kode_matkul']) ?></small>
                            </td>
                            <td class="text-center"><?= $row['nilai_angka'] ?></td>
                            <td class="text-center fw-bold"><?= $row['nilai_huruf'] ?></td>
                            <td>
                                <a href="?page=nilai&act=detail&id=<?= $row['id_nilai'] ?>" class="btn btn-info btn-sm text-white">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                                <a href="?page=nilai&act=edit&id=<?= $row['id_nilai'] ?>" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <a href="?page=nilai&act=delete&id=<?= $row['id_nilai'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus nilai ini?');">
                                    <i class="bi bi-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">Tidak ada data nilai.</td>
                    </tr>
                <?php endif ?>
            </tbody>
        </table>
    </div>

<?php elseif ($act === 'create'): ?>
    <div class="card-custom mb-4">
        <h5 class="mb-0">Tambah Nilai</h5>
    </div>
    <div class="card-custom">
        <form action="?page=nilai&act=store" method="POST">
            
            <div class="mb-3">
                <label class="form-label">Mahasiswa</label>
                <select name="id_mahasiswa" class="form-select" required>
                    <option value="">-- Pilih Mahasiswa --</option>
                    <?php foreach ($optMhs as $m): ?>
                        <option value="<?= $m['id_mahasiswa'] ?>">
                            <?= $m['nim'] ?> - <?= $m['nama_mahasiswa'] ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Mata Kuliah</label>
                <select name="id_matkul" class="form-select" required>
                    <option value="">-- Pilih Mata Kuliah --</option>
                    <?php foreach ($optMk as $mk): ?>
                        <option value="<?= $mk['id_matkul'] ?>">
                            <?= $mk['kode_matkul'] ?> - <?= $mk['nama_matkul'] ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Nilai Angka (0-100)</label>
                <input type="number" name="nilai_angka" class="form-control" min="0" max="100" step="0.01" required>
                <small class="text-muted">Nilai Huruf (A-E) akan dihitung otomatis.</small>
            </div>

            <button class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
            <a href="?page=nilai" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

<?php elseif ($act === 'edit' && $id_nilai): ?>
    <?php
    $stmt = $pdo->prepare("SELECT * FROM nilai WHERE id_nilai = :id");
    $stmt->execute(['id' => $id_nilai]);
    $nilai = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$nilai) die("Data tidak ditemukan");
    ?>
    <div class="card-custom mb-4">
        <h5 class="mb-0">Edit Nilai</h5>
    </div>
    <div class="card-custom">
        <form action="?page=nilai&act=update" method="POST">
            <input type="hidden" name="id_nilai" value="<?= $nilai['id_nilai'] ?>">

            <div class="mb-3">
                <label class="form-label">Mahasiswa</label>
                <select name="id_mahasiswa" class="form-select" required>
                    <option value="">-- Pilih Mahasiswa --</option>
                    <?php foreach ($optMhs as $m): ?>
                        <option value="<?= $m['id_mahasiswa'] ?>" <?= ($m['id_mahasiswa'] == $nilai['id_mahasiswa']) ? 'selected' : '' ?>>
                            <?= $m['nim'] ?> - <?= $m['nama_mahasiswa'] ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Mata Kuliah</label>
                <select name="id_matkul" class="form-select" required>
                    <option value="">-- Pilih Mata Kuliah --</option>
                    <?php foreach ($optMk as $mk): ?>
                        <option value="<?= $mk['id_matkul'] ?>" <?= ($mk['id_matkul'] == $nilai['id_matkul']) ? 'selected' : '' ?>>
                            <?= $mk['kode_matkul'] ?> - <?= $mk['nama_matkul'] ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Nilai Angka (0-100)</label>
                <input type="number" name="nilai_angka" value="<?= $nilai['nilai_angka'] ?>" class="form-control" min="0" max="100" step="0.01" required>
            </div>

            <button class="btn btn-primary"><i class="bi bi-pencil-square"></i> Update</button>
            <a href="?page=nilai" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

<?php elseif ($act === 'detail' && $id_nilai): ?>
    <?php
    $sql = "SELECT n.*, m.nama_mahasiswa, m.nim, mk.nama_matkul, mk.kode_matkul, mk.sks 
            FROM nilai n 
            JOIN mahasiswa m ON n.id_mahasiswa = m.id_mahasiswa 
            JOIN mata_kuliah mk ON n.id_matkul = mk.id_matkul 
            WHERE n.id_nilai = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id_nilai]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) die("Data tidak ditemukan");
    ?>
    <div class="card-custom mb-4">
        <h5 class="mb-0">Detail Nilai</h5>
    </div>
    <div class="card-custom">
        <table class="table table-bordered">
            <tr><th width="200">ID Nilai</th><td><?= $row['id_nilai'] ?></td></tr>
            <tr><th>Mahasiswa</th><td><?= htmlspecialchars($row['nama_mahasiswa']) ?> (<?= $row['nim'] ?>)</td></tr>
            <tr><th>Mata Kuliah</th><td><?= htmlspecialchars($row['nama_matkul']) ?> (<?= $row['kode_matkul'] ?>)</td></tr>
            <tr><th>SKS</th><td><?= $row['sks'] ?></td></tr>
            <tr><th>Nilai Angka</th><td><?= $row['nilai_angka'] ?></td></tr>
            <tr><th>Nilai Huruf</th><td><span class="badge bg-primary fs-6"><?= $row['nilai_huruf'] ?></span></td></tr>
            <tr><th>Tanggal Input</th><td><?= $row['created_at'] ?></td></tr>
        </table>

        <a href="?page=nilai&act=edit&id=<?= $row['id_nilai'] ?>" class="btn btn-warning">
            <i class="bi bi-pencil-square"></i> Edit
        </a>
        <a href="?page=nilai" class="btn btn-secondary">Kembali</a>
    </div>
<?php endif; ?>
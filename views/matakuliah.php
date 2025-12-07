<?php
// views/matakuliah.php
global $pdo;
if (empty($pdo)) { require_once __DIR__ . '/../config/database.php'; }

$act = $_GET['act'] ?? 'list';
$id_matkul = $_GET['id'] ?? null;

// Fetch Data Jurusan untuk Dropdown
if ($act === 'create' || $act === 'edit') {
    $stmtJur = $pdo->query("SELECT * FROM jurusan ORDER BY nama_jurusan ASC");
    $optJurusan = $stmtJur->fetchAll(PDO::FETCH_ASSOC);

    // Fetch Semua Matkul untuk Dropdown Prasyarat
    // Jika Edit, exclude ID diri sendiri agar tidak looping
    $sqlPras = "SELECT id_matkul, kode_matkul, nama_matkul FROM mata_kuliah";
    if ($act === 'edit' && $id_matkul) {
        $sqlPras .= " WHERE id_matkul != " . intval($id_matkul);
    }
    $sqlPras .= " ORDER BY nama_matkul ASC";
    $optPrasyarat = $pdo->query($sqlPras)->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?php if ($act === 'list'): ?>
    <?php
    // Join Self Reference untuk menampilkan nama prasyarat
    $sql = "SELECT m.*, j.nama_jurusan, p.nama_matkul as nama_prasyarat 
            FROM mata_kuliah m 
            LEFT JOIN jurusan j ON m.id_jurusan = j.id_jurusan 
            LEFT JOIN mata_kuliah p ON m.prasyarat_id = p.id_matkul
            ORDER BY m.semester ASC, m.kode_matkul ASC";
    $stmt = $pdo->query($sql);
    $dataMatkul = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="card-custom mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-journal-text me-2"></i>Katalog Mata Kuliah</h5>
            <a href="?page=matakuliah&act=create" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah Matkul</a>
        </div>
    </div>
    <div class="card-custom">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Kode</th>
                        <th>Nama Mata Kuliah</th>
                        <th class="text-center">SKS</th>
                        <th class="text-center">Smt</th>
                        <th>Jurusan</th>
                        <th>Prasyarat</th>
                        <th width="150" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($dataMatkul)): ?>
                        <?php foreach ($dataMatkul as $mk): ?>
                            <tr>
                                <td><?= htmlspecialchars($mk['kode_matkul']) ?></td>
                                <td><?= htmlspecialchars($mk['nama_matkul']) ?></td>
                                <td class="text-center"><?= $mk['sks'] ?></td>
                                <td class="text-center"><?= $mk['semester'] ?></td>
                                <td><?= htmlspecialchars($mk['nama_jurusan'] ?? '-') ?></td>
                                <td>
                                    <?php if($mk['nama_prasyarat']): ?>
                                        <span class="badge bg-warning text-dark"><?= htmlspecialchars($mk['nama_prasyarat']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="?page=matakuliah&act=edit&id=<?= $mk['id_matkul'] ?>" class="btn btn-warning btn-sm text-white"><i class="bi bi-pencil-square"></i></a>
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalHapus" data-href="index.php?page=matakuliah&act=delete&id=<?= $mk['id_matkul'] ?>"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center text-muted">Tidak ada data.</td></tr>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>

<?php elseif ($act === 'create'): ?>
    <div class="card-custom mb-4"><h5 class="mb-0 fw-bold">Tambah Mata Kuliah</h5></div>
    <div class="card-custom">
        <form action="index.php?page=matakuliah&act=store" method="POST">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Kode Mata Kuliah</label>
                    <input type="text" name="kode_matkul" class="form-control" placeholder="Contoh: TIK101" required>
                </div>
                <div class="col-md-8 mb-3">
                    <label class="form-label">Nama Mata Kuliah</label>
                    <input type="text" name="nama_matkul" class="form-control" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">SKS</label>
                    <input type="number" name="sks" class="form-control" min="1" max="6" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Semester</label>
                    <select name="semester" class="form-select" required>
                        <?php for($i=1; $i<=8; $i++): ?>
                            <option value="<?= $i ?>">Semester <?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Jurusan</label>
                    <select name="id_jurusan" class="form-select" required>
                        <option value="">-- Pilih Jurusan --</option>
                        <?php foreach ($optJurusan as $jur): ?>
                            <option value="<?= $jur['id_jurusan'] ?>"><?= $jur['nama_jurusan'] ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Mata Kuliah Prasyarat (Opsional)</label>
                <select name="prasyarat_id" class="form-select">
                    <option value="">-- Tidak Ada Prasyarat --</option>
                    <?php foreach ($optPrasyarat as $pras): ?>
                        <option value="<?= $pras['id_matkul'] ?>"><?= $pras['kode_matkul'] ?> - <?= $pras['nama_matkul'] ?></option>
                    <?php endforeach ?>
                </select>
                <div class="form-text">Mahasiswa harus lulus mata kuliah ini sebelum mengambil mata kuliah baru ini.</div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan</button>
            <a href="?page=matakuliah" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

<?php elseif ($act === 'edit' && $id_matkul): ?>
    <?php
    $stmt = $pdo->prepare("SELECT * FROM mata_kuliah WHERE id_matkul = :id");
    $stmt->execute(['id' => $id_matkul]);
    $mk = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$mk) die("Data tidak ditemukan");
    ?>
    <div class="card-custom mb-4"><h5 class="mb-0 fw-bold">Edit Mata Kuliah</h5></div>
    <div class="card-custom">
        <form action="index.php?page=matakuliah&act=update" method="POST">
            <input type="hidden" name="id_matkul" value="<?= $mk['id_matkul'] ?>">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Kode Mata Kuliah</label>
                    <input type="text" name="kode_matkul" value="<?= htmlspecialchars($mk['kode_matkul']) ?>" class="form-control" required>
                </div>
                <div class="col-md-8 mb-3">
                    <label class="form-label">Nama Mata Kuliah</label>
                    <input type="text" name="nama_matkul" value="<?= htmlspecialchars($mk['nama_matkul']) ?>" class="form-control" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">SKS</label>
                    <input type="number" name="sks" value="<?= $mk['sks'] ?>" class="form-control" min="1" max="6" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Semester</label>
                    <select name="semester" class="form-select" required>
                        <?php for($i=1; $i<=8; $i++): ?>
                            <option value="<?= $i ?>" <?= ($mk['semester'] == $i) ? 'selected' : '' ?>>Semester <?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Jurusan</label>
                    <select name="id_jurusan" class="form-select" required>
                        <option value="">-- Pilih Jurusan --</option>
                        <?php foreach ($optJurusan as $jur): ?>
                            <option value="<?= $jur['id_jurusan'] ?>" <?= ($jur['id_jurusan'] == $mk['id_jurusan']) ? 'selected' : '' ?>>
                                <?= $jur['nama_jurusan'] ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Mata Kuliah Prasyarat</label>
                <select name="prasyarat_id" class="form-select">
                    <option value="">-- Tidak Ada Prasyarat --</option>
                    <?php foreach ($optPrasyarat as $pras): ?>
                        <option value="<?= $pras['id_matkul'] ?>" <?= ($pras['id_matkul'] == $mk['prasyarat_id']) ? 'selected' : '' ?>>
                            <?= $pras['kode_matkul'] ?> - <?= $pras['nama_matkul'] ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check2-circle me-1"></i> Update</button>
            <a href="?page=matakuliah" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
<?php endif; ?>

<div class="modal fade" id="modalHapus" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title text-danger">Konfirmasi Hapus</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body"><p>Yakin ingin menghapus mata kuliah ini?</p></div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><a href="#" class="btn btn-danger" id="btn-confirm-hapus">Ya, Hapus</a></div>
        </div>
    </div>
</div>
<script>
    var modalHapus = document.getElementById('modalHapus');
    modalHapus.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        modalHapus.querySelector('#btn-confirm-hapus').setAttribute('href', button.getAttribute('data-href'));
    });
</script>
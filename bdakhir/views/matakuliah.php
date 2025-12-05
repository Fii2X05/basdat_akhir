<?php
// views/matakuliah.php
global $pdo;
if (empty($pdo)) { require_once __DIR__ . '/../config/database.php'; }

$act = $_GET['act'] ?? 'list';
$id_matkul = $_GET['id'] ?? null;

if ($act === 'create' || $act === 'edit') {
    $stmtJur = $pdo->query("SELECT * FROM jurusan ORDER BY nama_jurusan ASC");
    $optJurusan = $stmtJur->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?php if ($act === 'list'): ?>
    <?php
    $sql = "SELECT m.*, j.nama_jurusan FROM mata_kuliah m LEFT JOIN jurusan j ON m.id_jurusan = j.id_jurusan ORDER BY m.semester ASC, m.kode_matkul ASC";
    $stmt = $pdo->query($sql);
    $dataMatkul = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="card-custom mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-journal-text me-2"></i>Data Mata Kuliah</h5>
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
                        <th width="180" class="text-center">Aksi</th>
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
                                <td class="text-center">
                                    <a href="?page=matakuliah&act=detail&id=<?= $mk['id_matkul'] ?>" class="btn btn-info btn-sm text-white"><i class="bi bi-eye"></i></a>
                                    <a href="?page=matakuliah&act=edit&id=<?= $mk['id_matkul'] ?>" class="btn btn-warning btn-sm text-white"><i class="bi bi-pencil-square"></i></a>
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalHapus" data-href="index.php?page=matakuliah&act=delete&id=<?= $mk['id_matkul'] ?>"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center text-muted">Tidak ada data.</td></tr>
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
                <div class="col-md-4 mb-3">
                    <label class="form-label">SKS (1-6)</label>
                    <input type="number" name="sks" class="form-control" min="1" max="6" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Semester</label>
                    <select name="semester" class="form-select" required>
                        <?php for($i=1; $i<=8; $i++): ?>
                            <option value="<?= $i ?>">Semester <?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Jurusan</label>
                    <select name="id_jurusan" class="form-select" required>
                        <option value="">-- Pilih Jurusan --</option>
                        <?php foreach ($optJurusan as $jur): ?>
                            <option value="<?= $jur['id_jurusan'] ?>"><?= $jur['nama_jurusan'] ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
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
                <div class="col-md-4 mb-3">
                    <label class="form-label">SKS</label>
                    <input type="number" name="sks" value="<?= $mk['sks'] ?>" class="form-control" min="1" max="6" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Semester</label>
                    <select name="semester" class="form-select" required>
                        <?php for($i=1; $i<=8; $i++): ?>
                            <option value="<?= $i ?>" <?= ($mk['semester'] == $i) ? 'selected' : '' ?>>Semester <?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
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
            <button type="submit" class="btn btn-primary"><i class="bi bi-check2-circle me-1"></i> Update</button>
            <a href="?page=matakuliah" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

<?php elseif ($act === 'detail' && $id_matkul): ?>
    <?php
    $sql = "SELECT m.*, j.nama_jurusan FROM mata_kuliah m LEFT JOIN jurusan j ON m.id_jurusan = j.id_jurusan WHERE m.id_matkul = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id_matkul]);
    $mk = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$mk) die("Data tidak ditemukan");
    ?>
    <div class="card-custom mb-4"><h5 class="mb-0 fw-bold">Detail Mata Kuliah</h5></div>
    <div class="card-custom">
        <table class="table table-bordered">
            <tr><th width="200" class="bg-light">ID Matkul</th><td><?= $mk['id_matkul'] ?></td></tr>
            <tr><th class="bg-light">Kode Matkul</th><td><?= htmlspecialchars($mk['kode_matkul']) ?></td></tr>
            <tr><th class="bg-light">Nama Matkul</th><td><?= htmlspecialchars($mk['nama_matkul']) ?></td></tr>
            <tr><th class="bg-light">SKS</th><td><?= $mk['sks'] ?></td></tr>
            <tr><th class="bg-light">Semester</th><td><?= $mk['semester'] ?></td></tr>
            <tr><th class="bg-light">Jurusan</th><td><?= htmlspecialchars($mk['nama_jurusan'] ?? '-') ?></td></tr>
            <tr><th class="bg-light">Tanggal Dibuat</th><td><?= $mk['created_at'] ?></td></tr>
        </table>
        <a href="?page=matakuliah&act=edit&id=<?= $mk['id_matkul'] ?>" class="btn btn-warning text-white"><i class="bi bi-pencil-square"></i> Edit</a>
        <a href="?page=matakuliah" class="btn btn-secondary">Kembali</a>
    </div>
<?php endif; ?>

<div class="modal fade" id="modalHapus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title text-danger">Konfirmasi Hapus</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body"><p>Yakin ingin menghapus mata kuliah ini?</p></div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><a href="#" class="btn btn-danger" id="btn-confirm-hapus">Ya, Hapus</a></div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var modalHapus = document.getElementById('modalHapus');
    modalHapus.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var urlToDelete = button.getAttribute('data-href');
        modalHapus.querySelector('#btn-confirm-hapus').setAttribute('href', urlToDelete);
    });

    <?php if (isset($_SESSION['flash'])): ?>
    Swal.mixin({
        toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true
    }).fire({
        icon: '<?= $_SESSION['flash']['type'] == 'danger' ? 'error' : $_SESSION['flash']['type'] ?>',
        title: '<?= $_SESSION['flash']['message'] ?>'
    });
    <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
});
</script>
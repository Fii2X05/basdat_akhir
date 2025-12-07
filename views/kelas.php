<?php
// views/kelas.php
global $pdo;
if (empty($pdo)) { require_once __DIR__ . '/../config/database.php'; }

$act = $_GET['act'] ?? 'list';
$id_kelas = $_GET['id'] ?? null;

if ($act === 'create' || $act === 'edit') {
    $stmtJur = $pdo->query("SELECT * FROM jurusan ORDER BY nama_jurusan ASC");
    $optJurusan = $stmtJur->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?php if ($act === 'list'): ?>
    <?php
    $sql = "SELECT k.*, j.nama_jurusan FROM kelas k LEFT JOIN jurusan j ON k.id_jurusan = j.id_jurusan ORDER BY k.nama_kelas ASC";
    $stmt = $pdo->query($sql);
    $dataKelas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="card-custom mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-building me-2"></i>Data Kelas</h5>
            <a href="?page=kelas&act=create" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah Kelas</a>
        </div>
    </div>
    <div class="card-custom">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="50" class="text-center">No</th>
                        <th>Nama Kelas</th>
                        <th>Jurusan</th>
                        <th width="100" class="text-center">Semester</th>
                        <th>Keterangan</th>
                        <th width="180" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($dataKelas)): ?>
                        <?php foreach ($dataKelas as $index => $kls): ?>
                            <tr>
                                <td class="text-center"><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($kls['nama_kelas']) ?></td>
                                <td><?= htmlspecialchars($kls['nama_jurusan'] ?? '-') ?></td>
                                <td class="text-center"><?= $kls['semester'] ?></td>
                                <td><?= htmlspecialchars($kls['keterangan'] ?? '-') ?></td>
                                <td class="text-center">
                                    <a href="?page=kelas&act=detail&id=<?= $kls['id_kelas'] ?>" class="btn btn-info btn-sm text-white"><i class="bi bi-eye"></i></a>
                                    <a href="?page=kelas&act=edit&id=<?= $kls['id_kelas'] ?>" class="btn btn-warning btn-sm text-white"><i class="bi bi-pencil-square"></i></a>
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalHapus" data-href="index.php?page=kelas&act=delete&id=<?= $kls['id_kelas'] ?>"><i class="bi bi-trash"></i></button>
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
    <div class="card-custom mb-4"><h5 class="mb-0 fw-bold">Tambah Kelas</h5></div>
    <div class="card-custom">
        <form action="index.php?page=kelas&act=store" method="POST">
            <div class="mb-3">
                <label class="form-label">Jurusan</label>
                <select name="id_jurusan" class="form-select" required>
                    <option value="">-- Pilih Jurusan --</option>
                    <?php foreach ($optJurusan as $jur): ?>
                        <option value="<?= $jur['id_jurusan'] ?>"><?= $jur['nama_jurusan'] ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label">Nama Kelas</label>
                    <input type="text" name="nama_kelas" class="form-control" placeholder="Contoh: TI-1A" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Semester</label>
                    <input type="number" name="semester" class="form-control" min="1" max="8" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Keterangan (Opsional)</label>
                <textarea name="keterangan" class="form-control" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan</button>
            <a href="?page=kelas" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

<?php elseif ($act === 'edit' && $id_kelas): ?>
    <?php
    $stmt = $pdo->prepare("SELECT * FROM kelas WHERE id_kelas = :id");
    $stmt->execute(['id' => $id_kelas]);
    $kls = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$kls) die("Data tidak ditemukan");
    ?>
    <div class="card-custom mb-4"><h5 class="mb-0 fw-bold">Edit Kelas</h5></div>
    <div class="card-custom">
        <form action="index.php?page=kelas&act=update" method="POST">
            <input type="hidden" name="id_kelas" value="<?= $kls['id_kelas'] ?>">
            <div class="mb-3">
                <label class="form-label">Jurusan</label>
                <select name="id_jurusan" class="form-select" required>
                    <option value="">-- Pilih Jurusan --</option>
                    <?php foreach ($optJurusan as $jur): ?>
                        <option value="<?= $jur['id_jurusan'] ?>" <?= ($jur['id_jurusan'] == $kls['id_jurusan']) ? 'selected' : '' ?>>
                            <?= $jur['nama_jurusan'] ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label">Nama Kelas</label>
                    <input type="text" name="nama_kelas" value="<?= htmlspecialchars($kls['nama_kelas']) ?>" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Semester</label>
                    <input type="number" name="semester" value="<?= $kls['semester'] ?>" class="form-control" min="1" max="8" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3"><?= htmlspecialchars($kls['keterangan']) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check2-circle me-1"></i> Update</button>
            <a href="?page=kelas" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

<?php elseif ($act === 'detail' && $id_kelas): ?>
    <?php
    $sql = "SELECT k.*, j.nama_jurusan FROM kelas k LEFT JOIN jurusan j ON k.id_jurusan = j.id_jurusan WHERE k.id_kelas = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id_kelas]);
    $kls = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$kls) die("Data tidak ditemukan");
    ?>
    <div class="card-custom mb-4"><h5 class="mb-0 fw-bold">Detail Kelas</h5></div>
    <div class="card-custom">
        <table class="table table-bordered">
            <tr><th width="200" class="bg-light">ID Kelas</th><td><?= $kls['id_kelas'] ?></td></tr>
            <tr><th class="bg-light">Nama Kelas</th><td><?= htmlspecialchars($kls['nama_kelas']) ?></td></tr>
            <tr><th class="bg-light">Jurusan</th><td><?= htmlspecialchars($kls['nama_jurusan'] ?? '-') ?></td></tr>
            <tr><th class="bg-light">Semester</th><td><?= $kls['semester'] ?></td></tr>
            <tr><th class="bg-light">Keterangan</th><td><?= htmlspecialchars($kls['keterangan'] ?? '-') ?></td></tr>
            <tr><th class="bg-light">Tanggal Dibuat</th><td><?= $kls['created_at'] ?></td></tr>
        </table>
        <a href="?page=kelas&act=edit&id=<?= $kls['id_kelas'] ?>" class="btn btn-warning text-white"><i class="bi bi-pencil-square"></i> Edit</a>
        <a href="?page=kelas" class="btn btn-secondary">Kembali</a>
    </div>
<?php endif; ?>

<div class="modal fade" id="modalHapus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title text-danger">Konfirmasi Hapus</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body"><p>Yakin ingin menghapus kelas ini?</p></div>
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
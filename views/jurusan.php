<?php
// views/jurusan.php
global $pdo;
if (empty($pdo)) { require_once __DIR__ . '/../config/database.php'; }

$act = $_GET['act'] ?? 'list';
$id_jurusan = $_GET['id'] ?? null;
?>

<?php if ($act === 'list'): ?>
    <?php
    $stmt = $pdo->query("SELECT * FROM jurusan ORDER BY id_jurusan ASC");
    $dataJurusan = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="card-custom mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-diagram-3 me-2"></i>Data Jurusan</h5>
            <a href="?page=jurusan&act=create" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah Jurusan</a>
        </div>
    </div>

    <div class="card-custom">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="80" class="text-center">ID</th>
                        <th>Nama Jurusan</th>
                        <th width="180" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($dataJurusan)): ?>
                        <?php foreach ($dataJurusan as $jrs): ?>
                            <tr>
                                <td class="text-center"><?= $jrs['id_jurusan'] ?></td>
                                <td><?= htmlspecialchars($jrs['nama_jurusan']) ?></td>
                                <td class="text-center">
                                    <a href="?page=jurusan&act=detail&id=<?= $jrs['id_jurusan'] ?>" class="btn btn-info btn-sm text-white"><i class="bi bi-eye"></i></a>
                                    <a href="?page=jurusan&act=edit&id=<?= $jrs['id_jurusan'] ?>" class="btn btn-warning btn-sm text-white"><i class="bi bi-pencil-square"></i></a>
                                    <button type="button" class="btn btn-danger btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalHapus"
                                            data-href="index.php?page=jurusan&act=delete&id=<?= $jrs['id_jurusan'] ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="text-center text-muted">Tidak ada data.</td></tr>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>

<?php elseif ($act === 'create'): ?>
    <div class="card-custom mb-4"><h5 class="mb-0 fw-bold">Tambah Jurusan</h5></div>
    <div class="card-custom">
        <form action="index.php?page=jurusan&act=store" method="POST">
            <div class="mb-3">
                <label class="form-label">Nama Jurusan</label>
                <input type="text" name="nama_jurusan" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan</button>
            <a href="?page=jurusan" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

<?php elseif ($act === 'edit' && $id_jurusan): ?>
    <?php
    $stmt = $pdo->prepare("SELECT * FROM jurusan WHERE id_jurusan = :id");
    $stmt->execute(['id' => $id_jurusan]);
    $jrs = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$jrs) die("Data tidak ditemukan");
    ?>
    <div class="card-custom mb-4"><h5 class="mb-0 fw-bold">Edit Jurusan</h5></div>
    <div class="card-custom">
        <form action="index.php?page=jurusan&act=update" method="POST">
            <input type="hidden" name="id_jurusan" value="<?= $jrs['id_jurusan'] ?>">
            <div class="mb-3">
                <label class="form-label">ID Jurusan</label>
                <input type="text" value="<?= $jrs['id_jurusan'] ?>" class="form-control" disabled readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Nama Jurusan</label>
                <input type="text" name="nama_jurusan" value="<?= htmlspecialchars($jrs['nama_jurusan']) ?>" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check2-circle me-1"></i> Update</button>
            <a href="?page=jurusan" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

<?php elseif ($act === 'detail' && $id_jurusan): ?>
    <?php
    $stmt = $pdo->prepare("SELECT * FROM jurusan WHERE id_jurusan = :id");
    $stmt->execute(['id' => $id_jurusan]);
    $jrs = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$jrs) die("Data tidak ditemukan");
    ?>
    <div class="card-custom mb-4"><h5 class="mb-0 fw-bold">Detail Jurusan</h5></div>
    <div class="card-custom">
        <table class="table table-bordered">
            <tr><th width="200" class="bg-light">ID Jurusan</th><td><?= $jrs['id_jurusan'] ?></td></tr>
            <tr><th class="bg-light">Nama Jurusan</th><td><?= htmlspecialchars($jrs['nama_jurusan']) ?></td></tr>
            <tr><th class="bg-light">Tanggal Dibuat</th><td><?= $jrs['created_at'] ?></td></tr>
        </table>
        <a href="?page=jurusan&act=edit&id=<?= $jrs['id_jurusan'] ?>" class="btn btn-warning text-white"><i class="bi bi-pencil-square"></i> Edit</a>
        <a href="?page=jurusan" class="btn btn-secondary">Kembali</a>
    </div>
<?php endif; ?>

<div class="modal fade" id="modalHapus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title text-danger">Konfirmasi Hapus</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body"><p>Yakin ingin menghapus jurusan ini?</p></div>
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
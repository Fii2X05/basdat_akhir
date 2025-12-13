<?php
// views/jurusan.php
global $pdo;
if (empty($pdo)) { require_once __DIR__ . '/../config/database.php'; }

$act = $_GET['act'] ?? 'list';
$id_jurusan = $_GET['id'] ?? null;
?>

<style>
    .modal-backdrop { z-index: 1040 !important; }
    .modal { z-index: 1050 !important; }
</style>

<?php if ($act === 'list'): ?>
    <?php
    // [TUGAS 5.2.2] MATERIALIZED VIEW JURUSAN
    $stmt = $pdo->query("SELECT * FROM public.mv_jurusan_stats ORDER BY nama_jurusan ASC");
    $dataJurusan = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="card-custom mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1 fw-bold"><i class="bi bi-diagram-3 me-2"></i>Data Jurusan (MV)</h5>
                <small class="text-muted">Statistik Mahasiswa, Dosen, & Kelas</small>
            </div>
            <div>
                <a href="index.php?page=jurusan&act=refresh" class="btn btn-warning btn-sm text-dark me-2">
                    <i class="bi bi-arrow-clockwise"></i> Refresh Stats
                </a>
                <a href="?page=jurusan&act=create" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> Tambah Jurusan
                </a>
            </div>
        </div>
    </div>

    <div class="card-custom">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="50" class="text-center">No</th>
                        <th>Nama Jurusan</th>
                        <th width="120" class="text-center">Jml Mhs</th>
                        <th width="120" class="text-center">Jml Dosen</th>
                        <th width="120" class="text-center">Jml Kelas</th>
                        <th width="180" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($dataJurusan)): ?>
                        <?php foreach ($dataJurusan as $index => $jrs): ?>
                            <tr>
                                <td class="text-center"><?= $index + 1 ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($jrs['nama_jurusan']) ?></td>
                                
                                <td class="text-center">
                                    <span class="badge bg-info text-dark rounded-pill"><?= $jrs['total_mahasiswa'] ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success rounded-pill"><?= $jrs['total_dosen'] ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary rounded-pill"><?= $jrs['total_kelas'] ?></span>
                                </td>

                                <td class="text-center">
                                    <a href="?page=jurusan&act=detail&id=<?= $jrs['id_jurusan'] ?>" class="btn btn-info btn-sm text-white" title="Detail"><i class="bi bi-eye"></i></a>
                                    <a href="?page=jurusan&act=edit&id=<?= $jrs['id_jurusan'] ?>" class="btn btn-warning btn-sm text-white" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalHapus" data-href="index.php?page=jurusan&act=delete&id=<?= $jrs['id_jurusan'] ?>" title="Hapus"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center text-muted">Tidak ada data jurusan.</td></tr>
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
                <label class="form-label">Nama Jurusan <span class="text-danger">*</span></label>
                <input type="text" name="nama_jurusan" class="form-control" required placeholder="Contoh: Teknik Informatika">
            </div>
            <button type="submit" class="btn btn-primary px-4">Simpan</button>
            <a href="?page=jurusan" class="btn btn-secondary px-4">Kembali</a>
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
                <label class="form-label">Nama Jurusan <span class="text-danger">*</span></label>
                <input type="text" name="nama_jurusan" value="<?= htmlspecialchars($jrs['nama_jurusan']) ?>" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary px-4">Update</button>
            <a href="?page=jurusan" class="btn btn-secondary px-4">Kembali</a>
        </form>
    </div>

<?php elseif ($act === 'detail' && $id_jurusan): ?>
    <?php
    // Detail pakai MV
    $stmt = $pdo->prepare("SELECT * FROM public.mv_jurusan_stats WHERE id_jurusan = :id");
    $stmt->execute(['id' => $id_jurusan]);
    $jrs = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Fallback
    if (!$jrs) {
        $stmt = $pdo->prepare("SELECT * FROM jurusan WHERE id_jurusan = :id");
        $stmt->execute(['id' => $id_jurusan]);
        $jrs = $stmt->fetch(PDO::FETCH_ASSOC);
        $jrs['total_mahasiswa'] = 0; $jrs['total_dosen'] = 0; $jrs['total_kelas'] = 0;
    }
    ?>
    <div class="card-custom mb-4"><h5 class="mb-0 fw-bold">Detail Jurusan</h5></div>
    <div class="card-custom">
        <div class="row">
            <div class="col-md-8">
                <table class="table table-bordered">
                    <tr><th width="200" class="bg-light">ID Jurusan</th><td><?= $jrs['id_jurusan'] ?></td></tr>
                    <tr><th class="bg-light">Nama Jurusan</th><td><h3><?= htmlspecialchars($jrs['nama_jurusan']) ?></h3></td></tr>
                    <tr><th class="bg-light">Terakhir Update Stats</th><td>Sekarang (Realtime via Refresh)</td></tr>
                </table>
            </div>
            <div class="col-md-4">
                <div class="card bg-light border-0 mb-3">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Total Mahasiswa</h6>
                        <h2 class="text-primary fw-bold"><?= $jrs['total_mahasiswa'] ?></h2>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <div class="card bg-light border-0">
                            <div class="card-body text-center p-2">
                                <small class="text-muted">Dosen</small>
                                <h4 class="text-success fw-bold mb-0"><?= $jrs['total_dosen'] ?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card bg-light border-0">
                            <div class="card-body text-center p-2">
                                <small class="text-muted">Kelas</small>
                                <h4 class="text-secondary fw-bold mb-0"><?= $jrs['total_kelas'] ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4">
            <a href="?page=jurusan&act=edit&id=<?= $jrs['id_jurusan'] ?>" class="btn btn-warning text-white px-4">Edit</a>
            <a href="?page=jurusan" class="btn btn-secondary px-4">Kembali</a>
        </div>
    </div>
<?php endif; ?>

<div class="modal fade" id="modalHapus" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title text-danger">Konfirmasi Hapus</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><p>Yakin hapus data ini?</p><div class="alert alert-warning small">Hati-hati! Menghapus jurusan akan menghapus Kelas & Matkul di dalamnya.</div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><a href="#" class="btn btn-danger" id="btn-confirm-hapus">Ya, Hapus</a></div></div></div></div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modalHapus = document.getElementById('modalHapus');
        if (modalHapus) {
            document.body.appendChild(modalHapus);
            modalHapus.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                modalHapus.querySelector('#btn-confirm-hapus').setAttribute('href', button.getAttribute('data-href'));
            });
        }
        <?php if (isset($_SESSION['flash'])): ?>
            Swal.fire({ icon: '<?= $_SESSION['flash']['type'] == 'danger' ? 'error' : 'info' ?>', title: '<?= $_SESSION['flash']['message'] ?>', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>
    });
</script>
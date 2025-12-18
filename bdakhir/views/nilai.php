<?php
global $pdo;
if (empty($pdo)) { require_once __DIR__ . '/../config/database.php'; }

$act = $_GET['act'] ?? 'list';
$id_nilai = $_GET['id'] ?? null;

// Helper Dropdown
if ($act === 'create' || $act === 'edit') {
    $stmtMhs = $pdo->query("SELECT id_mahasiswa, nim, nama_mahasiswa FROM mahasiswa ORDER BY nama_mahasiswa ASC");
    $optMhs = $stmtMhs->fetchAll(PDO::FETCH_ASSOC);

    $stmtMk = $pdo->query("SELECT id_matkul, kode_matkul, nama_matkul FROM mata_kuliah ORDER BY nama_matkul ASC");
    $optMk = $stmtMk->fetchAll(PDO::FETCH_ASSOC);
}
?>

<style>
    .modal-backdrop { z-index: 1040 !important; }
    .modal { z-index: 1050 !important; }
</style>

<?php if ($act === 'list'): ?>
    <?php
    // [TUGAS 5.2.2] MATERIALIZED VIEW NILAI
    $stmt = $pdo->query("SELECT * FROM public.mv_nilai_stats ORDER BY created_at DESC");
    $dataNilai = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="card-custom mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1 fw-bold"><i class="bi bi-bar-chart me-2"></i>Data Nilai (MV)</h5>
                <small class="text-muted">Laporan Status Kelulusan Mata Kuliah</small>
            </div>
            <div>
                <a href="index.php?page=nilai&act=refresh" class="btn btn-warning btn-sm text-dark me-2">
                    <i class="bi bi-arrow-clockwise"></i> Refresh Stats
                </a>
                <a href="?page=nilai&act=create" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> Tambah Nilai
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
                        <th>Mahasiswa</th>
                        <th>Mata Kuliah</th>
                        <th width="80" class="text-center">SKS</th>
                        <th width="80" class="text-center">Angka</th>
                        <th width="80" class="text-center">Huruf</th>
                        <th width="100" class="text-center">Status</th>
                        <th width="150" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($dataNilai)): ?>
                        <?php foreach ($dataNilai as $index => $row): ?>
                            <tr>
                                <td class="text-center"><?= $index + 1 ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($row['nim']) ?></strong><br>
                                    <?= htmlspecialchars($row['nama_mahasiswa']) ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($row['nama_matkul']) ?><br>
                                    <small class="text-muted"><?= htmlspecialchars($row['kode_matkul']) ?></small>
                                </td>
                                <td class="text-center"><?= $row['sks'] ?></td>
                                <td class="text-center"><?= $row['nilai_angka'] ?></td>
                                <td class="text-center fw-bold">
                                    <?php
                                    $nh = $row['nilai_huruf'];
                                    $badge = 'bg-secondary';
                                    if ($nh == 'A') $badge = 'bg-success';
                                    elseif ($nh == 'B') $badge = 'bg-primary';
                                    elseif ($nh == 'C') $badge = 'bg-warning text-dark';
                                    elseif ($nh == 'D' || $nh == 'E') $badge = 'bg-danger';
                                    ?>
                                    <span class="badge <?= $badge ?> rounded-pill px-3"><?= $nh ?></span>
                                </td>
                                
                                <td class="text-center">
                                    <?php if ($row['status_kelulusan'] == 'Lulus'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success"><i class="bi bi-check-circle me-1"></i>Lulus</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger"><i class="bi bi-x-circle me-1"></i><?= $row['status_kelulusan'] ?></span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-center">
                                    <a href="?page=nilai&act=detail&id=<?= $row['id_nilai'] ?>" class="btn btn-info btn-sm text-white" title="Detail"><i class="bi bi-eye"></i></a>
                                    <a href="?page=nilai&act=edit&id=<?= $row['id_nilai'] ?>" class="btn btn-warning btn-sm text-white" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalHapus" data-href="index.php?page=nilai&act=delete&id=<?= $row['id_nilai'] ?>"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center text-muted">Belum ada data nilai.</td></tr>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>

<?php elseif ($act === 'create'): ?>
    <div class="card-custom mb-4"><h5 class="mb-0 fw-bold">Tambah Nilai</h5></div>
    <div class="card-custom">
        <form action="index.php?page=nilai&act=store" method="POST">
            <div class="mb-3">
                <label class="form-label">Mahasiswa <span class="text-danger">*</span></label>
                <select name="id_mahasiswa" class="form-select" required>
                    <option value="">-- Pilih Mahasiswa --</option>
                    <?php foreach ($optMhs as $m): ?><option value="<?= $m['id_mahasiswa'] ?>"><?= $m['nim'] ?> - <?= $m['nama_mahasiswa'] ?></option><?php endforeach ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Mata Kuliah <span class="text-danger">*</span></label>
                <select name="id_matkul" class="form-select" required>
                    <option value="">-- Pilih Mata Kuliah --</option>
                    <?php foreach ($optMk as $mk): ?><option value="<?= $mk['id_matkul'] ?>"><?= $mk['kode_matkul'] ?> - <?= $mk['nama_matkul'] ?></option><?php endforeach ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Nilai Angka (0-100) <span class="text-danger">*</span></label>
                <input type="number" name="nilai_angka" class="form-control" min="0" max="100" step="0.01" required>
                <div class="form-text">Nilai Huruf akan dihitung otomatis.</div>
            </div>
            <button type="submit" class="btn btn-primary px-4">Simpan</button>
            <a href="?page=nilai" class="btn btn-secondary px-4">Kembali</a>
        </form>
    </div>

<?php elseif ($act === 'edit' && $id_nilai): ?>
    <?php
    $stmt = $pdo->prepare("SELECT * FROM nilai WHERE id_nilai = :id");
    $stmt->execute(['id' => $id_nilai]);
    $nilai = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$nilai) die("Data tidak ditemukan");
    ?>
    <div class="card-custom mb-4"><h5 class="mb-0 fw-bold">Edit Nilai</h5></div>
    <div class="card-custom">
        <form action="index.php?page=nilai&act=update" method="POST">
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
            <button type="submit" class="btn btn-primary px-4">Update</button>
            <a href="?page=nilai" class="btn btn-secondary px-4">Kembali</a>
        </form>
    </div>

<?php elseif ($act === 'detail' && $id_nilai): ?>
    <?php
    // Detail menggunakan MV
    $stmt = $pdo->prepare("SELECT * FROM public.mv_nilai_stats WHERE id_nilai = :id");
    $stmt->execute(['id' => $id_nilai]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Fallback
    if (!$row) {
        $stmt = $pdo->prepare("SELECT n.*, m.nama_mahasiswa, m.nim, mk.nama_matkul, mk.kode_matkul, mk.sks, mk.semester FROM nilai n JOIN mahasiswa m ON n.id_mahasiswa=m.id_mahasiswa JOIN mata_kuliah mk ON n.id_matkul=mk.id_matkul WHERE n.id_nilai=:id");
        $stmt->execute(['id' => $id_nilai]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $row['status_kelulusan'] = '-';
    }
    ?>
    <div class="card-custom mb-4"><h5 class="mb-0 fw-bold">Detail Nilai</h5></div>
    <div class="card-custom">
        <div class="row">
            <div class="col-md-8">
                <table class="table table-bordered">
                    <tr><th width="200" class="bg-light">ID Nilai</th><td><?= $row['id_nilai'] ?></td></tr>
                    <tr><th class="bg-light">Mahasiswa</th><td><?= htmlspecialchars($row['nama_mahasiswa']) ?> (<?= $row['nim'] ?>)</td></tr>
                    <tr><th class="bg-light">Mata Kuliah</th><td><?= htmlspecialchars($row['nama_matkul']) ?> (<?= $row['kode_matkul'] ?>)</td></tr>
                    <tr><th class="bg-light">SKS</th><td><?= $row['sks'] ?></td></tr>
                    <tr><th class="bg-light">Semester</th><td><?= $row['semester'] ?></td></tr>
                    <tr><th class="bg-light">Tanggal Input</th><td><?= date('d F Y, H:i', strtotime($row['created_at'])) ?></td></tr>
                </table>
            </div>
            <div class="col-md-4 text-center">
                <div class="card bg-light border-0 mb-3">
                    <div class="card-body">
                        <h6 class="text-muted">Nilai Angka</h6>
                        <h2 class="text-primary fw-bold"><?= $row['nilai_angka'] ?></h2>
                    </div>
                </div>
                <div class="card bg-light border-0">
                    <div class="card-body">
                        <h6 class="text-muted">Status</h6>
                        <h3 class="fw-bold <?= ($row['status_kelulusan']=='Lulus')?'text-success':'text-danger' ?>">
                            <?= $row['status_kelulusan'] ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4">
            <a href="?page=nilai&act=edit&id=<?= $row['id_nilai'] ?>" class="btn btn-warning text-white px-4">Edit</a>
            <a href="?page=nilai" class="btn btn-secondary px-4">Kembali</a>
        </div>
    </div>
<?php endif; ?>

<div class="modal fade" id="modalHapus" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title text-danger">Konfirmasi Hapus</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><p>Yakin hapus data ini?</p></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><a href="#" class="btn btn-danger" id="btn-confirm-hapus">Ya, Hapus</a></div></div></div></div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modalHapus = document.getElementById('modalHapus');
        if (modalHapus) {
            document.body.appendChild(modalHapus);
            modalHapus.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var urlToDelete = button.getAttribute('data-href');
                modalHapus.querySelector('#btn-confirm-hapus').setAttribute('href', urlToDelete);
            });
        }
        <?php if (isset($_SESSION['flash'])): ?>
            Swal.fire({ icon: '<?= $_SESSION['flash']['type'] == 'danger' ? 'error' : 'info' ?>', title: '<?= $_SESSION['flash']['message'] ?>', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>
    });
</script>
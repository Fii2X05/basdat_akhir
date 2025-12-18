<?php
global $pdo;
if (empty($pdo)) { require_once __DIR__ . '/../config/database.php'; }

$act = $_GET['act'] ?? 'list';
$id_matkul = $_GET['id'] ?? null;

// Helper Dropdown
if ($act === 'create' || $act === 'edit') {
    $stmtJur = $pdo->query("SELECT * FROM jurusan ORDER BY nama_jurusan ASC");
    $optJurusan = $stmtJur->fetchAll(PDO::FETCH_ASSOC);

    // Dropdown Prasyarat
    $sqlPras = "SELECT id_matkul, kode_matkul, nama_matkul FROM mata_kuliah";
    if ($act === 'edit' && $id_matkul) {
        $sqlPras .= " WHERE id_matkul != " . intval($id_matkul);
    }
    $sqlPras .= " ORDER BY nama_matkul ASC";
    $optPrasyarat = $pdo->query($sqlPras)->fetchAll(PDO::FETCH_ASSOC);
}
?>

<style>
    .modal-backdrop { z-index: 1040 !important; }
    .modal { z-index: 1050 !important; }
</style>

<?php if ($act === 'list'): ?>
    <?php
    // [TUGAS 5.2.2] MATERIALIZED VIEW MATKUL
    $stmt = $pdo->query("SELECT * FROM public.mv_matkul_stats ORDER BY semester ASC, kode_matkul ASC");
    $dataMatkul = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="card-custom mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1 fw-bold"><i class="bi bi-journal-text me-2"></i>Katalog Matkul (MV)</h5>
                <small class="text-muted">Statistik Pengambilan & Jadwal</small>
            </div>
            <div>
                <a href="index.php?page=matakuliah&act=refresh" class="btn btn-warning btn-sm text-dark me-2">
                    <i class="bi bi-arrow-clockwise"></i> Refresh Stats
                </a>
                <a href="?page=matakuliah&act=create" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> Tambah Matkul
                </a>
            </div>
        </div>
    </div>

    <div class="card-custom">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="100">Kode</th>
                        <th>Nama Mata Kuliah</th>
                        <th width="50" class="text-center">SKS</th>
                        <th width="50" class="text-center">Smt</th>
                        <th width="80" class="text-center">Jadwal</th>
                        <th width="80" class="text-center">Diambil</th>
                        <th>Prasyarat</th>
                        <th width="180" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($dataMatkul)): ?>
                        <?php foreach ($dataMatkul as $mk): ?>
                            <tr>
                                <td class="fw-bold"><?= htmlspecialchars($mk['kode_matkul']) ?></td>
                                <td><?= htmlspecialchars($mk['nama_matkul']) ?></td>
                                <td class="text-center"><?= $mk['sks'] ?></td>
                                <td class="text-center"><?= $mk['semester'] ?></td>
                                
                                <td class="text-center">
                                    <span class="badge bg-info text-dark rounded-pill"><?= $mk['total_jadwal'] ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success rounded-pill"><?= $mk['total_mahasiswa_ambil'] ?></span>
                                </td>

                                <td>
                                    <?php if($mk['nama_prasyarat']): ?>
                                        <span class="badge bg-warning text-dark" title="<?= htmlspecialchars($mk['kode_prasyarat']) ?>">
                                            <?= htmlspecialchars($mk['nama_prasyarat']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="?page=matakuliah&act=detail&id=<?= $mk['id_matkul'] ?>" class="btn btn-info btn-sm text-white" title="Detail"><i class="bi bi-eye"></i></a>
                                    <a href="?page=matakuliah&act=edit&id=<?= $mk['id_matkul'] ?>" class="btn btn-warning btn-sm text-white" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalHapus" data-href="index.php?page=matakuliah&act=delete&id=<?= $mk['id_matkul'] ?>"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center text-muted">Tidak ada data.</td></tr>
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
                    <label class="form-label">Kode Mata Kuliah <span class="text-danger">*</span></label>
                    <input type="text" name="kode_matkul" class="form-control" placeholder="Contoh: TIK101" required>
                </div>
                <div class="col-md-8 mb-3">
                    <label class="form-label">Nama Mata Kuliah <span class="text-danger">*</span></label>
                    <input type="text" name="nama_matkul" class="form-control" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">SKS <span class="text-danger">*</span></label>
                    <input type="number" name="sks" class="form-control input-angka" min="1" max="6" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Semester <span class="text-danger">*</span></label>
                    <select name="semester" class="form-select" required>
                        <?php for($i=1; $i<=8; $i++): ?><option value="<?= $i ?>">Semester <?= $i ?></option><?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Jurusan <span class="text-danger">*</span></label>
                    <select name="id_jurusan" class="form-select" required>
                        <option value="">-- Pilih Jurusan --</option>
                        <?php foreach ($optJurusan as $jur): ?><option value="<?= $jur['id_jurusan'] ?>"><?= $jur['nama_jurusan'] ?></option><?php endforeach ?>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Mata Kuliah Prasyarat (Opsional)</label>
                <select name="prasyarat_id" class="form-select">
                    <option value="">-- Tidak Ada Prasyarat --</option>
                    <?php foreach ($optPrasyarat as $pras): ?><option value="<?= $pras['id_matkul'] ?>"><?= $pras['kode_matkul'] ?> - <?= $pras['nama_matkul'] ?></option><?php endforeach ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary px-4">Simpan</button>
            <a href="?page=matakuliah" class="btn btn-secondary px-4">Kembali</a>
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
                    <label class="form-label">Kode Mata Kuliah <span class="text-danger">*</span></label>
                    <input type="text" name="kode_matkul" value="<?= htmlspecialchars($mk['kode_matkul']) ?>" class="form-control" required>
                </div>
                <div class="col-md-8 mb-3">
                    <label class="form-label">Nama Mata Kuliah <span class="text-danger">*</span></label>
                    <input type="text" name="nama_matkul" value="<?= htmlspecialchars($mk['nama_matkul']) ?>" class="form-control" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">SKS <span class="text-danger">*</span></label>
                    <input type="number" name="sks" value="<?= $mk['sks'] ?>" class="form-control input-angka" min="1" max="6" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Semester <span class="text-danger">*</span></label>
                    <select name="semester" class="form-select" required>
                        <?php for($i=1; $i<=8; $i++): ?>
                            <option value="<?= $i ?>" <?= ($mk['semester'] == $i) ? 'selected' : '' ?>>Semester <?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Jurusan <span class="text-danger">*</span></label>
                    <select name="id_jurusan" class="form-select" required>
                        <?php foreach ($optJurusan as $jur): ?>
                            <option value="<?= $jur['id_jurusan'] ?>" <?= ($jur['id_jurusan'] == $mk['id_jurusan']) ? 'selected' : '' ?>><?= $jur['nama_jurusan'] ?></option>
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
            <button type="submit" class="btn btn-primary px-4">Update</button>
            <a href="?page=matakuliah" class="btn btn-secondary px-4">Kembali</a>
        </form>
    </div>

<?php elseif ($act === 'detail' && $id_matkul): ?>
    <?php
    // Detail menggunakan MV
    $stmt = $pdo->prepare("SELECT * FROM public.mv_matkul_stats WHERE id_matkul = :id");
    $stmt->execute(['id' => $id_matkul]);
    $mk = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Fallback
    if (!$mk) {
        $stmt = $pdo->prepare("SELECT * FROM view_info_matkul WHERE id_matkul = :id");
        $stmt->execute(['id' => $id_matkul]);
        $mk = $stmt->fetch(PDO::FETCH_ASSOC);
        $mk['total_jadwal'] = 0; $mk['total_mahasiswa_ambil'] = 0;
    }
    ?>
    <div class="card-custom mb-4"><h5 class="mb-0 fw-bold">Detail Mata Kuliah</h5></div>
    <div class="card-custom">
        <div class="row">
            <div class="col-md-8">
                <table class="table table-bordered">
                    <tr><th width="200" class="bg-light">Kode Mata Kuliah</th><td class="fw-bold"><?= htmlspecialchars($mk['kode_matkul']) ?></td></tr>
                    <tr><th class="bg-light">Nama Mata Kuliah</th><td><h3><?= htmlspecialchars($mk['nama_matkul']) ?></h3></td></tr>
                    <tr><th class="bg-light">Jurusan</th><td><?= htmlspecialchars($mk['nama_jurusan'] ?? '-') ?></td></tr>
                    <tr><th class="bg-light">Prasyarat</th>
                        <td>
                            <?php if ($mk['nama_prasyarat']): ?>
                                <span class="badge bg-warning text-dark"><?= htmlspecialchars($mk['kode_prasyarat']) ?> - <?= htmlspecialchars($mk['nama_prasyarat']) ?></span>
                            <?php else: ?>
                                <span class="text-muted">Tidak ada</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr><th class="bg-light">Terakhir Refresh Stats</th><td>Sekarang (Realtime via Refresh)</td></tr>
                </table>
            </div>
            <div class="col-md-4">
                <div class="card bg-light border-0 mb-3">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Beban Studi</h6>
                        <h2 class="text-primary fw-bold"><?= $mk['sks'] ?> SKS</h2>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <div class="card bg-light border-0">
                            <div class="card-body text-center p-2">
                                <small class="text-muted">Jadwal</small>
                                <h4 class="text-success fw-bold mb-0"><?= $mk['total_jadwal'] ?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card bg-light border-0">
                            <div class="card-body text-center p-2">
                                <small class="text-muted">Diambil Mhs</small>
                                <h4 class="text-secondary fw-bold mb-0"><?= $mk['total_mahasiswa_ambil'] ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4">
            <a href="?page=matakuliah&act=edit&id=<?= $mk['id_matkul'] ?>" class="btn btn-warning text-white px-4">Edit</a>
            <a href="?page=matakuliah" class="btn btn-secondary px-4">Kembali</a>
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
        const inputAngka = document.querySelectorAll('.input-angka');
        inputAngka.forEach(function(input) { input.addEventListener('input', function(e) { if (/[^0-9]/.test(this.value)) { this.value = this.value.replace(/[^0-9]/g, ''); } }); });
        <?php if (isset($_SESSION['flash'])): ?>
            Swal.fire({ icon: '<?= $_SESSION['flash']['type'] == 'danger' ? 'error' : 'info' ?>', title: '<?= $_SESSION['flash']['message'] ?>', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>
    });
</script>
<?php
global $pdo;
if (empty($pdo)) { require_once __DIR__ . '/../config/database.php'; }

$act = $_GET['act'] ?? 'list';
$id_dosen = $_GET['id'] ?? null;

if ($act === 'create' || $act === 'edit') {
    $stmtJur = $pdo->query("SELECT * FROM jurusan ORDER BY nama_jurusan ASC");
    $optJurusan = $stmtJur->fetchAll(PDO::FETCH_ASSOC);
}
?>

<style>
    .modal-backdrop { z-index: 1040 !important; }
    .modal { z-index: 1050 !important; }
    .icon-profile { font-size: 5rem; color: #adb5bd; }
</style>

<?php if ($act === 'list'): ?>
    <?php
    // MATERIALIZED VIEW DOSEN
    $sql = "SELECT * FROM public.mv_dosen_stats ORDER BY nama_dosen ASC";
    $stmt = $pdo->query($sql);
    $dataDosen = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="card-custom mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1 fw-bold"><i class="bi bi-person-badge me-2"></i>Data Dosen (Statistik Ajar)</h5>
                <small class="text-muted">Menampilkan jumlah kelas & total SKS ajar.</small>
            </div>
            <div>
                <a href="index.php?page=dosen&act=refresh" class="btn btn-warning btn-sm text-dark me-2">
                    <i class="bi bi-arrow-clockwise"></i> Refresh Beban Kerja
                </a>
                <a href="index.php?page=dosen&act=create" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> Tambah Dosen
                </a>
            </div>
        </div>
    </div>

    <div class="card-custom">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="50" class="text-center">No</th>
                        <th>NIDN</th>
                        <th>Nama Dosen</th>
                        <th>Jurusan</th>
                        <th width="100" class="text-center">Kelas Ajar</th>
                        <th width="100" class="text-center">Total SKS</th>
                        <th width="180" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($dataDosen)): ?>
                        <?php foreach ($dataDosen as $index => $dsn): ?>
                            <tr>
                                <td class="text-center"><?= $index + 1 ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($dsn['nidn'] ?? '-') ?></td>
                                <td>
                                    <?= htmlspecialchars($dsn['nama_dosen'] ?? '') ?><br>
                                    <small class="text-muted"><?= htmlspecialchars($dsn['no_hp'] ?? '-') ?></small>
                                </td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($dsn['nama_jurusan'] ?? 'Umum') ?></span></td>
                                
                                <td class="text-center">
                                    <span class="badge bg-info text-dark rounded-pill"><?= $dsn['jumlah_kelas_ajar'] ?></span>
                                </td>
                                <td class="text-center fw-bold text-success"><?= $dsn['total_sks_ajar'] ?></td>

                                <td class="text-center">
                                    <a href="index.php?page=dosen&act=detail&id=<?= $dsn['id_dosen'] ?>" class="btn btn-info btn-sm text-white"><i class="bi bi-eye"></i></a>
                                    <a href="index.php?page=dosen&act=edit&id=<?= $dsn['id_dosen'] ?>" class="btn btn-warning btn-sm text-white"><i class="bi bi-pencil-square"></i></a>
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalHapus" data-href="index.php?page=dosen&act=delete&id=<?= $dsn['id_dosen'] ?>"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada data dosen.</td></tr>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>

<?php elseif ($act === 'create'): ?>
    <div class="card-custom mb-4"><h5 class="mb-0 fw-bold">Tambah Dosen</h5></div>
    <div class="card-custom">
        <form action="index.php?page=dosen&act=store" method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">NIDN <span class="text-danger">*</span></label>
                    <input type="text" name="nidn" class="form-control input-angka" maxlength="20" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama_dosen" class="form-control" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">No HP</label>
                    <input type="text" name="no_hp" class="form-control input-angka" maxlength="15">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Jurusan <span class="text-danger">*</span></label>
                    <select name="id_jurusan" class="form-select" required>
                        <option value="">-- Pilih Jurusan --</option>
                        <?php foreach ($optJurusan as $jur): ?><option value="<?= $jur['id_jurusan'] ?>"><?= $jur['nama_jurusan'] ?></option><?php endforeach ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Alamat</label>
                    <input type="text" name="alamat" class="form-control">
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary px-4">Simpan</button>
                <a href="index.php?page=dosen" class="btn btn-secondary px-4">Kembali</a>
            </div>
        </form>
    </div>

<?php elseif ($act === 'edit' && $id_dosen): ?>
    <?php
    $stmt = $pdo->prepare("SELECT * FROM dosen WHERE id_dosen = :id");
    $stmt->execute(['id' => $id_dosen]);
    $dsn = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$dsn) die("Data tidak ditemukan");
    ?>
    <div class="card-custom mb-4"><h5 class="mb-0 fw-bold">Edit Dosen</h5></div>
    <div class="card-custom">
        <form action="index.php?page=dosen&act=update" method="POST">
            <input type="hidden" name="id_dosen" value="<?= $dsn['id_dosen'] ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">NIDN</label>
                    <input type="text" name="nidn" value="<?= htmlspecialchars($dsn['nidn']) ?>" class="form-control input-angka" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama_dosen" value="<?= htmlspecialchars($dsn['nama_dosen']) ?>" class="form-control" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">No HP</label>
                    <input type="text" name="no_hp" value="<?= htmlspecialchars($dsn['no_hp'] ?? '') ?>" class="form-control input-angka">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Jurusan</label>
                    <select name="id_jurusan" class="form-select" required>
                        <?php foreach ($optJurusan as $jur): ?>
                            <option value="<?= $jur['id_jurusan'] ?>" <?= ($jur['id_jurusan'] == $dsn['id_jurusan']) ? 'selected' : '' ?>><?= $jur['nama_jurusan'] ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($dsn['email'] ?? '') ?>" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Alamat</label>
                    <input type="text" name="alamat" value="<?= htmlspecialchars($dsn['alamat'] ?? '') ?>" class="form-control">
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary px-4">Update</button>
                <a href="index.php?page=dosen" class="btn btn-secondary px-4">Kembali</a>
            </div>
        </form>
    </div>

<?php elseif ($act === 'detail' && $id_dosen): ?>
    <?php
    // Detail pakai MV
    $stmt = $pdo->prepare("SELECT * FROM public.mv_dosen_stats WHERE id_dosen = :id");
    $stmt->execute(['id' => $id_dosen]);
    $dsn = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Fallback kalau data belum direfresh
    if (!$dsn) {
        $stmt = $pdo->prepare("SELECT d.*, j.nama_jurusan FROM dosen d LEFT JOIN jurusan j ON d.id_jurusan=j.id_jurusan WHERE id_dosen=:id");
        $stmt->execute(['id' => $id_dosen]);
        $dsn = $stmt->fetch(PDO::FETCH_ASSOC);
        $dsn['jumlah_kelas_ajar'] = 0; $dsn['total_sks_ajar'] = 0;
    }
    ?>
    <div class="card-custom mb-4"><h5 class="mb-0 fw-bold">Detail Dosen</h5></div>
    <div class="card-custom">
        <div class="row align-items-center">
            <div class="col-md-8">
                <table class="table table-bordered">
                    <tr><th width="200" class="bg-light">ID Dosen</th><td><?= $dsn['id_dosen'] ?></td></tr>
                    <tr><th class="bg-light">NIDN</th><td class="fw-bold text-primary"><?= htmlspecialchars($dsn['nidn']) ?></td></tr>
                    <tr><th class="bg-light">Nama Lengkap</th><td><?= htmlspecialchars($dsn['nama_dosen']) ?></td></tr>
                    <tr><th class="bg-light">Jurusan</th><td><?= htmlspecialchars($dsn['nama_jurusan'] ?? '-') ?></td></tr>
                    <tr><th class="bg-light">No HP</th><td><?= htmlspecialchars($dsn['no_hp'] ?? '-') ?></td></tr>
                    <tr><th class="bg-light">Email</th><td><?= htmlspecialchars($dsn['email'] ?? '-') ?></td></tr>
                    <tr><th class="bg-light">Alamat</th><td><?= htmlspecialchars($dsn['alamat'] ?? '-') ?></td></tr>
                </table>
            </div>
            <div class="col-md-4 text-center">
                <div class="card bg-light border-0 mb-3">
                    <div class="card-body">
                        <h6 class="text-muted">Kelas yang Diajar</h6>
                        <h2 class="text-primary fw-bold"><?= $dsn['jumlah_kelas_ajar'] ?></h2>
                    </div>
                </div>
                <div class="card bg-light border-0">
                    <div class="card-body">
                        <h6 class="text-muted">Total Beban SKS</h6>
                        <h2 class="text-success fw-bold"><?= $dsn['total_sks_ajar'] ?></h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4">
            <a href="index.php?page=dosen&act=edit&id=<?= $dsn['id_dosen'] ?>" class="btn btn-warning text-white px-4">Edit</a>
            <a href="index.php?page=dosen" class="btn btn-secondary px-4">Kembali</a>
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
<?php
global $pdo;
if (empty($pdo)) { require_once __DIR__ . '/../config/database.php'; }

$act = $_GET['act'] ?? 'list';
$id_mhs = $_GET['id'] ?? null;

// Logic Dropdown (Tetap dari tabel asli)
if ($act === 'create' || $act === 'edit') {
    $stmtJur = $pdo->query("SELECT * FROM jurusan ORDER BY nama_jurusan ASC");
    $optJurusan = $stmtJur->fetchAll(PDO::FETCH_ASSOC);
    $stmtKls = $pdo->query("SELECT * FROM kelas ORDER BY nama_kelas ASC");
    $optKelas = $stmtKls->fetchAll(PDO::FETCH_ASSOC);
}
?>

<style>
    .modal-backdrop { z-index: 1040 !important; }
    .modal { z-index: 1050 !important; }
    .img-avatar { width: 50px; height: 50px; object-fit: cover; border-radius: 50%; }
    .img-detail { max-width: 100%; height: auto; max-height: 300px; object-fit: contain; }
</style>

<?php if ($act === 'list'): ?>
    <?php
    // [TUGAS 5.2.2] MATERIALIZED VIEW
    // Menggunakan mv_mahasiswa_stats
    $sql = "SELECT * FROM public.mv_mahasiswa_stats ORDER BY nim ASC";
    $stmt = $pdo->query($sql);
    $dataMahasiswa = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="card-custom mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1 fw-bold"><i class="bi bi-people me-2"></i>Data Mahasiswa (MV)</h5>
                <small class="text-muted">Menampilkan Snapshot IPK & SKS</small>
            </div>
            <div>
                <a href="index.php?page=mahasiswa&act=refresh" class="btn btn-warning btn-sm text-dark me-2">
                    <i class="bi bi-arrow-clockwise"></i> Refresh Data
                </a>
                <a href="?page=mahasiswa&act=create" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> Tambah
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
                        <th width="80" class="text-center">Foto</th>
                        <th>NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Jurusan / Kelas</th>
                        <th width="80" class="text-center">SKS</th>
                        <th width="80" class="text-center">IPK</th>
                        <th width="150" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($dataMahasiswa)): ?>
                        <?php foreach ($dataMahasiswa as $index => $mhs): ?>
                            <tr>
                                <td class="text-center"><?= $index + 1 ?></td>
                                <td class="text-center">
                                    <?php 
                                        $fotoPath = "uploads/" . ($mhs['foto'] ?? '');
                                        if (!empty($mhs['foto']) && file_exists($fotoPath)): 
                                    ?>
                                        <img src="<?= $fotoPath ?>" class="img-avatar">
                                    <?php else: ?>
                                        <i class="bi bi-person-circle fs-2 text-secondary"></i>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($mhs['nim']) ?></td>
                                <td><?= htmlspecialchars($mhs['nama_mahasiswa']) ?></td>
                                <td>
                                    <?= htmlspecialchars($mhs['nama_jurusan'] ?? '-') ?><br>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($mhs['nama_kelas'] ?? '-') ?></span>
                                </td>
                                <td class="text-center"><?= $mhs['total_sks'] ?></td>
                                <td class="text-center fw-bold text-primary"><?= number_format($mhs['ipk'], 2) ?></td>
                                
                                <td class="text-center">
                                    <a href="?page=mahasiswa&act=detail&id=<?= $mhs['id_mahasiswa'] ?>" class="btn btn-info btn-sm text-white"><i class="bi bi-eye"></i></a>
                                    <a href="?page=mahasiswa&act=edit&id=<?= $mhs['id_mahasiswa'] ?>" class="btn btn-warning btn-sm text-white"><i class="bi bi-pencil-square"></i></a>
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalHapus" data-href="index.php?page=mahasiswa&act=delete&id=<?= $mhs['id_mahasiswa'] ?>"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center text-muted">Belum ada data.</td></tr>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php elseif ($act === 'create'): ?>
    <div class="card-custom mb-4"><h5 class="mb-0 fw-bold">Tambah Mahasiswa</h5></div>
    <div class="card-custom">
        <form action="index.php?page=mahasiswa&act=store" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">NIM</label>
                    <input type="text" name="nim" class="form-control input-angka" maxlength="20" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama_mahasiswa" class="form-control" maxlength="100" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Foto</label>
                <input type="file" name="foto" class="form-control" accept="image/*">
            </div>
            <div class="mb-3">
                <label class="form-label d-block">Jenis Kelamin</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="jenis_kelamin" value="L" required> <label>Laki-laki</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="jenis_kelamin" value="P" required> <label>Perempuan</label>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Jurusan</label>
                    <select name="id_jurusan" class="form-select" required>
                        <?php foreach ($optJurusan as $jur): ?><option value="<?= $jur['id_jurusan'] ?>"><?= $jur['nama_jurusan'] ?></option><?php endforeach ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Kelas</label>
                    <select name="id_kelas" class="form-select" required>
                        <?php foreach ($optKelas as $kls): ?><option value="<?= $kls['id_kelas'] ?>"><?= $kls['nama_kelas'] ?></option><?php endforeach ?>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Angkatan</label>
                <input type="number" name="angkatan" class="form-control" value="<?= date('Y') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary px-4">Simpan</button>
            <a href="?page=mahasiswa" class="btn btn-secondary px-4">Kembali</a>
        </form>
    </div>

<?php elseif ($act === 'edit' && $id_mhs): ?>
    <?php
    $stmt = $pdo->prepare("SELECT * FROM mahasiswa WHERE id_mahasiswa = :id");
    $stmt->execute(['id' => $id_mhs]);
    $mhs = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$mhs) die("Data tidak ditemukan");
    ?>
    <div class="card-custom mb-4"><h5 class="mb-0 fw-bold">Edit Mahasiswa</h5></div>
    <div class="card-custom">
        <form action="index.php?page=mahasiswa&act=update" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_mahasiswa" value="<?= $mhs['id_mahasiswa'] ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">NIM</label>
                    <input type="text" name="nim" value="<?= htmlspecialchars($mhs['nim']) ?>" class="form-control input-angka" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="nama_mahasiswa" value="<?= htmlspecialchars($mhs['nama_mahasiswa']) ?>" class="form-control" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Foto</label>
                <input type="file" name="foto" class="form-control" accept="image/*">
            </div>
            <div class="mb-3">
                <label class="form-label d-block">Jenis Kelamin</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="jenis_kelamin" value="L" <?= ($mhs['jenis_kelamin']=='L')?'checked':'' ?>> <label>Laki-laki</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="jenis_kelamin" value="P" <?= ($mhs['jenis_kelamin']=='P')?'checked':'' ?>> <label>Perempuan</label>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Jurusan</label>
                    <select name="id_jurusan" class="form-select" required>
                        <?php foreach ($optJurusan as $jur): ?>
                            <option value="<?= $jur['id_jurusan'] ?>" <?= ($jur['id_jurusan']==$mhs['id_jurusan'])?'selected':'' ?>><?= $jur['nama_jurusan'] ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Kelas</label>
                    <select name="id_kelas" class="form-select" required>
                        <?php foreach ($optKelas as $kls): ?>
                            <option value="<?= $kls['id_kelas'] ?>" <?= ($kls['id_kelas']==$mhs['id_kelas'])?'selected':'' ?>><?= $kls['nama_kelas'] ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Angkatan</label>
                <input type="number" name="angkatan" value="<?= $mhs['angkatan'] ?>" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control" rows="3"><?= htmlspecialchars($mhs['alamat'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary px-4">Update</button>
            <a href="?page=mahasiswa" class="btn btn-secondary px-4">Kembali</a>
        </form>
    </div>

<?php elseif ($act === 'detail' && $id_mhs): ?>
    <?php
    // Detail pakai MV agar konsisten
    $stmt = $pdo->prepare("SELECT * FROM public.mv_mahasiswa_stats WHERE id_mahasiswa = :id");
    $stmt->execute(['id' => $id_mhs]);
    $mhs = $stmt->fetch(PDO::FETCH_ASSOC);
    // Fallback jika belum refresh
    if (!$mhs) {
        $stmt = $pdo->prepare("SELECT * FROM view_info_mahasiswa WHERE id_mahasiswa = :id");
        $stmt->execute(['id' => $id_mhs]);
        $mhs = $stmt->fetch(PDO::FETCH_ASSOC);
        $mhs['total_sks'] = 0; $mhs['ipk'] = 0;
    }
    ?>
    <div class="card-custom mb-4"><h5 class="mb-0 fw-bold">Detail Mahasiswa</h5></div>
    <div class="card-custom">
        <div class="row">
            <div class="col-md-4 text-center mb-4">
                <div class="border p-2 rounded shadow-sm d-inline-block bg-light">
                    <?php $fotoPath = "uploads/" . ($mhs['foto'] ?? ''); ?>
                    <?php if (!empty($mhs['foto']) && file_exists($fotoPath)): ?>
                        <img src="<?= $fotoPath ?>" class="img-detail">
                    <?php else: ?>
                        <i class="bi bi-person-circle fs-1 text-muted"></i>
                    <?php endif; ?>
                </div>
                <h3 class="mt-3 text-primary fw-bold">IPK: <?= number_format($mhs['ipk'] ?? 0, 2) ?></h3>
                <p>Total SKS: <?= $mhs['total_sks'] ?? 0 ?></p>
            </div>
            <div class="col-md-8">
                <table class="table table-bordered">
                    <tr><th width="200" class="bg-light">NIM</th><td><?= htmlspecialchars($mhs['nim']) ?></td></tr>
                    <tr><th class="bg-light">Nama</th><td><?= htmlspecialchars($mhs['nama_mahasiswa']) ?></td></tr>
                    <tr><th class="bg-light">Jurusan</th><td><?= htmlspecialchars($mhs['nama_jurusan'] ?? '-') ?></td></tr>
                    <tr><th class="bg-light">Kelas</th><td><?= htmlspecialchars($mhs['nama_kelas'] ?? '-') ?></td></tr>
                    <tr><th class="bg-light">Angkatan</th><td><?= htmlspecialchars($mhs['angkatan'] ?? '-') ?></td></tr>
                    <tr><th class="bg-light">Alamat</th><td><?= htmlspecialchars($mhs['alamat'] ?? '-') ?></td></tr>
                </table>
                <div class="mt-4">
                    <a href="?page=mahasiswa&act=edit&id=<?= $mhs['id_mahasiswa'] ?>" class="btn btn-warning text-white px-4">Edit</a>
                    <a href="?page=mahasiswa" class="btn btn-secondary px-4">Kembali</a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="modal fade" id="modalHapus" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title text-danger">Konfirmasi Hapus</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body"><p>Yakin ingin menghapus data ini?</p></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" class="btn btn-danger" id="btn-confirm-hapus">Ya, Hapus</a>
            </div>
        </div>
    </div>
</div>

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
    Swal.fire({
        icon: '<?= $_SESSION['flash']['type'] == 'danger' ? 'error' : 'info' ?>',
        title: '<?= $_SESSION['flash']['message'] ?>',
        toast: true, position: 'top-end', showConfirmButton: false, timer: 3000
    });
    <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
});
</script>
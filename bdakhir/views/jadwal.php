<?php
// views/jadwal.php
global $pdo;
if (empty($pdo)) { require_once __DIR__ . '/../config/database.php'; }

$act = $_GET['act'] ?? 'list';
$id_jadwal = $_GET['id'] ?? null;

// Helper Dropdown
if ($act === 'create' || $act === 'edit') {
    $optMatkul = $pdo->query("SELECT * FROM mata_kuliah ORDER BY nama_matkul ASC")->fetchAll(PDO::FETCH_ASSOC);
    $optDosen = $pdo->query("SELECT * FROM dosen ORDER BY nama_dosen ASC")->fetchAll(PDO::FETCH_ASSOC);
    $optKelas = $pdo->query("SELECT * FROM kelas ORDER BY nama_kelas ASC")->fetchAll(PDO::FETCH_ASSOC);
    $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
}
?>

<style>
    .modal-backdrop { z-index: 1040 !important; }
    .modal { z-index: 1050 !important; }
</style>

<?php if ($act === 'list'): ?>
    <?php
    // [TUGAS 5.2.2] MATERIALIZED VIEW JADWAL
    // Menggunakan mv_jadwal_stats
    $sql = "SELECT * FROM public.mv_jadwal_stats 
            ORDER BY 
            CASE 
                WHEN hari = 'Senin' THEN 1 
                WHEN hari = 'Selasa' THEN 2 
                WHEN hari = 'Rabu' THEN 3 
                WHEN hari = 'Kamis' THEN 4 
                WHEN hari = 'Jumat' THEN 5 
                ELSE 6 
            END, jam_mulai ASC";
    $stmt = $pdo->query($sql);
    $dataJadwal = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="card-custom mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1 fw-bold"><i class="bi bi-calendar-week me-2"></i>Jadwal Kuliah (MV)</h5>
                <small class="text-muted">Estimasi Kepadatan Peserta</small>
            </div>
            <div>
                <a href="index.php?page=jadwal&act=refresh" class="btn btn-warning btn-sm text-dark me-2">
                    <i class="bi bi-arrow-clockwise"></i> Refresh Stats
                </a>
                <a href="?page=jadwal&act=create" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> Tambah Jadwal
                </a>
            </div>
        </div>
    </div>

    <div class="card-custom">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="100">Hari</th>
                        <th width="150" class="text-center">Jam</th>
                        <th>Mata Kuliah</th>
                        <th>Kelas</th>
                        <th>Dosen Pengampu</th>
                        <th width="80" class="text-center">Peserta</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($dataJadwal)): ?>
                        <?php foreach ($dataJadwal as $row): ?>
                            <tr>
                                <td class="fw-bold"><?= htmlspecialchars($row['hari']) ?></td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-clock me-1"></i>
                                        <?= substr($row['jam_mulai'], 0, 5) ?> - <?= substr($row['jam_selesai'], 0, 5) ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($row['nama_matkul']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($row['kode_matkul']) ?> (<?= $row['sks'] ?> SKS)</small>
                                </td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($row['nama_kelas']) ?></span></td>
                                <td>
                                    <?= htmlspecialchars($row['nama_dosen']) ?><br>
                                    <small class="text-muted">NIDN: <?= htmlspecialchars($row['nidn']) ?></small>
                                </td>
                                
                                <td class="text-center">
                                    <span class="badge bg-info text-dark rounded-pill" title="Estimasi Mahasiswa di Kelas Ini">
                                        <i class="bi bi-people-fill me-1"></i><?= $row['total_peserta'] ?>
                                    </span>
                                </td>

                                <td class="text-center">
                                    <a href="?page=jadwal&act=edit&id=<?= $row['id_jadwal'] ?>" class="btn btn-warning btn-sm text-white"><i class="bi bi-pencil-square"></i></a>
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalHapus" data-href="index.php?page=jadwal&act=delete&id=<?= $row['id_jadwal'] ?>"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center text-muted">Belum ada jadwal.</td></tr>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>

<?php elseif ($act === 'create'): ?>
    <div class="card-custom mb-4"><h5 class="mb-0 fw-bold">Tambah Jadwal</h5></div>
    <div class="card-custom">
        <form action="index.php?page=jadwal&act=store" method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mata Kuliah</label>
                    <select name="id_matkul" class="form-select" required>
                        <option value="">-- Pilih Matkul --</option>
                        <?php foreach ($optMatkul as $m): ?><option value="<?= $m['id_matkul'] ?>"><?= $m['kode_matkul'] ?> - <?= $m['nama_matkul'] ?></option><?php endforeach ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Kelas</label>
                    <select name="id_kelas" class="form-select" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach ($optKelas as $k): ?><option value="<?= $k['id_kelas'] ?>"><?= $k['nama_kelas'] ?></option><?php endforeach ?>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Dosen Pengampu</label>
                <select name="id_dosen" class="form-select" required>
                    <option value="">-- Pilih Dosen --</option>
                    <?php foreach ($optDosen as $d): ?><option value="<?= $d['id_dosen'] ?>"><?= $d['nama_dosen'] ?></option><?php endforeach ?>
                </select>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Hari</label>
                    <select name="hari" class="form-select" required>
                        <?php foreach ($hariList as $h): ?><option value="<?= $h ?>"><?= $h ?></option><?php endforeach ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Jam Mulai</label>
                    <input type="time" name="jam_mulai" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Jam Selesai</label>
                    <input type="time" name="jam_selesai" class="form-control" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary px-4">Simpan</button>
            <a href="?page=jadwal" class="btn btn-secondary px-4">Kembali</a>
        </form>
    </div>

<?php elseif ($act === 'edit' && $id_jadwal): ?>
    <?php
    $stmt = $pdo->prepare("SELECT * FROM jadwal WHERE id_jadwal = :id");
    $stmt->execute(['id' => $id_jadwal]);
    $jadwal = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$jadwal) die("Data tidak ditemukan");
    ?>
    <div class="card-custom mb-4"><h5 class="mb-0 fw-bold">Edit Jadwal</h5></div>
    <div class="card-custom">
        <form action="index.php?page=jadwal&act=update" method="POST">
            <input type="hidden" name="id_jadwal" value="<?= $jadwal['id_jadwal'] ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mata Kuliah</label>
                    <select name="id_matkul" class="form-select" required>
                        <?php foreach ($optMatkul as $m): ?>
                            <option value="<?= $m['id_matkul'] ?>" <?= ($m['id_matkul'] == $jadwal['id_matkul']) ? 'selected' : '' ?>><?= $m['kode_matkul'] ?> - <?= $m['nama_matkul'] ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Kelas</label>
                    <select name="id_kelas" class="form-select" required>
                        <?php foreach ($optKelas as $k): ?>
                            <option value="<?= $k['id_kelas'] ?>" <?= ($k['id_kelas'] == $jadwal['id_kelas']) ? 'selected' : '' ?>><?= $k['nama_kelas'] ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Dosen Pengampu</label>
                <select name="id_dosen" class="form-select" required>
                    <?php foreach ($optDosen as $d): ?>
                        <option value="<?= $d['id_dosen'] ?>" <?= ($d['id_dosen'] == $jadwal['id_dosen']) ? 'selected' : '' ?>><?= $d['nama_dosen'] ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Hari</label>
                    <select name="hari" class="form-select" required>
                        <?php foreach ($hariList as $h): ?>
                            <option value="<?= $h ?>" <?= ($h == $jadwal['hari']) ? 'selected' : '' ?>><?= $h ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Jam Mulai</label>
                    <input type="time" name="jam_mulai" value="<?= $jadwal['jam_mulai'] ?>" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Jam Selesai</label>
                    <input type="time" name="jam_selesai" value="<?= $jadwal['jam_selesai'] ?>" class="form-control" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary px-4">Update</button>
            <a href="?page=jadwal" class="btn btn-secondary px-4">Kembali</a>
        </form>
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
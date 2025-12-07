<?php
// views/dosen.php

// 1. Pastikan koneksi database tersedia & Global Variable
global $pdo;
if (empty($pdo)) {
    require_once __DIR__ . '/../config/database.php';
}

$act = $_GET['act'] ?? 'list';
$id_dosen = $_GET['id'] ?? null;

// 2. Logic Dropdown (Hanya saat Create/Edit)
if ($act === 'create' || $act === 'edit') {
    $stmtJur = $pdo->query("SELECT * FROM jurusan ORDER BY nama_jurusan ASC");
    $optJurusan = $stmtJur->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?php if ($act === 'list'): ?>
    <?php
    $sql = "SELECT d.*, j.nama_jurusan 
            FROM dosen d 
            LEFT JOIN jurusan j ON d.id_jurusan = j.id_jurusan 
            ORDER BY d.nama_dosen ASC";
    $stmt = $pdo->query($sql);
    $dataDosen = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="card-custom mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-person-badge me-2"></i>Data Dosen</h5>
            <a href="index.php?page=dosen&act=create" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Tambah Dosen
            </a>
        </div>
    </div>

    <div class="card-custom">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="50" class="text-center">No</th>
                        <th>NIP</th>
                        <th>Nama Dosen</th>
                        <th>Telepon</th>
                        <th>Jurusan</th>
                        <th width="180" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($dataDosen)): ?>
                        <?php foreach ($dataDosen as $index => $dsn): ?>
                            <tr>
                                <td class="text-center"><?= $index + 1 ?></td>
                                <td ><?= htmlspecialchars($dsn['nip'] ?? $dsn['nidn'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($dsn['nama_dosen'] ?? '') ?></td>
                                <td><?= htmlspecialchars($dsn['telepon'] ?? $dsn['no_hp'] ?? '-') ?></td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($dsn['nama_jurusan'] ?? 'Umum') ?></span></td>
                                <td class="text-center">
                                    <a href="index.php?page=dosen&act=detail&id=<?= $dsn['id_dosen'] ?>" class="btn btn-info btn-sm text-white" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="index.php?page=dosen&act=edit&id=<?= $dsn['id_dosen'] ?>" class="btn btn-warning btn-sm text-white" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    
                                    <button type="button" 
                                            class="btn btn-danger btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalHapus"
                                            data-href="index.php?page=dosen&act=delete&id=<?= $dsn['id_dosen'] ?>"
                                            title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada data dosen.</td>
                        </tr>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>

<?php elseif ($act === 'create'): ?>
    <div class="card-custom mb-4">
        <h5 class="mb-0 fw-bold"><i class="bi bi-person-plus me-2"></i>Tambah Dosen</h5>
    </div>

    <div class="card-custom">
        <form action="index.php?page=dosen&act=store" method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">NIP <span class="text-danger">*</span></label>
                    <input type="text" name="nip" class="form-control input-angka" 
                           placeholder="Maksimal 18 digit angka" maxlength="18" required>
                    <small class="text-muted">Wajib angka (0-9).</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama_dosen" class="form-control" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nomor Telepon</label>
                    <input type="text" name="telepon" class="form-control input-angka" 
                           placeholder="Maksimal 12 digit angka" maxlength="12">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Jurusan <span class="text-danger">*</span></label>
                    <select name="id_jurusan" class="form-select" required>
                        <option value="">-- Pilih Jurusan --</option>
                        <?php foreach ($optJurusan as $jur): ?>
                            <option value="<?= $jur['id_jurusan'] ?>"><?= $jur['nama_jurusan'] ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i> Simpan
                </button>
                <a href="index.php?page=dosen" class="btn btn-secondary px-4">Kembali</a>
            </div>
        </form>
    </div>

<?php elseif ($act === 'edit' && $id_dosen): ?>
    <?php
    $stmt = $pdo->prepare("SELECT * FROM dosen WHERE id_dosen = :id");
    $stmt->execute(['id' => $id_dosen]);
    $dsn = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$dsn) {
        echo "<div class='alert alert-danger'>Data dosen tidak ditemukan!</div>";
        exit;
    }
    ?>

    <div class="card-custom mb-4">
        <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Dosen</h5>
    </div>

    <div class="card-custom">
        <form action="index.php?page=dosen&act=update" method="POST">
            <input type="hidden" name="id_dosen" value="<?= $dsn['id_dosen'] ?>">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">NIP <span class="text-danger">*</span></label>
                    <input type="text" name="nip" value="<?= htmlspecialchars($dsn['nip'] ?? $dsn['nidn'] ?? '') ?>" 
                           class="form-control input-angka" maxlength="18" required>
                    <small class="text-muted">Wajib angka (0-9).</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama_dosen" value="<?= htmlspecialchars($dsn['nama_dosen'] ?? '') ?>" class="form-control" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nomor Telepon</label>
                    <input type="text" name="telepon" value="<?= htmlspecialchars($dsn['telepon'] ?? $dsn['no_hp'] ?? '') ?>" 
                           class="form-control input-angka" maxlength="12">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Jurusan <span class="text-danger">*</span></label>
                    <select name="id_jurusan" class="form-select" required>
                        <option value="">-- Pilih Jurusan --</option>
                        <?php foreach ($optJurusan as $jur): ?>
                            <option value="<?= $jur['id_jurusan'] ?>" <?= ($jur['id_jurusan'] == ($dsn['id_jurusan'] ?? '')) ? 'selected' : '' ?>>
                                <?= $jur['nama_jurusan'] ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check2-circle me-1"></i> Update
                </button>
                <a href="index.php?page=dosen" class="btn btn-secondary px-4">Kembali</a>
            </div>
        </form>
    </div>

<?php elseif ($act === 'detail' && $id_dosen): ?>
    <?php
    $sql = "SELECT d.*, j.nama_jurusan 
            FROM dosen d 
            LEFT JOIN jurusan j ON d.id_jurusan = j.id_jurusan 
            WHERE d.id_dosen = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id_dosen]);
    $dsn = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$dsn) {
        echo "<div class='alert alert-danger'>Data tidak ditemukan!</div>";
        exit;
    }
    ?>

    <div class="card-custom mb-4">
        <h5 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2"></i>Detail Dosen</h5>
    </div>

    <div class="card-custom">
        <div class="row">
            <div class="col-md-8">
                <table class="table table-bordered">
                    <tr>
                        <th width="200" class="bg-light">ID Dosen</th>
                        <td><?= $dsn['id_dosen'] ?></td>
                    </tr>
                    <tr>
                        <th class="bg-light">NIP</th>
                        <td class="fw-bold text-primary"><?= htmlspecialchars($dsn['nip'] ?? $dsn['nidn'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Nama Lengkap</th>
                        <td><?= htmlspecialchars($dsn['nama_dosen'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Jurusan</th>
                        <td><?= htmlspecialchars($dsn['nama_jurusan'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Telepon</th>
                        <td><?= htmlspecialchars($dsn['telepon'] ?? $dsn['no_hp'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Tanggal Dibuat</th>
                        <td><?= isset($dsn['created_at']) ? date('d F Y, H:i', strtotime($dsn['created_at'])) : '-' ?></td>
                    </tr>
                </table>

                <div class="mt-4">
                    <a href="index.php?page=dosen&act=edit&id=<?= $dsn['id_dosen'] ?>" class="btn btn-warning text-white px-4">
                        <i class="bi bi-pencil-square me-1"></i> Edit
                    </a>
                    <a href="index.php?page=dosen" class="btn btn-secondary px-4">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
            
            <div class="col-md-4 text-center">
                <div class="border p-3 rounded bg-light">
                    <i class="bi bi-person-circle text-secondary" style="font-size: 8rem;"></i>
                    <p class="text-muted mt-2">Profil Dosen</p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>


<div class="modal fade" id="modalHapus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data dosen ini?</p>
                <small class="text-muted">Data yang dihapus tidak dapat dikembalikan.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" class="btn btn-danger" id="btn-confirm-hapus">Ya, Hapus</a>
            </div>
        </div>
    </div>
</div>

<?php if (isset($_SESSION['flash'])): ?>
    <div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pt-0 pb-4">
                    <?php if ($_SESSION['flash']['type'] == 'success'): ?>
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                        <h4 class="mt-3 fw-bold">Berhasil!</h4>
                    <?php else: ?>
                        <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
                        <h4 class="mt-3 fw-bold">Gagal!</h4>
                    <?php endif; ?>
                    <p class="mt-2 mb-4 text-muted fs-5"><?= $_SESSION['flash']['message'] ?></p>
                    <button type="button" class="btn btn-<?= $_SESSION['flash']['type'] == 'success' ? 'success' : 'danger' ?> px-5" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var myModal = new bootstrap.Modal(document.getElementById('statusModal'));
            myModal.show();
        });
    </script>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Logic Modal Hapus (Memindahkan href dari tombol ke modal)
        var modalHapus = document.getElementById('modalHapus');
        modalHapus.addEventListener('show.bs.modal', function (event) {
            // Tombol yang memicu modal
            var button = event.relatedTarget;
            // Ambil data-href
            var urlToDelete = button.getAttribute('data-href');
            // Update link di tombol 'Ya, Hapus' dalam modal
            var confirmBtn = modalHapus.querySelector('#btn-confirm-hapus');
            confirmBtn.setAttribute('href', urlToDelete);
        });

        // 2. Logic Validasi Input Angka
        const inputAngka = document.querySelectorAll('.input-angka');
        inputAngka.forEach(function(input) {
            input.addEventListener('input', function(e) {
                if (/[^0-9]/.test(this.value)) {
                    this.value = this.value.replace(/[^0-9]/g, '');
                    const ToastWarning = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: false
                    });
                    ToastWarning.fire({
                        icon: 'warning',
                        title: 'Hanya boleh diisi angka!'
                    });
                }
            });
        });
    });
</script>
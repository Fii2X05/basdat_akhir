<?php
// views/mahasiswa.php
global $pdo; 
if (empty($pdo)) { require_once __DIR__ . '/../config/database.php'; }

$act = $_GET['act'] ?? 'list';
$id_mhs = $_GET['id'] ?? null;

// Helper function untuk display jurusan & kelas
if ($act === 'create' || $act === 'edit') {
    $stmtJur = $pdo->query("SELECT * FROM jurusan ORDER BY nama_jurusan ASC");
    $optJurusan = $stmtJur->fetchAll(PDO::FETCH_ASSOC);

    $stmtKls = $pdo->query("SELECT * FROM kelas ORDER BY nama_kelas ASC");
    $optKelas = $stmtKls->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?php if ($act === 'list'): ?>
    <?php
    $sql = "SELECT m.*, j.nama_jurusan, k.nama_kelas 
            FROM mahasiswa m 
            LEFT JOIN jurusan j ON m.id_jurusan = j.id_jurusan 
            LEFT JOIN kelas k ON m.id_kelas = k.id_kelas 
            ORDER BY m.nim ASC";
    $stmt = $pdo->query($sql);
    $dataMahasiswa = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="card-custom mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Data Mahasiswa</h5>
            <a href="?page=mahasiswa&act=create" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle"></i> Tambah Mahasiswa
            </a>
        </div>
    </div>

    <div class="card-custom">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th width="50">No</th>
                    <th width="80">Foto</th> <th>NIM</th>
                    <th>Nama Mahasiswa</th>
                    <th>L/P</th>
                    <th>Jurusan</th>
                    <th>Kelas</th>
                    <th width="200">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($dataMahasiswa)): ?>
                    <?php foreach ($dataMahasiswa as $index => $mhs): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td class="text-center">
                                <?php if (!empty($mhs['foto']) && file_exists("uploads/" . $mhs['foto'])): ?>
                                    <img src="uploads/<?= $mhs['foto'] ?>" alt="Foto" width="50" height="50" class="rounded-circle" style="object-fit: cover;">
                                <?php else: ?>
                                    <img src="https://via.placeholder.com/50?text=No+Img" alt="No Foto" class="rounded-circle">
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($mhs['nim']) ?></td>
                            <td><?= htmlspecialchars($mhs['nama_mahasiswa']) ?></td>
                            <td><?= $mhs['jenis_kelamin'] ?></td>
                            <td><?= htmlspecialchars($mhs['nama_jurusan'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($mhs['nama_kelas'] ?? '-') ?></td>
                            <td>
                                <a href="?page=mahasiswa&act=detail&id=<?= $mhs['id_mahasiswa'] ?>" class="btn btn-info btn-sm text-white" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="?page=mahasiswa&act=edit&id=<?= $mhs['id_mahasiswa'] ?>" class="btn btn-warning btn-sm" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="?page=mahasiswa&act=delete&id=<?= $mhs['id_mahasiswa'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus mahasiswa ini?');" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">Tidak ada data mahasiswa.</td>
                    </tr>
                <?php endif ?>
            </tbody>
        </table>
    </div>

<?php elseif ($act === 'create'): ?>
    <div class="card-custom mb-4">
        <h5 class="mb-0">Tambah Mahasiswa</h5>
    </div>
    <div class="card-custom">
        <form action="index.php?page=mahasiswa&act=store" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">NIM</label>
                    <input type="text" name="nim" class="form-control" maxlength="20" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama_mahasiswa" class="form-control" maxlength="100" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Foto Mahasiswa (Opsional)</label>
                <input type="file" name="foto" class="form-control" accept="image/*">
                <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 2MB.</small>
            </div>

            <div class="mb-3">
                <label class="form-label d-block">Jenis Kelamin</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="jenis_kelamin" value="L" required>
                    <label class="form-check-label">Laki-laki</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="jenis_kelamin" value="P" required>
                    <label class="form-check-label">Perempuan</label>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Jurusan</label>
                    <select name="id_jurusan" class="form-select" required>
                        <option value="">-- Pilih Jurusan --</option>
                        <?php foreach ($optJurusan as $jur): ?>
                            <option value="<?= $jur['id_jurusan'] ?>"><?= $jur['nama_jurusan'] ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Kelas</label>
                    <select name="id_kelas" class="form-select" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach ($optKelas as $kls): ?>
                            <option value="<?= $kls['id_kelas'] ?>"><?= $kls['nama_kelas'] ?></option>
                        <?php endforeach ?>
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

            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
            <a href="?page=mahasiswa" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

<?php elseif ($act === 'edit' && $id_mhs): ?>
    <?php
    $stmt = $pdo->prepare("SELECT * FROM mahasiswa WHERE id_mahasiswa = :id");
    $stmt->execute(['id' => $id_mhs]);
    $mhs = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$mhs) die("Data tidak ditemukan");
    ?>
    <div class="card-custom mb-4">
        <h5 class="mb-0">Edit Mahasiswa</h5>
    </div>
    <div class="card-custom">
        <form action="index.php?page=mahasiswa&act=update" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_mahasiswa" value="<?= $mhs['id_mahasiswa'] ?>">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">NIM</label>
                    <input type="text" name="nim" value="<?= htmlspecialchars($mhs['nim']) ?>" class="form-control" maxlength="20" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama_mahasiswa" value="<?= htmlspecialchars($mhs['nama_mahasiswa']) ?>" class="form-control" maxlength="100" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Foto Mahasiswa</label>
                <div class="mb-2">
                    <?php if (!empty($mhs['foto']) && file_exists("uploads/" . $mhs['foto'])): ?>
                        <img src="uploads/<?= $mhs['foto'] ?>" alt="Foto Lama" width="100" class="img-thumbnail">
                        <small class="d-block text-muted">Foto saat ini</small>
                    <?php else: ?>
                        <span class="text-muted fst-italic">Belum ada foto</span>
                    <?php endif; ?>
                </div>
                <input type="file" name="foto" class="form-control" accept="image/*">
                <small class="text-muted">Biarkan kosong jika tidak ingin mengubah foto.</small>
            </div>

            <div class="mb-3">
                <label class="form-label d-block">Jenis Kelamin</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="jenis_kelamin" value="L" <?= ($mhs['jenis_kelamin'] == 'L') ? 'checked' : '' ?>>
                    <label class="form-check-label">Laki-laki</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="jenis_kelamin" value="P" <?= ($mhs['jenis_kelamin'] == 'P') ? 'checked' : '' ?>>
                    <label class="form-check-label">Perempuan</label>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Jurusan</label>
                    <select name="id_jurusan" class="form-select" required>
                        <option value="">-- Pilih Jurusan --</option>
                        <?php foreach ($optJurusan as $jur): ?>
                            <option value="<?= $jur['id_jurusan'] ?>" <?= ($jur['id_jurusan'] == $mhs['id_jurusan']) ? 'selected' : '' ?>>
                                <?= $jur['nama_jurusan'] ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Kelas</label>
                    <select name="id_kelas" class="form-select" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach ($optKelas as $kls): ?>
                            <option value="<?= $kls['id_kelas'] ?>" <?= ($kls['id_kelas'] == $mhs['id_kelas']) ? 'selected' : '' ?>>
                                <?= $kls['nama_kelas'] ?>
                            </option>
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
                <textarea name="alamat" class="form-control" rows="3"><?= htmlspecialchars($mhs['alamat']) ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary"><i class="bi bi-pencil-square"></i> Update</button>
            <a href="?page=mahasiswa" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

<?php elseif ($act === 'detail' && $id_mhs): ?>
    <?php
    $sql = "SELECT m.*, j.nama_jurusan, k.nama_kelas 
            FROM mahasiswa m 
            LEFT JOIN jurusan j ON m.id_jurusan = j.id_jurusan 
            LEFT JOIN kelas k ON m.id_kelas = k.id_kelas 
            WHERE m.id_mahasiswa = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id_mhs]);
    $mhs = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$mhs) die("Data tidak ditemukan");
    ?>
    <div class="card-custom mb-4">
        <h5 class="mb-0">Detail Mahasiswa</h5>
    </div>
    <div class="card-custom">
        <div class="row">
            <div class="col-md-4 text-center mb-3">
                <?php if (!empty($mhs['foto']) && file_exists("uploads/" . $mhs['foto'])): ?>
                    <img src=".../uploads/<?= $mhs['foto'] ?>" alt="Foto Mahasiswa" class="img-fluid rounded shadow-sm" style="max-height: 300px;">
                <?php else: ?>
                    <img src="https://via.placeholder.com/200x250?text=No+Photo" alt="No Foto" class="img-fluid rounded shadow-sm">
                <?php endif; ?>
            </div>
            
            <div class="col-md-8">
                <table class="table table-bordered">
                    <tr><th width="200">NIM</th><td><?= htmlspecialchars($mhs['nim']) ?></td></tr>
                    <tr><th>Nama</th><td><?= htmlspecialchars($mhs['nama_mahasiswa']) ?></td></tr>
                    <tr><th>Jenis Kelamin</th><td><?= ($mhs['jenis_kelamin'] == 'L') ? 'Laki-laki' : 'Perempuan' ?></td></tr>
                    <tr><th>Jurusan</th><td><?= htmlspecialchars($mhs['nama_jurusan'] ?? '-') ?></td></tr>
                    <tr><th>Kelas</th><td><?= htmlspecialchars($mhs['nama_kelas'] ?? '-') ?></td></tr>
                    <tr><th>Angkatan</th><td><?= $mhs['angkatan'] ?></td></tr>
                    <tr><th>Alamat</th><td><?= htmlspecialchars($mhs['alamat'] ?? '-') ?></td></tr>
                    <tr><th>Terdaftar</th><td><?= $mhs['created_at'] ?></td></tr>
                </table>
                <a href="?page=mahasiswa&act=edit&id=<?= $mhs['id_mahasiswa'] ?>" class="btn btn-warning">
                    <i class="bi bi-pencil-square"></i> Edit
                </a>
                <a href="?page=mahasiswa" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    <?php if (isset($_SESSION['flash'])): ?>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
        Toast.fire({
            icon: '<?= $_SESSION['flash']['type'] ?>',
            title: '<?= $_SESSION['flash']['message'] ?>'
        });
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
</script>
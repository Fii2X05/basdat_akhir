<?php
// views/transkrip.php
global $pdo;
if (empty($pdo)) { require_once __DIR__ . '/../config/database.php'; }

$id_mhs = $_GET['id_mhs'] ?? null;

// Ambil daftar semua mahasiswa untuk dropdown pencarian
$stmtAllMhs = $pdo->query("SELECT id_mahasiswa, nim, nama_mahasiswa FROM mahasiswa ORDER BY nama_mahasiswa ASC");
$listMahasiswa = $stmtAllMhs->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    /* Sembunyikan elemen non-penting saat print */
    @media print {
        .no-print, .sidebar, .navbar { display: none !important; }
        .card-custom { box-shadow: none; border: none; }
        .content-area { width: 100%; padding: 0; }
        body { background: white; }
    }
</style>

<div class="card-custom mb-4 no-print">
    <h5 class="mb-3 fw-bold"><i class="bi bi-file-earmark-text me-2"></i>Cetak Transkrip Nilai</h5>
    <form action="" method="GET" class="row g-3 align-items-end">
        <input type="hidden" name="page" value="transkrip">
        <div class="col-md-6">
            <label class="form-label">Pilih Mahasiswa</label>
            <select name="id_mhs" class="form-select" onchange="this.form.submit()">
                <option value="">-- Pilih Mahasiswa --</option>
                <?php foreach ($listMahasiswa as $m): ?>
                    <option value="<?= $m['id_mahasiswa'] ?>" <?= ($id_mhs == $m['id_mahasiswa']) ? 'selected' : '' ?>>
                        <?= $m['nim'] ?> - <?= $m['nama_mahasiswa'] ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Cari</button>
        </div>
    </form>
</div>

<?php if ($id_mhs): ?>
    <?php
    // 1. Ambil Data Mahasiswa
    $stmtMhs = $pdo->prepare("
        SELECT m.*, j.nama_jurusan, k.nama_kelas 
        FROM mahasiswa m 
        LEFT JOIN jurusan j ON m.id_jurusan = j.id_jurusan 
        LEFT JOIN kelas k ON m.id_kelas = k.id_kelas
        WHERE m.id_mahasiswa = :id
    ");
    $stmtMhs->execute(['id' => $id_mhs]);
    $mhs = $stmtMhs->fetch(PDO::FETCH_ASSOC);

    // 2. Ambil Nilai Transkrip
    $stmtNilai = $pdo->prepare("
        SELECT mk.kode_matkul, mk.nama_matkul, mk.sks, mk.semester, n.nilai_huruf, n.nilai_angka
        FROM nilai n
        JOIN mata_kuliah mk ON n.id_matkul = mk.id_matkul
        WHERE n.id_mahasiswa = :id
        ORDER BY mk.semester ASC, mk.nama_matkul ASC
    ");
    $stmtNilai->execute(['id' => $id_mhs]);
    $transkrip = $stmtNilai->fetchAll(PDO::FETCH_ASSOC);

    // Helper Konversi Huruf ke Bobot
    function getBobot($huruf) {
        switch ($huruf) {
            case 'A': return 4.0;
            case 'B': return 3.0;
            case 'C': return 2.0;
            case 'D': return 1.0;
            default: return 0.0;
        }
    }
    ?>

    <?php if ($mhs): ?>
        <div class="card-custom">
            <div class="text-center mb-4">
                <h4 class="fw-bold text-uppercase">Transkrip Nilai Akademik</h4>
                <p class="mb-0">Tahun Ajaran <?= date('Y') ?></p>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-borderless table-sm">
                        <tr><td width="120">Nama</td><td>: <strong><?= htmlspecialchars($mhs['nama_mahasiswa']) ?></strong></td></tr>
                        <tr><td>NIM</td><td>: <?= htmlspecialchars($mhs['nim']) ?></td></tr>
                        <tr><td>Angkatan</td><td>: <?= htmlspecialchars($mhs['angkatan']) ?></td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless table-sm">
                        <tr><td width="120">Jurusan</td><td>: <?= htmlspecialchars($mhs['nama_jurusan'] ?? '-') ?></td></tr>
                        <tr><td>Kelas</td><td>: <?= htmlspecialchars($mhs['nama_kelas'] ?? '-') ?></td></tr>
                        <tr><td>Jenis Kelamin</td><td>: <?= ($mhs['jenis_kelamin'] == 'L') ? 'Laki-laki' : 'Perempuan' ?></td></tr>
                    </table>
                </div>
            </div>

            <table class="table table-bordered align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th width="50">No</th>
                        <th>Kode</th>
                        <th>Mata Kuliah</th>
                        <th width="80">Smt</th>
                        <th width="80">SKS</th>
                        <th width="80">Nilai</th>
                        <th width="80">Bobot</th>
                        <th width="80">Mutu (SKSxBobot)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totalSKS = 0;
                    $totalMutu = 0;
                    
                    if (!empty($transkrip)): 
                        foreach ($transkrip as $i => $row): 
                            $bobot = getBobot($row['nilai_huruf']);
                            $mutu = $row['sks'] * $bobot;
                            
                            $totalSKS += $row['sks'];
                            $totalMutu += $mutu;
                    ?>
                        <tr>
                            <td class="text-center"><?= $i + 1 ?></td>
                            <td class="text-center"><?= htmlspecialchars($row['kode_matkul']) ?></td>
                            <td><?= htmlspecialchars($row['nama_matkul']) ?></td>
                            <td class="text-center"><?= $row['semester'] ?></td>
                            <td class="text-center"><?= $row['sks'] ?></td>
                            <td class="text-center fw-bold"><?= $row['nilai_huruf'] ?></td>
                            <td class="text-center"><?= number_format($bobot, 1) ?></td>
                            <td class="text-center"><?= number_format($mutu, 1) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center text-muted">Belum ada data nilai.</td></tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="bg-light fw-bold">
                    <tr>
                        <td colspan="4" class="text-end">Total</td>
                        <td class="text-center"><?= $totalSKS ?></td>
                        <td colspan="2"></td>
                        <td class="text-center"><?= number_format($totalMutu, 1) ?></td>
                    </tr>
                </tfoot>
            </table>

            <div class="row mt-4">
                <div class="col-md-8">
                    <p class="text-muted small">
                        * Bobot Nilai: A=4, B=3, C=2, D=1, E=0<br>
                        * Mutu = SKS x Bobot<br>
                        * IPK = Total Mutu / Total SKS
                    </p>
                </div>
                <div class="col-md-4">
                    <div class="border p-3 rounded bg-light text-center">
                        <h6 class="mb-1">Indeks Prestasi Kumulatif (IPK)</h6>
                        <?php 
                        $ipk = ($totalSKS > 0) ? ($totalMutu / $totalSKS) : 0;
                        ?>
                        <h2 class="text-primary fw-bold mb-0"><?= number_format($ipk, 2) ?></h2>
                    </div>
                </div>
            </div>

            <div class="mt-4 text-end no-print">
                <button onclick="window.print()" class="btn btn-success">
                    <i class="bi bi-printer me-2"></i> Cetak Transkrip
                </button>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">Data mahasiswa tidak ditemukan.</div>
    <?php endif; ?>
<?php else: ?>
    <div class="alert alert-info text-center">
        <i class="bi bi-info-circle fs-4 d-block mb-2"></i>
        Silakan pilih mahasiswa terlebih dahulu untuk melihat transkrip nilai.
    </div>
<?php endif; ?>
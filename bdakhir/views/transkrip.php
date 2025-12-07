<?php
// views/transkrip.php - Academic Transcripts
global $pdo;
if (empty($pdo)) { require_once __DIR__ . '/../config/database.php'; }

$act = $_GET['act'] ?? 'list';
$id_mahasiswa = $_GET['id'] ?? null;

// Get list of students for selection
if ($act === 'list') {
    $stmtMhs = $pdo->query("
        SELECT m.*, j.nama_jurusan, k.nama_kelas 
        FROM mahasiswa m 
        LEFT JOIN jurusan j ON m.id_jurusan = j.id_jurusan 
        LEFT JOIN kelas k ON m.id_kelas = k.id_kelas 
        ORDER BY m.nama_mahasiswa ASC
    ");
    $dataMahasiswa = $stmtMhs->fetchAll(PDO::FETCH_ASSOC);
}

// Get transcript data for specific student
if ($act === 'view' && $id_mahasiswa) {
    // Get student info
    $stmtMhs = $pdo->prepare("
        SELECT m.*, j.nama_jurusan, k.nama_kelas 
        FROM mahasiswa m 
        LEFT JOIN jurusan j ON m.id_jurusan = j.id_jurusan 
        LEFT JOIN kelas k ON m.id_kelas = k.id_kelas 
        WHERE m.id_mahasiswa = :id
    ");
    $stmtMhs->execute(['id' => $id_mahasiswa]);
    $mahasiswa = $stmtMhs->fetch(PDO::FETCH_ASSOC);
    
    if (!$mahasiswa) {
        die("Mahasiswa tidak ditemukan");
    }
    
    // Get all grades grouped by semester
    $stmtNilai = $pdo->prepare("
        SELECT n.*, mk.kode_matkul, mk.nama_matkul, mk.sks, mk.semester
        FROM nilai n
        JOIN mata_kuliah mk ON n.id_matkul = mk.id_matkul
        WHERE n.id_mahasiswa = :id
        ORDER BY mk.semester ASC, mk.nama_matkul ASC
    ");
    $stmtNilai->execute(['id' => $id_mahasiswa]);
    $nilaiData = $stmtNilai->fetchAll(PDO::FETCH_ASSOC);
    
    // Group by semester
    $nilaiPerSemester = [];
    foreach ($nilaiData as $nilai) {
        $sem = $nilai['semester'];
        if (!isset($nilaiPerSemester[$sem])) {
            $nilaiPerSemester[$sem] = [];
        }
        $nilaiPerSemester[$sem][] = $nilai;
    }
    
    // Calculate statistics
    function hitungBobot($huruf) {
        $bobot = ['A' => 4.0, 'B' => 3.0, 'C' => 2.0, 'D' => 1.0, 'E' => 0.0];
        return $bobot[$huruf] ?? 0.0;
    }
    
    $totalSKS = 0;
    $totalBobotSKS = 0;
    foreach ($nilaiData as $nilai) {
        $sks = $nilai['sks'];
        $bobot = hitungBobot($nilai['nilai_huruf']);
        $totalSKS += $sks;
        $totalBobotSKS += ($bobot * $sks);
    }
    $ipk = $totalSKS > 0 ? round($totalBobotSKS / $totalSKS, 2) : 0;
}

// Get KHS (Semester Report) for specific semester
if ($act === 'khs' && $id_mahasiswa) {
    $semester = $_GET['semester'] ?? 1;
    
    // Get student info
    $stmtMhs = $pdo->prepare("
        SELECT m.*, j.nama_jurusan, k.nama_kelas 
        FROM mahasiswa m 
        LEFT JOIN jurusan j ON m.id_jurusan = j.id_jurusan 
        LEFT JOIN kelas k ON m.id_kelas = k.id_kelas 
        WHERE m.id_mahasiswa = :id
    ");
    $stmtMhs->execute(['id' => $id_mahasiswa]);
    $mahasiswa = $stmtMhs->fetch(PDO::FETCH_ASSOC);
    
    // Get grades for specific semester
    $stmtNilai = $pdo->prepare("
        SELECT n.*, mk.kode_matkul, mk.nama_matkul, mk.sks, mk.semester
        FROM nilai n
        JOIN mata_kuliah mk ON n.id_matkul = mk.id_matkul
        WHERE n.id_mahasiswa = :id AND mk.semester = :sem
        ORDER BY mk.nama_matkul ASC
    ");
    $stmtNilai->execute(['id' => $id_mahasiswa, 'sem' => $semester]);
    $nilaiSemester = $stmtNilai->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate IPS (Semester GPA)
    $totalSKS = 0;
    $totalBobotSKS = 0;
    foreach ($nilaiSemester as $nilai) {
        $sks = $nilai['sks'];
        $bobot = hitungBobot($nilai['nilai_huruf']);
        $totalSKS += $sks;
        $totalBobotSKS += ($bobot * $sks);
    }
    $ips = $totalSKS > 0 ? round($totalBobotSKS / $totalSKS, 2) : 0;
}
?>

<style>
@media print {
    .no-print { display: none !important; }
    .sidebar { display: none !important; }
    body { background: white !important; }
    .card-custom { box-shadow: none !important; border: 1px solid #ddd !important; }
}
.transcript-header {
    text-align: center;
    border-bottom: 3px solid #667eea;
    padding-bottom: 20px;
    margin-bottom: 30px;
}
.student-info {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}
.grade-table th {
    background: #667eea;
    color: white;
}
.summary-box {
    background: #e3f2fd;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #2196f3;
}
</style>

<?php if ($act === 'list'): ?>
    <div class="card-custom mb-4 no-print">
        <h5 class="mb-0 fw-bold"><i class="bi bi-file-earmark-text me-2"></i>Transkrip Nilai Mahasiswa</h5>
    </div>

    <div class="card-custom no-print">
        <p class="text-muted">Pilih mahasiswa untuk melihat transkrip nilai atau KHS</p>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="50">No</th>
                        <th>NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Jurusan</th>
                        <th>Kelas</th>
                        <th width="250" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($dataMahasiswa)): ?>
                        <?php foreach ($dataMahasiswa as $index => $mhs): ?>
                            <tr>
                                <td class="text-center"><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($mhs['nim']) ?></td>
                                <td><?= htmlspecialchars($mhs['nama_mahasiswa']) ?></td>
                                <td><?= htmlspecialchars($mhs['nama_jurusan'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($mhs['nama_kelas'] ?? '-') ?></td>
                                <td class="text-center">
                                    <a href="?page=transkrip&act=view&id=<?= $mhs['id_mahasiswa'] ?>" 
                                       class="btn btn-primary btn-sm" title="Transkrip Lengkap">
                                        <i class="bi bi-file-text"></i> Transkrip
                                    </a>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="bi bi-calendar3"></i> KHS
                                        </button>
                                        <ul class="dropdown-menu">
                                            <?php for($s=1; $s<=8; $s++): ?>
                                                <li><a class="dropdown-item" href="?page=transkrip&act=khs&id=<?= $mhs['id_mahasiswa'] ?>&semester=<?= $s ?>">Semester <?= $s ?></a></li>
                                            <?php endfor; ?>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center text-muted">Tidak ada data mahasiswa.</td></tr>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>

<?php elseif ($act === 'view' && $mahasiswa): ?>
    <!-- TRANSKRIP NILAI LENGKAP -->
    <div class="card-custom">
        <div class="no-print mb-3">
            <a href="?page=transkrip" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
            <button onclick="window.print()" class="btn btn-primary btn-sm"><i class="bi bi-printer"></i> Cetak</button>
        </div>

        <div class="transcript-header">
            <h3 class="mb-1">TRANSKRIP NILAI AKADEMIK</h3>
            <h5 class="text-muted">SISTEM INFORMASI AKADEMIK KAMPUS</h5>
        </div>

        <div class="student-info">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr><th width="150">NIM</th><td>: <?= htmlspecialchars($mahasiswa['nim']) ?></td></tr>
                        <tr><th>Nama</th><td>: <?= htmlspecialchars($mahasiswa['nama_mahasiswa']) ?></td></tr>
                        <tr><th>Jenis Kelamin</th><td>: <?= $mahasiswa['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr><th width="150">Jurusan</th><td>: <?= htmlspecialchars($mahasiswa['nama_jurusan'] ?? '-') ?></td></tr>
                        <tr><th>Kelas</th><td>: <?= htmlspecialchars($mahasiswa['nama_kelas'] ?? '-') ?></td></tr>
                        <tr><th>Angkatan</th><td>: <?= $mahasiswa['angkatan'] ?></td></tr>
                    </table>
                </div>
            </div>
        </div>

        <?php if (empty($nilaiData)): ?>
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i> Mahasiswa ini belum memiliki nilai.
            </div>
        <?php else: ?>
            <?php foreach ($nilaiPerSemester as $sem => $nilaiList): ?>
                <?php
                // Calculate IPS for this semester
                $semSKS = 0;
                $semBobot = 0;
                foreach ($nilaiList as $n) {
                    $semSKS += $n['sks'];
                    $semBobot += (hitungBobot($n['nilai_huruf']) * $n['sks']);
                }
                $ips = $semSKS > 0 ? round($semBobot / $semSKS, 2) : 0;
                ?>
                
                <h6 class="mt-4 mb-3 fw-bold">Semester <?= $sem ?></h6>
                <table class="table table-bordered grade-table">
                    <thead>
                        <tr>
                            <th width="100">Kode MK</th>
                            <th>Nama Mata Kuliah</th>
                            <th width="80" class="text-center">SKS</th>
                            <th width="100" class="text-center">Nilai Angka</th>
                            <th width="80" class="text-center">Nilai Huruf</th>
                            <th width="80" class="text-center">Bobot</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($nilaiList as $nilai): ?>
                            <tr>
                                <td><?= htmlspecialchars($nilai['kode_matkul']) ?></td>
                                <td><?= htmlspecialchars($nilai['nama_matkul']) ?></td>
                                <td class="text-center"><?= $nilai['sks'] ?></td>
                                <td class="text-center"><?= number_format($nilai['nilai_angka'], 2) ?></td>
                                <td class="text-center fw-bold"><?= $nilai['nilai_huruf'] ?></td>
                                <td class="text-center"><?= number_format(hitungBobot($nilai['nilai_huruf']), 2) ?></td>
                            </tr>
                        <?php endforeach ?>
                        <tr class="table-light fw-bold">
                            <td colspan="2" class="text-end">Total SKS Semester <?= $sem ?>:</td>
                            <td class="text-center"><?= $semSKS ?></td>
                            <td colspan="2" class="text-end">IPS:</td>
                            <td class="text-center"><?= number_format($ips, 2) ?></td>
                        </tr>
                    </tbody>
                </table>
            <?php endforeach ?>

            <div class="summary-box mt-4">
                <div class="row">
                    <div class="col-md-4">
                        <h6 class="mb-2">Total SKS Lulus</h6>
                        <h3 class="mb-0"><?= $totalSKS ?> SKS</h3>
                    </div>
                    <div class="col-md-4">
                        <h6 class="mb-2">Indeks Prestasi Kumulatif (IPK)</h6>
                        <h3 class="mb-0 text-primary"><?= number_format($ipk, 2) ?></h3>
                    </div>
                    <div class="col-md-4">
                        <h6 class="mb-2">Predikat</h6>
                        <h3 class="mb-0">
                            <?php
                            if ($ipk >= 3.50) echo '<span class="text-success">Cum Laude</span>';
                            elseif ($ipk >= 3.00) echo '<span class="text-info">Sangat Memuaskan</span>';
                            elseif ($ipk >= 2.75) echo '<span class="text-primary">Memuaskan</span>';
                            else echo '<span class="text-warning">Cukup</span>';
                            ?>
                        </h3>
                    </div>
                </div>
            </div>

            <div class="text-end mt-5 no-print">
                <p class="mb-1">Dicetak pada: <?= date('d F Y, H:i') ?> WIB</p>
            </div>
        <?php endif ?>
    </div>

<?php elseif ($act === 'khs' && $mahasiswa): ?>
    <!-- KARTU HASIL STUDI (KHS) PER SEMESTER -->
    <div class="card-custom">
        <div class="no-print mb-3">
            <a href="?page=transkrip" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
            <button onclick="window.print()" class="btn btn-primary btn-sm"><i class="bi bi-printer"></i> Cetak</button>
        </div>

        <div class="transcript-header">
            <h3 class="mb-1">KARTU HASIL STUDI (KHS)</h3>
            <h5 class="text-muted">Semester <?= $semester ?> - Tahun Akademik <?= $mahasiswa['angkatan'] ?>/<?= $mahasiswa['angkatan']+1 ?></h5>
        </div>

        <div class="student-info">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr><th width="150">NIM</th><td>: <?= htmlspecialchars($mahasiswa['nim']) ?></td></tr>
                        <tr><th>Nama</th><td>: <?= htmlspecialchars($mahasiswa['nama_mahasiswa']) ?></td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr><th width="150">Jurusan</th><td>: <?= htmlspecialchars($mahasiswa['nama_jurusan'] ?? '-') ?></td></tr>
                        <tr><th>Kelas</th><td>: <?= htmlspecialchars($mahasiswa['nama_kelas'] ?? '-') ?></td></tr>
                    </table>
                </div>
            </div>
        </div>

        <?php if (empty($nilaiSemester)): ?>
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i> Tidak ada nilai untuk semester <?= $semester ?>.
            </div>
        <?php else: ?>
            <table class="table table-bordered grade-table">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th width="100">Kode MK</th>
                        <th>Nama Mata Kuliah</th>
                        <th width="80" class="text-center">SKS</th>
                        <th width="100" class="text-center">Nilai Angka</th>
                        <th width="80" class="text-center">Nilai Huruf</th>
                        <th width="80" class="text-center">Bobot</th>
                        <th width="100" class="text-center">SKS × Bobot</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($nilaiSemester as $index => $nilai): ?>
                        <?php $bobot = hitungBobot($nilai['nilai_huruf']); ?>
                        <tr>
                            <td class="text-center"><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($nilai['kode_matkul']) ?></td>
                            <td><?= htmlspecialchars($nilai['nama_matkul']) ?></td>
                            <td class="text-center"><?= $nilai['sks'] ?></td>
                            <td class="text-center"><?= number_format($nilai['nilai_angka'], 2) ?></td>
                            <td class="text-center fw-bold"><?= $nilai['nilai_huruf'] ?></td>
                            <td class="text-center"><?= number_format($bobot, 2) ?></td>
                            <td class="text-center"><?= number_format($bobot * $nilai['sks'], 2) ?></td>
                        </tr>
                    <?php endforeach ?>
                    <tr class="table-light fw-bold">
                        <td colspan="3" class="text-end">TOTAL</td>
                        <td class="text-center"><?= $totalSKS ?></td>
                        <td colspan="3" class="text-end">IPS Semester <?= $semester ?>:</td>
                        <td class="text-center bg-primary text-white"><?= number_format($ips, 2) ?></td>
                    </tr>
                </tbody>
            </table>

            <div class="summary-box mt-4">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Jumlah SKS Semester Ini: <strong><?= $totalSKS ?> SKS</strong></h6>
                        <h6>Indeks Prestasi Semester (IPS): <strong class="text-primary"><?= number_format($ips, 2) ?></strong></h6>
                    </div>
                    <div class="col-md-6 text-end">
                        <p class="mb-1"><strong>Status:</strong> 
                            <?php
                            if ($ips >= 3.00) echo '<span class="badge bg-success">Sangat Baik</span>';
                            elseif ($ips >= 2.75) echo '<span class="badge bg-info">Baik</span>';
                            elseif ($ips >= 2.00) echo '<span class="badge bg-warning">Cukup</span>';
                            else echo '<span class="badge bg-danger">Kurang</span>';
                            ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="text-end mt-5">
                <p class="mb-5">Dicetak pada: <?= date('d F Y, H:i') ?> WIB</p>
                <div class="d-inline-block text-center" style="min-width: 200px;">
                    <p class="mb-5">Mengetahui,<br>Ketua Jurusan</p>
                    <p class="mb-0 fw-bold" style="border-top: 1px solid #000; padding-top: 5px;">
                        (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
                    </p>
                </div>
            </div>
        <?php endif ?>
    </div>
<?php endif ?>

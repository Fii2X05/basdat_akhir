<?php
// views/laporan.php - Academic Performance Reports
global $pdo;
if (empty($pdo)) { require_once __DIR__ . '/../config/database.php'; }

$act = $_GET['act'] ?? 'dashboard';
$id_jurusan = $_GET['id_jurusan'] ?? null;
?>

<style>
@media print {
    .no-print { display: none !important; }
    .sidebar { display: none !important; }
}
.stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
}
.chart-container {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
</style>

<?php if ($act === 'dashboard'): ?>
    <div class="card-custom mb-4 no-print">
        <h5 class="mb-0 fw-bold"><i class="bi bi-graph-up me-2"></i>Laporan Akademik</h5>
    </div>

    <!-- Statistics Overview -->
    <div class="row">
        <?php
        $totalMhs = $pdo->query("SELECT COUNT(*) as total FROM mahasiswa")->fetch()['total'];
        $totalDosen = $pdo->query("SELECT COUNT(*) as total FROM dosen")->fetch()['total'];
        $totalMatkul = $pdo->query("SELECT COUNT(*) as total FROM mata_kuliah")->fetch()['total'];
        $totalJurusan = $pdo->query("SELECT COUNT(*) as total FROM jurusan")->fetch()['total'];
        ?>
        <div class="col-md-3">
            <div class="stat-card">
                <h6>Total Mahasiswa</h6>
                <h2><?= $totalMhs ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <h6>Total Dosen</h6>
                <h2><?= $totalDosen ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <h6>Total Mata Kuliah</h6>
                <h2><?= $totalMatkul ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <h6>Total Jurusan</h6>
                <h2><?= $totalJurusan ?></h2>
            </div>
        </div>
    </div>

    <!-- Performance by Department -->
    <div class="card-custom mb-4">
        <h6 class="fw-bold mb-3">Performa Akademik per Jurusan</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Jurusan</th>
                        <th class="text-center">Jumlah Mahasiswa</th>
                        <th class="text-center">Rata-rata IPK</th>
                        <th class="text-center">IPK Tertinggi</th>
                        <th class="text-center">IPK Terendah</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmtJurusan = $pdo->query("SELECT * FROM jurusan ORDER BY nama_jurusan");
                    $dataJurusan = $stmtJurusan->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach ($dataJurusan as $jur):
                        // Count students
                        $stmtCount = $pdo->prepare("SELECT COUNT(*) as total FROM mahasiswa WHERE id_jurusan = :id");
                        $stmtCount->execute(['id' => $jur['id_jurusan']]);
                        $jmlMhs = $stmtCount->fetch()['total'];
                        
                        // Calculate average IPK
                        $stmtIPK = $pdo->prepare("
                            SELECT m.id_mahasiswa, m.nama_mahasiswa,
                                   SUM(mk.sks * CASE 
                                       WHEN n.nilai_huruf = 'A' THEN 4.0
                                       WHEN n.nilai_huruf = 'B' THEN 3.0
                                       WHEN n.nilai_huruf = 'C' THEN 2.0
                                       WHEN n.nilai_huruf = 'D' THEN 1.0
                                       ELSE 0.0 END) / NULLIF(SUM(mk.sks), 0) as ipk
                            FROM mahasiswa m
                            LEFT JOIN nilai n ON m.id_mahasiswa = n.id_mahasiswa
                            LEFT JOIN mata_kuliah mk ON n.id_matkul = mk.id_matkul
                            WHERE m.id_jurusan = :id
                            GROUP BY m.id_mahasiswa, m.nama_mahasiswa
                        ");
                        $stmtIPK->execute(['id' => $jur['id_jurusan']]);
                        $ipkData = $stmtIPK->fetchAll(PDO::FETCH_ASSOC);
                        
                        $ipkList = array_column($ipkData, 'ipk');
                        $ipkList = array_filter($ipkList);
                        
                        $avgIPK = !empty($ipkList) ? round(array_sum($ipkList) / count($ipkList), 2) : 0;
                        $maxIPK = !empty($ipkList) ? round(max($ipkList), 2) : 0;
                        $minIPK = !empty($ipkList) ? round(min($ipkList), 2) : 0;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($jur['nama_jurusan']) ?></td>
                            <td class="text-center"><?= $jmlMhs ?></td>
                            <td class="text-center fw-bold"><?= number_format($avgIPK, 2) ?></td>
                            <td class="text-center text-success"><?= number_format($maxIPK, 2) ?></td>
                            <td class="text-center text-danger"><?= number_format($minIPK, 2) ?></td>
                            <td class="text-center">
                                <a href="?page=laporan&act=detail&id_jurusan=<?= $jur['id_jurusan'] ?>" 
                                   class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>

<?php elseif ($act === 'detail' && $id_jurusan): ?>
    <?php
    // Get jurusan info
    $stmtJur = $pdo->prepare("SELECT * FROM jurusan WHERE id_jurusan = :id");
    $stmtJur->execute(['id' => $id_jurusan]);
    $jurusan = $stmtJur->fetch(PDO::FETCH_ASSOC);
    
    // Get all students with their IPK
    $stmtMhs = $pdo->prepare("
        SELECT m.*, 
               SUM(mk.sks * CASE 
                   WHEN n.nilai_huruf = 'A' THEN 4.0
                   WHEN n.nilai_huruf = 'B' THEN 3.0
                   WHEN n.nilai_huruf = 'C' THEN 2.0
                   WHEN n.nilai_huruf = 'D' THEN 1.0
                   ELSE 0.0 END) / NULLIF(SUM(mk.sks), 0) as ipk,
               SUM(mk.sks) as total_sks
        FROM mahasiswa m
        LEFT JOIN nilai n ON m.id_mahasiswa = n.id_mahasiswa
        LEFT JOIN mata_kuliah mk ON n.id_matkul = mk.id_matkul
        WHERE m.id_jurusan = :id
        GROUP BY m.id_mahasiswa
        ORDER BY ipk DESC
    ");
    $stmtMhs->execute(['id' => $id_jurusan]);
    $mahasiswaList = $stmtMhs->fetchAll(PDO::FETCH_ASSOC);
    
    // Grade distribution
    $stmtGrade = $pdo->prepare("
        SELECT n.nilai_huruf, COUNT(*) as jumlah
        FROM nilai n
        JOIN mahasiswa m ON n.id_mahasiswa = m.id_mahasiswa
        WHERE m.id_jurusan = :id
        GROUP BY n.nilai_huruf
        ORDER BY n.nilai_huruf
    ");
    $stmtGrade->execute(['id' => $id_jurusan]);
    $gradeDistribution = $stmtGrade->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="card-custom mb-4 no-print">
        <a href="?page=laporan" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
        <button onclick="window.print()" class="btn btn-primary btn-sm"><i class="bi bi-printer"></i> Cetak</button>
    </div>

    <div class="card-custom">
        <div class="text-center mb-4">
            <h4>Laporan Performa Akademik</h4>
            <h5 class="text-primary"><?= htmlspecialchars($jurusan['nama_jurusan']) ?></h5>
        </div>

        <!-- Grade Distribution -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h6 class="fw-bold">Distribusi Nilai</h6>
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Nilai</th>
                            <th class="text-center">Jumlah</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $totalNilai = array_sum(array_column($gradeDistribution, 'jumlah'));
                        foreach ($gradeDistribution as $grade): 
                            $persen = $totalNilai > 0 ? ($grade['jumlah'] / $totalNilai * 100) : 0;
                        ?>
                            <tr>
                                <td class="fw-bold"><?= $grade['nilai_huruf'] ?></td>
                                <td class="text-center"><?= $grade['jumlah'] ?></td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: <?= $persen ?>%">
                                            <?= number_format($persen, 1) ?>%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="fw-bold">Top 5 Mahasiswa Terbaik</h6>
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th width="50">Rank</th>
                            <th>NIM</th>
                            <th>Nama</th>
                            <th class="text-center">IPK</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $top5 = array_slice($mahasiswaList, 0, 5);
                        foreach ($top5 as $index => $mhs): 
                        ?>
                            <tr>
                                <td class="text-center fw-bold"><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($mhs['nim']) ?></td>
                                <td><?= htmlspecialchars($mhs['nama_mahasiswa']) ?></td>
                                <td class="text-center text-success fw-bold">
                                    <?= number_format($mhs['ipk'] ?? 0, 2) ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- All Students List -->
        <h6 class="fw-bold mb-3">Daftar Semua Mahasiswa</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th width="50">No</th>
                        <th>NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th class="text-center">Total SKS</th>
                        <th class="text-center">IPK</th>
                        <th class="text-center">Predikat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mahasiswaList as $index => $mhs): ?>
                        <tr>
                            <td class="text-center"><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($mhs['nim']) ?></td>
                            <td><?= htmlspecialchars($mhs['nama_mahasiswa']) ?></td>
                            <td class="text-center"><?= $mhs['total_sks'] ?? 0 ?></td>
                            <td class="text-center fw-bold"><?= number_format($mhs['ipk'] ?? 0, 2) ?></td>
                            <td class="text-center">
                                <?php
                                $ipk = $mhs['ipk'] ?? 0;
                                if ($ipk >= 3.50) echo '<span class="badge bg-success">Cum Laude</span>';
                                elseif ($ipk >= 3.00) echo '<span class="badge bg-info">Sangat Memuaskan</span>';
                                elseif ($ipk >= 2.75) echo '<span class="badge bg-primary">Memuaskan</span>';
                                elseif ($ipk > 0) echo '<span class="badge bg-warning">Cukup</span>';
                                else echo '<span class="badge bg-secondary">Belum Ada Nilai</span>';
                                ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>

        <div class="text-end mt-4 no-print">
            <p>Dicetak pada: <?= date('d F Y, H:i') ?> WIB</p>
        </div>
    </div>
<?php endif ?>

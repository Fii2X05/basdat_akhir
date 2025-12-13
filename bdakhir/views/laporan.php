<?php
// views/laporan.php
global $pdo;
if (empty($pdo)) { require_once __DIR__ . '/../config/database.php'; }

$act = $_GET['act'] ?? 'dashboard';
$id_jurusan = $_GET['id'] ?? null;
?>

<style>
    .stat-card {
        color: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .bg-grad-1 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .bg-grad-2 { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .bg-grad-3 { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .bg-grad-4 { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }

    /* CSS KHUSUS PRINT */
    @media print {
        .no-print, .sidebar, .navbar, .btn { display: none !important; }
        .content-area { margin: 0 !important; padding: 0 !important; width: 100% !important; }
        .card-custom { box-shadow: none !important; border: none !important; }
        body { background-color: white !important; -webkit-print-color-adjust: exact; }
    }
</style>

<?php if ($act === 'dashboard'): ?>
    
    <div class="card-custom mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0 fw-bold"><i class="bi bi-graph-up me-2"></i>Laporan Akademik (MV)</h5>
            <small class="text-muted">Data Statistik Kampus Terpadu</small>
        </div>
        <div class="no-print">
            <button onclick="window.print()" class="btn btn-secondary btn-sm me-2">
                <i class="bi bi-printer"></i> Cetak
            </button>
            <a href="index.php?page=laporan&act=refresh" class="btn btn-primary btn-sm">
                <i class="bi bi-arrow-clockwise me-1"></i> Refresh Semua Laporan
            </a>
        </div>
    </div>

    <div class="row">
        <?php
        $totalMhs = $pdo->query("SELECT COUNT(*) FROM mahasiswa")->fetchColumn();
        $totalDosen = $pdo->query("SELECT COUNT(*) FROM dosen")->fetchColumn();
        $totalMatkul = $pdo->query("SELECT COUNT(*) FROM mata_kuliah")->fetchColumn();
        $totalJurusan = $pdo->query("SELECT COUNT(*) FROM jurusan")->fetchColumn();
        ?>
        <div class="col-md-3">
            <div class="stat-card bg-grad-1">
                <h6>Mahasiswa</h6>
                <h2><?= $totalMhs ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-grad-2">
                <h6>Dosen</h6>
                <h2><?= $totalDosen ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-grad-3">
                <h6>Mata Kuliah</h6>
                <h2><?= $totalMatkul ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-grad-4">
                <h6>Jurusan</h6>
                <h2><?= $totalJurusan ?></h2>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card-custom h-100">
                <h6 class="fw-bold mb-3 border-bottom pb-2">Performa Akademik per Jurusan</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm align-middle">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>Jurusan</th>
                                <th>Jml Mhs</th>
                                <th>Rata-rata IPK</th>
                                <th>Tertinggi</th>
                                <th>Terendah</th>
                                <th class="no-print">Aksi</th> </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmtJur = $pdo->query("SELECT * FROM public.mv_laporan_jurusan");
                            while ($row = $stmtJur->fetch(PDO::FETCH_ASSOC)):
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nama_jurusan']) ?></td>
                                <td class="text-center"><?= $row['total_mahasiswa'] ?></td>
                                <td class="text-center fw-bold text-primary"><?= number_format($row['rata_rata_ipk'], 2) ?></td>
                                <td class="text-center text-success"><?= number_format($row['max_ipk'], 2) ?></td>
                                <td class="text-center text-danger"><?= number_format($row['min_ipk'], 2) ?></td>
                                <td class="text-center no-print">
                                    <a href="?page=laporan&act=detail&id=<?= $row['id_jurusan'] ?>" class="btn btn-sm btn-info text-white" title="Lihat Detail Mahasiswa">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card-custom h-100">
                <h6 class="fw-bold mb-3 border-bottom pb-2">Distribusi Nilai</h6>
                <table class="table table-bordered align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center">Nilai</th>
                            <th class="text-center">Jumlah</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmtDist = $pdo->query("SELECT * FROM public.mv_distribusi_nilai");
                        while ($d = $stmtDist->fetch(PDO::FETCH_ASSOC)):
                            $bg = 'bg-secondary';
                            if ($d['nilai_huruf'] == 'A') $bg = 'bg-success';
                            if ($d['nilai_huruf'] == 'B') $bg = 'bg-primary';
                            if ($d['nilai_huruf'] == 'C') $bg = 'bg-warning';
                            if ($d['nilai_huruf'] == 'D' || $d['nilai_huruf'] == 'E') $bg = 'bg-danger';
                        ?>
                        <tr>
                            <td class="text-center fw-bold"><?= $d['nilai_huruf'] ?></td>
                            <td class="text-center"><?= $d['jumlah'] ?></td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar <?= $bg ?>" role="progressbar" 
                                         style="width: <?= $d['persentase'] ?>%">
                                        <?= $d['persentase'] ?>%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php elseif ($act === 'detail' && $id_jurusan): ?>
    
    <?php
    // 1. Ambil Nama Jurusan
    $stmtJ = $pdo->prepare("SELECT nama_jurusan FROM jurusan WHERE id_jurusan = :id");
    $stmtJ->execute(['id' => $id_jurusan]);
    $namaJurusan = $stmtJ->fetchColumn();

    // 2. Ambil Daftar Mahasiswa di Jurusan ini (Menggunakan MV Mahasiswa Stats)
    // filter MV Mahasiswa berdasarkan nama_jurusan
    $stmtMhs = $pdo->prepare("
        SELECT * FROM public.mv_mahasiswa_stats 
        WHERE nama_jurusan = :jurusan 
        ORDER BY ipk DESC
    ");
    $stmtMhs->execute(['jurusan' => $namaJurusan]);
    $listMahasiswa = $stmtMhs->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="card-custom mb-4 no-print">
        <a href="?page=laporan" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali ke Dashboard</a>
        <button onclick="window.print()" class="btn btn-primary btn-sm float-end"><i class="bi bi-printer"></i> Cetak Laporan</button>
    </div>

    <div class="card-custom">
        <div class="text-center mb-4">
            <h4 class="fw-bold">Laporan Detail Akademik</h4>
            <h5 class="text-primary"><?= htmlspecialchars($namaJurusan) ?></h5>
            <p class="text-muted">Diurutkan berdasarkan IPK Tertinggi (Ranking)</p>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th width="50">Rank</th>
                        <th>NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Kelas</th>
                        <th width="100">Total SKS</th>
                        <th width="100">IPK</th>
                        <th width="150">Predikat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($listMahasiswa)): ?>
                        <?php foreach ($listMahasiswa as $i => $mhs): ?>
                            <tr>
                                <td class="text-center fw-bold"><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($mhs['nim']) ?></td>
                                <td><?= htmlspecialchars($mhs['nama_mahasiswa']) ?></td>
                                <td class="text-center"><span class="badge bg-secondary"><?= htmlspecialchars($mhs['nama_kelas'] ?? '-') ?></span></td>
                                <td class="text-center"><?= $mhs['total_sks'] ?></td>
                                <td class="text-center fw-bold text-primary"><?= number_format($mhs['ipk'], 2) ?></td>
                                <td class="text-center">
                                    <?php
                                    $ipk = $mhs['ipk'];
                                    if ($ipk >= 3.51) echo '<span class="badge bg-success">Cum Laude</span>';
                                    elseif ($ipk >= 3.00) echo '<span class="badge bg-primary">Sangat Memuaskan</span>';
                                    elseif ($ipk >= 2.76) echo '<span class="badge bg-info">Memuaskan</span>';
                                    elseif ($ipk >= 2.00) echo '<span class="badge bg-warning text-dark">Cukup</span>';
                                    else echo '<span class="badge bg-danger">Kurang</span>';
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada data mahasiswa di jurusan ini.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4 text-end d-none d-print-block">
            <p>Dicetak pada: <?= date('d F Y, H:i') ?></p>
        </div>
    </div>

<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
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
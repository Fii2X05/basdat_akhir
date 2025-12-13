<?php
// views/transaction_test.php
global $pdo;
?>

<div class="card-custom mb-4">
    <h5 class="fw-bold"><i class="bi bi-shield-check me-2"></i>Uji Coba Transaction (ACID)</h5>
    <p class="text-muted">Demonstrasi fitur Atomicity: Proses insert Nilai dan Log harus berhasil semua atau gagal semua.</p>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card p-3 mb-3 border-success shadow-sm">
            <h6 class="text-success fw-bold"><i class="bi bi-check-circle me-2"></i>Skenario Sukses (Commit)</h6>
            <ul class="small text-muted ps-3 mb-3">
                <li>Insert ke tabel <code>nilai</code> (Valid)</li>
                <li>Insert ke tabel <code>log_audit</code> (Valid)</li>
            </ul>
            <a href="index.php?page=transaction_test&act=success" class="btn btn-success w-100">Jalankan Transaksi</a>
        </div>

        <div class="card p-3 border-danger shadow-sm">
            <h6 class="text-danger fw-bold"><i class="bi bi-x-circle me-2"></i>Skenario Gagal (Rollback)</h6>
            <ul class="small text-muted ps-3 mb-3">
                <li>Insert ke tabel <code>nilai</code> (Valid)</li>
                <li>Insert ke tabel <code>log_audit</code> (Error Syntax)</li>
            </ul>
            <a href="index.php?page=transaction_test&act=fail" class="btn btn-danger w-100">Jalankan Transaksi</a>
        </div>
    </div>

    <div class="col-md-8">
        <div class="row">
            <div class="col-md-6">
                <div class="card-custom h-100">
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Tabel Nilai (Terbaru)</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-striped" style="font-size: 0.85rem;">
                            <thead class="table-dark"><tr><th>ID</th><th>Angka</th><th>Huruf</th></tr></thead>
                            <tbody>
                                <?php
                                $stmt = $pdo->query("SELECT * FROM nilai ORDER BY id_nilai DESC LIMIT 5");
                                while($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td><?= $row['id_nilai'] ?></td>
                                    <td><?= $row['nilai_angka'] ?></td>
                                    <td><?= $row['nilai_huruf'] ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-custom h-100">
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Tabel Log Audit (Terbaru)</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-striped" style="font-size: 0.85rem;">
                            <thead class="table-dark"><tr><th>ID</th><th>Aktivitas</th><th>Status</th></tr></thead>
                            <tbody>
                                <?php
                                $stmt = $pdo->query("SELECT * FROM log_audit ORDER BY id_log DESC LIMIT 5");
                                while($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td><?= $row['id_log'] ?></td>
                                    <td><?= htmlspecialchars($row['aktivitas']) ?></td>
                                    <td><?= $row['status'] ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    <?php if (isset($_SESSION['flash'])): ?>
    Swal.fire({
        icon: '<?= $_SESSION['flash']['type'] == 'danger' ? 'error' : 'success' ?>',
        title: '<?= $_SESSION['flash']['type'] == 'danger' ? 'Rollback Berhasil!' : 'Commit Berhasil!' ?>',
        html: '<?= $_SESSION['flash']['message'] ?>',
        timer: 5000,
        timerProgressBar: true
    });
    <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
</script>
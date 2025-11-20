<?php
require_once 'config/database.php';

$message = '';
$error = '';

$stm_mhs = $pdo->query("SELECT id_mahasiswa, nama_mahasiswa, nim FROM mahasiswa ORDER BY nama_mahasiswa");
$stm_mk = $pdo->query("SELECT id_matkul, nama_matkul, kode_matkul FROM mata_kuliah ORDER BY nama_matkul");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_mhs = $_POST['id_mahasiswa'];
    $id_mk  = $_POST['id_matkul'];
    $nilai  = $_POST['nilai_angka'];
    
    $simulate_error = isset($_POST['simulasi_error']);

    try {
        $pdo->beginTransaction();

        if ($nilai < 0 || $nilai > 100) {
            throw new Exception("Nilai harus antara 0 - 100.");
        }

        $sql = "INSERT INTO nilai (id_mahasiswa, id_matkul, nilai_angka, nilai_huruf) 
                VALUES (:id_mhs, :id_mk, :nilai, public.get_nilai_huruf(:nilai))";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_mhs' => $id_mhs,
            ':id_mk'  => $id_mk,
            ':nilai'  => $nilai
        ]);

        if ($simulate_error) {
            throw new Exception("Simulasi Error Terjadi! Transaksi di-Rollback.");
        }

        $pdo->commit();
        $message = "Nilai berhasil disimpan!";

    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Gagal menyimpan: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Input Nilai Transaksi</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2>Input Nilai Mahasiswa (Transaksi)</h2>

        <?php if($message): ?><div class="alert alert-success"><?= $message ?></div><?php endif; ?>
        <?php if($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>

        <form method="POST" action="">
            <label>Mahasiswa:</label>
            <select name="id_mahasiswa" required>
                <option value="">Pilih Mahasiswa</option>
                <?php while($row = $stm_mhs->fetch()): ?>
                    <option value="<?= $row['id_mahasiswa'] ?>">
                        <?= $row['nim'] ?> - <?= $row['nama_mahasiswa'] ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Mata Kuliah:</label>
            <select name="id_matkul" required>
                <option value="">Pilih Mata Kuliah</option>
                <?php while($row = $stm_mk->fetch()): ?>
                    <option value="<?= $row['id_matkul'] ?>">
                        <?= $row['kode_matkul'] ?> - <?= $row['nama_matkul'] ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Nilai Angka (0-100):</label>
            <input type="number" name="nilai_angka" min="0" max="100" step="0.01" required>

            <div style="margin: 15px 0;">
                <input type="checkbox" name="simulasi_error" id="err" style="width:auto;">
                <label for="err" style="display:inline;">Simulasi Transaksi Gagal (Rollback Demo)</label>
            </div>

            <button type="submit" class="btn btn-success">Simpan Nilai</button>
            <a href="mahasiswa.php" class="btn">Kembali</a>
        </form>
    </div>
</body>
</html>
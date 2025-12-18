<?php

class TransactionController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // --- SKENARIO 1: TRANSAKSI SUKSES (COMMIT) ---
    public function demoSuccess() {
        try {
            // 1. Mulai Transaksi
            $this->pdo->beginTransaction();

            $this->pdo->exec("DELETE FROM nilai WHERE id_mahasiswa = 1001 AND id_matkul = 501");

            // Query A: Input Nilai (Valid)
            $sql1 = "INSERT INTO nilai (id_mahasiswa, id_matkul, nilai_angka, nilai_huruf) 
                     VALUES (1001, 501, 95, 'A')";
            $this->pdo->exec($sql1);

            // Query B: Catat Log (Valid)
            $sql2 = "INSERT INTO log_audit (aktivitas, status) 
                     VALUES ('Input Nilai Mahasiswa 1001 Matkul 501', 'Sukses')";
            $this->pdo->exec($sql2);

            // 2. Commit (Simpan Permanen)
            $this->pdo->commit();

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Transaksi BERHASIL! Data Nilai (95/A) & Log tersimpan.'];
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal: ' . $e->getMessage()];
        }
        
        header('Location: index.php?page=transaction_test');
        exit;
    }

    // --- SKENARIO 2: TRANSAKSI GAGAL (ROLLBACK - DEMONSTRASI ACID) ---
    public function demoFail() {
        try {
            // 1. Mulai Transaksi
            $this->pdo->beginTransaction();
            $this->pdo->exec("DELETE FROM nilai WHERE id_mahasiswa = 1001 AND id_matkul = 502");

            $sql1 = "INSERT INTO nilai (id_mahasiswa, id_matkul, nilai_angka, nilai_huruf) 
                     VALUES (1001, 502, 88.88, 'B')";
            $this->pdo->exec($sql1);
            $sql2 = "INSERT INTO log_audit (aktivitas, kolom_ngawur) 
                     VALUES ('Percobaan Gagal', 'Error')"; 
            $this->pdo->exec($sql2);

            $this->pdo->commit();

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            
            $_SESSION['flash'] = ['type' => 'danger', 'message' => '<strong>ROLLBACK SUKSES!</strong><br>Sistem mendeteksi error pada Query ke-2.<br>Data Nilai (88.88) dibatalkan/dihapus otomatis demi konsistensi data.'];
        }
        
        header('Location: index.php?page=transaction_test');
        exit;
    }
}
<?php

class LaporanController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // --- LOGIC REFRESH SEMUA LAPORAN ---
    public function refresh() {
        try {
            $this->pdo->exec("REFRESH MATERIALIZED VIEW public.mv_laporan_jurusan");
            $this->pdo->exec("REFRESH MATERIALIZED VIEW public.mv_distribusi_nilai");
            
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data Laporan Akademik berhasil diperbarui!'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal refresh data: ' . $e->getMessage()];
        }

        header('Location: index.php?page=laporan');
        exit;
    }
}
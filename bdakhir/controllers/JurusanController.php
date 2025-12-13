<?php
// controllers/JurusanController.php

class JurusanController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // --- LOGIC TAMBAH DATA ---
    public function store() {
        $nama = trim($_POST['nama_jurusan']);

        // Validasi
        if (empty($nama)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal: Nama Jurusan tidak boleh kosong!'];
            header('Location: index.php?page=jurusan&act=create');
            exit;
        }

        try {
            // Cek Duplikat
            $stmtCek = $this->pdo->prepare("SELECT COUNT(*) FROM jurusan WHERE nama_jurusan ILIKE ?"); // ILIKE = Case insensitive di Postgres
            $stmtCek->execute([$nama]);
            if ($stmtCek->fetchColumn() > 0) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal: Nama Jurusan sudah ada!'];
                header('Location: index.php?page=jurusan&act=create');
                exit;
            }

            $sql = "INSERT INTO jurusan (nama_jurusan) VALUES (:nama)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['nama' => $nama]);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Jurusan berhasil ditambahkan!'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menyimpan: ' . $e->getMessage()];
        }

        header('Location: index.php?page=jurusan');
        exit;
    }

    // --- LOGIC UPDATE DATA ---
    public function update() {
        $id = $_POST['id_jurusan'];
        $nama = trim($_POST['nama_jurusan']);

        if (empty($nama)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal Update: Nama Jurusan kosong!'];
            header("Location: index.php?page=jurusan&act=edit&id=$id");
            exit;
        }

        try {
            $sql = "UPDATE jurusan SET nama_jurusan = :nama WHERE id_jurusan = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['nama' => $nama, 'id' => $id]);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data jurusan berhasil diperbarui!'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal update: ' . $e->getMessage()];
        }

        header('Location: index.php?page=jurusan');
        exit;
    }

    // --- LOGIC DELETE (STORED PROCEDURE) ---
    public function delete($id) {
        try {
            // Memanggil Procedure
            $sql = "CALL hapus_jurusan_lengkap(:id)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Jurusan berhasil dihapus (Mahasiswa & Dosen diputus hubungannya)!'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menghapus: ' . $e->getMessage()];
        }

        header('Location: index.php?page=jurusan');
        exit;
    }
    public function refresh() {
        try {
            $this->pdo->exec("REFRESH MATERIALIZED VIEW public.mv_jurusan_stats");
            
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Statistik Jurusan berhasil diperbarui!'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal refresh data: ' . $e->getMessage()];
        }

        header('Location: index.php?page=jurusan');
        exit;
    }
}
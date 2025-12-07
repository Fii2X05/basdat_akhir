<?php
class JurusanController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function store() {
        $nama = $_POST['nama_jurusan'];
        // Validasi sederhana
        if(empty($nama)) {
            echo "<script>alert('Nama jurusan tidak boleh kosong'); window.history.back();</script>";
            return;
        }

        $sql = "INSERT INTO jurusan (nama_jurusan) VALUES (:nama)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['nama' => $nama]);
        header("Location: ?page=jurusan");
        exit;
    }

    public function update() {
        $id = $_POST['id_jurusan'];
        $nama = $_POST['nama_jurusan'];
        
        $sql = "UPDATE jurusan SET nama_jurusan = :nama WHERE id_jurusan = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['nama' => $nama, 'id' => $id]);
        header("Location: ?page=jurusan");
        exit;
    }

    public function delete($id) {
        try {
            $sql = "DELETE FROM jurusan WHERE id_jurusan = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            // Menangkap error jika jurusan masih dipakai di tabel lain (Foreign Key)
            echo "<script>alert('Gagal menghapus! Jurusan sedang dipakai oleh Mahasiswa/Dosen/Kelas.'); window.location='?page=jurusan';</script>";
            exit;
        }
        header("Location: ?page=jurusan");
        exit;
    }
}
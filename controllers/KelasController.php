<?php
class KelasController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function store() {
        $nama = $_POST['nama_kelas'];
        $jurusan = $_POST['id_jurusan'];
        $smt = $_POST['semester'];
        $ket = $_POST['keterangan'];
        
        $sql = "INSERT INTO kelas (nama_kelas, id_jurusan, semester, keterangan) VALUES (:nama, :jur, :smt, :ket)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['nama' => $nama, 'jur' => $jurusan, 'smt' => $smt, 'ket' => $ket]);
        header("Location: ?page=kelas");
        exit;
    }

    public function update() {
        $id = $_POST['id_kelas'];
        $nama = $_POST['nama_kelas'];
        $jurusan = $_POST['id_jurusan'];
        $smt = $_POST['semester'];
        $ket = $_POST['keterangan'];
        
        $sql = "UPDATE kelas SET nama_kelas = :nama, id_jurusan = :jur, semester = :smt, keterangan = :ket WHERE id_kelas = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['nama' => $nama, 'jur' => $jurusan, 'smt' => $smt, 'ket' => $ket, 'id' => $id]);
        header("Location: ?page=kelas");
        exit;
    }

    public function delete($id) {
        try {
            $sql = "DELETE FROM kelas WHERE id_kelas = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
             echo "<script>alert('Gagal menghapus! Kelas sedang dipakai oleh Mahasiswa.'); window.location='?page=kelas';</script>";
             exit;
        }
        header("Location: ?page=kelas");
        exit;
    }
}
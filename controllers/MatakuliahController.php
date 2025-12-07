<?php
class MatakuliahController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function store() {
        $kode = $_POST['kode_matkul'];
        $nama = $_POST['nama_matkul'];
        $sks = $_POST['sks'];
        $smt = $_POST['semester'];
        $jurusan = $_POST['id_jurusan'];
        $prasyarat = !empty($_POST['prasyarat_id']) ? $_POST['prasyarat_id'] : null; // Tangkap Prasyarat
        
        try {
            $sql = "INSERT INTO mata_kuliah (kode_matkul, nama_matkul, sks, semester, id_jurusan, prasyarat_id) 
                    VALUES (:kode, :nama, :sks, :smt, :jur, :pras)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'kode' => $kode, 'nama' => $nama,
                'sks' => $sks, 'smt' => $smt, 'jur' => $jurusan,
                'pras' => $prasyarat
            ]);
            header("Location: ?page=matakuliah");
            exit;
        } catch (PDOException $e) {
            echo "<script>alert('Gagal! Kode Mata Kuliah mungkin sudah ada.'); window.history.back();</script>";
            exit;
        }
    }

    public function update() {
        $id = $_POST['id_matkul'];
        $kode = $_POST['kode_matkul'];
        $nama = $_POST['nama_matkul'];
        $sks = $_POST['sks'];
        $smt = $_POST['semester'];
        $jurusan = $_POST['id_jurusan'];
        $prasyarat = !empty($_POST['prasyarat_id']) ? $_POST['prasyarat_id'] : null; // Tangkap Prasyarat
        
        // Cek agar tidak menjadi prasyarat bagi dirinya sendiri
        if ($id == $prasyarat) {
            echo "<script>alert('Gagal! Matkul tidak bisa menjadi prasyarat untuk dirinya sendiri.'); window.history.back();</script>";
            exit;
        }

        $sql = "UPDATE mata_kuliah SET 
                kode_matkul = :kode, nama_matkul = :nama,
                sks = :sks, semester = :smt, id_jurusan = :jur, prasyarat_id = :pras
                WHERE id_matkul = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'kode' => $kode, 'nama' => $nama,
            'sks' => $sks, 'smt' => $smt, 'jur' => $jurusan,
            'pras' => $prasyarat,
            'id' => $id
        ]);
        header("Location: ?page=matakuliah");
        exit;
    }

    public function delete($id) {
        $sql = "DELETE FROM mata_kuliah WHERE id_matkul = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        header("Location: ?page=matakuliah");
        exit;
    }
}
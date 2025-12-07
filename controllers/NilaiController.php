<?php
class NilaiController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Helper: Konversi Angka ke Huruf
    private function hitungNilaiHuruf($angka) {
        if ($angka >= 85) return 'A';
        if ($angka >= 75) return 'B';
        if ($angka >= 65) return 'C';
        if ($angka >= 55) return 'D';
        return 'E';
    }

    public function store() {
        $id_mhs = $_POST['id_mahasiswa'];
        $id_mk = $_POST['id_matkul'];
        $angka = $_POST['nilai_angka'];
        $huruf = $this->hitungNilaiHuruf($angka);
        
        try {
            $sql = "INSERT INTO nilai (id_mahasiswa, id_matkul, nilai_angka, nilai_huruf) 
                    VALUES (:mhs, :mk, :angka, :huruf)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'mhs' => $id_mhs, 'mk' => $id_mk,
                'angka' => $angka, 'huruf' => $huruf
            ]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Nilai berhasil disimpan!'];
            header("Location: ?page=nilai");
            exit;
        } catch (PDOException $e) {
            // Biasanya terjadi jika kombinasi (mhs + matkul) sudah ada (UNIQUE constraint)
            echo "<script>alert('Gagal! Mahasiswa sudah memiliki nilai untuk matkul ini.'); window.history.back();</script>";
            exit;
        }
    }

    public function update() {
        $id = $_POST['id_nilai'];
        $id_mhs = $_POST['id_mahasiswa'];
        $id_mk = $_POST['id_matkul'];
        $angka = $_POST['nilai_angka'];
        $huruf = $this->hitungNilaiHuruf($angka);
        
        try {
            $sql = "UPDATE nilai SET 
                    id_mahasiswa = :mhs, id_matkul = :mk,
                    nilai_angka = :angka, nilai_huruf = :huruf
                    WHERE id_nilai = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'mhs' => $id_mhs, 'mk' => $id_mk,
                'angka' => $angka, 'huruf' => $huruf,
                'id' => $id
            ]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Nilai berhasil diupdate!'];
            header("Location: ?page=nilai");
            exit;
        } catch (PDOException $e) {
            echo "<script>alert('Gagal update! Data duplikat.'); window.history.back();</script>";
            exit;
        }
    }

    public function delete($id) {
        $sql = "DELETE FROM nilai WHERE id_nilai = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Nilai berhasil dihapus.'];
        header("Location: ?page=nilai");
        exit;
    }
}
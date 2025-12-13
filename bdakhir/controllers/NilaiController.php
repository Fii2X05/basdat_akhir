<?php
// controllers/NilaiController.php

class NilaiController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // --- HELPER: Konversi Angka ke Huruf ---
    private function hitungNilaiHuruf($angka) {
        if ($angka >= 85) return 'A';
        if ($angka >= 75) return 'B';
        if ($angka >= 60) return 'C';
        if ($angka >= 50) return 'D';
        return 'E';
    }

    // --- HELPER: Cek Duplikat Matkul per Mahasiswa ---
    private function isDuplicate($id_mhs, $id_matkul, $exclude_id = null) {
        $sql = "SELECT COUNT(*) FROM nilai WHERE id_mahasiswa = :mhs AND id_matkul = :mk";
        $params = ['mhs' => $id_mhs, 'mk' => $id_matkul];

        if ($exclude_id) {
            $sql .= " AND id_nilai != :ex_id";
            $params['ex_id'] = $exclude_id;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    // --- LOGIC TAMBAH DATA (STORE) ---
    public function store() {
        $mahasiswa = $_POST['id_mahasiswa'];
        $matkul = $_POST['id_matkul'];
        $angka = $_POST['nilai_angka'];

        // 1. Validasi Range Angka
        if ($angka < 0 || $angka > 100) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal: Nilai harus antara 0 sampai 100!'];
            header('Location: index.php?page=nilai&act=create');
            exit;
        }

        // 2. Validasi Duplikat (Satu Mahasiswa tdk boleh punya 2 nilai di matkul yg sama)
        if ($this->isDuplicate($mahasiswa, $matkul)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal: Mahasiswa ini sudah memiliki nilai untuk mata kuliah tersebut!'];
            header('Location: index.php?page=nilai&act=create');
            exit;
        }

        // 3. Hitung Huruf Otomatis
        $huruf = $this->hitungNilaiHuruf($angka);

        try {
            $sql = "INSERT INTO nilai (id_mahasiswa, id_matkul, nilai_angka, nilai_huruf) 
                    VALUES (:mhs, :mk, :angka, :huruf)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'mhs'   => $mahasiswa,
                'mk'    => $matkul,
                'angka' => $angka,
                'huruf' => $huruf
            ]);

            $_SESSION['flash'] = ['type' => 'success', 'message' => "Nilai berhasil disimpan! (Konversi: $huruf)"];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menyimpan: ' . $e->getMessage()];
        }

        header('Location: index.php?page=nilai');
        exit;
    }

    // --- LOGIC UPDATE DATA ---
    public function update() {
        $id = $_POST['id_nilai'];
        $mahasiswa = $_POST['id_mahasiswa'];
        $matkul = $_POST['id_matkul'];
        $angka = $_POST['nilai_angka'];

        if ($angka < 0 || $angka > 100) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal Update: Nilai harus 0-100!'];
            header("Location: index.php?page=nilai&act=edit&id=$id");
            exit;
        }

        if ($this->isDuplicate($mahasiswa, $matkul, $id)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal Update: Data ganda terdeteksi!'];
            header("Location: index.php?page=nilai&act=edit&id=$id");
            exit;
        }

        $huruf = $this->hitungNilaiHuruf($angka);

        try {
            $sql = "UPDATE nilai SET id_mahasiswa=:mhs, id_matkul=:mk, 
                    nilai_angka=:angka, nilai_huruf=:huruf 
                    WHERE id_nilai=:id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'mhs'   => $mahasiswa,
                'mk'    => $matkul,
                'angka' => $angka,
                'huruf' => $huruf,
                'id'    => $id
            ]);

            $_SESSION['flash'] = ['type' => 'success', 'message' => "Nilai diperbarui! (Konversi: $huruf)"];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal update: ' . $e->getMessage()];
        }

        header('Location: index.php?page=nilai');
        exit;
    }

    // --- LOGIC DELETE (STORED PROCEDURE) ---
    public function delete($id) {
        try {
            // Memanggil Stored Procedure
            $sql = "CALL hapus_nilai_lengkap(:id)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data nilai berhasil dihapus!'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menghapus data: ' . $e->getMessage()];
        }

        header('Location: index.php?page=nilai');
        exit;
    }
    public function refresh() {
        try {
            $this->pdo->exec("REFRESH MATERIALIZED VIEW public.mv_nilai_stats");
            
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data Nilai & Status Kelulusan berhasil diperbarui!'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal refresh data: ' . $e->getMessage()];
        }

        header('Location: index.php?page=nilai');
        exit;
    }
}
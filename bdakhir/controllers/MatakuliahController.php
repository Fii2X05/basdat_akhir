<?php
// controllers/MatakuliahController.php

class MatakuliahController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // --- LOGIC TAMBAH DATA (STORE) ---
    public function store() {
        $kode = $_POST['kode_matkul'];
        $nama = $_POST['nama_matkul'];
        $sks = $_POST['sks'];
        $smt = $_POST['semester'];
        $jurusan = $_POST['id_jurusan'];
        
        // Handle Prasyarat (Bisa Null/Kosong)
        $prasyarat = !empty($_POST['prasyarat_id']) ? $_POST['prasyarat_id'] : null;

        // Validasi
        if (empty($kode) || empty($nama)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal: Kode dan Nama Matkul wajib diisi!'];
            header('Location: index.php?page=matakuliah&act=create');
            exit;
        }

        try {
            // Cek Duplikat Kode Matkul
            $stmtCek = $this->pdo->prepare("SELECT COUNT(*) FROM mata_kuliah WHERE kode_matkul ILIKE ?");
            $stmtCek->execute([$kode]);
            if ($stmtCek->fetchColumn() > 0) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal: Kode Mata Kuliah sudah ada!'];
                header('Location: index.php?page=matakuliah&act=create');
                exit;
            }

            $sql = "INSERT INTO mata_kuliah (kode_matkul, nama_matkul, sks, semester, id_jurusan, prasyarat_id) 
                    VALUES (:kode, :nama, :sks, :smt, :jur, :pras)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'kode' => $kode,
                'nama' => $nama,
                'sks'  => $sks,
                'smt'  => $smt,
                'jur'  => $jurusan,
                'pras' => $prasyarat
            ]);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Mata Kuliah berhasil ditambahkan!'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menyimpan: ' . $e->getMessage()];
        }

        header('Location: index.php?page=matakuliah');
        exit;
    }

    // --- LOGIC UPDATE DATA ---
    public function update() {
        $id = $_POST['id_matkul'];
        $kode = $_POST['kode_matkul'];
        $nama = $_POST['nama_matkul'];
        $sks = $_POST['sks'];
        $smt = $_POST['semester'];
        $jurusan = $_POST['id_jurusan'];
        
        // Handle Prasyarat (Jika memilih diri sendiri, set null agar tidak error circular)
        $prasyarat = !empty($_POST['prasyarat_id']) ? $_POST['prasyarat_id'] : null;
        if ($prasyarat == $id) {
            $prasyarat = null; 
        }

        if (empty($kode) || empty($nama)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal Update: Data tidak lengkap!'];
            header("Location: index.php?page=matakuliah&act=edit&id=$id");
            exit;
        }

        try {
            $sql = "UPDATE mata_kuliah SET kode_matkul=:kode, nama_matkul=:nama, sks=:sks, 
                    semester=:smt, id_jurusan=:jur, prasyarat_id=:pras 
                    WHERE id_matkul=:id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'kode' => $kode,
                'nama' => $nama,
                'sks'  => $sks,
                'smt'  => $smt,
                'jur'  => $jurusan,
                'pras' => $prasyarat,
                'id'   => $id
            ]);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Mata Kuliah berhasil diperbarui!'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal update: ' . $e->getMessage()];
        }

        header('Location: index.php?page=matakuliah');
        exit;
    }

    // --- LOGIC DELETE (STORED PROCEDURE) ---
    public function delete($id) {
        try {
            // Memanggil Stored Procedure
            // 1. Matkul lain yang menjadikan ini prasyarat -> prasyarat_id jadi NULL
            // 2. Hapus Nilai & Jadwal terkait
            // 3. Hapus Matkul ini
            $sql = "CALL hapus_matkul_lengkap(:id)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Mata Kuliah berhasil dihapus (Data ketergantungan telah dibersihkan)!'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menghapus data: ' . $e->getMessage()];
        }

        header('Location: index.php?page=matakuliah');
        exit;
    }
    public function refresh() {
        try {
            $this->pdo->exec("REFRESH MATERIALIZED VIEW public.mv_matkul_stats");
            
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Statistik Mata Kuliah berhasil diperbarui!'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal refresh data: ' . $e->getMessage()];
        }

        header('Location: index.php?page=matakuliah');
        exit;
    }
}
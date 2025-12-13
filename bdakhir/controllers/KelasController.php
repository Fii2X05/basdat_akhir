<?php
// controllers/KelasController.php

class KelasController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // --- LOGIC TAMBAH DATA (STORE) ---
    public function store() {
        $nama = $_POST['nama_kelas'];
        $jurusan = $_POST['id_jurusan'];
        $semester = $_POST['semester'];
        $keterangan = $_POST['keterangan'];

        // Validasi Input
        if (empty($nama) || empty($jurusan)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal: Nama Kelas dan Jurusan wajib diisi!'];
            header('Location: index.php?page=kelas&act=create');
            exit;
        }

        try {
            // Cek Duplikat Nama Kelas
            $stmtCek = $this->pdo->prepare("SELECT COUNT(*) FROM kelas WHERE nama_kelas ILIKE ?");
            $stmtCek->execute([$nama]);
            if ($stmtCek->fetchColumn() > 0) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal: Nama Kelas sudah ada!'];
                header('Location: index.php?page=kelas&act=create');
                exit;
            }

            $sql = "INSERT INTO kelas (nama_kelas, id_jurusan, semester, keterangan) 
                    VALUES (:nama, :jur, :smt, :ket)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'nama' => $nama,
                'jur'  => $jurusan,
                'smt'  => $semester,
                'ket'  => $keterangan
            ]);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Kelas berhasil ditambahkan!'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menyimpan: ' . $e->getMessage()];
        }

        header('Location: index.php?page=kelas');
        exit;
    }

    // --- LOGIC UPDATE DATA ---
    public function update() {
        $id = $_POST['id_kelas'];
        $nama = $_POST['nama_kelas'];
        $jurusan = $_POST['id_jurusan'];
        $semester = $_POST['semester'];
        $keterangan = $_POST['keterangan'];

        if (empty($nama)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal Update: Nama Kelas tidak boleh kosong!'];
            header("Location: index.php?page=kelas&act=edit&id=$id");
            exit;
        }

        try {
            $sql = "UPDATE kelas SET nama_kelas=:nama, id_jurusan=:jur, semester=:smt, keterangan=:ket 
                    WHERE id_kelas=:id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'nama' => $nama,
                'jur'  => $jurusan,
                'smt'  => $semester,
                'ket'  => $keterangan,
                'id'   => $id
            ]);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data kelas berhasil diperbarui!'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal update: ' . $e->getMessage()];
        }

        header('Location: index.php?page=kelas');
        exit;
    }

    // --- LOGIC DELETE (STORED PROCEDURE) ---
    public function delete($id) {
        try {
            // Memanggil Procedure
            // Mahasiswa di kelas ini akan dikeluarkan (id_kelas = NULL)
            // Jadwal kelas ini akan dihapus
            $sql = "CALL hapus_kelas_lengkap(:id)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Kelas berhasil dihapus (Mahasiswa dikeluarkan dari kelas, Jadwal dihapus)!'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menghapus data: ' . $e->getMessage()];
        }

        header('Location: index.php?page=kelas');
        exit;
    }
    public function refresh() {
        try {
            $this->pdo->exec("REFRESH MATERIALIZED VIEW public.mv_kelas_stats");
            
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Statistik Kelas (Jml Mahasiswa) berhasil diperbarui!'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal refresh data: ' . $e->getMessage()];
        }

        header('Location: index.php?page=kelas');
        exit;
    }
}
<?php

class DosenController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // LOGIC TAMBAH DATA (STORE) 
    public function store() {
        // Ambil input sesuai name di form
        $nidn = $_POST['nidn']; 
        $nama = $_POST['nama_dosen'];
        $hp   = $_POST['no_hp'];
        $jur  = $_POST['id_jurusan'];
        $email = $_POST['email'] ?? null;
        $alamat = $_POST['alamat'] ?? null;

        // Validasi Angka
        if (!is_numeric($nidn)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal: NIDN harus berupa angka!'];
            header('Location: index.php?page=dosen&act=create');
            exit;
        }

        try {
            // Cek Duplikat NIDN
            $stmtCek = $this->pdo->prepare("SELECT COUNT(*) FROM dosen WHERE nidn = ?");
            $stmtCek->execute([$nidn]);
            
            if ($stmtCek->fetchColumn() > 0) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal: NIDN sudah terdaftar!'];
                header('Location: index.php?page=dosen&act=create');
                exit;
            }

            // Query Insert (Sesuai kolom di SQL)
            $sql = "INSERT INTO dosen (nidn, nama_dosen, no_hp, id_jurusan, email, alamat) 
                    VALUES (:nidn, :nama, :hp, :jur, :email, :alamat)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'nidn'   => $nidn,
                'nama'   => $nama,
                'hp'     => $hp,
                'jur'    => $jur,
                'email'  => $email,
                'alamat' => $alamat
            ]);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data dosen berhasil ditambahkan!'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menyimpan: ' . $e->getMessage()];
        }

        header('Location: index.php?page=dosen');
        exit;
    }

    //LOGIC UPDATE DATA
    public function update() {
        $id = $_POST['id_dosen'];
        $nidn = $_POST['nidn'];
        $nama = $_POST['nama_dosen'];
        $hp   = $_POST['no_hp'];
        $jur  = $_POST['id_jurusan'];
        $email = $_POST['email'] ?? null;
        $alamat = $_POST['alamat'] ?? null;

        if (!is_numeric($nidn)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal Update: NIDN harus angka!'];
            header("Location: index.php?page=dosen&act=edit&id=$id");
            exit;
        }

        try {
            // Query Update
            $sql = "UPDATE dosen SET nidn=:nidn, nama_dosen=:nama, no_hp=:hp, 
                    id_jurusan=:jur, email=:email, alamat=:alamat
                    WHERE id_dosen=:id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'nidn'   => $nidn,
                'nama'   => $nama,
                'hp'     => $hp,
                'jur'    => $jur,
                'email'  => $email,
                'alamat' => $alamat,
                'id'     => $id
            ]);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data dosen berhasil diperbarui!'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal update: ' . $e->getMessage()];
        }

        header('Location: index.php?page=dosen');
        exit;
    }

    //LOGIC DELETE (PAKAI STORED PROCEDURE)
    public function delete($id) {
        try {
            $sql = "CALL hapus_dosen_lengkap(:id)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Dosen & Jadwal mengajarnya berhasil dihapus!'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menghapus data: ' . $e->getMessage()];
        }

        header('Location: index.php?page=dosen');
        exit;
    }
    public function refresh() {
        try {
            $this->pdo->exec("REFRESH MATERIALIZED VIEW public.mv_dosen_stats");
            
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Statistik Beban Mengajar Dosen berhasil diperbarui!'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal refresh data: ' . $e->getMessage()];
        }

        header('Location: index.php?page=dosen');
        exit;
    }
}
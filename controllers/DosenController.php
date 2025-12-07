<?php
class DosenController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function store() {
        $nip = $_POST['nip'];
        $nama = $_POST['nama_dosen'];
        $telepon = $_POST['telepon'];
        $jurusan = $_POST['id_jurusan'];
        
        try {
            $sql = "INSERT INTO dosen (nip, nama_dosen, telepon, id_jurusan) 
                    VALUES (:nip, :nama, :telp, :jur)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'nip' => $nip, 'nama' => $nama, 'telp' => $telepon, 'jur' => $jurusan
            ]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data dosen berhasil disimpan!'];
        } catch (PDOException $e) {
            $pesan = ($e->getCode() == '23505') ? 'Gagal! NIP sudah terdaftar.' : 'Error: ' . $e->getMessage();
            $_SESSION['flash'] = ['type' => 'danger', 'message' => $pesan];
        }
        header("Location: ?page=dosen");
        exit;
    }

    public function update() {
        $id = $_POST['id_dosen'];
        $nip = $_POST['nip'];
        $nama = $_POST['nama_dosen'];
        $telepon = $_POST['telepon'];
        $jurusan = $_POST['id_jurusan'];
        
        try {
            $sql = "UPDATE dosen SET 
                    nip = :nip, nama_dosen = :nama, telepon = :telp, id_jurusan = :jur
                    WHERE id_dosen = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'nip' => $nip, 'nama' => $nama, 'telp' => $telepon, 
                'jur' => $jurusan, 'id' => $id
            ]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data dosen berhasil diperbarui!'];
        } catch (PDOException $e) {
            $pesan = ($e->getCode() == '23505') ? 'Gagal Update! NIP sudah digunakan.' : 'Error: ' . $e->getMessage();
            $_SESSION['flash'] = ['type' => 'danger', 'message' => $pesan];
        }
        header("Location: ?page=dosen");
        exit;
    }

    public function delete($id) {
        try {
            $sql = "DELETE FROM dosen WHERE id_dosen = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data dosen berhasil dihapus.'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal! Dosen tidak bisa dihapus (mungkin terikat data lain).'];
        }
        header("Location: ?page=dosen");
        exit;
    }
}
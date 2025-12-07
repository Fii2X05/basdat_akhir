<?php
class MahasiswaController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Fungsi Private untuk menangani upload file
    private function uploadFoto($file) {
        $targetDir = "uploads/";
        // Buat folder jika belum ada
        if (!file_exists($targetDir)) { mkdir($targetDir, 0777, true); }

        $fileName = basename($file["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        if(empty($file["name"])) return null;

        $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
        if(!in_array($fileType, $allowTypes)){
            return ['error' => 'Format file harus JPG, JPEG, PNG, atau GIF.'];
        }

        if ($file["size"] > 2000000) { // 2MB
            return ['error' => 'Ukuran file terlalu besar (Maksimal 2MB).'];
        }

        // Rename file agar unik (timestamp_namafile)
        $newFileName = time() . '_' . $fileName;
        $targetFilePath = $targetDir . $newFileName;

        if(move_uploaded_file($file["tmp_name"], $targetFilePath)){
            return $newFileName;
        } else {
            return ['error' => 'Gagal mengupload gambar ke server.'];
        }
    }

    public function store() {
        $nim = trim($_POST['nim']);
        $nama = trim($_POST['nama_mahasiswa']);
        $jk = $_POST['jenis_kelamin'];
        $jurusan = $_POST['id_jurusan'];
        $kelas = $_POST['id_kelas'];
        $angkatan = $_POST['angkatan'];
        $alamat = $_POST['alamat'];

        // Proses Upload
        $fotoName = null;
        if (!empty($_FILES['foto']['name'])) {
            $upload = $this->uploadFoto($_FILES['foto']);
            if (is_array($upload) && isset($upload['error'])) {
                $_SESSION['flash'] = ['type' => 'error', 'message' => $upload['error']];
                header("Location: ?page=mahasiswa");
                exit;
            }
            $fotoName = $upload;
        }

        try {
            $sql = "INSERT INTO mahasiswa (nim, nama_mahasiswa, jenis_kelamin, id_jurusan, id_kelas, angkatan, alamat, foto) 
                    VALUES (:nim, :nama, :jk, :jur, :kls, :angkatan, :alamat, :foto)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'nim' => $nim, 'nama' => $nama, 'jk' => $jk,
                'jur' => $jurusan, 'kls' => $kelas,
                'angkatan' => $angkatan, 'alamat' => $alamat,
                'foto' => $fotoName
            ]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data mahasiswa berhasil disimpan!'];
        } catch (PDOException $e) {
            if ($e->getCode() == '23505') { // Duplicate entry
                $_SESSION['flash'] = ['type' => 'error', 'message' => 'Gagal! NIM ' . $nim . ' sudah terdaftar.'];
            } else {
                $_SESSION['flash'] = ['type' => 'error', 'message' => 'Error: ' . $e->getMessage()];
            }
        }
        header("Location: ?page=mahasiswa");
        exit;
    }

    public function update() {
        $id = $_POST['id_mahasiswa'];
        $nim = trim($_POST['nim']);
        $nama = trim($_POST['nama_mahasiswa']);
        $jk = $_POST['jenis_kelamin'];
        $jurusan = $_POST['id_jurusan'];
        $kelas = $_POST['id_kelas'];
        $angkatan = $_POST['angkatan'];
        $alamat = $_POST['alamat'];
        
        // Ambil foto lama
        $stmtOld = $this->pdo->prepare("SELECT foto FROM mahasiswa WHERE id_mahasiswa = :id");
        $stmtOld->execute(['id' => $id]);
        $oldData = $stmtOld->fetch(PDO::FETCH_ASSOC);
        $fotoName = $oldData['foto']; 

        // Jika ada upload foto baru
        if (!empty($_FILES['foto']['name'])) {
            $upload = $this->uploadFoto($_FILES['foto']);
            if (is_array($upload) && isset($upload['error'])) {
                $_SESSION['flash'] = ['type' => 'error', 'message' => $upload['error']];
                header("Location: ?page=mahasiswa");
                exit;
            }
            // Hapus foto lama fisik
            if ($oldData['foto'] && file_exists("uploads/" . $oldData['foto'])) {
                unlink("uploads/" . $oldData['foto']);
            }
            $fotoName = $upload; 
        }

        try {
            $sql = "UPDATE mahasiswa SET 
                    nim = :nim, nama_mahasiswa = :nama, jenis_kelamin = :jk,
                    id_jurusan = :jur, id_kelas = :kls, angkatan = :angkatan, 
                    alamat = :alamat, foto = :foto
                    WHERE id_mahasiswa = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'nim' => $nim, 'nama' => $nama, 'jk' => $jk,
                'jur' => $jurusan, 'kls' => $kelas,
                'angkatan' => $angkatan, 'alamat' => $alamat,
                'foto' => $fotoName,
                'id' => $id
            ]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data mahasiswa berhasil diperbarui!'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Gagal Update: ' . $e->getMessage()];
        }
        header("Location: ?page=mahasiswa");
        exit;
    }

    public function delete($id) {
        // Hapus foto fisik sebelum hapus data DB
        $stmtOld = $this->pdo->prepare("SELECT foto FROM mahasiswa WHERE id_mahasiswa = :id");
        $stmtOld->execute(['id' => $id]);
        $mhs = $stmtOld->fetch(PDO::FETCH_ASSOC);
        
        if ($mhs && $mhs['foto'] && file_exists("uploads/" . $mhs['foto'])) {
            unlink("uploads/" . $mhs['foto']);
        }

        $sql = "DELETE FROM mahasiswa WHERE id_mahasiswa = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data mahasiswa berhasil dihapus.'];
        header("Location: ?page=mahasiswa");
        exit;
    }
}
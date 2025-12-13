<?php
// controllers/MahasiswaController.php

class MahasiswaController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // --- HELPER: Cek jika POST Max Size terlampaui (File Sangat Besar) ---
    private function checkPostMaxSize() {
        if (empty($_POST) && $_SERVER['CONTENT_LENGTH'] > 0) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal: File yang diupload melebih batas maksimal server (POST_MAX_SIZE)!'];
            // Redirect kembali ke halaman list karena kita tidak tahu ID-nya (POST data hilang)
            header('Location: index.php?page=mahasiswa'); 
            exit;
        }
    }

    // --- LOGIC TAMBAH DATA (STORE) ---
    public function store() {
        $this->checkPostMaxSize();

        $nim = $_POST['nim'];
        $nama = $_POST['nama_mahasiswa'];
        $jk = $_POST['jenis_kelamin'];
        $jurusan = $_POST['id_jurusan'];
        $kelas = $_POST['id_kelas'];
        $angkatan = $_POST['angkatan'];
        $alamat = $_POST['alamat'];
        
        $fotoName = null;
        
        // Cek Status Upload
        $fileError = $_FILES['foto']['error'];

        // Jika user mencoba upload file (Error bukan 4/Kosong)
        if ($fileError !== UPLOAD_ERR_NO_FILE) {
            
            // 1. Cek Error System (File Corrupt / Server Limit)
            if ($fileError !== UPLOAD_ERR_OK) {
                $msg = 'Gagal Upload: Terjadi kesalahan sistem (Kode: ' . $fileError . ')';
                if ($fileError === UPLOAD_ERR_INI_SIZE || $fileError === UPLOAD_ERR_FORM_SIZE) {
                    $msg = 'Gagal: File terlalu besar (Melebihi batas server)!';
                }
                $_SESSION['flash'] = ['type' => 'danger', 'message' => $msg];
                header('Location: index.php?page=mahasiswa&act=create');
                exit; // STOP PROSES INSERT
            }

            // 2. Validasi Ukuran Manual (2MB)
            if ($_FILES['foto']['size'] > 2 * 1024 * 1024) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal: Ukuran foto maksimal 2MB!'];
                header('Location: index.php?page=mahasiswa&act=create');
                exit; 
            }

            // 3. Validasi Ekstensi
            $fileName = $_FILES['foto']['name'];
            $tmpName  = $_FILES['foto']['tmp_name'];
            $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (!in_array($fileExtension, $validExtensions)) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal: Format foto harus JPG, JPEG, PNG, atau GIF!'];
                header('Location: index.php?page=mahasiswa&act=create');
                exit; // STOP PROSES INSERT
            }

            // Lolos Validasi -> Upload
            $fotoName = time() . '_' . $fileName;
            if (!move_uploaded_file($tmpName, 'uploads/' . $fotoName)) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal memindahkan file ke folder uploads!'];
                header('Location: index.php?page=mahasiswa&act=create');
                exit;
            }
        }

        try {
            $sql = "INSERT INTO mahasiswa (nim, nama_mahasiswa, jenis_kelamin, id_jurusan, id_kelas, angkatan, alamat, foto) 
                    VALUES (:nim, :nama, :jk, :jur, :kls, :angkatan, :alamat, :foto)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'nim' => $nim,
                'nama' => $nama,
                'jk' => $jk,
                'jur' => $jurusan,
                'kls' => $kelas,
                'angkatan' => $angkatan,
                'alamat' => $alamat,
                'foto' => $fotoName
            ]);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data mahasiswa berhasil ditambahkan!'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menyimpan: ' . $e->getMessage()];
        }

        header('Location: index.php?page=mahasiswa');
        exit;
    }

    // --- LOGIC UPDATE DATA ---
    public function update() {
        $this->checkPostMaxSize();

        $id = $_POST['id_mahasiswa'];
        $nim = $_POST['nim'];
        $nama = $_POST['nama_mahasiswa'];
        $jk = $_POST['jenis_kelamin'];
        $jurusan = $_POST['id_jurusan'];
        $kelas = $_POST['id_kelas'];
        $angkatan = $_POST['angkatan'];
        $alamat = $_POST['alamat'];

        $fileError = $_FILES['foto']['error'];
        
        // --- SKENARIO 1: USER MENCOBA UPLOAD FOTO (Validasi Ketat) ---
        if ($fileError !== UPLOAD_ERR_NO_FILE) {
            
            // 1. Cek Error System
            if ($fileError !== UPLOAD_ERR_OK) {
                $msg = 'Gagal Update: Terjadi kesalahan upload (Kode: ' . $fileError . ')';
                if ($fileError === UPLOAD_ERR_INI_SIZE || $fileError === UPLOAD_ERR_FORM_SIZE) {
                    $msg = 'Gagal Update: File terlalu besar (Melebihi batas server)!';
                }
                $_SESSION['flash'] = ['type' => 'danger', 'message' => $msg];
                header("Location: index.php?page=mahasiswa&act=edit&id=$id");
                exit; // [PENTING] STOP UPDATE DATABASE
            }

            // 2. Validasi Ukuran (2MB)
            if ($_FILES['foto']['size'] > 2 * 1024 * 1024) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal Update: Ukuran foto maksimal 2MB! Data TIDAK disimpan.'];
                header("Location: index.php?page=mahasiswa&act=edit&id=$id");
                exit; // [PENTING] STOP UPDATE DATABASE
            }

            // 3. Validasi Ekstensi
            $fileName = $_FILES['foto']['name'];
            $tmpName  = $_FILES['foto']['tmp_name'];
            $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (!in_array($fileExtension, $validExtensions)) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal Update: Format tidak didukung!'];
                header("Location: index.php?page=mahasiswa&act=edit&id=$id");
                exit; // [PENTING] STOP UPDATE DATABASE
            }

            // Lolos Validasi -> Upload
            $fotoName = time() . '_' . $fileName;
            if (!move_uploaded_file($tmpName, 'uploads/' . $fotoName)) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal memindahkan file!'];
                header("Location: index.php?page=mahasiswa&act=edit&id=$id");
                exit;
            }

            // Query Update DENGAN Foto
            $sql = "UPDATE mahasiswa SET nim=:nim, nama_mahasiswa=:nama, jenis_kelamin=:jk, 
                    id_jurusan=:jur, id_kelas=:kls, angkatan=:angkatan, alamat=:alamat, foto=:foto 
                    WHERE id_mahasiswa=:id";
            $params = [
                'nim' => $nim, 'nama' => $nama, 'jk' => $jk, 'jur' => $jurusan, 
                'kls' => $kelas, 'angkatan' => $angkatan, 'alamat' => $alamat, 
                'foto' => $fotoName, 'id' => $id
            ];

        } 
        // --- SKENARIO 2: TIDAK ADA UPLOAD FOTO (Update Data Saja) ---
        else {
            $sql = "UPDATE mahasiswa SET nim=:nim, nama_mahasiswa=:nama, jenis_kelamin=:jk, 
                    id_jurusan=:jur, id_kelas=:kls, angkatan=:angkatan, alamat=:alamat 
                    WHERE id_mahasiswa=:id";
            $params = [
                'nim' => $nim, 'nama' => $nama, 'jk' => $jk, 'jur' => $jurusan, 
                'kls' => $kelas, 'angkatan' => $angkatan, 'alamat' => $alamat, 
                'id' => $id
            ];
        }

        // Eksekusi Database
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data mahasiswa berhasil diperbarui!'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal update: ' . $e->getMessage()];
        }

        header('Location: index.php?page=mahasiswa');
        exit;
    }
    

    // --- LOGIC DELETE (STORED PROCEDURE) ---
    public function delete($id) {
        try {
            $sql = "CALL hapus_mahasiswa_lengkap(:id)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Mahasiswa & Riwayat Nilai berhasil dihapus!'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menghapus data: ' . $e->getMessage()];
        }

        header('Location: index.php?page=mahasiswa');
        exit;
    }
    public function refresh() {
        try {
            $this->pdo->exec("REFRESH MATERIALIZED VIEW public.mv_mahasiswa_stats");
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data Mahasiswa berhasil diperbarui!'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal refresh: ' . $e->getMessage()];
        }
        
        header('Location: index.php?page=mahasiswa');
        exit;
    }
}

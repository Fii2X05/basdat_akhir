<?php
// index.php - Main Controller
session_start();

// Include database connection
// File database.php akan membuat variabel $pdo
$dbConfigPath = __DIR__ . '/config/database.php';

// Check if config file exists
if (!file_exists($dbConfigPath)) {
    die("
        <div style='max-width: 600px; margin: 50px auto; padding: 20px; background: #f8d7da; border-radius: 5px; font-family: Arial;'>
            <h3 style='color: #721c24;'>❌ Configuration File Not Found</h3>
            <p style='color: #721c24;'>File <code>config/database.php</code> tidak ditemukan!</p>
            <p style='color: #856404;'>Path: <code>{$dbConfigPath}</code></p>
        </div>
    ");
}

// Include database configuration
require_once $dbConfigPath;

// Verify connection
if (!isset($pdo) || !$pdo) {
    die("
        <div style='max-width: 600px; margin: 50px auto; padding: 20px; background: #f8d7da; border-radius: 5px; font-family: Arial;'>
            <h3 style='color: #721c24;'>❌ Database Connection Failed</h3>
            <p style='color: #721c24;'>Variabel \$pdo tidak tersedia. Periksa file <code>config/database.php</code></p>
            <hr style='border-color: #f5c6cb;'>
            <p style='color: #856404;'>
                <strong>Kemungkinan penyebab:</strong><br>
                1. PostgreSQL service tidak running<br>
                2. Database 'siakad' belum dibuat<br>
                3. Username/password salah<br>
                4. File database.php error (check syntax)
            </p>
        </div>
    ");
}

// Get page and action parameters
$page = $_GET['page'] ?? 'dashboard';
$act = $_GET['act'] ?? 'list';
$id = $_GET['id'] ?? null;

// ========================================
// CRUD LOGIC HANDLERS
// ========================================

// JURUSAN HANDLERS
if ($page === 'jurusan') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $act === 'store') {
        $nama = $_POST['nama_jurusan'];
        $sql = "INSERT INTO jurusan (nama_jurusan) VALUES (:nama)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['nama' => $nama]);
        header("Location: ?page=jurusan");
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $act === 'update') {
        $id = $_POST['id_jurusan'];
        $nama = $_POST['nama_jurusan'];
        $sql = "UPDATE jurusan SET nama_jurusan = :nama WHERE id_jurusan = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['nama' => $nama, 'id' => $id]);
        header("Location: ?page=jurusan");
        exit;
    }
    
    if ($act === 'delete' && $id) {
        try {
            $sql = "DELETE FROM jurusan WHERE id_jurusan = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            echo "<script>alert('Gagal menghapus! Jurusan sedang dipakai.'); window.location='?page=jurusan';</script>";
            exit;
        }
        header("Location: ?page=jurusan");
        exit;
    }
}

// KELAS HANDLERS
if ($page === 'kelas') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $act === 'store') {
        $nama = $_POST['nama_kelas'];
        $jurusan = $_POST['id_jurusan'];
        $smt = $_POST['semester'];
        $ket = $_POST['keterangan'];
        
        $sql = "INSERT INTO kelas (nama_kelas, id_jurusan, semester, keterangan) VALUES (:nama, :jur, :smt, :ket)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['nama' => $nama, 'jur' => $jurusan, 'smt' => $smt, 'ket' => $ket]);
        header("Location: ?page=kelas");
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $act === 'update') {
        $id = $_POST['id_kelas'];
        $nama = $_POST['nama_kelas'];
        $jurusan = $_POST['id_jurusan'];
        $smt = $_POST['semester'];
        $ket = $_POST['keterangan'];
        
        $sql = "UPDATE kelas SET nama_kelas = :nama, id_jurusan = :jur, semester = :smt, keterangan = :ket WHERE id_kelas = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['nama' => $nama, 'jur' => $jurusan, 'smt' => $smt, 'ket' => $ket, 'id' => $id]);
        header("Location: ?page=kelas");
        exit;
    }
    
    if ($act === 'delete' && $id) {
        $sql = "DELETE FROM kelas WHERE id_kelas = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        header("Location: ?page=kelas");
        exit;
    }
}

// MAHASISWA HANDLERS
if ($page === 'mahasiswa') {
    
    // Fungsi Helper untuk Upload Foto
    function uploadFoto($file) {
        $targetDir = "uploads/";
        $fileName = basename($file["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        // 1. Cek apakah ada file yang diupload
        if(empty($file["name"])) return null;

        // 2. Validasi Ekstensi
        $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
        if(!in_array($fileType, $allowTypes)){
            return ['error' => 'Format file harus JPG, JPEG, PNG, atau GIF.'];
        }

        // 3. Validasi Ukuran (Max 2MB)
        if ($file["size"] > 2000000) {
            return ['error' => 'Ukuran file terlalu besar (Maksimal 2MB).'];
        }

        // 4. Rename agar unik (timestamp_namafile)
        $newFileName = time() . '_' . $fileName;
        $targetFilePath = $targetDir . $newFileName;

        // 5. Pindahkan file
        if(move_uploaded_file($file["tmp_name"], $targetFilePath)){
            return $newFileName;
        } else {
            return ['error' => 'Gagal mengupload gambar.'];
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $act === 'store') {
        $nim = trim($_POST['nim']);
        $nama = trim($_POST['nama_mahasiswa']);
        $jk = $_POST['jenis_kelamin'];
        $jurusan = $_POST['id_jurusan'];
        $kelas = $_POST['id_kelas'];
        $angkatan = $_POST['angkatan'];
        $alamat = $_POST['alamat'];

        // Validasi Text
        if (strlen($nim) > 20 || strlen($nama) > 100) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Gagal! Karakter melebihi batas.'];
            header("Location: ?page=mahasiswa");
            exit;
        }

        // --- PROSES UPLOAD FOTO ---
        $fotoName = null;
        if (!empty($_FILES['foto']['name'])) {
            $upload = uploadFoto($_FILES['foto']);
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
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'nim' => $nim, 'nama' => $nama, 'jk' => $jk,
                'jur' => $jurusan, 'kls' => $kelas,
                'angkatan' => $angkatan, 'alamat' => $alamat,
                'foto' => $fotoName
            ]);
            
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data mahasiswa berhasil disimpan!'];
            header("Location: ?page=mahasiswa");
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() == '23505') {
                $_SESSION['flash'] = ['type' => 'error', 'message' => 'Gagal! NIM ' . $nim . ' sudah terdaftar.'];
            } else {
                $_SESSION['flash'] = ['type' => 'error', 'message' => 'Terjadi kesalahan database: ' . $e->getMessage()];
            }
            header("Location: ?page=mahasiswa");
            exit;
        }
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $act === 'update') {
        $id = $_POST['id_mahasiswa'];
        $nim = trim($_POST['nim']);
        $nama = trim($_POST['nama_mahasiswa']);
        $jk = $_POST['jenis_kelamin'];
        $jurusan = $_POST['id_jurusan'];
        $kelas = $_POST['id_kelas'];
        $angkatan = $_POST['angkatan'];
        $alamat = $_POST['alamat'];
        
        $stmtOld = $pdo->prepare("SELECT foto FROM mahasiswa WHERE id_mahasiswa = :id");
        $stmtOld->execute(['id' => $id]);
        $oldData = $stmtOld->fetch(PDO::FETCH_ASSOC);
        $fotoName = $oldData['foto']; 

        if (!empty($_FILES['foto']['name'])) {
            $upload = uploadFoto($_FILES['foto']);
            if (is_array($upload) && isset($upload['error'])) {
                $_SESSION['flash'] = ['type' => 'error', 'message' => $upload['error']];
                header("Location: ?page=mahasiswa");
                exit;
            }
            
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
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'nim' => $nim, 'nama' => $nama, 'jk' => $jk,
                'jur' => $jurusan, 'kls' => $kelas,
                'angkatan' => $angkatan, 'alamat' => $alamat,
                'foto' => $fotoName,
                'id' => $id
            ]);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data mahasiswa berhasil diperbarui!'];
            header("Location: ?page=mahasiswa");
            exit;

        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Gagal Update: ' . $e->getMessage()];
            header("Location: ?page=mahasiswa");
            exit;
        }
    }
    
    if ($act === 'delete' && $id) {
        $stmtOld = $pdo->prepare("SELECT foto FROM mahasiswa WHERE id_mahasiswa = :id");
        $stmtOld->execute(['id' => $id]);
        $mhs = $stmtOld->fetch(PDO::FETCH_ASSOC);
        
        if ($mhs && $mhs['foto'] && file_exists("uploads/" . $mhs['foto'])) {
            unlink("uploads/" . $mhs['foto']);
        }

        $sql = "DELETE FROM mahasiswa WHERE id_mahasiswa = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data mahasiswa berhasil dihapus.'];
        header("Location: ?page=mahasiswa");
        exit;
    }
}

// MATA KULIAH HANDLERS
if ($page === 'matakuliah') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $act === 'store') {
        $kode = $_POST['kode_matkul'];
        $nama = $_POST['nama_matkul'];
        $sks = $_POST['sks'];
        $smt = $_POST['semester'];
        $jurusan = $_POST['id_jurusan'];
        
        try {
            $sql = "INSERT INTO mata_kuliah (kode_matkul, nama_matkul, sks, semester, id_jurusan) 
                    VALUES (:kode, :nama, :sks, :smt, :jur)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'kode' => $kode, 'nama' => $nama,
                'sks' => $sks, 'smt' => $smt, 'jur' => $jurusan
            ]);
            header("Location: ?page=matakuliah");
            exit;
        } catch (PDOException $e) {
            echo "<script>alert('Gagal! Kode Mata Kuliah mungkin sudah ada.'); window.history.back();</script>";
            exit;
        }
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $act === 'update') {
        $id = $_POST['id_matkul'];
        $kode = $_POST['kode_matkul'];
        $nama = $_POST['nama_matkul'];
        $sks = $_POST['sks'];
        $smt = $_POST['semester'];
        $jurusan = $_POST['id_jurusan'];
        
        $sql = "UPDATE mata_kuliah SET 
                kode_matkul = :kode, nama_matkul = :nama,
                sks = :sks, semester = :smt, id_jurusan = :jur
                WHERE id_matkul = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'kode' => $kode, 'nama' => $nama,
            'sks' => $sks, 'smt' => $smt, 'jur' => $jurusan,
            'id' => $id
        ]);
        header("Location: ?page=matakuliah");
        exit;
    }
    
    if ($act === 'delete' && $id) {
        $sql = "DELETE FROM mata_kuliah WHERE id_matkul = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        header("Location: ?page=matakuliah");
        exit;
    }
}

// DOSEN HANDLERS
if ($page === 'dosen') {
    
    // 1. CREATE / STORE (Simpan Data Baru)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $act === 'store') {
        // Ambil data sesuai name="" di form views/dosen.php
        $nip     = $_POST['nip'];
        $nama    = $_POST['nama_dosen'];
        $telepon = $_POST['telepon']; // Database pakai 'telepon', bukan 'no_hp'
        $jurusan = $_POST['id_jurusan'];
        
        try {
            // Sesuaikan query dengan kolom yang ADA di tabel dosen (siakad.sql)
            // Kolom: nip, nama_dosen, telepon, id_jurusan
            $sql = "INSERT INTO dosen (nip, nama_dosen, telepon, id_jurusan) 
                    VALUES (:nip, :nama, :telp, :jur)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'nip'   => $nip, 
                'nama'  => $nama, 
                'telp'  => $telepon, 
                'jur'   => $jurusan
            ]);
            
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data dosen berhasil disimpan!'];
            header("Location: ?page=dosen");
            exit;

        } catch (PDOException $e) {
            // Cek error duplicate (kode 23505 di PostgreSQL)
            $pesan = ($e->getCode() == '23505') ? 'Gagal! NIP sudah terdaftar.' : 'Terjadi kesalahan sistem: ' . $e->getMessage();
            $_SESSION['flash'] = ['type' => 'danger', 'message' => $pesan];
            header("Location: ?page=dosen");
            exit;
        }
    }
    
    // 2. UPDATE (Simpan Perubahan)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $act === 'update') {
        $id      = $_POST['id_dosen'];
        $nip     = $_POST['nip'];
        $nama    = $_POST['nama_dosen'];
        $telepon = $_POST['telepon'];
        $jurusan = $_POST['id_jurusan'];
        
        try {
            $sql = "UPDATE dosen SET 
                    nip = :nip, 
                    nama_dosen = :nama, 
                    telepon = :telp, 
                    id_jurusan = :jur
                    WHERE id_dosen = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'nip'   => $nip, 
                'nama'  => $nama, 
                'telp'  => $telepon, 
                'jur'   => $jurusan,
                'id'    => $id
            ]);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data dosen berhasil diperbarui!'];
            header("Location: ?page=dosen");
            exit;

        } catch (PDOException $e) {
            $pesan = ($e->getCode() == '23505') ? 'Gagal Update! NIP sudah digunakan dosen lain.' : 'Terjadi kesalahan sistem: ' . $e->getMessage();
            $_SESSION['flash'] = ['type' => 'danger', 'message' => $pesan];
            header("Location: ?page=dosen");
            exit;
        }
    }
    
    // 3. DELETE (Hapus Data)
    if ($act === 'delete' && $id) {
        try {
            $sql = "DELETE FROM dosen WHERE id_dosen = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id' => $id]);
            
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data dosen berhasil dihapus.'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menghapus data. Dosen mungkin sedang aktif mengajar.'];
        }
        header("Location: ?page=dosen");
        exit;
    }
}
// NILAI HANDLERS
if ($page === 'nilai') {
    // Helper function
    function hitungNilaiHuruf($angka) {
        if ($angka >= 85) return 'A';
        if ($angka >= 75) return 'B';
        if ($angka >= 65) return 'C';
        if ($angka >= 55) return 'D';
        return 'E';
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $act === 'store') {
        $id_mhs = $_POST['id_mahasiswa'];
        $id_mk = $_POST['id_matkul'];
        $angka = $_POST['nilai_angka'];
        $huruf = hitungNilaiHuruf($angka);
        
        try {
            $sql = "INSERT INTO nilai (id_mahasiswa, id_matkul, nilai_angka, nilai_huruf) 
                    VALUES (:mhs, :mk, :angka, :huruf)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'mhs' => $id_mhs, 'mk' => $id_mk,
                'angka' => $angka, 'huruf' => $huruf
            ]);
            header("Location: ?page=nilai");
            exit;
        } catch (PDOException $e) {
            echo "<script>alert('Gagal! Mahasiswa sudah punya nilai untuk mata kuliah ini.'); window.history.back();</script>";
            exit;
        }
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $act === 'update') {
        $id = $_POST['id_nilai'];
        $id_mhs = $_POST['id_mahasiswa'];
        $id_mk = $_POST['id_matkul'];
        $angka = $_POST['nilai_angka'];
        $huruf = hitungNilaiHuruf($angka);
        
        try {
            $sql = "UPDATE nilai SET 
                    id_mahasiswa = :mhs, id_matkul = :mk,
                    nilai_angka = :angka, nilai_huruf = :huruf
                    WHERE id_nilai = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'mhs' => $id_mhs, 'mk' => $id_mk,
                'angka' => $angka, 'huruf' => $huruf,
                'id' => $id
            ]);
            header("Location: ?page=nilai");
            exit;
        } catch (PDOException $e) {
            echo "<script>alert('Gagal update! Kombinasi sudah ada.'); window.history.back();</script>";
            exit;
        }
    }
    
    if ($act === 'delete' && $id) {
        $sql = "DELETE FROM nilai WHERE id_nilai = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        header("Location: ?page=nilai");
        exit;
    }
}

// ========================================
// HTML LAYOUT
// ========================================
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIAKAD - Sistem Informasi Akademik</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding-top: 20px;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 5px 15px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255,255,255,0.2);
        }
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
        }
        .content-area {
            padding: 30px;
        }
        .card-custom {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            color: white !important;
            font-weight: bold;
            font-size: 1.4rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <div class="text-center mb-4">
                    <h4 class="navbar-brand">SIAKAD</h4>
                    <small class="text-white-50">Sistem Akademik</small>
                </div>
                
                <nav class="nav flex-column">
                    <a class="nav-link <?= ($page === 'dashboard') ? 'active' : '' ?>" href="?page=dashboard">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a class="nav-link <?= ($page === 'mahasiswa') ? 'active' : '' ?>" href="?page=mahasiswa">
                        <i class="bi bi-people"></i> Mahasiswa
                    </a>
                    <a class="nav-link <?= ($page === 'dosen') ? 'active' : '' ?>" href="?page=dosen">
                        <i class="bi bi-person-badge"></i> Dosen
                    </a>
                    <a class="nav-link <?= ($page === 'jurusan') ? 'active' : '' ?>" href="?page=jurusan">
                        <i class="bi bi-diagram-3"></i> Jurusan
                    </a>
                    <a class="nav-link <?= ($page === 'kelas') ? 'active' : '' ?>" href="?page=kelas">
                        <i class="bi bi-building"></i> Kelas
                    </a>
                    <a class="nav-link <?= ($page === 'matakuliah') ? 'active' : '' ?>" href="?page=matakuliah">
                        <i class="bi bi-journal-text"></i> Mata Kuliah
                    </a>
                    <a class="nav-link <?= ($page === 'nilai') ? 'active' : '' ?>" href="?page=nilai">
                        <i class="bi bi-bar-chart"></i> Nilai
                    </a>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 content-area">
                <?php
                // Load view file berdasarkan parameter page
                $viewFile = __DIR__ . "/views/{$page}.php";
                
                if (file_exists($viewFile)) {
                    // Include view file - $pdo variable will be available in views
                    include $viewFile;
                } else {
                    echo "<div class='alert alert-danger'>
                        <h5>Halaman tidak ditemukan!</h5>
                        <p>File <code>views/{$page}.php</code> tidak ditemukan.</p>
                        <a href='?page=dashboard' class='btn btn-primary btn-sm'>Kembali ke Dashboard</a>
                    </div>";
                }
                ?>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
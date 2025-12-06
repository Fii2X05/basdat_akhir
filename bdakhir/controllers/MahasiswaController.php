<?php
require_once "models/MahasiswaModel.php";
require_once "models/JurusanModel.php";
require_once "models/KelasModel.php";

class MahasiswaController {
    private $model;
    private $jurusan;
    private $kelas;

    // PERBAIKAN: Menerima $db di constructor
    public function __construct($db) {
        $this->model = new MahasiswaModel($db);
        $this->jurusan = new JurusanModel($db);
        $this->kelas = new KelasModel($db);
    }

    public function index() {
        $data = $this->model->getAll();
        include "views/mahasiswa_index.php";
    }

    public function create() {
        $jurusan = $this->jurusan->getAll();
        $kelas = $this->kelas->getAll();
        include "views/mahasiswa_create.php";
    }

    private function handleUpload() {
        if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == 0) {
            $target_dir = "assets/images/mahasiswa_photos/";
            $file_name = uniqid() . basename($_FILES['foto_profil']['name']);
            $target_file = $target_dir . $file_name;
            
            // Pindahkan file yang di-upload
            if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $target_file)) {
                return $file_name; 
            }
        }
        return null; // Tidak ada upload atau upload gagal
    }

    public function store() {
        $foto_profil = $this->handleUpload();
        
        $data = [
            'npm'         => $_POST['npm'],
            'nama'        => $_POST['nama'],
            'email'       => $_POST['email'],
            'no_hp'       => $_POST['no_hp'],
            'id_jurusan'  => $_POST['id_jurusan'],
            'id_kelas'    => $_POST['id_kelas'],
            'tahun_masuk' => $_POST['tahun_masuk'], // Tambah tahun masuk
            'foto_profil' => $foto_profil // Tambah foto
        ];

        // PERBAIKAN: Menggunakan create()
        $this->model->create($data); 
        header("Location: index.php?action=mahasiswa_index");
        exit;
    }

    public function edit() {
        $id = $_GET['id'];
        // PERBAIKAN: Menggunakan getById()
        $mhs = $this->model->getById($id); 
        $jurusan = $this->jurusan->getAll();
        $kelas = $this->kelas->getAll();
        include "views/mahasiswa_edit.php";
    }

    public function update() {
        $id_mahasiswa = $_POST['id'];
        $foto_profil_baru = $this->handleUpload();

        $data = [
            'npm'         => $_POST['npm'],
            'nama'        => $_POST['nama'],
            'email'       => $_POST['email'],
            'no_hp'       => $_POST['no_hp'],
            'id_jurusan'  => $_POST['id_jurusan'],
            'id_kelas'    => $_POST['id_kelas'],
            'tahun_masuk' => $_POST['tahun_masuk'], 
            'foto_profil' => $foto_profil_baru // akan kosong jika tidak ada upload baru
        ];

        // PERBAIKAN: Memanggil update() dengan DUA argumen: ID dan Data
        $this->model->update($id_mahasiswa, $data); 
        header("Location: index.php?action=mahasiswa_index");
        exit;
    }

    public function delete() {
        $this->model->delete($_GET['id']);
        header("Location: index.php?action=mahasiswa_index");
        exit;
    }
}
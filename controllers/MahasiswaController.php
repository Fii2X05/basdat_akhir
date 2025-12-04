<?php
require_once "models/MahasiswaModel.php";
require_once "models/JurusanModel.php";
require_once "models/KelasModel.php";

class MahasiswaController {
    private $model;
    private $jurusan;
    private $kelas;

    public function __construct() {
        $this->model = new MahasiswaModel();
        $this->jurusan = new JurusanModel();
        $this->kelas = new KelasModel();
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

    public function store() {
        $data = [
            'npm'        => $_POST['npm'],
            'nama'       => $_POST['nama'],
            'email'      => $_POST['email'],
            'no_hp'      => $_POST['no_hp'],
            'id_jurusan' => $_POST['id_jurusan'],
            'id_kelas'   => $_POST['id_kelas']
        ];

        $this->model->insert($data);
        header("Location: index.php?action=mahasiswa_index");
        exit;
    }

    public function edit() {
        $id = $_GET['id'];
        $mhs = $this->model->find($id);
        $jurusan = $this->jurusan->getAll();
        $kelas = $this->kelas->getAll();
        include "views/mahasiswa_edit.php";
    }

    public function update() {
        $data = [
            'id'         => $_POST['id'],
            'npm'        => $_POST['npm'],
            'nama'       => $_POST['nama'],
            'email'      => $_POST['email'],
            'no_hp'      => $_POST['no_hp'],
            'id_jurusan' => $_POST['id_jurusan'],
            'id_kelas'   => $_POST['id_kelas']
        ];

        $this->model->update($data);
        header("Location: index.php?action=mahasiswa_index");
        exit;
    }

    public function delete() {
        $this->model->delete($_GET['id']);
        header("Location: index.php?action=mahasiswa_index");
        exit;
    }
}
?>

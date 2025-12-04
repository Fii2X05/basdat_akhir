<?php
require_once "models/MatakuliahModel.php";
require_once "models/JurusanModel.php";

class MatakuliahController {
    private $model;
    private $jurusan;

    public function __construct() {
        $this->model = new MatakuliahModel();
        $this->jurusan = new JurusanModel();
    }

    public function index() {
        $data = $this->model->getAll();
        include "views/matakuliah_index.php";
    }

    public function create() {
        $jurusan = $this->jurusan->getAll();
        include "views/matakuliah_create.php";
    }

    public function store() {
        $data = [
            'kode'       => $_POST['kode'],
            'nama'       => $_POST['nama'],
            'sks'        => $_POST['sks'],
            'id_jurusan' => $_POST['id_jurusan']
        ];

        $this->model->insert($data);
        header("Location: index.php?action=matakuliah_index");
        exit;
    }

    public function edit() {
        $mk = $this->model->find($_GET['id']);
        $jurusan = $this->jurusan->getAll();
        include "views/matakuliah_edit.php";
    }

    public function update() {
        $data = [
            'id'         => $_POST['id'],
            'kode'       => $_POST['kode'],
            'nama'       => $_POST['nama'],
            'sks'        => $_POST['sks'],
            'id_jurusan' => $_POST['id_jurusan']
        ];

        $this->model->update($data);
        header("Location: index.php?action=matakuliah_index");
        exit;
    }

    public function delete() {
        $this->model->delete($_GET['id']);
        header("Location: index.php?action=matakuliah_index");
        exit;
    }
}
?>

<?php
require_once "models/MatakuliahModel.php";
require_once "models/JurusanModel.php";

class MatakuliahController {
    private $model;
    private $jurusan;

    public function __construct($db) {
        $this->model = new MatakuliahModel($db);
        $this->jurusan = new JurusanModel($db); 
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

        $this->model->create($data); 
        header("Location: index.php?action=matakuliah_index");
        exit;
    }

    public function edit() {
        $mk = $this->model->getById($_GET['id']);
        $jurusan = $this->jurusan->getAll();
        include "views/matakuliah_edit.php";
    }

    public function update() {
        $id_matakuliah = $_POST['id'];
        
        $data = [
            'kode'       => $_POST['kode'],
            'nama'       => $_POST['nama'],
            'sks'        => $_POST['sks'],
            'id_jurusan' => $_POST['id_jurusan']
        ];

        $this->model->update($id_matakuliah, $data); 
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

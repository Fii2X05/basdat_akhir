<?php
require_once "models/KelasModel.php";
require_once "models/JurusanModel.php";

class KelasController {
    private $model;
    private $jurusan;

    public function __construct($db) {
        $this->model = new KelasModel($db);
        $this->jurusan = new JurusanModel($db);
    }

    public function index() {
        $data = $this->model->getAll();
        include "views/kelas.php"; 
    }

    public function create() {
        $jurusan = $this->jurusan->getAll();
        include "views/kelas_create.php";
    }

    public function store() {
        $data = [
            'nama'       => $_POST['nama'],
            'id_jurusan' => $_POST['id_jurusan']
        ];
        $this->model->create($data);
        header("Location: index.php?action=kelas_index");
        exit;
    }

    public function edit() {
        $kelas = $this->model->getById($_GET['id']);
        $jurusan = $this->jurusan->getAll();
        include "views/kelas_edit.php";
    }

    public function update() {
        $id_kelas = $_POST['id'];
        
        $data = [
            'nama'       => $_POST['nama'],
            'id_jurusan' => $_POST['id_jurusan']
        ];

        $this->model->update($id_kelas, $data);
        header("Location: index.php?action=kelas_index");
        exit;
    }

    public function delete() {
        $this->model->delete($_GET['id']);
        header("Location: index.php?action=kelas_index");
        exit;
    }
}
?>
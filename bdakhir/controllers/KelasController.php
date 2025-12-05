<?php
require_once "models/KelasModel.php";
require_once "models/JurusanModel.php";

class KelasController {
    private $model;
    private $jurusan;

    public function __construct() {
        $this->model = new KelasModel();
        $this->jurusan = new JurusanModel();
    }

    public function index() {
        $data = $this->model->getAll();
        include "views/kelas_index.php";
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
        $this->model->insert($data);
        header("Location: index.php?action=kelas_index");
        exit;
    }

    public function edit() {
        $kelas = $this->model->find($_GET['id']);
        $jurusan = $this->jurusan->getAll();
        include "views/kelas_edit.php";
    }

    public function update() {
        $data = [
            'id'         => $_POST['id'],
            'nama'       => $_POST['nama'],
            'id_jurusan' => $_POST['id_jurusan']
        ];

        $this->model->update($data);
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

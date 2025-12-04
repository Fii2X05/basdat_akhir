<?php
require_once "models/DosenModel.php";
require_once "models/JurusanModel.php";

class DosenController {
    private $model;
    private $jurusan;

    public function __construct() {
        $this->model = new DosenModel();
        $this->jurusan = new JurusanModel();
    }

    public function index() {
        $data = $this->model->getAll();
        include "views/dosen_index.php";
    }

    public function create() {
        $jurusan = $this->jurusan->getAll();
        include "views/dosen_create.php";
    }

    public function store() {
        $data = [
            'nip'        => $_POST['nip'],
            'nama'       => $_POST['nama'],
            'email'      => $_POST['email'],
            'no_hp'      => $_POST['no_hp'],
            'id_jurusan' => $_POST['id_jurusan'],
            'status'     => $_POST['status']
        ];

        $this->model->insert($data);
        header("Location: index.php?action=dosen_index");
        exit;
    }

    public function edit() {
        $id = $_GET['id'];
        $dosen = $this->model->find($id);
        $jurusan = $this->jurusan->getAll();
        include "views/dosen_edit.php";
    }

    public function update() {
        $data = [
            'id'         => $_POST['id'],
            'nip'        => $_POST['nip'],
            'nama'       => $_POST['nama'],
            'email'      => $_POST['email'],
            'no_hp'      => $_POST['no_hp'],
            'id_jurusan' => $_POST['id_jurusan'],
            'status'     => $_POST['status']
        ];

        $this->model->update($data);
        header("Location: index.php?action=dosen_index");
        exit;
    }

    public function delete() {
        $this->model->delete($_GET['id']);
        header("Location: index.php?action=dosen_index");
        exit;
    }
}
?>

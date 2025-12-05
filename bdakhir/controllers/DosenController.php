<?php
require_once "models/DosenModel.php";

class DosenController {
    private $model;

    public function __construct() {
        $this->model = new DosenModel();
    }

    public function index() {
        $data = $this->model->getAll();
        include "views/dosen_index.php";
    }

    public function create() {
        include "views/dosen_create.php";
    }

    public function store() {
        $data = [
            'nip'       => $_POST['nip'],
            'nama'      => $_POST['nama'],
            'keahlian'  => $_POST['keahlian']
        ];

        $this->model->create($data);
        header("Location: index.php?page=dosen_list");
        exit;
    }

    public function edit() {
        $id = $_GET['id'];
        $dosen = $this->model->getById($id);
        include "views/dosen_edit.php";
    }

    public function update() {
        $data = [
            'id_dosen' => $_POST['id_dosen'],
            'nip'      => $_POST['nip'],
            'nama'     => $_POST['nama'],
            'keahlian' => $_POST['keahlian']
        ];

        $this->model->update($data['id_dosen'], $data);
        header("Location: index.php?page=dosen_list");
        exit;
    }

    public function delete() {
        $this->model->delete($_GET['id']);
        header("Location: index.php?page=dosen_list");
        exit;
    }
}
?>

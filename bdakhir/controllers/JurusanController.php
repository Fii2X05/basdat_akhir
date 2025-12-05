<?php
require_once "models/JurusanModel.php";

class JurusanController {
    private $model;

    public function __construct() {
        $this->model = new JurusanModel();
    }

    public function index() {
        $data = $this->model->getAll();
        include "views/jurusan_index.php";
    }

    public function create() {
        include "views/jurusan_create.php";
    }

    public function store() {
        $this->model->insert(['nama' => $_POST['nama']]);
        header("Location: index.php?action=jurusan_index");
        exit;
    }

    public function edit() {
        $jurusan = $this->model->find($_GET['id']);
        include "views/jurusan_edit.php";
    }

    public function update() {
        $data = [
            'id'   => $_POST['id'],
            'nama' => $_POST['nama']
        ];

        $this->model->update($data);
        header("Location: index.php?action=jurusan_index");
        exit;
    }

    public function delete() {
        $this->model->delete($_GET['id']);
        header("Location: index.php?action=jurusan_index");
        exit;
    }
}
?>

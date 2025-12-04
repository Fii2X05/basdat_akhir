<?php
require_once "models/NilaiModel.php";
require_once "models/MahasiswaModel.php";
require_once "models/MatakuliahModel.php";

class NilaiController {
    private $model;
    private $mhs;
    private $mk;

    public function __construct() {
        $this->model = new NilaiModel();
        $this->mhs = new MahasiswaModel();
        $this->mk = new MatakuliahModel();
    }

    public function index() {
        $data = $this->model->getAll();
        include "views/nilai_index.php";
    }

    public function create() {
        $mhs = $this->mhs->getAll();
        $mk  = $this->mk->getAll();
        include "views/nilai_create.php";
    }

    public function store() {
        $data = [
            'id_mhs'  => $_POST['id_mhs'],
            'id_mk'   => $_POST['id_mk'],
            'nilai'   => $_POST['nilai']
        ];

        $this->model->insert($data);
        header("Location: index.php?action=nilai_index");
        exit;
    }

    public function edit() {
        $nilai = $this->model->find($_GET['id']);
        $mhs = $this->mhs->getAll();
        $mk  = $this->mk->getAll();
        include "views/nilai_edit.php";
    }

    public function update() {
        $data = [
            'id'     => $_POST['id'],
            'id_mhs' => $_POST['id_mhs'],
            'id_mk'  => $_POST['id_mk'],
            'nilai'  => $_POST['nilai']
        ];

        $this->model->update($data);
        header("Location: index.php?action=nilai_index");
        exit;
    }

    public function delete() {
        $this->model->delete($_GET['id']);
        header("Location: index.php?action=nilai_index");
        exit;
    }
}
?>

<?php
require_once "models/DosenModel.php";

class DosenController {
    private $model;

    public function __construct($db) { 
        $this->model = new DosenModel($db);
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
            'nip' 	 	  => $_POST['nip'],
            'nama' 	 	  => $_POST['nama'],
            'email' 	  => $_POST['email'] ?? null,
            'no_hp' 	  => $_POST['no_hp'] ?? null,
            'id_jurusan'  => $_POST['id_jurusan'] ?? null,
            'status' 	  => $_POST['status'] ?? 'Aktif',
        ];

        $this->model->insert($data); 

        header("Location: index.php?page=dosen_list");
        exit;
    }

    public function edit() {
        $id = $_GET['id'];
        
        $dosen = $this->model->find($id);

        include "views/dosen_edit.php";
    }

    public function update() {
        $id_dosen = $_POST['id_dosen'];

        $data = [
            'nip' 	 	  => $_POST['nip'],
            'nama' 	 	  => $_POST['nama'],
            'email' 	  => $_POST['email'] ?? null,
            'no_hp' 	  => $_POST['no_hp'] ?? null,
            'id_jurusan'  => $_POST['id_jurusan'] ?? null,
            'status' 	  => $_POST['status'] ?? 'Aktif',
        ];

        $this->model->update($id_dosen, $data); 

        header("Location: index.php?page=dosen_list");
        exit;
    }

    public function delete() {
        $this->model->delete($_GET['id']);
        header("Location: index.php?page=dosen_list");
        exit;
    }
}
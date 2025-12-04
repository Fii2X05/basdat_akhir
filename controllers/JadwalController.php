<?php
require_once "models/JadwalModel.php";
require_once "models/DosenModel.php";
require_once "models/MatakuliahModel.php";
require_once "models/KelasModel.php";

class JadwalController {
    private $model;
    private $dosen;
    private $mk;
    private $kelas;

    public function __construct() {
        $this->model = new JadwalModel();
        $this->dosen = new DosenModel();
        $this->mk = new MatakuliahModel();
        $this->kelas = new KelasModel();
    }

    public function index() {
        $data = $this->model->getAll();
        include "views/jadwal_index.php";
    }

    public function create() {
        $dosen = $this->dosen->getAll();
        $mk = $this->mk->getAll();
        $kelas = $this->kelas->getAll();
        include "views/jadwal_create.php";
    }

    public function store() {
        $data = [
            'id_dosen'     => $_POST['id_dosen'],
            'id_mk'        => $_POST['id_mk'],
            'id_kelas'     => $_POST['id_kelas'],
            'hari'         => $_POST['hari'],
            'jam_mulai'    => $_POST['jam_mulai'],
            'jam_selesai'  => $_POST['jam_selesai']
        ];

        $this->model->insert($data);
        header("Location: index.php?action=jadwal_index");
        exit;
    }

    public function edit() {
        $jadwal = $this->model->find($_GET['id']);
        $dosen = $this->dosen->getAll();
        $mk = $this->mk->getAll();
        $kelas = $this->kelas->getAll();
        include "views/jadwal_edit.php";
    }

    public function update() {
        $data = [
            'id'           => $_POST['id'],
            'id_dosen'     => $_POST['id_dosen'],
            'id_mk'        => $_POST['id_mk'],
            'id_kelas'     => $_POST['id_kelas'],
            'hari'         => $_POST['hari'],
            'jam_mulai'    => $_POST['jam_mulai'],
            'jam_selesai'  => $_POST['jam_selesai']
        ];

        $this->model->update($data);
        header("Location: index.php?action=jadwal_index");
        exit;
    }

    public function delete() {
        $this->model->delete($_GET['id']);
        header("Location: index.php?action=jadwal_index");
        exit;
    }
}
?>

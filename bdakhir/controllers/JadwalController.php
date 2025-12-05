<?php
require_once "models/JadwalModel.php";
require_once "models/DosenModel.php";
require_once "models/MatakuliahModel.php";
require_once "models/KelasModel.php";
require_once "config.php";

class JadwalController {
    private $model;
    private $dosen;
    private $mk;
    private $kelas;

    public function __construct() {
        $db = (new Database())->connect();

        $this->model = new JadwalModel($db);
        $this->dosen = new DosenModel($db);
        $this->mk    = new MatakuliahModel($db);
        $this->kelas = new KelasModel($db);
    }

    public function index() {
        $data = $this->model->getAll();
        include "views/jadwal_index.php";
    }

    public function create() {
        $dosen = $this->dosen->getAll();
        $mk    = $this->mk->getAll();
        $kelas = $this->kelas->getAll();

        include "views/jadwal_create.php";
    }

    public function store() {
        $data = [
            'id_dosen'      => $_POST['id_dosen'],
            'id_matakuliah' => $_POST['id_matakuliah'],
            'id_kelas'      => $_POST['id_kelas'],
            'hari'          => $_POST['hari'],
            'jam_mulai'     => $_POST['jam_mulai'],
            'jam_selesai'   => $_POST['jam_selesai']
        ];

        $this->model->create($data);
        header("Location: index.php?page=jadwal_list");
        exit;
    }

    public function edit() {
        $jadwal = $this->model->getById($_GET['id']);
        $dosen  = $this->dosen->getAll();
        $mk     = $this->mk->getAll();
        $kelas  = $this->kelas->getAll();

        include "views/jadwal_edit.php";
    }

    public function update() {
        $id = $_POST['id_jadwal'];

        $data = [
            'id_dosen'      => $_POST['id_dosen'],
            'id_matakuliah' => $_POST['id_matakuliah'],
            'id_kelas'      => $_POST['id_kelas'],
            'hari'          => $_POST['hari'],
            'jam_mulai'     => $_POST['jam_mulai'],
            'jam_selesai'   => $_POST['jam_selesai']
        ];

        $this->model->update($id, $data);
        header("Location: index.php?page=jadwal_list");
        exit;
    }

    public function delete() {
        $this->model->delete($_GET['id']);
        header("Location: index.php?page=jadwal_list");
        exit;
    }
}
?>

<?php
require_once "models/NilaiModel.php";
require_once "models/MahasiswaModel.php"; 
require_once "models/MatakuliahModel.php"; 

class NilaiController {
    private $model;
    private $mhs;
    private $mk;

    public function __construct($db) { 
        $this->model = new NilaiModel($db);
        $this->mhs = new MahasiswaModel($db); 
        $this->mk = new MatakuliahModel($db); 
    }
    private function konversiNilaiHuruf($nilai_angka) {
        if ($nilai_angka >= 80) return 'A';
        if ($nilai_angka >= 70) return 'B';
        if ($nilai_angka >= 60) return 'C';
        if ($nilai_angka >= 50) return 'D';
        return 'E';
    }

    public function index() {
        $data = $this->model->getAll();
        include "views/nilai_index.php";
    }

    public function create() {
        $mhs = $this->mhs->getAll(); 
        $mk 	= $this->mk->getAll();
        include "views/nilai_create.php";
    }

    public function store() {
        $nilai_angka = (int)$_POST['nilai'];
        $nilai_huruf = $this->konversiNilaiHuruf($nilai_angka);
        
        $data = [
            'id_mahasiswa' 	=> $_POST['id_mhs'], 
            'id_matakuliah' => $_POST['id_mk'], 	
            'nilai_angka' 	=> $nilai_angka, 	
            'nilai_huruf' 	=> $nilai_huruf 	
        ];

        $this->model->create($data); 
        header("Location: index.php?action=nilai_index");
        exit;
    }

    public function edit() {
        $nilai = $this->model->getById($_GET['id']); 
        $mhs = $this->mhs->getAll();
        $mk 	= $this->mk->getAll();
        include "views/nilai_edit.php";
    }

    public function update() {
        $id_nilai = $_POST['id']; 
        $nilai_angka = (int)$_POST['nilai'];
        $nilai_huruf = $this->konversiNilaiHuruf($nilai_angka);

        $data = [
            'id_mahasiswa' 	=> $_POST['id_mhs'],
            'id_matakuliah' => $_POST['id_mk'],
            'nilai_angka' 	=> $nilai_angka,
            'nilai_huruf' 	=> $nilai_huruf
        ];

        $this->model->update($id_nilai, $data); 
        header("Location: index.php?action=nilai_index");
        exit;
    }

    public function delete() {
        $this->model->delete($_GET['id']);
        header("Location: index.php?action=nilai_index");
        exit;
    }
}
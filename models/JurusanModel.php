<?php
class JurusanModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM jurusan ORDER BY id_jurusan DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id_jurusan) {
        $stmt = $this->db->prepare("SELECT * FROM jurusan WHERE id_jurusan = ?");
        $stmt->execute([$id_jurusan]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO jurusan (nama_jurusan) VALUES (?)");
        return $stmt->execute([$data['nama_jurusan']]);
    }

    public function update($id_jurusan, $data) {
        $stmt = $this->db->prepare("UPDATE jurusan SET nama_jurusan = ? WHERE id_jurusan = ?");
        return $stmt->execute([$data['nama_jurusan'], $id_jurusan]);
    }

    public function delete($id_jurusan) {
        $stmt = $this->db->prepare("DELETE FROM jurusan WHERE id_jurusan = ?");
        return $stmt->execute([$id_jurusan]);
    }
}

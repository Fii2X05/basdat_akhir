<?php
class KelasModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM kelas ORDER BY id_kelas DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id_kelas) {
        $stmt = $this->db->prepare("SELECT * FROM kelas WHERE id_kelas = ?");
        $stmt->execute([$id_kelas]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO kelas (nama_kelas, id_jurusan) VALUES (?, ?)");
        return $stmt->execute([$data['nama'], $data['id_jurusan']]);
    }

    public function update($id_kelas, $data) {
        $stmt = $this->db->prepare("UPDATE kelas SET nama_kelas = ?, id_jurusan = ? WHERE id_kelas = ?");
        return $stmt->execute([$data['nama'], $data['id_jurusan'], $id_kelas]);
    }

    public function delete($id_kelas) {
        $stmt = $this->db->prepare("DELETE FROM kelas WHERE id_kelas = ?");
        return $stmt->execute([$id_kelas]);
    }
}
?>
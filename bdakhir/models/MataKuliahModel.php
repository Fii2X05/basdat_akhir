<?php
class MatakuliahModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM matakuliah ORDER BY id_matakuliah DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id_matakuliah) {
        $stmt = $this->db->prepare("SELECT * FROM matakuliah WHERE id_matakuliah = ?");
        $stmt->execute([$id_matakuliah]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO matakuliah (nama_matakuliah, sks)
            VALUES (?, ?)
        ");
        return $stmt->execute([
            $data['nama_matakuliah'],
            $data['sks']
        ]);
    }

    public function update($id_matakuliah, $data) {
        $stmt = $this->db->prepare("
            UPDATE matakuliah
            SET nama_matakuliah = ?, sks = ?
            WHERE id_matakuliah = ?
        ");

        return $stmt->execute([
            $data['nama_matakuliah'],
            $data['sks'],
            $id_matakuliah
        ]);
    }

    public function delete($id_matakuliah) {
        $stmt = $this->db->prepare("DELETE FROM matakuliah WHERE id_matakuliah = ?");
        return $stmt->execute([$id_matakuliah]);
    }
}

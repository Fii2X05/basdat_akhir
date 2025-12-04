<?php
class DosenModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM dosen ORDER BY id_dosen DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id_dosen) {
        $stmt = $this->db->prepare("SELECT * FROM dosen WHERE id_dosen = ?");
        $stmt->execute([$id_dosen]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO dosen (nama_dosen, nidn, email) VALUES (?, ?, ?)");
        return $stmt->execute([
            $data['nama_dosen'],
            $data['nidn'],
            $data['email']
        ]);
    }

    public function update($id_dosen, $data) {
        $stmt = $this->db->prepare("UPDATE dosen SET nama_dosen = ?, nidn = ?, email = ? WHERE id_dosen = ?");
        return $stmt->execute([
            $data['nama_dosen'],
            $data['nidn'],
            $data['email'],
            $id_dosen
        ]);
    }

    public function delete($id_dosen) {
        $stmt = $this->db->prepare("DELETE FROM dosen WHERE id_dosen = ?");
        return $stmt->execute([$id_dosen]);
    }
}

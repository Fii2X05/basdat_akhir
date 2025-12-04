<?php
class JadwalModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        $query = "
            SELECT j.*, m.nama_matakuliah, d.nama_dosen, k.nama_kelas
            FROM jadwal j
            LEFT JOIN matakuliah m ON j.id_matakuliah = m.id_matakuliah
            LEFT JOIN dosen d ON j.id_dosen = d.id_dosen
            LEFT JOIN kelas k ON j.id_kelas = k.id_kelas
            ORDER BY j.id_jadwal DESC";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id_jadwal) {
        $stmt = $this->db->prepare("SELECT * FROM jadwal WHERE id_jadwal = ?");
        $stmt->execute([$id_jadwal]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO jadwal (id_matakuliah, id_dosen, id_kelas, hari, jam)
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['id_matakuliah'],
            $data['id_dosen'],
            $data['id_kelas'],
            $data['hari'],
            $data['jam']
        ]);
    }

    public function update($id_jadwal, $data) {
        $stmt = $this->db->prepare("
            UPDATE jadwal 
            SET id_matakuliah = ?, id_dosen = ?, id_kelas = ?, hari = ?, jam = ?
            WHERE id_jadwal = ?
        ");
        return $stmt->execute([
            $data['id_matakuliah'],
            $data['id_dosen'],
            $data['id_kelas'],
            $data['hari'],
            $data['jam'],
            $id_jadwal
        ]);
    }

    public function delete($id_jadwal) {
        $stmt = $this->db->prepare("DELETE FROM jadwal WHERE id_jadwal = ?");
        return $stmt->execute([$id_jadwal]);
    }
}

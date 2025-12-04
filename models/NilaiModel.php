<?php
class NilaiModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        $query = "
            SELECT n.*, mhs.nama_mahasiswa, mk.nama_matakuliah
            FROM nilai n
            LEFT JOIN mahasiswa mhs ON n.id_mahasiswa = mhs.id_mahasiswa
            LEFT JOIN matakuliah mk ON n.id_matakuliah = mk.id_matakuliah
            ORDER BY n.id_nilai DESC";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id_nilai) {
        $stmt = $this->db->prepare("SELECT * FROM nilai WHERE id_nilai = ?");
        $stmt->execute([$id_nilai]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO nilai (id_mahasiswa, id_matakuliah, nilai_angka, nilai_huruf)
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['id_mahasiswa'],
            $data['id_matakuliah'],
            $data['nilai_angka'],
            $data['nilai_huruf']
        ]);
    }

    public function update($id_nilai, $data) {
        $stmt = $this->db->prepare("
            UPDATE nilai 
            SET id_mahasiswa = ?, id_matakuliah = ?, nilai_angka = ?, nilai_huruf = ?
            WHERE id_nilai = ?
        ");
        return $stmt->execute([
            $data['id_mahasiswa'],
            $data['id_matakuliah'],
            $data['nilai_angka'],
            $data['nilai_huruf'],
            $id_nilai
        ]);
    }

    public function delete($id_nilai) {
        $stmt = $this->db->prepare("DELETE FROM nilai WHERE id_nilai = ?");
        return $stmt->execute([$id_nilai]);
    }
}

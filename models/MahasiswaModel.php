<?php
class MahasiswaModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        $query = "
            SELECT m.*, j.nama_jurusan, k.nama_kelas
            FROM mahasiswa m
            LEFT JOIN jurusan j ON m.id_jurusan = j.id_jurusan
            LEFT JOIN kelas k ON m.id_kelas = k.id_kelas
            ORDER BY m.id_mahasiswa DESC
        ";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id_mahasiswa) {
        $stmt = $this->db->prepare("SELECT * FROM mahasiswa WHERE id_mahasiswa = ?");
        $stmt->execute([$id_mahasiswa]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO mahasiswa (nama_mahasiswa, nim, email, id_jurusan, id_kelas)
            VALUES (?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $data['nama_mahasiswa'],
            $data['nim'],
            $data['email'],
            $data['id_jurusan'],
            $data['id_kelas']
        ]);
    }

    public function update($id_mahasiswa, $data) {
        $stmt = $this->db->prepare("
            UPDATE mahasiswa
            SET nama_mahasiswa = ?, nim = ?, email = ?, id_jurusan = ?, id_kelas = ?
            WHERE id_mahasiswa = ?
        ");

        return $stmt->execute([
            $data['nama_mahasiswa'],
            $data['nim'],
            $data['email'],
            $data['id_jurusan'],
            $data['id_kelas'],
            $id_mahasiswa
        ]);
    }

    public function delete($id_mahasiswa) {
        $stmt = $this->db->prepare("DELETE FROM mahasiswa WHERE id_mahasiswa = ?");
        return $stmt->execute([$id_mahasiswa]);
    }
}

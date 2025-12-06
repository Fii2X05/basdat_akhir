<?php
class DosenModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        $sql = "SELECT d.*, j.nama_jurusan 
                FROM dosen d 
                LEFT JOIN jurusan j ON d.id_jurusan = j.id_jurusan
                ORDER BY d.id_dosen DESC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $sql = "SELECT * FROM dosen WHERE id_dosen = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]); 
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function insert($data) {
        $sql = "INSERT INTO dosen (nip, nama, email, no_hp, id_jurusan, status) 
                VALUES (?, ?, ?, ?, ?, ?)"; 

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $data['nip'],
            $data['nama'],
            $data['email'],
            $data['no_hp'],
            $data['id_jurusan'],
            $data['status']
        ]);
    }

    public function update($id, $data) { 
        $sql = "UPDATE dosen SET 
                    nip = ?,
                    nama = ?,
                    email = ?,
                    no_hp = ?,
                    id_jurusan = ?,
                    status = ?
                WHERE id_dosen = ?";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $data['nip'],
            $data['nama'],
            $data['email'],
            $data['no_hp'],
            $data['id_jurusan'],
            $data['status'],
            $id
        ]);
    }

    public function delete($id) {
        $sql = "DELETE FROM dosen WHERE id_dosen = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
<?php
class JurusanModel {
    private $db;

    public function __construct() {
        require_once "Database.php";
        $database = new Database();
        $this->db = $database->connect();
    }

    public function getAll() {
        $sql = "SELECT * FROM jurusan ORDER BY id_jurusan DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $sql = "SELECT * FROM jurusan WHERE id_jurusan = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert($data) {
        $sql = "INSERT INTO jurusan (nama) VALUES (:nama)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ":nama" => $data['nama']
        ]);
    }

    public function update($data) {
        $sql = "
            UPDATE jurusan
            SET nama = :nama
            WHERE id_jurusan = :id
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ":nama" => $data['nama'],
            ":id"   => $data['id']
        ]);
    }

    public function delete($id) {
        $sql = "DELETE FROM jurusan WHERE id_jurusan = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([":id" => $id]);
    }
}

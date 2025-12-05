<?php
class DosenModel {
    private $db;

    public function __construct() {
        require_once "Database.php";
        $database = new Database();
        $this->db = $database->connect();
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
        $sql = "SELECT * FROM dosen WHERE id_dosen = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert($data) {
        $sql = "INSERT INTO dosen (nip, nama, email, no_hp, id_jurusan, status) 
                VALUES (:nip, :nama, :email, :no_hp, :id_jurusan, :status)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ":nip"         => $data['nip'],
            ":nama"        => $data['nama'],
            ":email"       => $data['email'],
            ":no_hp"       => $data['no_hp'],
            ":id_jurusan"  => $data['id_jurusan'],
            ":status"      => $data['status']
        ]);
    }

    public function update($data) {
        $sql = "UPDATE dosen SET 
                    nip = :nip,
                    nama = :nama,
                    email = :email,
                    no_hp = :no_hp,
                    id_jurusan = :id_jurusan,
                    status = :status
                WHERE id_dosen = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ":id"          => $data['id'],
            ":nip"         => $data['nip'],
            ":nama"        => $data['nama'],
            ":email"       => $data['email'],
            ":no_hp"       => $data['no_hp'],
            ":id_jurusan"  => $data['id_jurusan'],
            ":status"      => $data['status']
        ]);
    }

    public function delete($id) {
        $sql = "DELETE FROM dosen WHERE id_dosen = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([":id" => $id]);
    }
}

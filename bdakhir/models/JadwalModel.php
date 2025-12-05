<?php
class JadwalModel {
    private $db;

    public function __construct() {
        require_once "Database.php";
        $database = new Database();
        $this->db = $database->connect();
    }

    public function getAll() {
        $sql = "
            SELECT j.*, 
                   m.nama_matakuliah, 
                   d.nama AS nama_dosen, 
                   k.nama_kelas
            FROM jadwal j
            LEFT JOIN matakuliah m ON j.id_matakuliah = m.id_matakuliah
            LEFT JOIN dosen d ON j.id_dosen = d.id_dosen
            LEFT JOIN kelas k ON j.id_kelas = k.id_kelas
            ORDER BY j.id_jadwal DESC
        ";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT * FROM jadwal WHERE id_jadwal = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $sql = "
            INSERT INTO jadwal (id_matakuliah, id_dosen, id_kelas, hari, jam)
            VALUES (:id_matakuliah, :id_dosen, :id_kelas, :hari, :jam)
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ":id_matakuliah" => $data['id_matakuliah'],
            ":id_dosen"      => $data['id_dosen'],
            ":id_kelas"      => $data['id_kelas'],
            ":hari"          => $data['hari'],
            ":jam"           => $data['jam']
        ]);
    }

    public function update($id, $data) {
        $sql = "
            UPDATE jadwal SET
                id_matakuliah = :id_matakuliah,
                id_dosen      = :id_dosen,
                id_kelas      = :id_kelas,
                hari          = :hari,
                jam           = :jam
            WHERE id_jadwal = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ":id"            => $id,
            ":id_matakuliah" => $data['id_matakuliah'],
            ":id_dosen"      => $data['id_dosen'],
            ":id_kelas"      => $data['id_kelas'],
            ":hari"          => $data['hari'],
            ":jam"           => $data['jam']
        ]);
    }

    public function delete($id) {
        $sql = "DELETE FROM jadwal WHERE id_jadwal = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([":id" => $id]);
    }
}

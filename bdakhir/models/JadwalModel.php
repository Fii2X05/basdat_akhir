<?php
class JadwalModel {
    private $db;

    // PERBAIKAN: Menerima $db di constructor
    public function __construct($db) {
        $this->db = $db;
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
    // ... (method getById, create, update, delete lainnya tidak diubah, namun pastikan di controller dipanggil dengan 2 argumen)
    // ...
    // ...
    public function getById($id) {
        $sql = "SELECT * FROM jadwal WHERE id_jadwal = ?"; // Ubah :id menjadi ?
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]); // Ubah array parameter
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $sql = "
            INSERT INTO jadwal (id_matakuliah, id_dosen, id_kelas, hari, jam)
            VALUES (?, ?, ?, ?, ?)
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $data['id_matakuliah'],
            $data['id_dosen'],
            $data['id_kelas'],
            $data['hari'],
            $data['jam']
        ]);
    }

    public function update($id, $data) {
        $sql = "
            UPDATE jadwal SET
                id_matakuliah = ?,
                id_dosen      = ?,
                id_kelas      = ?,
                hari          = ?,
                jam           = ?
            WHERE id_jadwal = ?
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $data['id_matakuliah'],
            $data['id_dosen'],
            $data['id_kelas'],
            $data['hari'],
            $data['jam'],
            $id // ID diletakkan di akhir parameter
        ]);
    }

    public function delete($id) {
        $sql = "DELETE FROM jadwal WHERE id_jadwal = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
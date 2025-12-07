<?php
class JadwalController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function store() {
        $matkul = $_POST['id_matkul'];
        $dosen = $_POST['id_dosen'];
        $kelas = $_POST['id_kelas'];
        $hari = $_POST['hari'];
        $mulai = $_POST['jam_mulai'];
        $selesai = $_POST['jam_selesai'];

        // Validasi dasar
        if ($mulai >= $selesai) {
            echo "<script>alert('Jam selesai harus lebih besar dari jam mulai!'); window.history.back();</script>";
            exit;
        }

        // Simpan
        try {
            $sql = "INSERT INTO jadwal (id_matkul, id_dosen, id_kelas, hari, jam_mulai, jam_selesai) 
                    VALUES (:matkul, :dosen, :kelas, :hari, :mulai, :selesai)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'matkul' => $matkul, 'dosen' => $dosen, 'kelas' => $kelas,
                'hari' => $hari, 'mulai' => $mulai, 'selesai' => $selesai
            ]);
            header("Location: ?page=jadwal");
            exit;
        } catch (PDOException $e) {
            echo "<script>alert('Gagal menyimpan jadwal: " . $e->getMessage() . "'); window.history.back();</script>";
            exit;
        }
    }

    public function update() {
        $id = $_POST['id_jadwal'];
        $matkul = $_POST['id_matkul'];
        $dosen = $_POST['id_dosen'];
        $kelas = $_POST['id_kelas'];
        $hari = $_POST['hari'];
        $mulai = $_POST['jam_mulai'];
        $selesai = $_POST['jam_selesai'];

        if ($mulai >= $selesai) {
            echo "<script>alert('Jam selesai harus lebih besar dari jam mulai!'); window.history.back();</script>";
            exit;
        }

        try {
            $sql = "UPDATE jadwal SET 
                    id_matkul = :matkul, id_dosen = :dosen, id_kelas = :kelas, 
                    hari = :hari, jam_mulai = :mulai, jam_selesai = :selesai
                    WHERE id_jadwal = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'matkul' => $matkul, 'dosen' => $dosen, 'kelas' => $kelas,
                'hari' => $hari, 'mulai' => $mulai, 'selesai' => $selesai,
                'id' => $id
            ]);
            header("Location: ?page=jadwal");
            exit;
        } catch (PDOException $e) {
            echo "<script>alert('Gagal update jadwal.'); window.history.back();</script>";
            exit;
        }
    }

    public function delete($id) {
        $sql = "DELETE FROM jadwal WHERE id_jadwal = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        header("Location: ?page=jadwal");
        exit;
    }
}
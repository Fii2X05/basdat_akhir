<?php

class JadwalController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    //Cek Bentrok Jadwal
    private function isScheduleConflict($hari, $jam_mulai, $jam_selesai, $id_kelas, $id_dosen, $exclude_id = null) {
        // Logika: 
        // 1. Hari harus sama
        // 2. Waktu irisan (Start A < End B) dan (End A > Start B)
        // 3. Entitas sama (Kelas sama ATAU Dosen sama)
        
        $sql = "SELECT COUNT(*) FROM jadwal 
                WHERE hari = :hari 
                AND jam_mulai < :js AND jam_selesai > :jm 
                AND (id_kelas = :kls OR id_dosen = :dsn)";
        
        $params = [
            'hari' => $hari, 
            'js'   => $jam_selesai, 
            'jm'   => $jam_mulai, 
            'kls'  => $id_kelas, 
            'dsn'  => $id_dosen
        ];

        // Jika sedang edit, jangan cek jadwal diri sendiri
        if ($exclude_id) {
            $sql .= " AND id_jadwal != :ex_id";
            $params['ex_id'] = $exclude_id;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
    }

    //LOGIC TAMBAH DATA (STORE)
    public function store() {
        $matkul = $_POST['id_matkul'];
        $kelas = $_POST['id_kelas'];
        $dosen = $_POST['id_dosen'];
        $hari = $_POST['hari'];
        $jam_mulai = $_POST['jam_mulai'];
        $jam_selesai = $_POST['jam_selesai'];

        // Validasi Dasar
        if ($jam_mulai >= $jam_selesai) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal: Jam selesai harus lebih besar dari jam mulai!'];
            header('Location: index.php?page=jadwal&act=create');
            exit;
        }

        try {
            // Validasi Bentrok
            if ($this->isScheduleConflict($hari, $jam_mulai, $jam_selesai, $kelas, $dosen)) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal: Jadwal bentrok! Kelas atau Dosen sudah ada jadwal di jam tersebut.'];
                header('Location: index.php?page=jadwal&act=create');
                exit;
            }

            $sql = "INSERT INTO jadwal (id_matkul, id_kelas, id_dosen, hari, jam_mulai, jam_selesai) 
                    VALUES (:mk, :kls, :dsn, :hari, :jm, :js)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'mk'   => $matkul,
                'kls'  => $kelas,
                'dsn'  => $dosen,
                'hari' => $hari,
                'jm'   => $jam_mulai,
                'js'   => $jam_selesai
            ]);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Jadwal berhasil ditambahkan!'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menyimpan: ' . $e->getMessage()];
        }

        header('Location: index.php?page=jadwal');
        exit;
    }

    //LOGIC UPDATE DATA
    public function update() {
        $id = $_POST['id_jadwal'];
        $matkul = $_POST['id_matkul'];
        $kelas = $_POST['id_kelas'];
        $dosen = $_POST['id_dosen'];
        $hari = $_POST['hari'];
        $jam_mulai = $_POST['jam_mulai'];
        $jam_selesai = $_POST['jam_selesai'];

        if ($jam_mulai >= $jam_selesai) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal Update: Jam error!'];
            header("Location: index.php?page=jadwal&act=edit&id=$id");
            exit;
        }

        try {
            // Validasi Bentrok (Exclude ID sendiri)
            if ($this->isScheduleConflict($hari, $jam_mulai, $jam_selesai, $kelas, $dosen, $id)) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal Update: Jadwal bentrok dengan data lain!'];
                header("Location: index.php?page=jadwal&act=edit&id=$id");
                exit;
            }

            $sql = "UPDATE jadwal SET id_matkul=:mk, id_kelas=:kls, id_dosen=:dsn, 
                    hari=:hari, jam_mulai=:jm, jam_selesai=:js 
                    WHERE id_jadwal=:id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'mk'   => $matkul,
                'kls'  => $kelas,
                'dsn'  => $dosen,
                'hari' => $hari,
                'jm'   => $jam_mulai,
                'js'   => $jam_selesai,
                'id'   => $id
            ]);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Jadwal berhasil diperbarui!'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal update: ' . $e->getMessage()];
        }

        header('Location: index.php?page=jadwal');
        exit;
    }

    //LOGIC DELETE (STORED PROCEDURE)
    public function delete($id) {
        try {
            $sql = "CALL hapus_jadwal_lengkap(:id)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Jadwal berhasil dihapus!'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menghapus data: ' . $e->getMessage()];
        }

        header('Location: index.php?page=jadwal');
        exit;
    }
    public function refresh() {
        try {
            $this->pdo->exec("REFRESH MATERIALIZED VIEW public.mv_jadwal_stats");
            
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Statistik Jadwal (Jumlah Peserta) berhasil diperbarui!'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal refresh data: ' . $e->getMessage()];
        }

        header('Location: index.php?page=jadwal');
        exit;
    }
}
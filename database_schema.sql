-- ============================================
-- SIAKAD Database Schema for PostgreSQL
-- Database: db_kampus
-- ============================================

-- Drop existing tables if they exist (in correct order due to foreign keys)
DROP TABLE IF EXISTS nilai CASCADE;
DROP TABLE IF EXISTS mahasiswa CASCADE;
DROP TABLE IF EXISTS mata_kuliah CASCADE;
DROP TABLE IF EXISTS dosen CASCADE;
DROP TABLE IF EXISTS kelas CASCADE;
DROP TABLE IF EXISTS jurusan CASCADE;

-- ============================================
-- 1. JURUSAN TABLE
-- ============================================
CREATE TABLE jurusan (
    id_jurusan SERIAL PRIMARY KEY,
    nama_jurusan VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- 2. KELAS TABLE
-- ============================================
CREATE TABLE kelas (
    id_kelas SERIAL PRIMARY KEY,
    nama_kelas VARCHAR(50) NOT NULL,
    id_jurusan INTEGER REFERENCES jurusan(id_jurusan) ON DELETE SET NULL,
    semester INTEGER CHECK (semester BETWEEN 1 AND 8),
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- 3. MAHASISWA TABLE
-- ============================================
CREATE TABLE mahasiswa (
    id_mahasiswa SERIAL PRIMARY KEY,
    nim VARCHAR(20) NOT NULL UNIQUE,
    nama_mahasiswa VARCHAR(100) NOT NULL,
    jenis_kelamin CHAR(1) CHECK (jenis_kelamin IN ('L', 'P')),
    id_jurusan INTEGER REFERENCES jurusan(id_jurusan) ON DELETE SET NULL,
    id_kelas INTEGER REFERENCES kelas(id_kelas) ON DELETE SET NULL,
    angkatan INTEGER,
    alamat TEXT,
    foto VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- 4. DOSEN TABLE
-- ============================================
CREATE TABLE dosen (
    id_dosen SERIAL PRIMARY KEY,
    nip VARCHAR(18) NOT NULL UNIQUE,
    nama_dosen VARCHAR(100) NOT NULL,
    telepon VARCHAR(15),
    id_jurusan INTEGER REFERENCES jurusan(id_jurusan) ON DELETE SET NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- 5. MATA KULIAH TABLE
-- ============================================
CREATE TABLE mata_kuliah (
    id_matkul SERIAL PRIMARY KEY,
    kode_matkul VARCHAR(20) NOT NULL UNIQUE,
    nama_matkul VARCHAR(100) NOT NULL,
    sks INTEGER CHECK (sks BETWEEN 1 AND 6),
    semester INTEGER CHECK (semester BETWEEN 1 AND 8),
    id_jurusan INTEGER REFERENCES jurusan(id_jurusan) ON DELETE SET NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- 6. NILAI TABLE
-- ============================================
CREATE TABLE nilai (
    id_nilai SERIAL PRIMARY KEY,
    id_mahasiswa INTEGER NOT NULL REFERENCES mahasiswa(id_mahasiswa) ON DELETE CASCADE,
    id_matkul INTEGER NOT NULL REFERENCES mata_kuliah(id_matkul) ON DELETE CASCADE,
    nilai_angka DECIMAL(5,2) CHECK (nilai_angka BETWEEN 0 AND 100),
    nilai_huruf CHAR(1) CHECK (nilai_huruf IN ('A', 'B', 'C', 'D', 'E')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(id_mahasiswa, id_matkul)
);

-- ============================================
-- CREATE INDEXES FOR BETTER PERFORMANCE
-- ============================================
CREATE INDEX idx_mahasiswa_nim ON mahasiswa(nim);
CREATE INDEX idx_mahasiswa_jurusan ON mahasiswa(id_jurusan);
CREATE INDEX idx_mahasiswa_kelas ON mahasiswa(id_kelas);
CREATE INDEX idx_dosen_nip ON dosen(nip);
CREATE INDEX idx_dosen_jurusan ON dosen(id_jurusan);
CREATE INDEX idx_matkul_kode ON mata_kuliah(kode_matkul);
CREATE INDEX idx_matkul_jurusan ON mata_kuliah(id_jurusan);
CREATE INDEX idx_nilai_mahasiswa ON nilai(id_mahasiswa);
CREATE INDEX idx_nilai_matkul ON nilai(id_matkul);

-- ============================================
-- INSERT SAMPLE DATA
-- ============================================

-- Sample Jurusan
INSERT INTO jurusan (nama_jurusan) VALUES
('Teknik Informatika'),
('Sistem Informasi'),
('Teknik Komputer'),
('Manajemen Informatika');

-- Sample Kelas
INSERT INTO kelas (nama_kelas, id_jurusan, semester, keterangan) VALUES
('TI-1A', 1, 1, 'Kelas Reguler Pagi'),
('TI-1B', 1, 1, 'Kelas Reguler Siang'),
('SI-1A', 2, 1, 'Kelas Reguler Pagi'),
('TK-1A', 3, 1, 'Kelas Reguler Pagi');

-- Sample Mahasiswa
INSERT INTO mahasiswa (nim, nama_mahasiswa, jenis_kelamin, id_jurusan, id_kelas, angkatan, alamat) VALUES
('2024001', 'Ahmad Fauzi', 'L', 1, 1, 2024, 'Jl. Merdeka No. 10, Jakarta'),
('2024002', 'Siti Nurhaliza', 'P', 1, 1, 2024, 'Jl. Sudirman No. 25, Bandung'),
('2024003', 'Budi Santoso', 'L', 2, 3, 2024, 'Jl. Gatot Subroto No. 15, Surabaya'),
('2024004', 'Dewi Lestari', 'P', 1, 2, 2024, 'Jl. Ahmad Yani No. 30, Yogyakarta');

-- Sample Dosen
INSERT INTO dosen (nip, nama_dosen, telepon, id_jurusan) VALUES
('198501012010121001', 'Dr. Ir. Bambang Suryadi, M.Kom', '081234567890', 1),
('198703152012122002', 'Dr. Siti Aminah, S.Kom, M.T', '081234567891', 1),
('199001202015041001', 'Agus Setiawan, S.Kom, M.Kom', '081234567892', 2),
('198805102013031002', 'Rina Wati, S.T, M.T', '081234567893', 3);

-- Sample Mata Kuliah
INSERT INTO mata_kuliah (kode_matkul, nama_matkul, sks, semester, id_jurusan) VALUES
('TIF101', 'Algoritma dan Pemrograman', 4, 1, 1),
('TIF102', 'Matematika Diskrit', 3, 1, 1),
('TIF103', 'Basis Data', 3, 2, 1),
('TIF104', 'Struktur Data', 4, 2, 1),
('SIF101', 'Pengantar Sistem Informasi', 3, 1, 2),
('SIF102', 'Analisis dan Perancangan Sistem', 3, 3, 2);

-- Sample Nilai
INSERT INTO nilai (id_mahasiswa, id_matkul, nilai_angka, nilai_huruf) VALUES
(1, 1, 85.5, 'A'),
(1, 2, 78.0, 'B'),
(2, 1, 92.0, 'A'),
(2, 2, 88.5, 'A'),
(3, 5, 75.0, 'B'),
(4, 1, 68.0, 'C');

-- ============================================
-- VERIFICATION QUERIES
-- ============================================
-- Run these to verify the data was inserted correctly:

-- SELECT * FROM jurusan;
-- SELECT * FROM kelas;
-- SELECT * FROM mahasiswa;
-- SELECT * FROM dosen;
-- SELECT * FROM mata_kuliah;
-- SELECT * FROM nilai;

-- ============================================
-- NOTES
-- ============================================
-- 1. Make sure PostgreSQL is running on port 5433 (or update config/database.php)
-- 2. Create database first: CREATE DATABASE db_kampus;
-- 3. Run this script: psql -U postgres -d db_kampus -f database_schema.sql
-- 4. Create uploads directory: mkdir bdakhir/uploads
-- 5. Set permissions: chmod 755 bdakhir/uploads

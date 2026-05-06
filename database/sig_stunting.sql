-- ============================================
-- DATABASE FINAL: SIG Visualisasi Stunting
-- Desa Sumberwaru
-- ============================================

CREATE DATABASE IF NOT EXISTS sig_stunting;
USE sig_stunting;

DROP TABLE IF EXISTS stunting;
DROP TABLE IF EXISTS balita;
DROP TABLE IF EXISTS laporan;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS posyandu;
DROP TABLE IF EXISTS tahun;

-- TABEL posyandu
CREATE TABLE posyandu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_posyandu VARCHAR(100) NOT NULL,
    nama_dusun VARCHAR(100) NOT NULL,
    koordinat_lat DECIMAL(10,8) DEFAULT NULL,
    koordinat_lng DECIMAL(11,8) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- TABEL tahun
CREATE TABLE tahun (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tahun YEAR NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- TABEL users (ada id_posyandu untuk kader)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('kader','kpm','kelurahan') NOT NULL,
    id_posyandu INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_posyandu) REFERENCES posyandu(id)
);

-- TABEL balita (ada id_posyandu)
CREATE TABLE balita (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_bayi VARCHAR(100) NOT NULL,
    nama_ortu VARCHAR(100) NOT NULL,
    nik_ortu VARCHAR(30) DEFAULT NULL,
    tanggal_lahir DATE NOT NULL,
    umur_bulan INT NOT NULL,
    jenis_kelamin ENUM('L','P') NOT NULL,
    berat_badan DECIMAL(5,2) NOT NULL,
    tinggi_badan DECIMAL(5,2) NOT NULL,
    lingkar_kepala DECIMAL(5,2) DEFAULT NULL,
    lingkar_lengan DECIMAL(5,2) DEFAULT NULL,
    status_gizi ENUM('normal','beresiko','stunting') NOT NULL,
    bulan_pencatatan INT NOT NULL,
    id_posyandu INT NOT NULL,
    id_tahun INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_posyandu) REFERENCES posyandu(id),
    FOREIGN KEY (id_tahun) REFERENCES tahun(id)
);

-- TABEL stunting
CREATE TABLE stunting (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_balita INT NOT NULL,
    status_verifikasi ENUM('pending','terverifikasi','disetujui') DEFAULT 'pending',
    catatan TEXT,
    id_tahun INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_balita) REFERENCES balita(id),
    FOREIGN KEY (id_tahun) REFERENCES tahun(id)
);

-- TABEL laporan
CREATE TABLE laporan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    isi TEXT NOT NULL,
    id_posyandu INT NOT NULL,
    id_tahun INT NOT NULL,
    bulan_pencatatan INT DEFAULT NULL,
    status ENUM('dikirim','dibaca') DEFAULT 'dikirim',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_posyandu) REFERENCES posyandu(id),
    FOREIGN KEY (id_tahun) REFERENCES tahun(id)
);

-- ============================================
-- DATA POSYANDU (11 posyandu)
-- ============================================
INSERT INTO posyandu (nama_posyandu, nama_dusun) VALUES
('Posyandu Cotek 1',     'Dusun Cotek'),
('Posyandu Cotek 2',     'Dusun Cotek'),
('Posyandu Sidodadi 3',  'Dusun Sidodadi'),
('Posyandu Sidodadi 4',  'Dusun Sidodadi'),
('Posyandu Krajan 1',    'Dusun Krajan'),
('Posyandu Krajan 2',    'Dusun Krajan'),
('Posyandu Sidomulyo 1', 'Dusun Sidomulyo'),
('Posyandu Sidomulyo 2', 'Dusun Sidomulyo'),
('Posyandu Sidomulyo 3', 'Dusun Sidomulyo'),
('Posyandu Merak',       'Dusun Merak'),
('Posyandu Blangguan',   'Dusun Blangguan');

-- ============================================
-- DATA TAHUN
-- ============================================
INSERT INTO tahun (tahun) VALUES (2024),(2025),(2026);

-- ============================================
-- DATA USERS
-- Kader: 11 akun, masing-masing terikat ke 1 posyandu
-- KPM: 1 akun
-- Kelurahan: 1 akun
-- Password semua: password123
-- ============================================
INSERT INTO users (nama, username, password, role, id_posyandu) VALUES
-- Kader (id_posyandu 1-11 sesuai urutan posyandu di atas)
('Kader Cotek 1',     'kader_cotek1',     MD5('password123'), 'kader', 1),
('Kader Cotek 2',     'kader_cotek2',     MD5('password123'), 'kader', 2),
('Kader Sidodadi 3',  'kader_sidodadi3',  MD5('password123'), 'kader', 3),
('Kader Sidodadi 4',  'kader_sidodadi4',  MD5('password123'), 'kader', 4),
('Kader Krajan 1',    'kader_krajan1',    MD5('password123'), 'kader', 5),
('Kader Krajan 2',    'kader_krajan2',    MD5('password123'), 'kader', 6),
('Kader Sidomulyo 1', 'kader_sidomulyo1', MD5('password123'), 'kader', 7),
('Kader Sidomulyo 2', 'kader_sidomulyo2', MD5('password123'), 'kader', 8),
('Kader Sidomulyo 3', 'kader_sidomulyo3', MD5('password123'), 'kader', 9),
('Kader Merak',       'kader_merak',      MD5('password123'), 'kader', 10),
('Kader Blangguan',   'kader_blangguan',  MD5('password123'), 'kader', 11),
-- KPM & Kelurahan (tidak terikat posyandu)
('KPM Sumberwaru',    'kpm1',             MD5('password123'), 'kpm',       NULL),
('User Kelurahan',    'kelurahan1',       MD5('password123'), 'kelurahan', NULL);

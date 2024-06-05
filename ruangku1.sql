CREATE DATABASE ruangku1;

USE ruangku1;

CREATE TABLE pengguna (
    id_pengguna INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    alamat TEXT DEFAULT NULL,
    telepon VARCHAR(12) DEFAULT NULL,
    nama_pengguna VARCHAR(50) NOT NULL UNIQUE,
    kata_sandi VARCHAR(255) NOT NULL,
    peran ENUM('Admin', 'Operator', 'Manajer') NOT NULL,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE ruang (
    id_ruang INT AUTO_INCREMENT PRIMARY KEY,
    nama_ruang VARCHAR(50) NOT NULL,
    kapasitas INT NOT NULL CHECK (kapasitas BETWEEN 7 AND 20),
    status ENUM('tersedia', 'tidak tersedia') DEFAULT 'tersedia',
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE alat (
    id_alat INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    status ENUM('tersedia', 'tidak tersedia') DEFAULT 'tersedia',
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE tarif (
    id_tarif INT AUTO_INCREMENT PRIMARY KEY,
    id_ruang INT DEFAULT NULL,
    id_alat INT DEFAULT NULL,
    tarif_ruang DECIMAL(10, 2) DEFAULT NULL,
    tarif_alat DECIMAL(10, 2) DEFAULT NULL,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_ruang) REFERENCES ruang(id_ruang),
    FOREIGN KEY (id_alat) REFERENCES alat(id_alat)
);

CREATE TABLE pelanggan (
    id_pelanggan INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    alamat TEXT,
    nomor_hp VARCHAR(20) NOT NULL,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE transaksi (
    id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
    id_pelanggan INT NOT NULL,
    id_operator INT NOT NULL,
    id_ruang INT NOT NULL,
    waktu_mulai DATETIME NOT NULL,
    waktu_selesai DATETIME NOT NULL,
    total_biaya DECIMAL(10, 2) NOT NULL,
    status_pembayaran ENUM('lunas', 'belum lunas') DEFAULT 'belum lunas',
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan),
    FOREIGN KEY (id_operator) REFERENCES pengguna(id_pengguna),
    FOREIGN KEY (id_ruang) REFERENCES ruang(id_ruang)
);

CREATE TABLE detail_transaksi (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_transaksi INT NOT NULL,
    id_alat INT NOT NULL,
    tarif_alat DECIMAL(10, 2) NOT NULL,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_transaksi) REFERENCES transaksi(id_transaksi),
    FOREIGN KEY (id_alat) REFERENCES alat(id_alat)
);

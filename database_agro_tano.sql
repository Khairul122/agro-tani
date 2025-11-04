-- Database: agro_tano
-- Struktur tabel untuk database toko_gani_agro_tani

-- 1. USERS (Admin, Kasir, Owner)
CREATE DATABASE IF NOT EXISTS agro_tano;
USE agro_tano;

CREATE TABLE users (
  id_user INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  nama_lengkap VARCHAR(100),
  role ENUM('admin','kasir','owner') DEFAULT 'kasir',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. KATEGORI BARANG
CREATE TABLE kategori_barang (
  id_kategori INT AUTO_INCREMENT PRIMARY KEY,
  nama_kategori VARCHAR(100) NOT NULL
);

-- 3. BARANG
CREATE TABLE barang (
  id_barang INT AUTO_INCREMENT PRIMARY KEY,
  id_kategori INT,
  kode_barang VARCHAR(30) UNIQUE,
  nama_barang VARCHAR(150) NOT NULL,
  satuan VARCHAR(50),
  harga_beli DECIMAL(15,2),
  harga_jual DECIMAL(15,2),
  stok INT DEFAULT 0,
  FOREIGN KEY (id_kategori) REFERENCES kategori_barang(id_kategori) ON DELETE SET NULL
);

-- 4. PELANGGAN (opsional)
CREATE TABLE pelanggan (
  id_pelanggan INT AUTO_INCREMENT PRIMARY KEY,
  nama_pelanggan VARCHAR(150),
  alamat TEXT,
  no_hp VARCHAR(20)
);

-- 5. SUPPLIER
CREATE TABLE supplier (
  id_supplier INT AUTO_INCREMENT PRIMARY KEY,
  nama_supplier VARCHAR(150),
  alamat TEXT,
  no_hp VARCHAR(20)
);

-- 6. PENJUALAN
CREATE TABLE penjualan (
  id_penjualan INT AUTO_INCREMENT PRIMARY KEY,
  no_faktur VARCHAR(50) UNIQUE,
  tanggal DATE DEFAULT (CURRENT_DATE),
  id_pelanggan INT,
  total DECIMAL(15,2) DEFAULT 0,
  id_user INT,
  FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan) ON DELETE SET NULL,
  FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE SET NULL
);

-- 7. DETAIL PENJUALAN
CREATE TABLE detail_penjualan (
  id_detail INT AUTO_INCREMENT PRIMARY KEY,
  id_penjualan INT,
  id_barang INT,
  jumlah INT,
  harga_satuan DECIMAL(15,2),
  subtotal DECIMAL(15,2),
  FOREIGN KEY (id_penjualan) REFERENCES penjualan(id_penjualan) ON DELETE CASCADE,
  FOREIGN KEY (id_barang) REFERENCES barang(id_barang) ON DELETE CASCADE
);

-- 8. PEMBELIAN (stok masuk)
CREATE TABLE pembelian (
  id_pembelian INT AUTO_INCREMENT PRIMARY KEY,
  no_nota VARCHAR(50) UNIQUE,
  tanggal DATE DEFAULT (CURRENT_DATE),
  id_supplier INT,
  total DECIMAL(15,2) DEFAULT 0,
  id_user INT,
  FOREIGN KEY (id_supplier) REFERENCES supplier(id_supplier) ON DELETE SET NULL,
  FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE SET NULL
);

-- 9. DETAIL PEMBELIAN
CREATE TABLE detail_pembelian (
  id_detail INT AUTO_INCREMENT PRIMARY KEY,
  id_pembelian INT,
  id_barang INT,
  jumlah INT,
  harga_beli DECIMAL(15,2),
  subtotal DECIMAL(15,2),
  FOREIGN KEY (id_pembelian) REFERENCES pembelian(id_pembelian) ON DELETE CASCADE,
  FOREIGN KEY (id_barang) REFERENCES barang(id_barang) ON DELETE CASCADE
);

-- 10. AKTIVITAS USER (opsional untuk log aktivitas)
CREATE TABLE aktivitas_user (
  id_log INT AUTO_INCREMENT PRIMARY KEY,
  id_user INT,
  aktivitas VARCHAR(255),
  waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE SET NULL
);

-- INSERT DATA AWAL
-- Tambahkan data role pengguna (admin, kasir, owner) dengan password dalam bentuk teks biasa untuk kompatibilitas
INSERT INTO users (username, password, nama_lengkap, role)
VALUES 
('admin', 'admin123', 'Administrator', 'admin'),
('kasir', 'kasir123', 'Kasir Utama', 'kasir'),
('owner', 'owner123', 'Pemilik Toko', 'owner');

-- Tambahkan kategori awal
INSERT INTO kategori_barang (nama_kategori)
VALUES ('Pupuk'), ('Benih'), ('Pestisida'), ('Alat Pertanian');

-- Tambahkan beberapa barang contoh
INSERT INTO barang (id_kategori, kode_barang, nama_barang, satuan, harga_beli, harga_jual, stok)
VALUES
(1, 'P001', 'Pupuk Urea', 'kg', 12000, 15000, 100),
(1, 'P002', 'Pupuk NPK', 'kg', 14000, 17500, 80),
(2, 'B001', 'Benih Padi IR64', 'kg', 20000, 25000, 60);

-- Tambahkan supplier contoh
INSERT INTO supplier (nama_supplier, alamat, no_hp)
VALUES
('CV. Tani Jaya', 'Jl. Raya Padang - Pariaman', '08123456789'),
('PT. Agro Mandiri', 'Jl. Lintas Sumatera No. 88', '08129876543');

-- Tambahkan pelanggan contoh
INSERT INTO pelanggan (nama_pelanggan, alamat, no_hp)
VALUES
('Budi Santoso', 'Tanjung Basung II', '081234111222'),
('Siti Aminah', 'Batang Anai', '081233334444');
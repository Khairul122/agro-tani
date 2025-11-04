<?php
require_once 'models/PelangganModel.php';

class PelangganController {
    private $model;

    public function __construct() {
        $this->model = new PelangganModel();
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=index');
            exit();
        }
        
        $pelanggan = $this->model->getAll();
        include 'views/pelanggan/index.php';
    }

    public function tambah() {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'kasir')) {
            echo "<script>alert('Akses ditolak!'); window.location.href='index.php?controller=dashboard&action=index';</script>";
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama_pelanggan = trim($_POST['nama_pelanggan']);
            $alamat = trim($_POST['alamat']);
            $no_hp = trim($_POST['no_hp']);
            
            // Validasi
            if (empty($nama_pelanggan) || empty($no_hp)) {
                echo "<script>alert('Nama pelanggan dan nomor HP wajib diisi!'); window.location.href='index.php?controller=pelanggan&action=tambah';</script>";
                exit();
            }
            
            // Validasi format nomor HP
            if (!preg_match('/^[0-9+\-\s()]+$/', $no_hp)) {
                echo "<script>alert('Format nomor HP tidak valid!'); window.location.href='index.php?controller=pelanggan&action=tambah';</script>";
                exit();
            }
            
            // Cek apakah nama pelanggan sudah ada
            if ($this->model->existsByNama($nama_pelanggan)) {
                echo "<script>alert('Nama pelanggan sudah ada!'); window.location.href='index.php?controller=pelanggan&action=tambah';</script>";
                exit();
            }
            
            if ($this->model->create($nama_pelanggan, $alamat, $no_hp)) {
                echo "<script>alert('Pelanggan berhasil ditambahkan!'); window.location.href='index.php?controller=pelanggan&action=index';</script>";
            } else {
                echo "<script>alert('Gagal menambahkan pelanggan!'); window.location.href='index.php?controller=pelanggan&action=tambah';</script>";
            }
            exit();
        }
        
        include 'views/pelanggan/form.php';
    }

    public function edit() {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'kasir')) {
            echo "<script>alert('Akses ditolak!'); window.location.href='index.php?controller=dashboard&action=index';</script>";
            exit();
        }
        
        $id = $_GET['id'] ?? 0;
        $pelanggan = $this->model->getById($id);
        
        if (!$pelanggan) {
            echo "<script>alert('Pelanggan tidak ditemukan!'); window.location.href='index.php?controller=pelanggan&action=index';</script>";
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama_pelanggan = trim($_POST['nama_pelanggan']);
            $alamat = trim($_POST['alamat']);
            $no_hp = trim($_POST['no_hp']);
            
            // Validasi
            if (empty($nama_pelanggan) || empty($no_hp)) {
                echo "<script>alert('Nama pelanggan dan nomor HP wajib diisi!'); window.location.href='index.php?controller=pelanggan&action=edit&id=$id';</script>";
                exit();
            }
            
            // Validasi format nomor HP
            if (!preg_match('/^[0-9+\-\s()]+$/', $no_hp)) {
                echo "<script>alert('Format nomor HP tidak valid!'); window.location.href='index.php?controller=pelanggan&action=edit&id=$id';</script>";
                exit();
            }
            
            // Cek apakah nama pelanggan sudah ada (selain pelanggan saat ini)
            if ($this->model->existsByNama($nama_pelanggan, $id)) {
                echo "<script>alert('Nama pelanggan sudah ada!'); window.location.href='index.php?controller=pelanggan&action=edit&id=$id';</script>";
                exit();
            }
            
            if ($this->model->update($id, $nama_pelanggan, $alamat, $no_hp)) {
                echo "<script>alert('Pelanggan berhasil diupdate!'); window.location.href='index.php?controller=pelanggan&action=index';</script>";
            } else {
                echo "<script>alert('Gagal mengupdate pelanggan!'); window.location.href='index.php?controller=pelanggan&action=edit&id=$id';</script>";
            }
            exit();
        }
        
        include 'views/pelanggan/form.php';
    }

    public function hapus() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            echo "<script>alert('Akses ditolak! Hanya admin yang dapat menghapus pelanggan.'); window.location.href='index.php?controller=pelanggan&action=index';</script>";
            exit();
        }
        
        $id = $_GET['id'] ?? 0;
        $pelanggan = $this->model->getById($id);
        
        if (!$pelanggan) {
            echo "<script>alert('Pelanggan tidak ditemukan!'); window.location.href='index.php?controller=pelanggan&action=index';</script>";
            exit();
        }
        
        // Cek apakah pelanggan ini digunakan dalam transaksi penjualan
        $database = new Database();
        $conn = $database->getConnection();
        $query_check = "SELECT COUNT(*) FROM penjualan WHERE id_pelanggan = :id_pelanggan";
        $stmt_check = $conn->prepare($query_check);
        $stmt_check->bindParam(':id_pelanggan', $id);
        $stmt_check->execute();
        $count_penjualan = $stmt_check->fetchColumn();
        
        if ($count_penjualan > 0) {
            echo "<script>alert('Tidak dapat menghapus pelanggan yang sedang digunakan dalam transaksi penjualan!'); window.location.href='index.php?controller=pelanggan&action=index';</script>";
            exit();
        }
        
        if ($this->model->delete($id)) {
            echo "<script>alert('Pelanggan berhasil dihapus!'); window.location.href='index.php?controller=pelanggan&action=index';</script>";
        } else {
            echo "<script>alert('Gagal menghapus pelanggan!'); window.location.href='index.php?controller=pelanggan&action=index';</script>";
        }
        exit();
    }
}
?>
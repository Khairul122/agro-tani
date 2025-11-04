<?php
require_once 'models/SupplierModel.php';

class SupplierController {
    private $model;

    public function __construct() {
        $this->model = new SupplierModel();
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=index');
            exit();
        }
        
        $supplier = $this->model->getAll();
        include 'views/supplier/index.php';
    }

    public function tambah() {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'kasir')) {
            echo "<script>alert('Akses ditolak!'); window.location.href='index.php?controller=dashboard&action=index';</script>";
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama_supplier = trim($_POST['nama_supplier']);
            $alamat = trim($_POST['alamat']);
            $no_hp = trim($_POST['no_hp']);
            
            // Validasi
            if (empty($nama_supplier) || empty($no_hp)) {
                echo "<script>alert('Nama supplier dan nomor HP wajib diisi!'); window.location.href='index.php?controller=supplier&action=tambah';</script>";
                exit();
            }
            
            // Validasi format nomor HP
            if (!preg_match('/^[0-9+\-\s()]+$/', $no_hp)) {
                echo "<script>alert('Format nomor HP tidak valid!'); window.location.href='index.php?controller=supplier&action=tambah';</script>";
                exit();
            }
            
            // Cek apakah nama supplier sudah ada
            if ($this->model->existsByNama($nama_supplier)) {
                echo "<script>alert('Nama supplier sudah ada!'); window.location.href='index.php?controller=supplier&action=tambah';</script>";
                exit();
            }
            
            if ($this->model->create($nama_supplier, $alamat, $no_hp)) {
                echo "<script>alert('Supplier berhasil ditambahkan!'); window.location.href='index.php?controller=supplier&action=index';</script>";
            } else {
                echo "<script>alert('Gagal menambahkan supplier!'); window.location.href='index.php?controller=supplier&action=tambah';</script>";
            }
            exit();
        }
        
        include 'views/supplier/form.php';
    }

    public function edit() {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'kasir')) {
            echo "<script>alert('Akses ditolak!'); window.location.href='index.php?controller=dashboard&action=index';</script>";
            exit();
        }
        
        $id = $_GET['id'] ?? 0;
        $supplier = $this->model->getById($id);
        
        if (!$supplier) {
            echo "<script>alert('Supplier tidak ditemukan!'); window.location.href='index.php?controller=supplier&action=index';</script>";
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama_supplier = trim($_POST['nama_supplier']);
            $alamat = trim($_POST['alamat']);
            $no_hp = trim($_POST['no_hp']);
            
            // Validasi
            if (empty($nama_supplier) || empty($no_hp)) {
                echo "<script>alert('Nama supplier dan nomor HP wajib diisi!'); window.location.href='index.php?controller=supplier&action=edit&id=$id';</script>";
                exit();
            }
            
            // Validasi format nomor HP
            if (!preg_match('/^[0-9+\-\s()]+$/', $no_hp)) {
                echo "<script>alert('Format nomor HP tidak valid!'); window.location.href='index.php?controller=supplier&action=edit&id=$id';</script>";
                exit();
            }
            
            // Cek apakah nama supplier sudah ada (selain supplier saat ini)
            if ($this->model->existsByNama($nama_supplier, $id)) {
                echo "<script>alert('Nama supplier sudah ada!'); window.location.href='index.php?controller=supplier&action=edit&id=$id';</script>";
                exit();
            }
            
            if ($this->model->update($id, $nama_supplier, $alamat, $no_hp)) {
                echo "<script>alert('Supplier berhasil diupdate!'); window.location.href='index.php?controller=supplier&action=index';</script>";
            } else {
                echo "<script>alert('Gagal mengupdate supplier!'); window.location.href='index.php?controller=supplier&action=edit&id=$id';</script>";
            }
            exit();
        }
        
        include 'views/supplier/form.php';
    }

    public function hapus() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            echo "<script>alert('Akses ditolak! Hanya admin yang dapat menghapus supplier.'); window.location.href='index.php?controller=supplier&action=index';</script>";
            exit();
        }
        
        $id = $_GET['id'] ?? 0;
        $supplier = $this->model->getById($id);
        
        if (!$supplier) {
            echo "<script>alert('Supplier tidak ditemukan!'); window.location.href='index.php?controller=supplier&action=index';</script>";
            exit();
        }
        
        // Cek apakah supplier ini digunakan dalam transaksi pembelian
        $database = new Database();
        $conn = $database->getConnection();
        $query_check = "SELECT COUNT(*) FROM pembelian WHERE id_supplier = :id_supplier";
        $stmt_check = $conn->prepare($query_check);
        $stmt_check->bindParam(':id_supplier', $id);
        $stmt_check->execute();
        $count_pembelian = $stmt_check->fetchColumn();
        
        if ($count_pembelian > 0) {
            echo "<script>alert('Tidak dapat menghapus supplier yang sedang digunakan dalam transaksi pembelian!'); window.location.href='index.php?controller=supplier&action=index';</script>";
            exit();
        }
        
        if ($this->model->delete($id)) {
            echo "<script>alert('Supplier berhasil dihapus!'); window.location.href='index.php?controller=supplier&action=index';</script>";
        } else {
            echo "<script>alert('Gagal menghapus supplier!'); window.location.href='index.php?controller=supplier&action=index';</script>";
        }
        exit();
    }
}
?>
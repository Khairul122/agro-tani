<?php
require_once 'models/KategoriBarangModel.php';

class KategoriBarangController {
    private $model;

    public function __construct() {
        $this->model = new KategoriBarangModel();
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=index');
            exit();
        }
        
        $kategori_barang = $this->model->getAll();
        include 'views/kategori-barang/index.php';
    }

    public function tambah() {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'kasir')) {
            echo "<script>alert('Akses ditolak!'); window.location.href='index.php?controller=dashboard&action=index';</script>";
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama_kategori = trim($_POST['nama_kategori']);
            
            // Validasi
            if (empty($nama_kategori)) {
                echo "<script>alert('Nama kategori tidak boleh kosong!'); window.location.href='index.php?controller=kategoriBarang&action=tambah';</script>";
                exit();
            }
            
            // Cek apakah kategori sudah ada
            if ($this->model->existsByName($nama_kategori)) {
                echo "<script>alert('Nama kategori sudah ada!'); window.location.href='index.php?controller=kategoriBarang&action=tambah';</script>";
                exit();
            }
            
            if ($this->model->create($nama_kategori)) {
                echo "<script>alert('Kategori berhasil ditambahkan!'); window.location.href='index.php?controller=kategoriBarang&action=index';</script>";
            } else {
                echo "<script>alert('Gagal menambahkan kategori!'); window.location.href='index.php?controller=kategoriBarang&action=tambah';</script>";
            }
            exit();
        }
        
        include 'views/kategori-barang/form.php';
    }

    public function edit() {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'kasir')) {
            echo "<script>alert('Akses ditolak!'); window.location.href='index.php?controller=dashboard&action=index';</script>";
            exit();
        }
        
        $id = $_GET['id'] ?? 0;
        $kategori = $this->model->getById($id);
        
        if (!$kategori) {
            echo "<script>alert('Kategori tidak ditemukan!'); window.location.href='index.php?controller=kategoriBarang&action=index';</script>";
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama_kategori = trim($_POST['nama_kategori']);
            
            // Validasi
            if (empty($nama_kategori)) {
                echo "<script>alert('Nama kategori tidak boleh kosong!'); window.location.href='index.php?controller=kategoriBarang&action=edit&id=$id';</script>";
                exit();
            }
            
            // Cek apakah kategori sudah ada (selain kategori saat ini)
            if ($this->model->existsByName($nama_kategori, $id)) {
                echo "<script>alert('Nama kategori sudah ada!'); window.location.href='index.php?controller=kategoriBarang&action=edit&id=$id';</script>";
                exit();
            }
            
            if ($this->model->update($id, $nama_kategori)) {
                echo "<script>alert('Kategori berhasil diupdate!'); window.location.href='index.php?controller=kategoriBarang&action=index';</script>";
            } else {
                echo "<script>alert('Gagal mengupdate kategori!'); window.location.href='index.php?controller=kategoriBarang&action=edit&id=$id';</script>";
            }
            exit();
        }
        
        include 'views/kategori-barang/form.php';
    }

    public function hapus() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            echo "<script>alert('Akses ditolak! Hanya admin yang dapat menghapus kategori.'); window.location.href='index.php?controller=kategoriBarang&action=index';</script>";
            exit();
        }
        
        $id = $_GET['id'] ?? 0;
        $kategori = $this->model->getById($id);
        
        if (!$kategori) {
            echo "<script>alert('Kategori tidak ditemukan!'); window.location.href='index.php?controller=kategoriBarang&action=index';</script>";
            exit();
        }
        
        // Cek apakah kategori ini digunakan oleh barang
        $database = new Database();
        $conn = $database->getConnection();
        $query_check = "SELECT COUNT(*) FROM barang WHERE id_kategori = :id_kategori";
        $stmt_check = $conn->prepare($query_check);
        $stmt_check->bindParam(':id_kategori', $id);
        $stmt_check->execute();
        $count_barang = $stmt_check->fetchColumn();
        
        if ($count_barang > 0) {
            echo "<script>alert('Tidak dapat menghapus kategori yang sedang digunakan oleh barang!'); window.location.href='index.php?controller=kategoriBarang&action=index';</script>";
            exit();
        }
        
        if ($this->model->delete($id)) {
            echo "<script>alert('Kategori berhasil dihapus!'); window.location.href='index.php?controller=kategoriBarang&action=index';</script>";
        } else {
            echo "<script>alert('Gagal menghapus kategori!'); window.location.href='index.php?controller=kategoriBarang&action=index';</script>";
        }
        exit();
    }
}
?>
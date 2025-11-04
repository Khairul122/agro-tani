<?php
require_once 'models/BarangModel.php';

class BarangController {
    private $model;

    public function __construct() {
        $this->model = new BarangModel();
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=index');
            exit();
        }
        
        $barang = $this->model->getAll();
        include 'views/barang/index.php';
    }

    public function tambah() {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'kasir')) {
            echo "<script>alert('Akses ditolak!'); window.location.href='index.php?controller=dashboard&action=index';</script>";
            exit();
        }
        
        $kategori = $this->model->getKategoriAll();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $kode_barang = trim($_POST['kode_barang']);
            $nama_barang = trim($_POST['nama_barang']);
            $id_kategori = $_POST['id_kategori'];
            $satuan = trim($_POST['satuan']);
            $harga_beli = $_POST['harga_beli'];
            $harga_jual = $_POST['harga_jual'];
            $stok = $_POST['stok'];
            
            // Validasi
            if (empty($kode_barang) || empty($nama_barang) || empty($id_kategori) || empty($satuan) || empty($harga_beli) || empty($harga_jual)) {
                echo "<script>alert('Semua field wajib diisi!'); window.location.href='index.php?controller=barang&action=tambah';</script>";
                exit();
            }
            
            // Validasi harga
            if (!is_numeric($harga_beli) || !is_numeric($harga_jual) || $harga_beli < 0 || $harga_jual < 0) {
                echo "<script>alert('Harga harus berupa angka positif!'); window.location.href='index.php?controller=barang&action=tambah';</script>";
                exit();
            }
            
            // Validasi stok
            if (!is_numeric($stok) || $stok < 0) {
                echo "<script>alert('Stok harus berupa angka positif!'); window.location.href='index.php?controller=barang&action=tambah';</script>";
                exit();
            }
            
            // Cek apakah kode barang sudah ada
            if ($this->model->existsByKode($kode_barang)) {
                echo "<script>alert('Kode barang sudah ada!'); window.location.href='index.php?controller=barang&action=tambah';</script>";
                exit();
            }
            
            if ($this->model->create($kode_barang, $nama_barang, $id_kategori, $satuan, $harga_beli, $harga_jual, $stok)) {
                echo "<script>alert('Barang berhasil ditambahkan!'); window.location.href='index.php?controller=barang&action=index';</script>";
            } else {
                echo "<script>alert('Gagal menambahkan barang!'); window.location.href='index.php?controller=barang&action=tambah';</script>";
            }
            exit();
        }
        
        include 'views/barang/form.php';
    }

    public function edit() {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'kasir')) {
            echo "<script>alert('Akses ditolak!'); window.location.href='index.php?controller=dashboard&action=index';</script>";
            exit();
        }
        
        $id = $_GET['id'] ?? 0;
        $barang = $this->model->getById($id);
        $kategori = $this->model->getKategoriAll();
        
        if (!$barang) {
            echo "<script>alert('Barang tidak ditemukan!'); window.location.href='index.php?controller=barang&action=index';</script>";
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $kode_barang = trim($_POST['kode_barang']);
            $nama_barang = trim($_POST['nama_barang']);
            $id_kategori = $_POST['id_kategori'];
            $satuan = trim($_POST['satuan']);
            $harga_beli = $_POST['harga_beli'];
            $harga_jual = $_POST['harga_jual'];
            $stok = $_POST['stok'];
            
            // Validasi
            if (empty($kode_barang) || empty($nama_barang) || empty($id_kategori) || empty($satuan) || empty($harga_beli) || empty($harga_jual)) {
                echo "<script>alert('Semua field wajib diisi!'); window.location.href='index.php?controller=barang&action=edit&id=$id';</script>";
                exit();
            }
            
            // Validasi harga
            if (!is_numeric($harga_beli) || !is_numeric($harga_jual) || $harga_beli < 0 || $harga_jual < 0) {
                echo "<script>alert('Harga harus berupa angka positif!'); window.location.href='index.php?controller=barang&action=edit&id=$id';</script>";
                exit();
            }
            
            // Validasi stok
            if (!is_numeric($stok) || $stok < 0) {
                echo "<script>alert('Stok harus berupa angka positif!'); window.location.href='index.php?controller=barang&action=edit&id=$id';</script>";
                exit();
            }
            
            // Cek apakah kode barang sudah ada (selain barang saat ini)
            if ($this->model->existsByKode($kode_barang, $id)) {
                echo "<script>alert('Kode barang sudah ada!'); window.location.href='index.php?controller=barang&action=edit&id=$id';</script>";
                exit();
            }
            
            if ($this->model->update($id, $kode_barang, $nama_barang, $id_kategori, $satuan, $harga_beli, $harga_jual, $stok)) {
                echo "<script>alert('Barang berhasil diupdate!'); window.location.href='index.php?controller=barang&action=index';</script>";
            } else {
                echo "<script>alert('Gagal mengupdate barang!'); window.location.href='index.php?controller=barang&action=edit&id=$id';</script>";
            }
            exit();
        }
        
        include 'views/barang/form.php';
    }

    public function hapus() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            echo "<script>alert('Akses ditolak! Hanya admin yang dapat menghapus barang.'); window.location.href='index.php?controller=barang&action=index';</script>";
            exit();
        }
        
        $id = $_GET['id'] ?? 0;
        $barang = $this->model->getById($id);
        
        if (!$barang) {
            echo "<script>alert('Barang tidak ditemukan!'); window.location.href='index.php?controller=barang&action=index';</script>";
            exit();
        }
        
        // Cek apakah barang ini digunakan dalam transaksi penjualan atau pembelian
        $database = new Database();
        $conn = $database->getConnection();
        
        // Cek di detail_penjualan
        $query_check_penjualan = "SELECT COUNT(*) FROM detail_penjualan WHERE id_barang = :id_barang";
        $stmt_check_penjualan = $conn->prepare($query_check_penjualan);
        $stmt_check_penjualan->bindParam(':id_barang', $id);
        $stmt_check_penjualan->execute();
        $count_penjualan = $stmt_check_penjualan->fetchColumn();
        
        // Cek di detail_pembelian
        $query_check_pembelian = "SELECT COUNT(*) FROM detail_pembelian WHERE id_barang = :id_barang";
        $stmt_check_pembelian = $conn->prepare($query_check_pembelian);
        $stmt_check_pembelian->bindParam(':id_barang', $id);
        $stmt_check_pembelian->execute();
        $count_pembelian = $stmt_check_pembelian->fetchColumn();
        
        if ($count_penjualan > 0 || $count_pembelian > 0) {
            echo "<script>alert('Tidak dapat menghapus barang yang sedang digunakan dalam transaksi!'); window.location.href='index.php?controller=barang&action=index';</script>";
            exit();
        }
        
        if ($this->model->delete($id)) {
            echo "<script>alert('Barang berhasil dihapus!'); window.location.href='index.php?controller=barang&action=index';</script>";
        } else {
            echo "<script>alert('Gagal menghapus barang!'); window.location.href='index.php?controller=barang&action=index';</script>";
        }
        exit();
    }
}
?>